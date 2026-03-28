<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightSegment extends Model
{
    protected $fillable = [
    'booking_id',
    'from_city',
    'to_city',
    'from_airport',
    'to_airport',
    'departure_date',
    'return_date',
    'airline_name',
    'flight_number',
    'segment_pnr',
    'cabin_class',
    'airline_code',
    'airline_pnr',   // ← was 'pnr'
    // cabin_type removed ✓
    ];
    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    // Relationship
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    protected static function booted(): void
{
    static::saved(function ($segment) {
        $segment->booking?->syncCitiesFromSegments();
    });

    static::deleted(function ($segment) {
        $segment->booking?->syncCitiesFromSegments();
    });
}

}

