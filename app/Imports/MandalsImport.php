<?php

namespace App\Imports;

use App\Models\District;
use App\Models\Mandal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MandalsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find district by name
        $district = District::where('name', $row['district_name'])->first();
        
        if (!$district) {
            throw new \Exception("District '{$row['district_name']}' not found. Please create the district first.");
        }

        // Check if mandal already exists (unique across all districts)
        $existingMandal = Mandal::where('name', $row['name'])->first();
        
        if ($existingMandal) {
            // Update existing mandal
            $existingMandal->update([
                'district_id' => $district->id,
                'is_active' => true,
            ]);
            return null;
        }

        // Create new mandal
        return new Mandal([
            'district_id' => $district->id,
            'name' => $row['name'],
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'district_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:mandals,name',
        ];
    }
}

