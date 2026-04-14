<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCard extends Model
{
    protected $table = 'booking_cards';

    protected $fillable = [
        'booking_id',
        'merchant_id',
        'card_holder_name',
        'card_number',
        'card_type',
        'card_last_four',
        'expiration_month',
        'expiration_year',
        'cvv',
        'billing_address',
        'billing_phone',
        'billing_email',
        'charge_amount',
        'is_charged',
        'charged_at',
        'transaction_id',
        'payment_status',
        'card_order',
    ];

    protected $casts = [
        'is_charged'   => 'boolean',
        'charged_at'   => 'datetime',
        'charge_amount' => 'decimal:2',
    ];

    // ✅ Hide sensitive fields from JSON responses
    protected $hidden = [
        'card_number',
        'cvv',
    ];

    // ✅ RELATIONSHIPS
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    // ✅ HELPERS
    public function getMaskedCardAttribute(): string
    {
        return '**** **** **** ' . ($this->card_last_four ?? '****');
    }

    public function getExpiryAttribute(): string
    {
        return ($this->expiration_month ?? '--') . '/' . ($this->expiration_year ?? '----');
    }

// decrypt accessor for cvv and card number 

    public function getFullCardNumberAttribute() {
    return Crypt::decryptString($this->cardnumber);
}
public function getFullCvvAttribute() {
    return Crypt::decryptString($this->cvv);
}

}
