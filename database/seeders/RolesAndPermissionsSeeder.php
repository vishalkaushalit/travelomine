<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions (idempotent)
        $permissions = [
            'view_bookings',
            'create_bookings',
            'edit_bookings',
            'delete_bookings',
            'view_agents',
            'manage_agents',
            'view_settings',
            'manage_settings',
            'view_reports',
            'export_data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles + attach permissions (idempotent)
        $roleMatrix = [
            'admin' => Permission::all()->pluck('name')->toArray(),
            'agent' => ['view_bookings', 'create_bookings'],
            'mis' => ['view_bookings', 'edit_bookings', 'view_agents'],
            'charge' => ['view_bookings', 'edit_bookings', 'view_agents'],
            'manager' => ['view_bookings', 'view_agents', 'view_reports', 'export_data'],
            'support' => ['view_bookings', 'view_agents', 'view_reports'],
        ];

        foreach ($roleMatrix as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        // FIX: Handle admin user properly
        $adminEmail = 'duke.nelson@callinggenie.com';
        $adminUser = User::where('email', $adminEmail)->first();

        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Super Admin',
                'email' => $adminEmail,
                'phone' => '+919999402961',
                'password' => Hash::make('Duke.Nishant@123'),
                'agent_custom_id' => 'ADMIN_' . rand(1000, 9999),
                'role' => 'admin',
                'is_active' => true,
                'is_blocked' => false,
                'created_by' => null,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('✅ Admin user created successfully!');
        }

        // IMPORTANT: Sync the correct role (remove any existing roles first)
        $adminUser->syncRoles(['admin']); // This replaces all existing roles with just 'admin'
        $adminUser->update(['role' => 'admin']); // Ensure database field matches
        
        $this->command->info('✅ Admin role synced for: ' . $adminUser->email);

        // Fix for other users - prevent overriding admin
        User::where('email', 'like', '%@callinggenie.com')
            ->orWhere('email', 'like', '%@trafficpirates.com')
            ->where('email', '!=', $adminEmail) // Exclude admin
            ->each(function (User $user) {
                // Only assign agent role if they don't have a special role
                if (!in_array($user->role, ['admin', 'manager', 'mis', 'charge', 'support'])) {
                    $user->syncRoles(['agent']);
                    $user->update(['role' => 'agent']);
                    $this->command->info("✅ Assigned agent role to: {$user->email}");
                }
            });

        $this->command->info('----------------------------------------');
        $this->command->info('📋 Roles and Permissions seeded successfully!');
    }
}