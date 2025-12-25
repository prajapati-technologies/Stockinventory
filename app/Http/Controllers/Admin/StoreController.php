<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\District;
use App\Models\Mandal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StoresImport;
use App\Exports\StoresExport;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::with(['district', 'mandal', 'user']);

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('mandal_id')) {
            $query->where('mandal_id', $request->mandal_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        $stores = $query->paginate(15)->withQueryString();
        $districts = District::where('is_active', true)->get();
        $mandals = collect();

        if ($request->filled('district_id')) {
            $mandals = Mandal::where('district_id', $request->district_id)
                ->where('is_active', true)
                ->get();
        }

        return view('admin.stores.index', compact('stores', 'districts', 'mandals'));
    }

    public function create()
    {
        $districts = District::where('is_active', true)->get();
        return view('admin.stores.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:stores,phone_number|unique:users,phone_number',
            'address' => 'nullable|string',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
            'validity_period' => 'required|in:6,12',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'district_id' => $request->district_id,
                'mandal_id' => $request->mandal_id,
                'password' => Hash::make('guest'),
                'must_change_password' => true,
            ]);

            $user->assignRole('store_manager');

            // Create store
            Store::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'district_id' => $request->district_id,
                'mandal_id' => $request->mandal_id,
                'user_id' => $user->id,
                'valid_till' => now()->addMonths((int) $request->validity_period),
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('admin.stores.index')
                ->with('success', 'Store created successfully. Default password is "guest".');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create store: ' . $e->getMessage());
        }
    }

    public function edit(Store $store)
    {
        $districts = District::where('is_active', true)->get();
        $mandals = Mandal::where('district_id', $store->district_id)
            ->where('is_active', true)
            ->get();
        return view('admin.stores.edit', compact('store', 'districts', 'mandals'));
    }

    public function update(Request $request, Store $store)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:stores,phone_number,' . $store->id,
            'address' => 'nullable|string',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
        ]);

        DB::beginTransaction();
        try {
            $store->update([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'district_id' => $request->district_id,
                'mandal_id' => $request->mandal_id,
            ]);

            if ($store->user) {
                $store->user->update([
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'district_id' => $request->district_id,
                    'mandal_id' => $request->mandal_id,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stores.index')
                ->with('success', 'Store updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update store: ' . $e->getMessage());
        }
    }

    public function destroy(Store $store)
    {
        DB::beginTransaction();
        try {
            if ($store->user) {
                $store->user->delete();
            }
            $store->delete();
            DB::commit();

            return redirect()->route('admin.stores.index')
                ->with('success', 'Store deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete store: ' . $e->getMessage());
        }
    }

    public function resetPassword(Store $store)
    {
        if ($store->user) {
            $store->user->update([
                'password' => Hash::make('guest'),
                'must_change_password' => true,
            ]);

            return back()->with('success', 'Password reset successfully. New password is "guest".');
        }

        return back()->with('error', 'User not found for this store.');
    }

    public function extendValidity(Request $request, Store $store)
    {
        $request->validate([
            'validity_period' => 'required|in:6,12',
        ]);

        $store->update([
            'valid_till' => now()->addMonths((int) $request->validity_period),
        ]);

        return back()->with('success', 'Validity extended successfully.');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new StoresImport, $request->file('excel_file'));

            return back()->with('success', 'Stores imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import stores: ' . $e->getMessage());
        }
    }

    public function getMandals($districtId)
    {
        $mandals = Mandal::where('district_id', $districtId)
            ->where('is_active', true)
            ->get();

        return response()->json($mandals);
    }

    public function export()
    {
        return Excel::download(new StoresExport, 'stores_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function toggleStatus(Store $store)
    {
        $store->update(['is_active' => !$store->is_active]);
        
        $status = $store->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Store {$status} successfully.");
    }
}
