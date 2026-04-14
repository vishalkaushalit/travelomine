<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passenger extends Model
{
    protected $fillable = [
        'booking_id',
        'passenger_type',   // ADT, CHD, INF, INL
        'title',            // Mr, Mrs, Ms, Miss, Dr, Master
        'first_name',
        'middle_name',
        'last_name',
        'gender',           // male, female, other
        'dob',
        'passport_number',
        'passport_expiry',
        'nationality',
        'seat_preference',
        'meal_preference',
        'special_assistance',
    ];

    protected function casts(): array
    {
        return [
            'dob'             => 'date',
            'passport_expiry' => 'date',
        ];
    }

    // ✅ RELATIONSHIP
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // ✅ HELPER: Full name with title
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->title,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
        ]);

        return implode(' ', $parts);
    }

    // ✅ HELPER: Label for passenger type
    public function getPassengerTypeLabelAttribute(): string
    {
        return match($this->passenger_type) {
            'ADT' => 'Adult',
            'CHD' => 'Child',
            'INF' => 'Infant (on seat)',
            'INL' => 'Infant (on lap)',
            default => $this->passenger_type,
        };
    }
}
