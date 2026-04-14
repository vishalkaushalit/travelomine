<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles/permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles (idempotent - won't duplicate)
        $roles = ['admin', 'manager', 'agent', 'charging', 'support', 'mis'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName],
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }
    }
}
