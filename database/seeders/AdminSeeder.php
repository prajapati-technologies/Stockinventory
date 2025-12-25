<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'phone_number' => '9999999999',
            'email' => 'admin@storemanagement.com',
            'password' => Hash::make('admin123'),
            'must_change_password' => false,
        ]);

        $admin->assignRole('admin');
    }
}
