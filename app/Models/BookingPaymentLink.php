<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingPaymentLink extends Model
{
    protected $fillable = [
        'booking_id',
        'merchant_id',
        'token',
        'customer_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'amount',
        'currency',
        'status',
        'expires_at',
        'paid_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at'    => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (BookingPaymentLink $link) {
            if (empty($link->token)) {
                $link->token = (string) Str::uuid();
            }
            if (empty($link->currency)) {
                $link->currency = 'USD';
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
