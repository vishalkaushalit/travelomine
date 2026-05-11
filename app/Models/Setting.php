<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get options
     */
    public static function getOptions(string $key): array
    {
        return match ($key) {

            'service_type' => DB::table('service_type')
                ->where('is_active', 1)
                ->pluck('type_name', 'id')
                ->toArray(),

            'service_provided' => DB::table('service_provided')
                ->where('is_active', 1)
                ->pluck('service_name', 'id')
                ->toArray(),

            default => [],
        };
    }

    /**
     * Add option
     */
    public static function addOption(string $key, string $value): bool
    {
        return match ($key) {

            'service_type' => DB::table('service_type')
                ->insertOrIgnore([
                    'type_name' => $value,
                    'slug' => Str::slug($value),
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),

            'service_provided' => DB::table('service_provided')
                ->insertOrIgnore([
                    'service_name' => $value,
                    'slug' => Str::slug($value),
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),

            default => false,
        };
    }

    /**
     * Delete option
     */
    public static function deleteOption(string $key, int $id): bool
    {
        return match ($key) {

            'service_type' => DB::table('service_type')
                ->where('id', $id)
                ->delete(),

            'service_provided' => DB::table('service_provided')
                ->where('id', $id)
                ->delete(),

            default => false,
        };
    }
}