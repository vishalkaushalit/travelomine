<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NmiTransaction extends Model
{
    protected $fillable = [
        'merchant_id',
        'booking_id',
        'payment_link_id',
        'order_id',
        'transaction_id',
        'type',
        'customer_first_name',
        'customer_last_name',
        'email',
        'card_last4',
        'card_brand',
        'address1',
        'city',
        'state',
        'zip',
        'country',
        'amount',
        'currency',
        'status',
        'processed_at',
        'raw_response',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
        'raw_response' => 'array',
    ];
}
