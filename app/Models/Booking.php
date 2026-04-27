<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $appends = ['badge_class'];

    // protected $fillable = [

    //     'user_id',
    //     'agent_custom_id',
    //     'booking_reference',
    //     'booking_date',
    //     'call_type',
    //     'service_provided',
    //     'service_type',
    //     'booking_portal',
    //     'email_auth_taken',
    //     'customer_name',
    //     'customer_email',
    //     'customer_phone',
    //     'billing_phone',
    //     'billing_address',
    //     'flight_type',
    //     'departure_city',
    //     'arrival_city',
    //     'gk_pnr',
    //     'airline_pnr',
    //     'total_passengers',
    //     'adults',
    //     'children',
    //     'infants',
    //     'card_last_four',
    //     'currency',
    //     'amount_charged',
    //     'amount_paid_airline',
    //     'total_mco',
    //     'status',
    //     'agent_remarks',
    //     'charging_remarks',
    //     'mis_remarks',
    //     'hotel_required',
    //     'cab_required',
    //     'insurance_required',
    //     'auth_email_sent_at',
    //     'payment_confirmed_at',
    //     'ticketed_at',
    // ];

    protected $fillable = [
        'user_id',
        'agent_custom_id',
        'booking_reference',
        'booking_date',
        'call_type',
        'service_provided',
        'service_type',
        'booking_portal',
        'email_auth_taken',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_phone',
        'billing_address',
        'flight_type',
        'departure_city',
        'arrival_city',
        'departure_date',
        'return_date',
        'airline_name',
        'flight_number',
        'cabin_class',
        'gk_pnr',
        'airline_pnr',
        'total_passengers',
        'adults',
        'children',
        'infants',
        'card_last_four',
        'expiration_month',
        'expiration_year',
        'currency',
        'amount_charged',
        'amount_paid_airline',
        'language',
        'total_mco',
        'agency_merchant_id',
        'agency_merchant_name',
        'airline_merchant',
        'status',
        'agent_remarks',
        'charging_remarks',
        'mis_remarks',
        'hotel_required',
        'cab_required',
        'insurance_required',
        'auth_email_sent_at',
        'payment_confirmed_at',
        'ticketed_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'departure_date' => 'date',
        'return_date' => 'date',
        'email_auth_taken' => 'boolean',
        'hotel_required' => 'boolean',
        'cab_required' => 'boolean',
        'insurance_required' => 'boolean',
        'auth_email_sent_at' => 'datetime',
        'payment_confirmed_at' => 'datetime',
        'ticketed_at' => 'datetime',
        'amount_charged' => 'decimal:2',
        'amount_paid_airline' => 'decimal:2',
        'total_mco' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            // Generate unique booking reference
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'BTK'.strtoupper(substr(uniqid(), -5));
            }
            // Auto-calculate total_mco
            // if ($booking->amount_charged && $booking->amount_paid_airline) {
            //     $booking->total_mco = $booking->amount_charged - $booking->amount_paid_airline;
            // }

            // Calculate total passengers
            $booking->total_passengers = ($booking->adults ?? 0) + ($booking->children ?? 0) + ($booking->infants ?? 0);
        });

        static::updating(function ($booking) {
            if ($booking->isDirty(['adults', 'children', 'infants'])) {
                $booking->total_passengers = ($booking->adults ?? 0) + ($booking->children ?? 0) + ($booking->infants ?? 0);
            }
        });
    }

    public function syncCitiesFromSegments(): void
    {
        $firstSegment = $this->segments()->orderBy('id')->first();
        $lastSegment = $this->segments()->orderByDesc('id')->first();

        $this->updateQuietly([
            'departure_city' => $firstSegment?->from_city,
            'arrival_city' => $lastSegment?->to_city,
        ]);
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(BookingCard::class);
    }

    public function hotel(): HasOne
    {
        return $this->hasOne(BookingHotel::class);
    }

    public function cab(): HasOne
    {
        return $this->hasOne(BookingCab::class);
    }

    public function insurance(): HasOne
    {
        return $this->hasOne(BookingInsurance::class);
    }

    public function flightSegments()
    {
        return $this->hasMany(\App\Models\FlightSegment::class);
    }

    // Accessors
    public function getTotalChargedAttribute(): float
    {
        return $this->cards->where('is_charged', true)->sum('charge_amount');
    }

    public function getPrimaryCardAttribute(): ?BookingCard
    {
        return $this->cards->sortBy('card_order')->first();
    }

    // create multiple flight segmants relationship for one booking
    public function segments()
    {
        return $this->hasMany(FlightSegment::class);
    }

    // encryption and decryption for card details
    protected $encrypted = ['card_number', 'cvv'];

    public function setCardNumberAttribute($value)
    {
        $this->attributes['card_number'] = encrypt($value);
    }

    public function getCardNumberAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function setCvvAttribute($value)
    {
        $this->attributes['cvv'] = encrypt($value);
    }

    public function getCvvAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function getBadgeClassAttribute()
    {
        // When status is 'ticketed' we want the badge-ticketed classes
        return $this->status === 'ticketed'
            ? 'badge badge-ticketed'
            : '';
    }

    public function agencyMerchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class, 'agency_merchant_id');
    }

    public function agent()
    {
        // We use agent_custom_id as the foreign key AND the owner key
        return $this->belongsTo(User::class, 'agent_custom_id', 'agent_custom_id');
    }

    public function bookingStatusRecord()
    {
        return $this->hasOne(Status::class, 'booking_id');
    }
}
