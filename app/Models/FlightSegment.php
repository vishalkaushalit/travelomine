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
        'departure_time',
        'arrival_time',
        'airline_name',
        'flight_number',
        'segment_pnr',
        'cabin_class',
        'airline_code',
        'airline_pnr',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'departure_time' => 'datetime:H:i',
        'arrival_time' => 'datetime:H:i',
    ];

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

    public function airline()
    {
        return $this->belongsTo(
            Airline::class,
            'airline_code',
            'airline_code'
        );
    }
}
