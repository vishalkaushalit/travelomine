<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingHotel extends Model
{
    protected $table = 'booking_hotels';

    protected $fillable = [
        'booking_id',
        'hotel_name',
        'hotel_location',
        'check_in_date',
        'check_out_date',
        'room_type',
        'number_of_rooms',
        'hotel_cost',
        'hotel_remarks',
    ];

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
        'hotel_cost'     => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getNightsAttribute(): int
    {
        return $this->check_in_date->diffInDays($this->check_out_date);
    }
}
