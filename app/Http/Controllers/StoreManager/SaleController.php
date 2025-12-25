<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\StockAllotmentCondition;
use App\Exports\SalesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function create(Request $request)
    {
        $customerId = $request->input('customer_id');
        
        if (!$customerId) {
            return redirect()->route('store.customer.search')
                ->with('error', 'Please search for a customer first.');
        }

        $customer = Customer::with(['district', 'mandal', 'sales.store'])->findOrFail($customerId);
        
        $user = Auth::user();
        $store = $user->store;

        $balanceStock = $customer->balance_stock;

        // Get sales from other stores
        $otherStoresSales = $customer->sales()
            // ->where('store_id', '!=', $store->id)
            ->with('store')
            ->get();

        // Get stock allotment condition for this district
        $condition = StockAllotmentCondition::getStockAllocationForDistrict(
            $customer->district_id, 
            $customer->total_land
        );

        $maxQuantity = $balanceStock;
        $daysRemaining = null;
        $canSell = true;
        $restrictionMessage = null;
        $intervalInfo = null;

        if ($condition && $condition['interval_days'] > 0) {
            $atATime = $condition['at_a_time'];
            $intervalDays = $condition['interval_days'];
            
            // Get ALL sales for this customer across ALL stores (condition applies globally)
            $allSales = $customer->sales()
                ->orderBy('created_at', 'desc')
                ->get();

            $now = Carbon::now();
            
            if ($allSales->count() > 0) {
                // Find the first sale that started the current active interval
                // We need to find which interval we're currently in
                $salesChronological = $allSales->sortBy('created_at');
                $nowStartOfDay = $now->copy()->startOfDay();
                
                // Find the current active interval by calculating intervals sequentially from first sale
                $currentIntervalStart = null;
                $currentIntervalEndDate = null;
                
                $firstSaleDate = Carbon::parse($salesChronological->first()->created_at)->startOfDay();
                
                // Calculate intervals sequentially from first sale
                $intervalStart = $firstSaleDate->copy();
                
                while ($intervalStart->lte($nowStartOfDay->copy()->addDays($intervalDays * 2))) {
                    $intervalEnd = $intervalStart->copy()->addDays($intervalDays - 1); // Inclusive end date
                    
                    // Check if today falls within this interval
                    if ($nowStartOfDay->gte($intervalStart) && $nowStartOfDay->lte($intervalEnd)) {
                        // This is the current interval
                        $currentIntervalStart = $intervalStart->copy();
                        $currentIntervalEndDate = $intervalEnd->copy();
                        break;
                    }
                    
                    // Move to next interval (day after current interval ends)
                    $intervalStart = $intervalEnd->copy()->addDay();
                }
                
                // If we found a current interval
                if ($currentIntervalStart && $currentIntervalEndDate) {
                    // Get all sales in this current interval
                    $salesInCurrentInterval = $allSales->filter(function($sale) use ($currentIntervalStart, $currentIntervalEndDate) {
                        $saleDate = Carbon::parse($sale->created_at)->startOfDay();
                        return $saleDate->gte($currentIntervalStart) && $saleDate->lte($currentIntervalEndDate);
                    });
                    
                    $totalSoldInInterval = $salesInCurrentInterval->sum('quantity');
                    $remainingFromCurrentInterval = max(0, $atATime - $totalSoldInInterval);
                    
                    // Calculate accumulated remaining from expired intervals
                    $accumulatedRemaining = 0;
                    $processedIntervals = [];
                    
                    foreach ($salesChronological as $sale) {
                        $saleDate = Carbon::parse($sale->created_at)->startOfDay();
                        $intervalEnd = $saleDate->copy()->addDays($intervalDays - 1);
                        
                        // Skip if this interval hasn't expired yet or is the current interval
                        if ($now->startOfDay()->lte($intervalEnd)) {
                            continue;
                        }
                        
                        // Check if we've already processed this interval
                        $intervalKey = $saleDate->format('Y-m-d');
                        if (isset($processedIntervals[$intervalKey])) {
                            continue;
                        }
                        
                        // Get all sales that belong to this interval
                        $salesInThisInterval = $allSales->filter(function($s) use ($saleDate, $intervalEnd) {
                            $sd = Carbon::parse($s->created_at)->startOfDay();
                            return $sd->gte($saleDate) && $sd->lte($intervalEnd);
                        });
                        
                        // Calculate remaining from this interval
                        $totalSoldInThisInterval = $salesInThisInterval->sum('quantity');
                        $remainingFromThisInterval = max(0, $atATime - $totalSoldInThisInterval);
                        
                        // Add to accumulated remaining
                        if ($remainingFromThisInterval > 0) {
                            $accumulatedRemaining += $remainingFromThisInterval;
                        }
                        
                        $processedIntervals[$intervalKey] = true;
                    }
                    
                    // Total available = current interval remaining + accumulated remaining
                    $totalAvailable = $remainingFromCurrentInterval + $accumulatedRemaining;
                    $maxQuantity = min($balanceStock, $totalAvailable);
                    
                    if ($totalAvailable <= 0) {
                        $canSell = false;
                        // Next interval starts the day after current interval ends
                        $nextIntervalStart = $currentIntervalEndDate->copy()->addDay();
                        $restrictionMessage = "Sale locked until " . $nextIntervalStart->format('d M Y') . ". Remaining " . $atATime . " bags will be released on " . $nextIntervalStart->format('d M Y');
                    } else {
                        $canSell = true;
                        if ($accumulatedRemaining > 0) {
                            $intervalInfo = "You can take up to {$totalAvailable} bags ({$remainingFromCurrentInterval} from current interval + {$accumulatedRemaining} remaining from previous intervals). Valid until " . $currentIntervalEndDate->format('d M Y');
                        } else {
                            $intervalInfo = "You can take remaining {$remainingFromCurrentInterval} bag(s) from current interval (valid until " . $currentIntervalEndDate->format('d M Y') . ")";
                        }
                    }
                } else {
                    // Current interval has expired - calculate accumulated remaining from all expired intervals
                    $accumulatedRemaining = 0;
                    $processedIntervals = [];
                    
                    // Process all sales chronologically (oldest first) to calculate remaining from each interval
                    $salesChronological = $allSales->sortBy('created_at');
                    
                    foreach ($salesChronological as $sale) {
                        $saleDate = Carbon::parse($sale->created_at)->startOfDay();
                        // Interval end date is inclusive: sale date + intervalDays - 1
                        $intervalEnd = $saleDate->copy()->addDays($intervalDays - 1);
                        
                        // Skip if this interval hasn't expired yet (check if today is before or equal to interval end)
                        if ($now->startOfDay()->lte($intervalEnd)) {
                            continue;
                        }
                        
                        // Check if we've already processed this interval (same date = same interval start)
                        $intervalKey = $saleDate->format('Y-m-d');
                        if (isset($processedIntervals[$intervalKey])) {
                            continue;
                        }
                        
                        // Get all sales that belong to this interval
                        // A sale belongs to an interval if it falls within intervalDays from the interval start date
                        $salesInThisInterval = $allSales->filter(function($s) use ($saleDate, $intervalEnd) {
                            $sd = Carbon::parse($s->created_at)->startOfDay();
                            return $sd->gte($saleDate) && $sd->lte($intervalEnd);
                        });
                        
                        // Calculate remaining from this interval
                        $totalSoldInInterval = $salesInThisInterval->sum('quantity');
                        $remainingFromInterval = max(0, $atATime - $totalSoldInInterval);
                        
                        // Add to accumulated remaining (only if interval has fully expired and had remaining)
                        if ($remainingFromInterval > 0) {
                            $accumulatedRemaining += $remainingFromInterval;
                        }
                        
                        $processedIntervals[$intervalKey] = true;
                    }
                    
                    // New interval starts now - can take max + accumulated remaining
                    $maxAllowed = $atATime + $accumulatedRemaining;
                    $maxQuantity = min($balanceStock, $maxAllowed);
                    $canSell = true;
                    
                    // Find the last expired interval to calculate next interval start
                    $lastExpiredIntervalEnd = null;
                    foreach ($salesChronological as $sale) {
                        $saleDate = Carbon::parse($sale->created_at)->startOfDay();
                        $intervalEnd = $saleDate->copy()->addDays($intervalDays - 1);
                        
                        // If this interval has expired
                        if ($now->startOfDay()->gt($intervalEnd)) {
                            if (!$lastExpiredIntervalEnd || $intervalEnd->gt($lastExpiredIntervalEnd)) {
                                $lastExpiredIntervalEnd = $intervalEnd;
                            }
                        }
                    }
                    
                    // Calculate next interval start and end
                    if ($lastExpiredIntervalEnd) {
                        // Next interval starts the day after last expired interval ends
                        $nextIntervalStart = $lastExpiredIntervalEnd->copy()->addDay();
                        $nextIntervalEnd = $nextIntervalStart->copy()->addDays($intervalDays - 1);
                    } else {
                        // Fallback: calculate from now
                        $nextIntervalStart = $now->copy()->startOfDay();
                        $nextIntervalEnd = $nextIntervalStart->copy()->addDays($intervalDays - 1);
                    }
                    
                    if ($accumulatedRemaining > 0) {
                        $intervalInfo = "You can take up to {$maxAllowed} bags ({$atATime} max + {$accumulatedRemaining} remaining from previous intervals). Valid until " . $nextIntervalEnd->format('d M Y');
                    } else {
                        $intervalInfo = "You can take up to {$atATime} bags. Valid until " . $nextIntervalEnd->format('d M Y');
                    }
                }
            } else {
                // No previous sales - can take full max
                $maxQuantity = min($balanceStock, $atATime);
                $canSell = true;
                // Interval end date (inclusive)
                $nextIntervalEnd = $now->copy()->startOfDay()->addDays($intervalDays - 1);
                $intervalInfo = "You can take up to {$atATime} bags. Valid until " . $nextIntervalEnd->format('d M Y');
            }
        } elseif ($condition) {
            // No interval days - just limit by at_a_time
            $maxQuantity = min($balanceStock, $condition['at_a_time']);
            $canSell = true;
        }

        return view('store.sale.create', compact(
            'customer', 
            'store', 
            'balanceStock', 
            'otherStoresSales',
            'condition',
            'maxQuantity',
            'daysRemaining',
            'canSell',
            'restrictionMessage',
            'intervalInfo'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $customer = Customer::findOrFail($request->customer_id);
        $user = Auth::user();
        $store = $user->store;

        // Check if quantity exceeds balance
        $balanceStock = $customer->balance_stock;
        
        if ($request->quantity > $balanceStock) {
            return back()->withInput()->with('error', 'Sale quantity cannot exceed balance stock (' . $balanceStock . ').');
        }

        // Get stock allotment condition for this district
        $condition = StockAllotmentCondition::getStockAllocationForDistrict(
            $customer->district_id, 
            $customer->total_land
        );

        // Validate using interval-based logic
        if ($condition && $condition['interval_days'] > 0) {
            $atATime = $condition['at_a_time'];
            $intervalDays = $condition['interval_days'];
            
            // Get ALL sales for this customer across ALL stores (condition applies globally)
            $allSales = $customer->sales()
                ->orderBy('created_at', 'desc')
                ->get();

            $now = Carbon::now();
            
            if ($allSales->count() > 0) {
                // Find the first sale that started the current active interval
                $salesChronological = $allSales->sortBy('created_at');
                $nowStartOfDay = $now->copy()->startOfDay();
                
                // Find the current active interval by checking each sale's interval
                $currentIntervalStart = null;
                $currentIntervalEndDate = null;
                
                foreach ($salesChronological as $sale) {
                    $saleDate = Carbon::parse($sale->created_at)->startOfDay();
                    $intervalEnd = $saleDate->copy()->addDays($intervalDays - 1); // Inclusive end date
                    
                    // Check if today falls within this interval
                    if ($nowStartOfDay->gte($saleDate) && $nowStartOfDay->lte($intervalEnd)) {
                        // This is the current interval
                        $currentIntervalStart = $saleDate;
                        $currentIntervalEndDate = $intervalEnd;
                        break;
                    }
                }
                
                // If we found a current interval
                if ($currentIntervalStart && $currentIntervalEndDate) {
                    // Get all sales in this current interval (excluding the sale being made now)
                    $salesInCurrentInterval = $allSales->filter(function($sale) use ($currentIntervalStart, $currentIntervalEndDate) {
                        $saleDate = Carbon::parse($sale->created_at)->startOfDay();
                        return $saleDate->gte($currentIntervalStart) && $saleDate->lte($currentIntervalEndDate);
                    });
                    
                    $totalSoldInInterval = $salesInCurrentInterval->sum('quantity');
                    $remainingFromInterval = max(0, $atATime - $totalSoldInInterval);
                    
                    if ($request->quantity > $remainingFromInterval) {
                        $nextIntervalStart = $currentIntervalEndDate->copy()->addDay();
                        return back()->withInput()->with('error', "You can only take {$remainingFromInterval} bag(s) remaining from current interval. Sale locked until " . $nextIntervalStart->format('d M Y'));
                    }
                } else {
                    // Current interval has passed - calculate accumulated remaining from all expired intervals
                    $accumulatedRemaining = 0;
                    $processedIntervals = [];
                    
                    // Process all sales chronologically (oldest first) to calculate remaining from each interval
                    $salesChronological = $allSales->sortBy('created_at');
                    
                    foreach ($salesChronological as $sale) {
                        $saleDate = Carbon::parse($sale->created_at)->startOfDay();
                        // Interval end date is inclusive: sale date + intervalDays - 1
                        $intervalEnd = $saleDate->copy()->addDays($intervalDays - 1);
                        
                        // Skip if this interval hasn't expired yet
                        if ($now->startOfDay()->lte($intervalEnd)) {
                            continue;
                        }
                        
                        // Check if we've already processed this interval
                        $intervalKey = $saleDate->format('Y-m-d');
                        if (isset($processedIntervals[$intervalKey])) {
                            continue;
                        }
                        
                        // Get all sales that belong to this interval
                        // A sale belongs to an interval if it falls within intervalDays from the interval start date
                        $salesInThisInterval = $allSales->filter(function($s) use ($saleDate, $intervalEnd) {
                            $sd = Carbon::parse($s->created_at)->startOfDay();
                            return $sd->gte($saleDate) && $sd->lte($intervalEnd);
                        });
                        
                        // Calculate remaining from this interval
                        $totalSoldInInterval = $salesInThisInterval->sum('quantity');
                        $remainingFromInterval = max(0, $atATime - $totalSoldInInterval);
                        
                        // Add to accumulated remaining (only if interval has fully expired)
                        if ($remainingFromInterval > 0) {
                            $accumulatedRemaining += $remainingFromInterval;
                        }
                        
                        $processedIntervals[$intervalKey] = true;
                    }
                    
                    // New interval starts now - can take max + accumulated remaining
                    $maxAllowed = $atATime + $accumulatedRemaining;
                    if ($request->quantity > $maxAllowed) {
                        return back()->withInput()->with('error', "Maximum {$maxAllowed} bags can be sold ({$atATime} max + {$accumulatedRemaining} remaining from previous intervals).");
                    }
                }
            } else {
                // No previous sales - validate against max
                if ($request->quantity > $atATime) {
                    return back()->withInput()->with('error', 'Maximum ' . $atATime . ' bags can be sold at a time for this district.');
                }
            }
        } elseif ($condition) {
            // No interval days - just validate against at_a_time
            if ($request->quantity > $condition['at_a_time']) {
                return back()->withInput()->with('error', 'Maximum ' . $condition['at_a_time'] . ' bags can be sold at a time for this district.');
            }
        }

        DB::beginTransaction();
        try {
            $balanceBefore = $balanceStock;
            $balanceAfter = $balanceBefore - $request->quantity;

            // Create sale
            Sale::create([
                'customer_id' => $customer->id,
                'store_id' => $store->id,
                'user_id' => $user->id,
                'quantity' => $request->quantity,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
            ]);

            // Update customer stock availed
            $customer->increment('stock_availed', $request->quantity);

            DB::commit();

            return redirect()->route('store.dashboard')
                ->with('success', 'Sale completed successfully. Balance: ' . $balanceAfter . ' bags.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to complete sale: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $user = Auth::user();
        $store = $user->store;

        $sales = Sale::where('store_id', $store->id)
            ->with(['customer', 'user'])
            ->latest()
            ->paginate(20);

        return view('store.sale.history', compact('sales', 'store'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('store.dashboard')
                ->with('error', 'Store not found for your account.');
        }

        // Get all sales for this store (not paginated for export)
        $query = Sale::where('store_id', $store->id)
            ->with(['customer.district', 'customer.mandal', 'store', 'user']);

        // Apply date filters if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->latest()->get();

        $fileName = 'sales_report_' . $store->name . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new SalesExport($sales), $fileName);
    }
}
