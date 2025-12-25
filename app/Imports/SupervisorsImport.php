<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupervisorsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $user = User::create([
            'name' => $row['name'],
            'phone_number' => $row['phone_number'],
            'district_id' => $row['district_id'],
            'mandal_id' => $row['mandal_id'],
            'password' => Hash::make('guest'),
            'must_change_password' => true,
        ]);

        $user->assignRole('supervisor');

        return $user;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'phone_number' => 'required|numeric|digits_between:10,15|unique:users,phone_number',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
        ];
    }
}
