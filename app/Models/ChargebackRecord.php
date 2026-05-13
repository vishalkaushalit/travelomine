<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargebackRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'status',
        'time_remaining',
        'remarks',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the chargeback record.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user who created this chargeback record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include records of a specific status.
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include alert records.
     */
    public function scopeAlerts($query)
    {
        return $query->where('status', 'Alert');
    }

    /**
     * Scope a query to only include chargeback records.
     */
    public function scopeChargebacks($query)
    {
        return $query->where('status', 'Chargeback');
    }

    /**
     * Get the formatted time remaining.
     */
    public function getFormattedTimeRemainingAttribute()
    {
        if (!$this->time_remaining) {
            return null;
        }

        // Parse the time remaining (format: "HH:MM")
        $parts = explode(':', $this->time_remaining);
        $hours = (int)($parts[0] ?? 0);
        $minutes = (int)($parts[1] ?? 0);

        $result = [];
        if ($hours > 0) {
            $result[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        }
        if ($minutes > 0) {
            $result[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }

        return implode(' ', $result);
    }

    /**
     * Check if the alert has expired (only for Alert status).
     */
    public function hasExpired()
    {
        if ($this->status !== 'Alert' || !$this->time_remaining) {
            return false;
        }

        // Calculate if the time has passed
        // This is a simplified version - you might want to calculate based on created_at + time_remaining
        return false; // Implement your logic here
    }
}