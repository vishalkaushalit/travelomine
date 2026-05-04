<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRemark extends Model
{
    protected $fillable = [
        'booking_id',
        'agent_id',
        'remark_text',
        'remark_type',
        'amount_changed',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'amount_changed' => 'decimal:2'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}