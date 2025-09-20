<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            // User management
            'user_management',
            'user_view',
            'user_create',
            'user_edit',
            'user_delete',

            // Role & Permission management
            'role_management',
            'permission_management',

            // Category permissions
            'category_view',
            'category_create',
            'category_edit',
            'category_delete',

            // Unit permissions
            'unit_view',
            'unit_create',
            'unit_edit',
            'unit_delete',

            // Supplier permissions
            'supplier_view',
            'supplier_create',
            'supplier_edit',
            'supplier_delete',

            // Item permissions
            'item_view',
            'item_create',
            'item_edit',
            'item_delete',

            // Purchase permissions
            'purchase_view',
            'purchase_create',
            'purchase_edit',
            'purchase_delete',

            // Purchase Item permissions
            'purchase_item_view',
            'purchase_item_create',
            'purchase_item_edit',
            'purchase_item_delete',

            // Sale permissions
            'sale_view',
            'sale_create',
            'sale_edit',
            'sale_delete',

            // Sale Item permissions
            'sale_item_view',
            'sale_item_create',
            'sale_item_edit',
            'sale_item_delete',

            // Stock Movement permissions
            'stock_movement_view',
            'stock_movement_create',
            'stock_movement_edit',
            'stock_movement_delete',

            // Additional purchase-related permissions for extended functionality
            'purchase_report_view',
            'purchase_batch_manage',
            'purchase_payment_manage',
            'purchase_export',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $user = Role::firstOrCreate(['name' => 'User']);
        $pharmacist = Role::firstOrCreate(['name' => 'Pharmacist']);
        $storeManager = Role::firstOrCreate(['name' => 'Store Manager']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());

        $admin->givePermissionTo([
            // User CRUD
            'user_view', 'user_create', 'user_edit', 'user_delete',
            // Management group
            'user_management',

            // Category permissions
            'category_view', 'category_create', 'category_edit', 'category_delete',

            // Unit permissions
            'unit_view', 'unit_create', 'unit_edit', 'unit_delete',

            // Supplier permissions
            'supplier_view', 'supplier_create', 'supplier_edit', 'supplier_delete',

            // Item permissions
            'item_view', 'item_create', 'item_edit', 'item_delete',

            // Purchase permissions
            'purchase_view', 'purchase_create', 'purchase_edit', 'purchase_delete',
            'purchase_report_view', 'purchase_batch_manage', 'purchase_payment_manage', 'purchase_export',

            // Purchase Item permissions
            'purchase_item_view', 'purchase_item_create', 'purchase_item_edit', 'purchase_item_delete',

            // Sale permissions
            'sale_view', 'sale_create', 'sale_edit', 'sale_delete',

            // Sale Item permissions
            'sale_item_view', 'sale_item_create', 'sale_item_edit', 'sale_item_delete',

            // Stock Movement permissions
            'stock_movement_view', 'stock_movement_create', 'stock_movement_edit', 'stock_movement_delete',
        ]);

        $storeManager->givePermissionTo([
            'purchase_view', 'purchase_create', 'purchase_edit',
            'purchase_report_view', 'purchase_batch_manage', 'purchase_payment_manage',
            'purchase_item_view', 'purchase_item_create', 'purchase_item_edit',
            'supplier_view', 'supplier_create', 'supplier_edit',
            'item_view', 'item_create', 'item_edit',
            'category_view', 'unit_view',
            'stock_movement_view', 'stock_movement_create',
        ]);

        $pharmacist->givePermissionTo([
            'purchase_view',
            'purchase_report_view',
            'purchase_item_view',
            'item_view',
            'category_view',
            'unit_view',
            'supplier_view',
            'stock_movement_view',
            'sale_view', 'sale_create', 'sale_edit',
            'sale_item_view', 'sale_item_create', 'sale_item_edit',
        ]);

        $user->givePermissionTo([
            'user_view',
            // View permissions only for basic user
            'category_view',
            'unit_view',
            'supplier_view',
            'item_view',
            'purchase_view',
            'purchase_item_view',
            'sale_view',
            'sale_item_view',
            'stock_movement_view',
        ]);

        // Create super admin user if not exists
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
            ]
        );

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create store manager
        $storeManagerUser = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Store Manager',
                'password' => bcrypt('password'),
            ]
        );

        // Create pharmacist
        $pharmacistUser = User::firstOrCreate(
            ['email' => 'pharmacist@example.com'],
            [
                'name' => 'Pharmacist',
                'password' => bcrypt('password'),
            ]
        );

        // Create regular user
        $regularUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => bcrypt('password'),
            ]
        );

        $superAdminUser->assignRole($superAdmin);
        $adminUser->assignRole($admin);
        $storeManagerUser->assignRole($storeManager);
        $pharmacistUser->assignRole($pharmacist);
        $regularUser->assignRole($user);
    }
}
