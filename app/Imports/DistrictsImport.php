<?php

namespace App\Imports;

use App\Models\District;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DistrictsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Check if district already exists
        $existingDistrict = District::where('name', $row['name'])->first();
        
        if ($existingDistrict) {
            // Update existing district
            $existingDistrict->update([
                'is_active' => true,
            ]);
            return null;
        }

        // Create new district
        return new District([
            'name' => $row['name'],
            'is_active' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:districts,name',
        ];
    }
}

