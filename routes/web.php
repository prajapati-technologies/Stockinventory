<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\SupervisorController;
use App\Http\Controllers\Admin\DistrictMandalController;
use App\Http\Controllers\Admin\PurchaseHistoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\StockAllotmentConditionController;
use App\Http\Controllers\StoreManager\DashboardController as StoreDashboardController;
use App\Http\Controllers\StoreManager\CustomerController;
use App\Http\Controllers\StoreManager\SaleController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\ReportController;
use App\Http\Controllers\Supervisor\ContactController as SupervisorContactController;
use App\Http\Controllers\Supervisor\DistrictMandalController as SupervisorDistrictMandalController;
use App\Http\Controllers\StoreManager\ContactController as StoreContactController;
use App\Http\Controllers\StoreManager\DistrictMandalController as StoreDistrictMandalController;
use App\Http\Controllers\SubAdmin\DashboardController as SubAdminDashboardController;
use App\Http\Controllers\SubAdmin\ReportController as SubAdminReportController;
use App\Http\Controllers\SubAdmin\PurchaseHistoryController as SubAdminPurchaseHistoryController;
use App\Http\Controllers\SubAdmin\ContactController as SubAdminContactController;
use App\Http\Controllers\SubAdmin\CustomerController as SubAdminCustomerController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// API routes for dropdowns
Route::get('/api/mandals/{district}', function ($districtId) {
    $mandals = \App\Models\Mandal::where('district_id', $districtId)
        ->where('is_active', true)
        ->select('id', 'name')
        ->get();
    
    return response()->json($mandals);
});

