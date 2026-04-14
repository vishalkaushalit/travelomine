<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultSettings = [
            // Service Provided Options
            ['key' => 'service_provided', 'value' => 'Flight'],
            ['key' => 'service_provided', 'value' => 'Hotel'],
            ['key' => 'service_provided', 'value' => 'Cab'],
            
            // Service Type Options
            ['key' => 'service_type', 'value' => 'New Booking'],
            ['key' => 'service_type', 'value' => 'Change'],
            ['key' => 'service_type', 'value' => 'Update'],
            
            // Cabin Type Options
            ['key' => 'cabin_type', 'value' => 'Economy'],
            ['key' => 'cabin_type', 'value' => 'Premium Economy'],
            ['key' => 'cabin_type', 'value' => 'Business'],
            ['key' => 'cabin_type', 'value' => 'First Class'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
