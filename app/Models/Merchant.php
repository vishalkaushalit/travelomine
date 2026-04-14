<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    protected $table = 'merchants';

    protected $fillable = [
        'name',
        'merchant_code',
        'security_key',
        'api_url',
        'tokenization_key',
        'contact_number',
        'support_mail',
        'wallet_balance',
        'is_active',
        'notes',

        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'from_email',
        'from_name',
        'reply_to_email',
        'reply_to_name',
        'is_smtp_active',

        // temporary compatibility with old schema
        'code',
        'account_number',
        'currency',
    ];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_smtp_active' => 'boolean',
        'smtp_password' => 'encrypted',
    ];

    public function bookingCards(): HasMany
    {
        return $this->hasMany(BookingCard::class);
    }

    public function agencyBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'agency_merchant_id');
    }

    public function getMailFromAddressAttribute(): ?string
    {
        return $this->from_email ?: $this->support_mail;
    }

    public function getMailFromNameAttribute(): string
    {
        return $this->from_name ?: $this->name;
    }

    public function getMailReplyToAddressAttribute(): ?string
    {
        return $this->reply_to_email ?: $this->support_mail;
    }

    public function getMailReplyToNameAttribute(): string
    {
        return $this->reply_to_name ?: $this->name;
    }
}