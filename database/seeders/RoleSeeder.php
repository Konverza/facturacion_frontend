<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //Create some permissions
        Permission::create(['name' => 'create negocios']);
        Permission::create(['name' => 'read negocios']);
        Permission::create(['name' => 'update negocios']);
        Permission::create(['name' => 'delete negocios']);

        Permission::create(['name' => 'create planes']);
        Permission::create(['name' => 'read planes']);
        Permission::create(['name' => 'update planes']);
        Permission::create(['name' => 'delete planes']);

        Permission::create(['name' => 'create usuarios']);
        Permission::create(['name' => 'read usuarios']);
        Permission::create(['name' => 'update usuarios']);
        Permission::create(['name' => 'delete usuarios']);

        // Create roles
        $super_admin_role = Role::create(['name' => 'super-admin']);
        $negocio_role = Role::create(['name' => 'negocio']);
        $vendedor_role = Role::create(['name' => 'vendedor']);

        // Assign permissions to roles
        $negocio_role->givePermissionTo(
            'create negocios',
            'read negocios',
            'update negocios',
            'delete negocios',
        );
        $vendedor_role->givePermissionTo(
            'read negocios',
        );

        // Create users
        $super_admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@konverza.digital',
            'password' => bcrypt('password')
        ]);
        // Assign roles to users
        $super_admin->assignRole($super_admin_role);
    }
}
