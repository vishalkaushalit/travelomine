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
        'infant_in_lap', // Added this missing field
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
        'airline_merchant_id', // Added this missing field
        'airline_merchant_name', // Added this missing field
        'status',
        'agent_remarks',
        'payment_card_details',
        'charging_remarks',
        'mis_remarks',
        'hotel_required',
        'cab_required',
        'insurance_required',
        'auth_email_sent_at',
        'payment_confirmed_at',
        'ticketed_at',
        'payment_type', // Added this missing field
        'manager_remark', // Added this missing field
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
            $booking->total_passengers = ($booking->adults ?? 0) + ($booking->children ?? 0) + ($booking->infants ?? 0) + ($booking->infant_in_lap ?? 0);
        });

        static::updating(function ($booking) {
            if ($booking->isDirty(['adults', 'children', 'infants', 'infant_in_lap'])) {
                $booking->total_passengers = ($booking->adults ?? 0) + ($booking->children ?? 0) + ($booking->infants ?? 0) + ($booking->infant_in_lap ?? 0);
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

    public function getAllRemarksAttribute()
    {
        $remarks = collect();

        // Add old single remark if exists
        if ($this->agent_remarks) {
            $remarks->push((object) [
                'id' => null,
                'remark_text' => $this->agent_remarks,
                'remark_type' => 'general',
                'created_at' => $this->created_at,
                'is_legacy' => true,
                'agent' => $this->agent,
                'amount_changed' => null,
            ]);
        }

        // Add new multi remarks
        $remarks = $remarks->concat($this->remarks);

        return $remarks->sortByDesc('created_at');
    }

    // Relationships
    public function remarks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookingRemark::class)->orderBy('created_at', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function changes(): HasMany
    {
        return $this->hasMany(BookingChange::class);
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

    public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookingAssignment::class);
    }

    public function activeAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BookingAssignment::class)->whereIn('status', ['pending', 'accepted', 'rejected'])->latest();
    }

    public function chargebackRecords()
    {
        return $this->hasMany(ChargebackRecord::class);
    }

    // Accessor: current dispute status (null if no record)
    public function getCurrentDisputeStatusAttribute()
    {
        $latest = $this->chargebackRecords()->latest()->first();

        return $latest ? $latest->status : null;
    }
}