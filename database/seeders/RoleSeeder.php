<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
<<<<<<< HEAD
use App\Models\User;
use Illuminate\Support\Facades\Hash;
=======
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7

class RoleSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        // Define allowed roles
        $roles = ['admin', 'manager', 'agent', 'charging', 'support', 'mis', 'mis-manager'];
        
        $this->command->info('📋 Available roles: ' . implode(', ', $roles));
        
        // Create or update admin user
        $adminEmail = 'duke.nelson@callinggenie.com';
        $adminUser = User::where('email', $adminEmail)->first();

        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Super Admin',
                'alias_name' => null,
                'email' => $adminEmail,
                'extension_number' => null,
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
        } else {
            // Ensure admin has correct role
            $adminUser->role = 'admin';
            $adminUser->save();
            $this->command->info('✅ Admin user role updated successfully!');
        }

        // Fix for other users - assign appropriate roles based on email domain or existing role
        User::where('email', 'like', '%@callinggenie.com')
            ->orWhere('email', 'like', '%@trafficpirates.com')
            ->where('email', '!=', $adminEmail) // Exclude admin
            ->each(function (User $user) {
                // Only assign agent role if they don't have a special role
                $specialRoles = ['admin', 'manager', 'mis', 'mis-manager', 'charging', 'support'];
                
                if (!in_array($user->role, $specialRoles)) {
                    $oldRole = $user->role;
                    $user->role = 'agent';
                    $user->save();
                    $this->command->info("✅ Assigned agent role to: {$user->email} (was: {$oldRole})");
                } else {
                    $this->command->info("✓ Preserved role '{$user->role}' for: {$user->email}");
                }
            });

        $this->command->info('----------------------------------------');
        $this->command->info('✅ Role seeder completed successfully!');
        
        // Display all users and their roles
        $this->command->info("\n📊 Current Users:");
        $users = User::all(['id', 'name', 'email', 'role', 'is_active']);
        foreach ($users as $user) {
            $this->command->line("  - {$user->name} ({$user->email}): [{$user->role}]" . ($user->is_active ? ' ✓' : ' ✗'));
        }
    }
}
=======
        // Reset cached roles/permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles (idempotent - won't duplicate)
        $roles = ['admin', 'manager', 'agent', 'charging', 'support', 'mis', 'mis-manager'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName],
                ['name' => $roleName, 'guard_name' => 'web']
            );
        }
    }
}
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
