<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeAssignment extends Model
{
    protected $fillable = [
        'booking_id',
        'charger_id',
        'agent_id',
        'merchant_id',
        'status',
        'assigned_at',
        'accepted_at',
        'rejected_at',
        'viewed_at',
        'completed_at',
    ];
    
    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'viewed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
    
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
    
    public function charger()
    {
        return $this->belongsTo(User::class, 'charger_id');
    }
    
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
    
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}