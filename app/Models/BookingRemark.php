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

    protected static function boot()
    {
        parent::boot();

        static::created(function ($remark) {
            // Requirement 4: Agent - when anyone adds a new comment on their bookings
            try {
                $booking = $remark->booking;
                if ($booking && $booking->user) {
                    $agent = $booking->user;
                    // Only notify if the commenter is NOT the agent themselves
                    if ($remark->agent_id != $agent->id && $agent->is_active && !$agent->is_blocked) {
                        $commenterName = $remark->agent ? $remark->agent->name : 'Someone';

                        $agent->notify(new \App\Notifications\CrmNotification(
                            'New Comment on Booking #' . $booking->booking_reference,
                            "{$commenterName} added a comment: " . \Illuminate\Support\Str::limit($remark->remark_text, 60),
                            'fa-comment-dots',
                            'info',
                            route('agent.bookings.show', $booking->id)
                        ));
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Comment notification error: ' . $e->getMessage());
            }
        });
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}