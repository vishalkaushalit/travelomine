<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingInsurance extends Model
{
    protected $table = 'booking_insurances';

    protected $fillable = [
        'booking_id',
        'insurance_type',
        'insurance_provider',
        'coverage_amount',
        'insurance_cost',
        'policy_number',
        'insurance_remarks',
    ];

    protected $casts = [
        'coverage_amount' => 'decimal:2',
        'insurance_cost'  => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
