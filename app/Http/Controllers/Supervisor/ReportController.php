<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mandal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\CustomersImport;
use App\Imports\AdditionalBagsImport;
use App\Exports\SalesExport;
use App\Exports\CustomerTemplateExport;
use App\Exports\AdditionalBagsTemplateExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $stores = Store::where('district_id', $user->district_id)
            ->where('mandal_id', $user->mandal_id)
            ->get();

        $sales = collect();
        $selectedStore = null;
        $showAllStores = false;

        if ($request->filled('store_id')) {
            if ($request->store_id === 'all') {
                // Show all stores
                $showAllStores = true;
                $storeIds = $stores->pluck('id')->toArray();
                
                $query = Sale::whereIn('store_id', $storeIds)
                    ->with(['customer', 'store']);

                if ($request->filled('date_from')) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }

                if ($request->filled('date_to')) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }

                $sales = $query->latest()->paginate(20)->withQueryString();
            } else {
                // Show specific store
                $selectedStore = Store::find($request->store_id);
                
                if ($selectedStore) {
                    $query = Sale::where('store_id', $request->store_id)
                        ->with(['customer', 'store']);

                    if ($request->filled('date_from')) {
                        $query->whereDate('created_at', '>=', $request->date_from);
                    }

                    if ($request->filled('date_to')) {
                        $query->whereDate('created_at', '<=', $request->date_to);
                    }

                    $sales = $query->latest()->paginate(20)->withQueryString();
                }
            }
        }

        return view('supervisor.reports.index', compact('stores', 'sales', 'selectedStore', 'showAllStores'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        
        if (!$request->filled('store_id')) {
            return redirect()->back()->with('error', 'Please select a store first.');
        }

        $stores = Store::where('district_id', $user->district_id)
            ->where('mandal_id', $user->mandal_id)
            ->get();

        if ($request->store_id === 'all') {
            // Export all stores
            $storeIds = $stores->pluck('id')->toArray();
            $query = Sale::whereIn('store_id', $storeIds)
                ->with(['customer', 'store']);
        } else {
            // Export specific store
            $selectedStore = Store::find($request->store_id);
            
            if (!$selectedStore) {
                return redirect()->back()->with('error', 'Store not found.');
            }
            
            $query = Sale::where('store_id', $request->store_id)
                ->with(['customer', 'store']);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->latest()->get();

        if ($request->store_id === 'all') {
            $fileName = 'sales_report_all_stores_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        } else {
            $fileName = 'sales_report_' . $selectedStore->name . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        }

        return Excel::download(new SalesExport($sales), $fileName);
    }

    public function customerDetails(Customer $customer)
    {
        // Load customer with all sales from all stores, not just supervisor's mandal
        $customer->load(['district', 'mandal', 'sales.store.district', 'sales.store.mandal', 'createdBy']);
        
        return view('supervisor.reports.customer-details', compact('customer'));
    }

    public function customerSearch(Request $request)
    {
        $customer = null;
        $documentNumber = $request->get('document_number');

        if ($documentNumber) {
            // Search customer by document number irrespective of mandal
            $customer = Customer::where('document_number', $documentNumber)
                ->with([
                    'district', 
                    'mandal', 
                    'sales.store.district', 
                    'sales.store.mandal', 
                    'createdBy',
                    'additionalBags' => function($q) {
                        $q->with('addedBy')->orderBy('created_at', 'desc');
                    }
                ])
                ->first();
        }

        return view('supervisor.customer.search', compact('customer', 'documentNumber'));
    }

    public function customers(Request $request)
    {
        $user = Auth::user();
        
        // Only show customers when search is provided, don't show all by default
        if ($request->filled('search')) {
            $query = Customer::with(['district', 'mandal', 'additionalBags.addedBy'])
                ->where('document_number', $request->search);
            
            $customers = $query->paginate(20)->withQueryString();
        } else {
            // Return empty collection when no search is provided
            $customers = Customer::whereRaw('1 = 0')->paginate(20)->withQueryString();
        }

        return view('supervisor.customers.index', compact('customers'));
    }

    public function uploadAdditionalBags(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excel_file.required' => 'Please select an Excel file to upload.',
            'excel_file.file' => 'The uploaded file must be a valid file.',
            'excel_file.mimes' => 'The file must be a file of type: xlsx, xls, csv.',
            'excel_file.max' => 'The file size must not exceed 10MB.',
        ]);

        try {
            $import = new AdditionalBagsImport();
            Excel::import($import, $request->file('excel_file'));

            $successCount = $import->getSuccessCount();
            $errorCount = $import->getErrorCount();
            $errors = $import->getErrors();

            $message = "Successfully processed {$successCount} record(s).";
            if ($errorCount > 0) {
                $message .= " {$errorCount} error(s) occurred.";
                if (count($errors) > 0) {
                    $message .= " Errors: " . implode(', ', array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= " and " . (count($errors) - 5) . " more.";
                    }
                }
            }

            return redirect()->route('supervisor.customers.index')
                ->with($errorCount > 0 ? 'warning' : 'success', $message);
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return redirect()->route('supervisor.customers.index')
                ->with('error', 'Validation errors: ' . implode('; ', array_slice($errorMessages, 0, 5)));
        } catch (\Exception $e) {
            return redirect()->route('supervisor.customers.index')
                ->with('error', 'Failed to upload: ' . $e->getMessage());
        }
    }

    public function downloadAdditionalBagsTemplate()
    {
        return Excel::download(new AdditionalBagsTemplateExport, 'additional_bags_template.xlsx');
    }

    public function editCustomer(Customer $customer)
    {
        // Allow supervisor to edit any customer
        return view('supervisor.customers.edit', compact('customer'));
    }

    public function updateCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'total_stock_allotted' => 'required|integer|min:0',
        ]);

        try {
            $customer->update([
                'total_stock_allotted' => $request->total_stock_allotted,
            ]);

            return redirect()->route('supervisor.customers.index')
                ->with('success', 'Customer stock allocation updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    public function createCustomer()
    {
        $user = Auth::user();
        return view('supervisor.customers.create', compact('user'));
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'document_number' => 'required|string|unique:customers,document_number',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'total_land' => 'required|numeric|min:0',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
        ]);

        try {
            $totalStockAllotted = Customer::calculateStockAllocation($request->total_land, $request->district_id);

            Customer::create([
                'document_number' => $request->document_number,
                'name' => $request->name,
                'phone' => $request->phone,
                'total_land' => $request->total_land,
                'district_id' => $request->district_id,
                'mandal_id' => $request->mandal_id,
                'total_stock_allotted' => $totalStockAllotted,
                'stock_availed' => 0,
                'created_by' => Auth::id(),
            ]);

            return redirect()->route('supervisor.customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    public function uploadCustomers(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new CustomersImport, $request->file('excel_file'));

            return back()->with('success', 'Customers imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = $e->failures();
            $errorMessages = [];
            
            foreach ($errors as $failure) {
                $row = $failure->row();
                $attribute = $failure->attribute();
                
                // Clean up attribute name (remove row number prefix)
                $cleanAttribute = preg_replace('/^\d+\./', '', $attribute);
                
                foreach ($failure->errors() as $error) {
                    // Clean up error message to remove row number prefix
                    $cleanError = preg_replace('/^\d+\./', '', $error);
                    $errorMessages[] = "Row {$row}: " . ucfirst($cleanAttribute) . " - " . $cleanError;
                }
            }
            
            $message = 'Failed to import customers: ' . implode('; ', $errorMessages);
            return back()->with('error', $message);
        } catch (\Exception $e) {
            // Clean error message to remove row number prefix
            $errorMessage = preg_replace('/^\d+\./', '', $e->getMessage());
            return back()->with('error', 'Failed to import customers: ' . $errorMessage);
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'customer_template_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new CustomerTemplateExport, $fileName);
    }
}
