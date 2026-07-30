<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargebackRecord extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($record) {
            // Requirement 6: Agent, Admin, MIS - when support team change the status of booking
            try {
                $booking = $record->booking;
                if (!$booking) return;

                $creator = $record->user; // The user who made the change

                // Define recipients
                $recipients = collect();

                // 1. Agent always gets notified
                if ($booking->user && $booking->user->role === 'agent' && $booking->user->is_active && !$booking->user->is_blocked) {
                    $recipients->put($booking->user->id, $booking->user);
                }

                // 2. If the creator is support (or if this is a chargeback/dispute status update), notify Admin and MIS
                if ($creator && $creator->role === 'support') {
                    $additionalUsers = \App\Models\User::whereIn('role', ['admin', 'mis'])
                        ->where('is_active', true)
                        ->where('is_blocked', false)
                        ->get();
                    foreach ($additionalUsers as $u) {
                        $recipients->put($u->id, $u);
                    }
                }

                foreach ($recipients as $recipient) {
                    $title = 'Booking Dispute Status Changed';
                    $message = "Booking #{$booking->booking_reference} dispute status updated to \"{$record->status}\"";
                    if ($creator && $creator->role === 'support') {
                        $message .= " by Support Team (" . ($creator->name ?? 'Support') . ")";
                    }
                    $message .= ".";

                    $actionUrl = \App\Models\Booking::getBookingShowRouteForUser($booking, $recipient);

                    $recipient->notify(new \App\Notifications\CrmNotification(
                        $title,
                        $message,
                        'fa-exclamation-triangle',
                        'warning',
                        $actionUrl
                    ));
                }
            } catch (\Throwable $e) {
                \Log::error('Dispute status notification error: ' . $e->getMessage());
            }
        });
    }

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