// API route for document number check
Route::get('/api/check-document/{documentNumber}', function ($documentNumber) {
    $customer = \App\Models\Customer::where('document_number', $documentNumber)
        ->with(['district', 'mandal'])
        ->first();
    
    if ($customer) {
        return response()->json([
            'exists' => true,
            'customer' => [
                'name' => $customer->name,
                'district' => $customer->district,
                'mandal' => $customer->mandal,
                'total_land' => $customer->total_land,
                'total_stock_allotted' => $customer->total_stock_allotted,
                'stock_availed' => $customer->stock_availed,
                'balance_stock' => $customer->balance_stock
            ]
        ]);
    }
    
    return response()->json(['exists' => false]);
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/password/change', [LoginController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [LoginController::class, 'changePassword'])->name('password.update');
    
    // Profile routes (view and change password for all users)
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Districts and Mandals
    Route::get('/districts', [DistrictMandalController::class, 'index'])->name('districts.index');
    Route::post('/districts', [DistrictMandalController::class, 'storeDistrict'])->name('districts.store');
    Route::post('/mandals', [DistrictMandalController::class, 'storeMandal'])->name('mandals.store');
    Route::patch('/districts/{district}/toggle', [DistrictMandalController::class, 'toggleDistrictStatus'])->name('districts.toggle');
    Route::patch('/mandals/{mandal}/toggle', [DistrictMandalController::class, 'toggleMandalStatus'])->name('mandals.toggle');
    Route::post('/districts/upload', [DistrictMandalController::class, 'uploadDistricts'])->name('districts.upload');
    Route::post('/mandals/upload', [DistrictMandalController::class, 'uploadMandals'])->name('mandals.upload');
    Route::get('/districts/export', [DistrictMandalController::class, 'exportDistricts'])->name('districts.export');
    Route::get('/mandals/export', [DistrictMandalController::class, 'exportMandals'])->name('mandals.export');
    
    // Purchase History
    Route::get('/purchase-history', [PurchaseHistoryController::class, 'index'])->name('purchase-history.index');
    Route::delete('/purchase-history/{sale}', [PurchaseHistoryController::class, 'destroy'])->name('purchase-history.destroy');
    
    // Stores
    Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/create', [StoreController::class, 'create'])->name('stores.create');
    Route::post('/stores', [StoreController::class, 'store'])->name('stores.store');
    Route::get('/stores/{store}/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores/{store}', [StoreController::class, 'update'])->name('stores.update');
    Route::delete('/stores/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');
    Route::post('/stores/{store}/reset-password', [StoreController::class, 'resetPassword'])->name('stores.reset-password');
    Route::post('/stores/{store}/extend-validity', [StoreController::class, 'extendValidity'])->name('stores.extend-validity');
    Route::patch('/stores/{store}/toggle-status', [StoreController::class, 'toggleStatus'])->name('stores.toggle-status');
    Route::post('/stores/upload', [StoreController::class, 'uploadExcel'])->name('stores.upload');
    Route::get('/stores/export', [StoreController::class, 'export'])->name('stores.export');
    Route::get('/mandals/{district}', [StoreController::class, 'getMandals'])->name('mandals.get');
    
    // Supervisors
    Route::get('/supervisors', [SupervisorController::class, 'index'])->name('supervisors.index');
    Route::get('/supervisors/create', [SupervisorController::class, 'create'])->name('supervisors.create');
    Route::post('/supervisors', [SupervisorController::class, 'store'])->name('supervisors.store');
    Route::get('/supervisors/{supervisor}/edit', [SupervisorController::class, 'edit'])->name('supervisors.edit');
    Route::put('/supervisors/{supervisor}', [SupervisorController::class, 'update'])->name('supervisors.update');
    Route::delete('/supervisors/{supervisor}', [SupervisorController::class, 'destroy'])->name('supervisors.destroy');
    Route::post('/supervisors/{supervisor}/reset-password', [SupervisorController::class, 'resetPassword'])->name('supervisors.reset-password');
    Route::patch('/supervisors/{supervisor}/toggle-status', [SupervisorController::class, 'toggleStatus'])->name('supervisors.toggle-status');
    Route::post('/supervisors/upload', [SupervisorController::class, 'uploadExcel'])->name('supervisors.upload');
    Route::get('/supervisors/export', [SupervisorController::class, 'export'])->name('supervisors.export');
    
    // Customer Search
    Route::get('/customers/search', [AdminCustomerController::class, 'search'])->name('customers.search');
    Route::delete('/customers/{customer}', [AdminCustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/customers/template/download', [AdminCustomerController::class, 'downloadTemplate'])->name('customers.template.download');
    Route::post('/customers/upload', [AdminCustomerController::class, 'upload'])->name('customers.upload');
    
    // Stock Allotment Conditions
    Route::get('/stock-allotment-conditions', [StockAllotmentConditionController::class, 'index'])->name('stock-allotment-conditions.index');
    Route::get('/stock-allotment-conditions/create', [StockAllotmentConditionController::class, 'create'])->name('stock-allotment-conditions.create');
    Route::post('/stock-allotment-conditions', [StockAllotmentConditionController::class, 'store'])->name('stock-allotment-conditions.store');
    Route::get('/stock-allotment-conditions/{condition}/edit', [StockAllotmentConditionController::class, 'edit'])->name('stock-allotment-conditions.edit');
    Route::put('/stock-allotment-conditions/{condition}', [StockAllotmentConditionController::class, 'update'])->name('stock-allotment-conditions.update');
    Route::delete('/stock-allotment-conditions/{condition}', [StockAllotmentConditionController::class, 'destroy'])->name('stock-allotment-conditions.destroy');
    
    // Sub-Admins Level-1
    Route::get('/sub-admins/level-1', [SubAdminController::class, 'indexLevel1'])->name('sub-admins.level1.index');
    Route::get('/sub-admins/level-1/create', [SubAdminController::class, 'createLevel1'])->name('sub-admins.level1.create');
    Route::post('/sub-admins/level-1', [SubAdminController::class, 'storeLevel1'])->name('sub-admins.level1.store');
    Route::get('/sub-admins/level-1/{subAdmin}/edit', [SubAdminController::class, 'editLevel1'])->name('sub-admins.level1.edit');
    Route::put('/sub-admins/level-1/{subAdmin}', [SubAdminController::class, 'updateLevel1'])->name('sub-admins.level1.update');
    Route::post('/sub-admins/level-1/{subAdmin}/reset-password', [SubAdminController::class, 'resetPasswordLevel1'])->name('sub-admins.level1.reset-password');
    Route::patch('/sub-admins/level-1/{subAdmin}/toggle-status', [SubAdminController::class, 'toggleStatusLevel1'])->name('sub-admins.level1.toggle-status');
    
    // Sub-Admins Level-2
    Route::get('/sub-admins/level-2', [SubAdminController::class, 'indexLevel2'])->name('sub-admins.level2.index');
    Route::get('/sub-admins/level-2/create', [SubAdminController::class, 'createLevel2'])->name('sub-admins.level2.create');
    Route::post('/sub-admins/level-2', [SubAdminController::class, 'storeLevel2'])->name('sub-admins.level2.store');
    Route::get('/sub-admins/level-2/{subAdmin}/edit', [SubAdminController::class, 'editLevel2'])->name('sub-admins.level2.edit');
    Route::put('/sub-admins/level-2/{subAdmin}', [SubAdminController::class, 'updateLevel2'])->name('sub-admins.level2.update');
    Route::post('/sub-admins/level-2/{subAdmin}/reset-password', [SubAdminController::class, 'resetPasswordLevel2'])->name('sub-admins.level2.reset-password');
    Route::patch('/sub-admins/level-2/{subAdmin}/toggle-status', [SubAdminController::class, 'toggleStatusLevel2'])->name('sub-admins.level2.toggle-status');
    
    // Admin-only profile routes
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/settings', [App\Http\Controllers\ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/settings', [App\Http\Controllers\ProfileController::class, 'updateSettings'])->name('profile.update-settings');
});

// Store Manager routes
Route::middleware(['auth', 'role:store_manager'])->prefix('store')->name('store.')->group(function () {
    Route::get('/dashboard', [StoreDashboardController::class, 'index'])->name('dashboard');
    
    // Customer management
    Route::get('/customer/search', [CustomerController::class, 'search'])->name('customer.search');
    Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('/customer/{customer}', [CustomerController::class, 'show'])->name('customer.show');
    
    // Sales
    Route::get('/sale/create', [SaleController::class, 'create'])->name('sale.create');
    Route::post('/sale', [SaleController::class, 'store'])->name('sale.store');
    Route::get('/sale/history', [SaleController::class, 'history'])->name('sale.history');
    Route::get('/sale/export', [SaleController::class, 'export'])->name('sale.export');
    
    // Purchase History
    Route::get('/purchase-history', [App\Http\Controllers\StoreManager\PurchaseHistoryController::class, 'index'])->name('purchase-history.index');
    
    // Contact Us
    Route::get('/contact', [StoreContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [StoreContactController::class, 'store'])->name('contact.store');
    
    // Districts & Mandals Export (disabled, kept for reference)
    // Route::get('/districts/export', [StoreDistrictMandalController::class, 'exportDistricts'])->name('districts.export');
    // Route::get('/mandals/export', [StoreDistrictMandalController::class, 'exportMandals'])->name('mandals.export');
});

// Supervisor routes
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/customer/{customer}', [ReportController::class, 'customerDetails'])->name('reports.customer');
    Route::get('/customer/search', [ReportController::class, 'customerSearch'])->name('customer.search');
    
    // Customer management
    Route::get('/customers', [ReportController::class, 'customers'])->name('customers.index');
    Route::get('/customers/create', [ReportController::class, 'createCustomer'])->name('customers.create');
    Route::post('/customers', [ReportController::class, 'storeCustomer'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [ReportController::class, 'editCustomer'])->name('customers.edit');
    Route::put('/customers/{customer}', [ReportController::class, 'updateCustomer'])->name('customers.update');
    Route::post('/customers/upload-additional-bags', [ReportController::class, 'uploadAdditionalBags'])->name('customers.upload-additional-bags');
    Route::get('/customers/additional-bags-template/download', [ReportController::class, 'downloadAdditionalBagsTemplate'])->name('customers.additional-bags-template.download');
    // Route::post('/customers/upload', [ReportController::class, 'uploadCustomers'])->name('customers.upload'); // Disabled for supervisors
    // Route::get('/customers/template/download', [ReportController::class, 'downloadTemplate'])->name('customers.template.download'); // Disabled for supervisors
    
    // Purchase History
    Route::get('/purchase-history', [App\Http\Controllers\Supervisor\PurchaseHistoryController::class, 'index'])->name('purchase-history.index');
    
    // Contact Us
    Route::get('/contact', [SupervisorContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [SupervisorContactController::class, 'store'])->name('contact.store');
    
    // Districts & Mandals Export (disabled, kept for reference)
    // Route::get('/districts/export', [SupervisorDistrictMandalController::class, 'exportDistricts'])->name('districts.export');
    // Route::get('/mandals/export', [SupervisorDistrictMandalController::class, 'exportMandals'])->name('mandals.export');
});

// Sub-Admin routes (Level-1 and Level-2)
Route::middleware(['auth', 'sub_admin'])->prefix('sub-admin')->name('sub-admin.')->group(function () {
    Route::get('/dashboard', [SubAdminDashboardController::class, 'index'])->name('dashboard');
    Route::match(['get', 'post'], '/reports', [SubAdminReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/export', [SubAdminReportController::class, 'export'])->name('reports.export');
    Route::post('/reports/send', [SubAdminReportController::class, 'sendReport'])->name('reports.send');
    Route::get('/purchase-history', [SubAdminPurchaseHistoryController::class, 'index'])->name('purchase-history.index');
    
    // Customer Search (available for both Level-1 and Level-2)
    Route::get('/customers/search', [SubAdminCustomerController::class, 'search'])->name('customers.search');
    
    // Contact Us
    Route::get('/contact', [SubAdminContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [SubAdminContactController::class, 'store'])->name('contact.store');
    
    Route::middleware('role:sub_admin_level_1')->group(function () {
        Route::get('/customers/upload', [SubAdminCustomerController::class, 'create'])->name('customers.upload-form');
        Route::post('/customers/upload', [SubAdminCustomerController::class, 'store'])->name('customers.upload');
        Route::get('/customers/template/download', [SubAdminCustomerController::class, 'downloadTemplate'])->name('customers.template.download');
    });
});

// Fallback route - redirect to login if not authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});
