<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'store_manager']);
        Role::firstOrCreate(['name' => 'supervisor']);
        Role::firstOrCreate(['name' => 'sub_admin_level_1']);
        Role::firstOrCreate(['name' => 'sub_admin_level_2']);

        // Create permissions
        $permissions = [
            // Admin permissions
            'manage_stores',
            'manage_supervisors',
            'manage_districts',
            'manage_mandals',
            'extend_validity',
            'reset_passwords',
            
            // Store Manager permissions
            'manage_customers',
            'create_sales',
            'view_own_store',
            
            // Supervisor permissions
            'view_reports',
            'modify_stock_allocation',
            'upload_customer_data',
            'view_stores_in_mandal',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(Permission::all());

        $storeRole = Role::findByName('store_manager');
        $storeRole->givePermissionTo([
            'manage_customers',
            'create_sales',
            'view_own_store',
        ]);

        $supervisorRole = Role::findByName('supervisor');
        $supervisorRole->givePermissionTo([
            'view_reports',
            'modify_stock_allocation',
            'upload_customer_data',
            'view_stores_in_mandal',
            'manage_customers',
        ]);
    }
}
