<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCard extends Model
{
    protected $table = 'booking_cards';

        protected $fillable = [
            'bookingid',
            'merchantid',
            'merchantname',
            'merchanttype',
            'cardholdername',
            'cardnumber',
            'cardtype',
            'cardlastfour',
            'expirationmonth',
            'expirationyear',
            'cvv',
            'billingaddress',
            'billingphone',
            'billingemail',
            'chargeamount',
            'ischarged',
            'chargedat',
            'transactionid',
            'paymentstatus',
            'cardorder',
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

