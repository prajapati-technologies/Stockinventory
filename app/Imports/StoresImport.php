<?php

namespace App\Imports;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class StoresImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    /**
     * Prepare each row before validation.
     * Cast phone number to string to avoid numeric issues from Excel.
     */
    public function prepareForValidation(array $row): array
    {
        $row['phone_number'] = isset($row['phone_number']) ? (string) $row['phone_number'] : null;
        return $row;
    }

    /**
     * Validation rules for each row
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'phone_number' => 'required|string|unique:stores,phone_number|unique:users,phone_number',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
        ];
    }

    /**
     * Insert row into database
     */
    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            $phone = (string) $row['phone_number'];

            // Create user account
            $user = User::create([
                'name' => $row['name'],
                'phone_number' => $phone,
                'district_id' => $row['district_id'],
                'mandal_id' => $row['mandal_id'],
                'password' => Hash::make('guest'),
                'must_change_password' => true,
            ]);

            $user->assignRole('store_manager');

            // Create store
            $store = Store::create([
                'name' => $row['name'],
                'phone_number' => $phone,
                'address' => $row['address'] ?? null,
                'district_id' => $row['district_id'],
                'mandal_id' => $row['mandal_id'],
                'user_id' => $user->id,
                'valid_till' => now()->addMonths((int) ($row['validity_months'] ?? 6)),
                'is_active' => true,
            ]);

            DB::commit();
            return $store;

        } catch (\Exception $e) {
            DB::rollBack();

            $storeName = $row['name'] ?? 'Unknown';
            throw new \Exception("Failed to import store '{$storeName}': " . $e->getMessage());
        }
    }
}