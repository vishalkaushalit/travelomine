<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
         $this->call([
<<<<<<< HEAD
            // RolesAndPermissionsSeeder::class,
                        RoleSeeder::class,

=======
            RolesAndPermissionsSeeder::class,
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
            // Other seeders...
        ]);
    }
}
