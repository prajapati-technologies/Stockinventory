<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $documentNumber = $request->input('document_number');
        $customer = null;

        if ($documentNumber) {
            $customer = Customer::with([
                'district', 
                'mandal', 
                'sales.store',
                'additionalBags' => function($q) {
                    $q->with('addedBy')->orderBy('created_at', 'desc');
                }
            ])
                ->where('document_number', $documentNumber)
                ->first();

            // Recalculate total_stock_allotted for old customers based on current conditions
            if ($customer) {
                $newTotalStockAllotted = Customer::calculateStockAllocation(
                    $customer->total_land, 
                    $customer->district_id
                );

                // Update if different (for old customers)
                if ($customer->total_stock_allotted != $newTotalStockAllotted) {
                    // Only update if stock_availed doesn't exceed new total
                    if ($customer->stock_availed <= $newTotalStockAllotted) {
                        $customer->total_stock_allotted = $newTotalStockAllotted;
                        $customer->save();
                    }
                }
            }
        }

        $user = Auth::user();
        $store = $user->store;

        return view('store.customer.search', compact('customer', 'store'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $store = $user->store;

        $documentNumber = $request->input('document_number');

        return view('store.customer.create', compact('store', 'documentNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_number' => 'required|string|unique:customers,document_number',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'total_land' => 'required|numeric|min:0',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
            'document_photo' => 'nullable|image|max:100', // 100KB max
        ]);

        DB::beginTransaction();
        try {
            $totalStockAllotted = Customer::calculateStockAllocation($request->total_land, $request->district_id);

            $customerData = [
                'document_number' => $request->document_number,
                'name' => $request->name,
                'phone' => $request->phone,
                'total_land' => $request->total_land,
                'district_id' => $request->district_id,
                'mandal_id' => $request->mandal_id,
                'total_stock_allotted' => $totalStockAllotted,
                'stock_availed' => 0,
                'created_by' => Auth::id(),
            ];

            // Handle photo upload
            if ($request->hasFile('document_photo')) {
                try {
                    $image = $request->file('document_photo');
                    $imageName = time() . '_' . $request->document_number . '.' . $image->getClientOriginalExtension();
                    
                    // Ensure documents directory exists
                    $documentsPath = storage_path('app/public/documents');
                    if (!file_exists($documentsPath)) {
                        mkdir($documentsPath, 0755, true);
                    }
                    
                    // Resize and compress image
                    $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($image);
                    $img->scale(width: 800);
                    $img->toJpeg(quality: 75);
                    
                    $fullPath = $documentsPath . '/' . $imageName;
                    $img->save($fullPath);
                    
                    // Verify file was saved
                    if (file_exists($fullPath)) {
                        $customerData['document_photo'] = 'documents/' . $imageName;
                    } else {
                        throw new \Exception('Failed to save image file');
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->withInput()->with('error', 'Failed to upload document photo: ' . $e->getMessage());
                }
            }

            $customer = Customer::create($customerData);

            DB::commit();

            return redirect()->route('store.sale.create', ['customer_id' => $customer->id])
                ->with('success', 'Customer data saved successfully. You can now proceed with the sale.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save customer data: ' . $e->getMessage());
        }
    }

    public function show(Customer $customer)
    {
        $customer->load(['district', 'mandal', 'sales.store']);
        
        // Recalculate total_stock_allotted for old customers based on current conditions
        $newTotalStockAllotted = Customer::calculateStockAllocation(
            $customer->total_land, 
            $customer->district_id
        );

        // Update if different (for old customers)
        if ($customer->total_stock_allotted != $newTotalStockAllotted) {
            // Only update if stock_availed doesn't exceed new total
            if ($customer->stock_availed <= $newTotalStockAllotted) {
                $customer->total_stock_allotted = $newTotalStockAllotted;
                $customer->save();
            }
        }
        
        $user = Auth::user();
        $store = $user->store;

        return view('store.customer.show', compact('customer', 'store'));
    }
}
