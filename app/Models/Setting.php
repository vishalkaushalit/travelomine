<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get all values for a specific setting key
     */
public static function getOptions(string $key): array
{
    $settingsOptions = self::where('key', $key)
        ->pluck('value', 'id')
        ->toArray();

    $tableData = match ($key) {
        'service_type'     => DB::table('service_type')
            ->where('is_active', 1)
            ->pluck('type_name', 'id')      // ✅ Correct column name
            ->toArray(),
        'service_provided' => DB::table('service_provided')
            ->where('is_active', 1)
            ->pluck('service_name', 'id')   // ✅ Correct column name
            ->toArray(),
        default            => [],
    };

    return array_merge($settingsOptions, $tableData);
}

    /**
     * Add a new option
     */
public static function addOption(string $key, string $value): bool
{
    if (self::where('key', $key)->where('value', $value)->exists()) {
        return false;
    }

    self::create(['key' => $key, 'value' => $value]);

    match ($key) {
        'service_type'     => DB::table('service_type')->insertOrIgnore([
            'type_name' => $value,
            'slug' => Str::slug($value),
            'is_active' => 1
        ]),
        'service_provided' => DB::table('service_provided')->insertOrIgnore([
            'service_name' => $value,
            'slug' => Str::slug($value),
            'is_active' => 1
        ]),
        default            => null,
    };

    return true;
}

public static function deleteOption(int $id): bool
{
    $setting = self::find($id);
    if (!$setting) return false;

    match ($setting->key) {
        'service_type'     => DB::table('service_type')
            ->where('type_name', $setting->value)->delete(),
        'service_provided' => DB::table('service_provided')
            ->where('service_name', $setting->value)->delete(),
        default            => null,
    };

    return $setting->delete();
}

}
