<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';

    protected $fillable = [
        'booking_id',
        'booking_reference',
        'transaction_status',
        'ticket_status',
        'booking_status',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getTransactionStatusAttribute($value)
    {
        return $value ?: 'Pending';
    }
}