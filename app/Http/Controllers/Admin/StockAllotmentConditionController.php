<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAllotmentCondition;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAllotmentConditionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAllotmentCondition::with('district');

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        $conditions = $query->orderBy('district_id')
            ->orderBy('land_extent_from')
            ->paginate(20)->withQueryString();
        
        $districts = District::where('is_active', true)->get();

        return view('admin.stock-allotment-conditions.index', compact('conditions', 'districts'));
    }

    public function create()
    {
        $districts = District::where('is_active', true)->get();
        return view('admin.stock-allotment-conditions.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'conditions' => 'required|array|min:1',
            'conditions.*.land_extent_from' => 'required|numeric|min:0',
            'conditions.*.land_extent_to' => 'required|numeric|min:0|gte:conditions.*.land_extent_from',
            'conditions.*.number_of_bags' => 'required|integer|min:1',
            'conditions.*.at_a_time_how_many' => 'required|integer|min:1',
            'conditions.*.interval_time_days' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing conditions for this district
            StockAllotmentCondition::where('district_id', $request->district_id)->delete();

            // Insert new conditions
            foreach ($request->conditions as $conditionData) {
                StockAllotmentCondition::create([
                    'district_id' => $request->district_id,
                    'land_extent_from' => $conditionData['land_extent_from'],
                    'land_extent_to' => $conditionData['land_extent_to'],
                    'number_of_bags' => $conditionData['number_of_bags'],
                    'at_a_time_how_many' => $conditionData['at_a_time_how_many'],
                    'interval_time_days' => $conditionData['interval_time_days'],
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stock-allotment-conditions.index')
                ->with('success', 'Stock allotment conditions saved successfully for the district.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to save conditions: ' . $e->getMessage());
        }
    }

    public function edit(StockAllotmentCondition $condition)
    {
        $districts = District::where('is_active', true)->get();
        $conditions = StockAllotmentCondition::where('district_id', $condition->district_id)
            ->orderBy('land_extent_from')
            ->get();
        
        return view('admin.stock-allotment-conditions.edit', compact('condition', 'districts', 'conditions'));
    }

    public function update(Request $request, StockAllotmentCondition $condition)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'conditions' => 'required|array|min:1',
            'conditions.*.land_extent_from' => 'required|numeric|min:0',
            'conditions.*.land_extent_to' => 'required|numeric|min:0|gte:conditions.*.land_extent_from',
            'conditions.*.number_of_bags' => 'required|integer|min:1',
            'conditions.*.at_a_time_how_many' => 'required|integer|min:1',
            'conditions.*.interval_time_days' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing conditions for this district
            StockAllotmentCondition::where('district_id', $request->district_id)->delete();

            // Insert new conditions
            foreach ($request->conditions as $conditionData) {
                StockAllotmentCondition::create([
                    'district_id' => $request->district_id,
                    'land_extent_from' => $conditionData['land_extent_from'],
                    'land_extent_to' => $conditionData['land_extent_to'],
                    'number_of_bags' => $conditionData['number_of_bags'],
                    'at_a_time_how_many' => $conditionData['at_a_time_how_many'],
                    'interval_time_days' => $conditionData['interval_time_days'],
                    'is_active' => true,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stock-allotment-conditions.index')
                ->with('success', 'Stock allotment conditions updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update conditions: ' . $e->getMessage());
        }
    }

    public function destroy(StockAllotmentCondition $condition)
    {
        $districtId = $condition->district_id;
        StockAllotmentCondition::where('district_id', $districtId)->delete();

        return redirect()->route('admin.stock-allotment-conditions.index')
            ->with('success', 'Stock allotment conditions deleted successfully.');
    }
}
