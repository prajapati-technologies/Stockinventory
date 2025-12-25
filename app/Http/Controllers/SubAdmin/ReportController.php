<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Sale;
use App\Models\Mandal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Mail\ReportMail;
use Illuminate\Support\Facades\File;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get accessible mandals based on level
        $accessibleMandalIds = $this->getAccessibleMandalIds($user);
        $accessibleMandals = Mandal::whereIn('id', $accessibleMandalIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Store or retrieve filters from session
        // If POST request, store/update filters in session
        if ($request->isMethod('post')) {
            $selectedMandalIds = $this->parseIds($request->input('mandal_ids'));
            $selectedStoreIds = $this->parseIds($request->input('store_ids'));
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            
            // Store filters in session (even if empty, to clear previous filters)
            session([
                'reports_filters.mandal_ids' => $selectedMandalIds,
                'reports_filters.store_ids' => $selectedStoreIds,
                'reports_filters.date_from' => $dateFrom,
                'reports_filters.date_to' => $dateTo,
            ]);
        } else {
            // Retrieve filters from session for pagination
            $selectedMandalIds = session('reports_filters.mandal_ids', []);
            $selectedStoreIds = session('reports_filters.store_ids', []);
            $dateFrom = session('reports_filters.date_from');
            $dateTo = session('reports_filters.date_to');
            
            // If no filters in session and no page request, default to all accessible mandals
            if (empty($selectedMandalIds) && !$request->has('page')) {
                $selectedMandalIds = $accessibleMandalIds;
            }
        }
        
        // Filter selected mandals to only accessible ones
        $selectedMandalIds = array_values(array_intersect($selectedMandalIds, $accessibleMandalIds));
        
        $stores = Store::where('district_id', $user->district_id)
            ->whereIn('mandal_id', $accessibleMandalIds)
            ->where('is_active', true)
            ->with(['district', 'mandal'])
            ->orderBy('name')
            ->get();
        
        $storesForSelectedMandals = $stores->whereIn('mandal_id', $selectedMandalIds)->values();
        
        $sales = collect();
        
        // Filter stores to only accessible ones for selected mandals
        $storeIdsForSelectedMandals = $storesForSelectedMandals->pluck('id')->toArray();
        $selectedStoreIds = array_values(array_intersect($selectedStoreIds, $storeIdsForSelectedMandals));
        
        if (!empty($selectedStoreIds)) {
            $query = Sale::whereIn('store_id', $selectedStoreIds)
                ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal', 'user']);
            
            if (!empty($dateFrom)) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            
            if (!empty($dateTo)) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
            
            $sales = $query->latest()->paginate(20);
            
            // Don't append filter parameters to pagination links (they're in session)
        }
        
        $filtersApplied = $request->isMethod('post') || !empty($selectedMandalIds) || !empty($selectedStoreIds) || !empty($dateFrom) || !empty($dateTo);
        
        return view('sub-admin.reports.index', compact(
            'user',
            'accessibleMandals',
            'stores',
            'sales',
            'selectedMandalIds',
            'selectedStoreIds',
            'filtersApplied',
            'dateFrom',
            'dateTo'
        ));
    }
    
    public function export(Request $request)
    {
        $user = Auth::user();
        
        // Get accessible mandals
        $accessibleMandalIds = $this->getAccessibleMandalIds($user);
        
        // Get selected mandals
        $selectedMandalIds = $this->parseIds($request->input('mandal_ids'));
        
        // Filter to accessible mandals
        $selectedMandalIds = array_values(array_intersect($selectedMandalIds, $accessibleMandalIds));
        
        // Get stores
        $selectedStoreIds = $this->parseIds($request->input('store_ids'));
        
        $stores = Store::where('district_id', $user->district_id)
            ->whereIn('mandal_id', $selectedMandalIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $storeIdsForSelectedMandals = $stores->pluck('id')->toArray();
        
        $selectedStoreIds = array_values(array_intersect($selectedStoreIds, $storeIdsForSelectedMandals));
        
        if (empty($selectedStoreIds)) {
            return redirect()->back()->with('error', 'Please select at least one store.');
        }
        
        $query = Sale::whereIn('store_id', $selectedStoreIds)
            ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal']);
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $sales = $query->latest()->get();
        
        $fileName = 'reports_' . $user->district->name . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new SalesExport($sales), $fileName);
    }
    
    private function getAccessibleMandalIds($user)
    {
        if ($user->hasRole('sub_admin_level_1')) {
            // Level-1: All mandals of the district
            return \App\Models\Mandal::where('district_id', $user->district_id)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
        } elseif ($user->hasRole('sub_admin_level_2')) {
            // Level-2: Only selected mandals
            return $user->mandal_ids ?? [];
        }
        
        return [];
    }

    private function parseIds($input): array
    {
        if (is_array($input)) {
            $values = array_map('intval', $input);
            $values = array_filter($values, fn ($value) => $value !== null && $value !== 0);
            return array_values(array_unique($values));
        }

        if (is_string($input) && $input !== '') {
            $parts = array_map('trim', explode(',', $input));
            $values = array_map('intval', $parts);
            $values = array_filter($values, fn ($value) => $value !== null && $value !== 0);
            return array_values(array_unique($values));
        }

        return [];
    }

    public function sendReport(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'format' => 'required|in:excel',
            'mandal_ids' => 'required|array|min:1',
            'mandal_ids.*' => 'exists:mandals,id',
            'store_ids' => 'required|array|min:1',
            'store_ids.*' => 'exists:stores,id',
        ]);

        $user = Auth::user();
        
        // Get accessible mandals
        $accessibleMandalIds = $this->getAccessibleMandalIds($user);
        
        // Get selected mandals
        $selectedMandalIds = $this->parseIds($request->input('mandal_ids'));
        
        // Filter to accessible mandals
        $selectedMandalIds = array_values(array_intersect($selectedMandalIds, $accessibleMandalIds));
        
        // Get stores
        $selectedStoreIds = $this->parseIds($request->input('store_ids'));
        
        $stores = Store::where('district_id', $user->district_id)
            ->whereIn('mandal_id', $selectedMandalIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $storeIdsForSelectedMandals = $stores->pluck('id')->toArray();
        
        $selectedStoreIds = array_values(array_intersect($selectedStoreIds, $storeIdsForSelectedMandals));
        
        if (empty($selectedStoreIds)) {
            // Store filters in session before redirect
            session([
                'reports_filters.mandal_ids' => $selectedMandalIds,
                'reports_filters.store_ids' => $this->parseIds($request->input('store_ids')),
                'reports_filters.date_from' => $request->input('date_from'),
                'reports_filters.date_to' => $request->input('date_to'),
            ]);
            return redirect()->route('sub-admin.reports.index')->with('error', 'Please select at least one store.');
        }
        
        $query = Sale::whereIn('store_id', $selectedStoreIds)
            ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal']);
        
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }
        
        $sales = $query->latest()->get();
        
        if ($sales->isEmpty()) {
            // Store filters in session before redirect
            session([
                'reports_filters.mandal_ids' => $selectedMandalIds,
                'reports_filters.store_ids' => $selectedStoreIds,
                'reports_filters.date_from' => $dateFrom,
                'reports_filters.date_to' => $dateTo,
            ]);
            return redirect()->route('sub-admin.reports.index')->with('error', 'No sales data found for the selected criteria.');
        }

        try {
            $userRole = $user->hasRole('sub_admin_level_1') ? 'Sub Admin Level-1' : 'Sub Admin Level-2';
            
            $format = 'excel';
            $fileName = 'reports_' . $user->district->name . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
            // Increase execution time and memory limit for large datasets
            set_time_limit(0); // No time limit
            ini_set('memory_limit', '2048M'); // 2GB for very large datasets
            
            $tempDirectory = storage_path('app/temp');
            if (!File::exists($tempDirectory)) {
                File::makeDirectory($tempDirectory, 0755, true);
            }
            
            // Generate Excel file
            $filePath = 'temp/' . $fileName;
            $stored = Excel::store(new SalesExport($sales), $filePath, 'local');
            
            if (!$stored) {
                throw new \Exception('Failed to store Excel file. Please check storage permissions.');
            }
            
            $fullPath = Storage::disk('local')->path($filePath);
            
            // Verify file was created
            if (!File::exists($fullPath)) {
                throw new \Exception('Excel file was not created at: ' . $fullPath);
            }
            
            // Send email
            Mail::to($request->email)->send(new ReportMail(
                $fileName,
                $fullPath,
                $format,
                $user->name,
                $userRole
            ));
            
            // Clean up temporary file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            $message = 'Excel report has been sent successfully to ' . $request->email . '.';
            
            // Store filters in session before redirect (filters are already in session from form submission)
            // Redirect to reports index (filters will be loaded from session)
            return redirect()->route('sub-admin.reports.index')->with('success', $message);
        } catch (\Exception $e) {
            // Clean up temporary file in case of error
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            // Store filters in session before redirect
            session([
                'reports_filters.mandal_ids' => $this->parseIds($request->input('mandal_ids')),
                'reports_filters.store_ids' => $this->parseIds($request->input('store_ids')),
                'reports_filters.date_from' => $request->input('date_from'),
                'reports_filters.date_to' => $request->input('date_to'),
            ]);
            
            // Redirect to reports index (filters will be loaded from session)
            return redirect()->route('sub-admin.reports.index')->with('error', 'Failed to send report: ' . $e->getMessage());
        }
    }
}
