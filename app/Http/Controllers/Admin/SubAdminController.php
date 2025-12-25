<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\District;
use App\Models\Mandal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SubAdminController extends Controller
{
    // Level-1 Sub-Admin Management
    public function indexLevel1(Request $request)
    {
        $query = User::role('sub_admin_level_1')->with(['district']);

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        $subAdmins = $query->paginate(15)->withQueryString();
        $districts = District::where('is_active', true)->get();

        return view('admin.sub-admins.level1.index', compact('subAdmins', 'districts'));
    }

    public function createLevel1()
    {
        $districts = District::where('is_active', true)->get();
        return view('admin.sub-admins.level1.create', compact('districts'));
    }

    public function storeLevel1(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'district_id' => 'required|exists:districts,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'district_id' => $request->district_id,
                'password' => Hash::make('guest'),
                'must_change_password' => true,
                'is_active' => true,
            ]);

            $user->assignRole('sub_admin_level_1');

            return redirect()->route('admin.sub-admins.level1.index')
                ->with('success', 'Sub Admin Level-1 created successfully. Default password is "guest".');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create sub-admin: ' . $e->getMessage());
        }
    }

    public function resetPasswordLevel1(User $subAdmin)
    {
        $subAdmin->update([
            'password' => Hash::make('guest'),
            'must_change_password' => true,
        ]);

        return back()->with('success', 'Password reset successfully. New password is "guest".');
    }

    public function toggleStatusLevel1(User $subAdmin)
    {
        $subAdmin->update(['is_active' => !$subAdmin->is_active]);
        
        $status = $subAdmin->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Sub Admin Level-1 {$status} successfully.");
    }

    public function editLevel1(User $subAdmin)
    {
        if (!$subAdmin->hasRole('sub_admin_level_1')) {
            abort(404);
        }

        $districts = District::where('is_active', true)->get();

        return view('admin.sub-admins.level1.edit', compact('subAdmin', 'districts'));
    }

    public function updateLevel1(Request $request, User $subAdmin)
    {
        if (!$subAdmin->hasRole('sub_admin_level_1')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                Rule::unique('users', 'phone_number')->ignore($subAdmin->id),
            ],
            'district_id' => 'required|exists:districts,id',
        ]);

        $subAdmin->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'district_id' => $request->district_id,
        ]);

        return redirect()
            ->route('admin.sub-admins.level1.index')
            ->with('success', 'Sub Admin Level-1 updated successfully.');
    }

    // Level-2 Sub-Admin Management
    public function indexLevel2(Request $request)
    {
        $query = User::role('sub_admin_level_2')->with(['district']);

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        $subAdmins = $query->paginate(15)->withQueryString();
        $districts = District::where('is_active', true)->get();

        return view('admin.sub-admins.level2.index', compact('subAdmins', 'districts'));
    }

    public function createLevel2()
    {
        $districts = District::where('is_active', true)->get();
        return view('admin.sub-admins.level2.create', compact('districts'));
    }

    public function storeLevel2(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
            'district_id' => 'required|exists:districts,id',
            'mandal_ids' => 'required|array|min:1',
            'mandal_ids.*' => 'exists:mandals,id',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'district_id' => $request->district_id,
                'mandal_ids' => $request->mandal_ids,
                'password' => Hash::make('guest'),
                'must_change_password' => true,
                'is_active' => true,
            ]);

            $user->assignRole('sub_admin_level_2');

            return redirect()->route('admin.sub-admins.level2.index')
                ->with('success', 'Sub Admin Level-2 created successfully. Default password is "guest".');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create sub-admin: ' . $e->getMessage());
        }
    }

    public function resetPasswordLevel2(User $subAdmin)
    {
        $subAdmin->update([
            'password' => Hash::make('guest'),
            'must_change_password' => true,
        ]);

        return back()->with('success', 'Password reset successfully. New password is "guest".');
    }

    public function toggleStatusLevel2(User $subAdmin)
    {
        $subAdmin->update(['is_active' => !$subAdmin->is_active]);
        
        $status = $subAdmin->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Sub Admin Level-2 {$status} successfully.");
    }

    public function editLevel2(User $subAdmin)
    {
        if (!$subAdmin->hasRole('sub_admin_level_2')) {
            abort(404);
        }

        $districts = District::where('is_active', true)->get();
        $mandals = $subAdmin->district_id
            ? Mandal::where('district_id', $subAdmin->district_id)->get()
            : collect();

        return view('admin.sub-admins.level2.edit', [
            'subAdmin' => $subAdmin,
            'districts' => $districts,
            'mandals' => $mandals,
        ]);
    }

    public function updateLevel2(Request $request, User $subAdmin)
    {
        if (!$subAdmin->hasRole('sub_admin_level_2')) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'string',
                Rule::unique('users', 'phone_number')->ignore($subAdmin->id),
            ],
            'district_id' => 'required|exists:districts,id',
            'mandal_ids' => 'required|array|min:1',
            'mandal_ids.*' => 'exists:mandals,id',
        ]);

        $subAdmin->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'district_id' => $request->district_id,
            'mandal_ids' => $request->mandal_ids,
        ]);

        return redirect()
            ->route('admin.sub-admins.level2.index')
            ->with('success', 'Sub Admin Level-2 updated successfully.');
    }
}
