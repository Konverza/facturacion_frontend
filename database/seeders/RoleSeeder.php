<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create([
            "name" => "admin",
        ]);

        $business = Role::create([
            "name" => "business",
        ]);

        $atm = Role::create([
            "name" => "atm",
        ]);

        $permissions = [
            'business' => ['show', 'create', 'edit', 'disable'],
            'plans' => ['show', 'create', 'edit', 'delete'],
            'users' => ['show', 'create', 'edit', 'delete'],
            'dtes' => ['create', 'show', 'cancel'],
            'products' => ['create', 'show', 'edit', 'delete'],
            'customers' => ['create', 'show', 'edit', 'delete'],
        ];

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::create(["name" => "{$action}-{$module}"]);
            }
        }

        $admin->givePermissionTo([
            "show-business",
            "create-business",
            "edit-business",
            "disable-business",
            "show-plans",
            "create-plans",
            "edit-plans",
            "delete-plans",
            "show-users",
            "create-users",
            "edit-users",
            "delete-users",
        ]);

        $business->givePermissionTo([
            "create-dtes",
            "show-dtes",
            "cancel-dtes",
            "create-products",
            "show-products",
            "edit-products",
            "delete-products",
            "create-customers",
            "show-customers",
            "edit-customers",
            "delete-customers",
        ]);

        $atm->givePermissionTo([
            "show-dtes",
            "create-dtes",
            "create-customers"
        ]);
    }
}
