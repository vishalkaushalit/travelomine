<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCab extends Model
{
    protected $table = 'booking_cabs';

    protected $fillable = [
        'booking_id',
        'cab_type',
        'pickup_location',
        'drop_location',
        'pickup_datetime',
        'cab_cost',
        'cab_remarks',
    ];

    protected $casts = [
        'pickup_datetime' => 'datetime',
        'cab_cost'        => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function getCabTypeLabelAttribute(): string
    {
        return match($this->cab_type) {
            'pickup'    => 'Airport Pickup',
            'drop'      => 'Airport Drop',
            'roundtrip' => 'Round Trip',
            default     => $this->cab_type,
        };
    }
}
