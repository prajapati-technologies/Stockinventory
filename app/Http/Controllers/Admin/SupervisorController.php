<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\District;
use App\Models\Mandal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SupervisorsImport;
use App\Exports\SupervisorsExport;

class SupervisorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role('supervisor')->with(['district', 'mandal']);

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

        $supervisors = $query->paginate(15)->withQueryString();
        $districts = District::where('is_active', true)->get();
        $mandals = collect();

        if ($request->filled('district_id')) {
            $mandals = Mandal::where('district_id', $request->district_id)
                ->where('is_active', true)
                ->get();
        }

        return view('admin.supervisors.index', compact('supervisors', 'districts', 'mandals'));
    }

    public function create()
    {
        $districts = District::where('is_active', true)->get();
        return view('admin.supervisors.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'district_id' => $request->district_id,
                'mandal_id' => $request->mandal_id,
                'password' => Hash::make('guest'),
                'must_change_password' => true,
            ]);

            $user->assignRole('supervisor');

            return redirect()->route('admin.supervisors.index')
                ->with('success', 'Supervisor created successfully. Default password is "guest".');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create supervisor: ' . $e->getMessage());
        }
    }

    public function edit(User $supervisor)
    {
        $districts = District::where('is_active', true)->get();
        $mandals = Mandal::where('district_id', $supervisor->district_id)
            ->where('is_active', true)
            ->get();
        return view('admin.supervisors.edit', compact('supervisor', 'districts', 'mandals'));
    }

    public function update(Request $request, User $supervisor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number,' . $supervisor->id,
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
        ]);

        try {
            $supervisor->update([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'district_id' => $request->district_id,
                'mandal_id' => $request->mandal_id,
            ]);

            return redirect()->route('admin.supervisors.index')
                ->with('success', 'Supervisor updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update supervisor: ' . $e->getMessage());
        }
    }

    public function destroy(User $supervisor)
    {
        try {
            $supervisor->delete();

            return redirect()->route('admin.supervisors.index')
                ->with('success', 'Supervisor deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete supervisor: ' . $e->getMessage());
        }
    }

    public function resetPassword(User $supervisor)
    {
        $supervisor->update([
            'password' => Hash::make('guest'),
            'must_change_password' => true,
        ]);

        return back()->with('success', 'Password reset successfully. New password is "guest".');
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new SupervisorsImport, $request->file('excel_file'));

            return back()->with('success', 'Supervisors imported successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to import supervisors: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $supervisor)
    {
        $supervisor->update(['is_active' => !$supervisor->is_active]);
        
        $status = $supervisor->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Supervisor {$status} successfully.");
    }

    public function export()
    {
        return Excel::download(new SupervisorsExport, 'supervisors_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
