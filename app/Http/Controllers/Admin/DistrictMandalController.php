<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Mandal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DistrictsImport;
use App\Imports\MandalsImport;
use App\Exports\DistrictsExport;
use App\Exports\MandalsExport;

class DistrictMandalController extends Controller
{
    public function index()
    {
        $districts = District::with('mandals')->get();
        return view('admin.districts.index', compact('districts'));
    }

    public function storeDistrict(Request $request)
    {
        $request->validate([
            'district_name' => 'required|string|max:255|unique:districts,name',
        ]);

        District::create([
            'name' => $request->district_name,
            'is_active' => true,
        ]);

        return back()->with('success', 'District created successfully.');
    }

    public function storeMandal(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'mandal_name' => 'required|string|max:255|unique:mandals,name',
        ]);

        Mandal::create([
            'district_id' => $request->district_id,
            'name' => $request->mandal_name,
            'is_active' => true,
        ]);

        return back()->with('success', 'Mandal created successfully.');
    }

    public function toggleDistrictStatus(District $district)
    {
        $district->update(['is_active' => !$district->is_active]);
        return back()->with('success', 'District status updated.');
    }

    public function toggleMandalStatus(Mandal $mandal)
    {
        $mandal->update(['is_active' => !$mandal->is_active]);
        return back()->with('success', 'Mandal status updated.');
    }

    public function uploadDistricts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new DistrictsImport, $request->file('file'));
            
            return back()->with('success', 'Districts uploaded successfully from Excel file.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload districts: ' . $e->getMessage());
        }
    }

    public function uploadMandals(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new MandalsImport, $request->file('file'));
            
            return back()->with('success', 'Mandals uploaded successfully from Excel file.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload mandals: ' . $e->getMessage());
        }
    }

    public function exportDistricts()
    {
        return Excel::download(new DistrictsExport, 'districts_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportMandals()
    {
        return Excel::download(new MandalsExport, 'mandals_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
