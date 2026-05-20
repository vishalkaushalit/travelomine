<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'city',
        'follow_up',
        'call_detail',
        'remark'
    ];

    protected $casts = [
        'follow_up' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the agent that created this call log
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scope for follow up calls
     */
    public function scopeFollowUp($query)
    {
        return $query->where('follow_up', true);
    }

    /**
     * Scope for today's calls
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}