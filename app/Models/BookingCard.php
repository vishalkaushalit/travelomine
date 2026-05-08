<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class BookingCard extends Model
{
    protected $table = 'booking_cards';

    protected $fillable = [
        'booking_id',           // ✅ Correct - matches database
        'merchant_id',          // ✅ Correct - matches database
        'card_holder_name',     // ✅ Correct - matches database
        'card_number',          // ✅ Correct - matches database
        'card_type',            // ✅ Correct - matches database
        'card_last_four',       // ✅ Correct - matches database
        'expiration_month',     // ✅ Correct - matches database
        'expiration_year',      // ✅ Correct - matches database
        'cvv',                  // ✅ Correct - matches database
        'billing_address',      // ✅ Correct - matches database
        'billing_phone',        // ✅ Correct - matches database
        'billing_email',        // ✅ Correct - matches database
        'charge_amount',        // ✅ Correct - matches database
        'is_charged',           // ✅ Correct - matches database
        'charged_at',           // ✅ Correct - matches database
        'transaction_id',       // ✅ Correct - matches database
        'payment_status',       // ✅ Correct - matches database
        'card_order',           // ✅ Correct - matches database
        'merchantname',         // Extra field from your table
        'merchanttype',         // Extra field from your table
    ];

    protected $casts = [
        'is_charged' => 'boolean',
        'charged_at' => 'datetime',
        'charge_amount' => 'decimal:2',
    ];

    protected $hidden = [
        'card_number',
        'cvv',
    ];

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    // Helpers
    public function getMaskedCardAttribute(): string
    {
        return '**** **** **** ' . ($this->card_last_four ?? '****');
    }

    public function getExpiryAttribute(): string
    {
        return ($this->expiration_month ?? '--') . '/' . ($this->expiration_year ?? '----');
    }

    // Decrypt accessors
    public function getFullCardNumberAttribute()
    {
        return $this->card_number ? Crypt::decryptString($this->card_number) : null;
    }

    public function getFullCvvAttribute()
    {
        return $this->cvv ? Crypt::decryptString($this->cvv) : null;
    }
}