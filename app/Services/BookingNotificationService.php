<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Mail\NewBookingNotification;
use App\Notifications\BookingCreated;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Mail;

class BookingNotificationService
{
    /**
     * Send notification and announcement when a new booking is created
     */
    public function notifyNewBooking(Booking $booking): void
    {
        // Get all active admin, CRM and MIS users
        $recipients = User::where('is_active', true)
            ->where('is_blocked', false)
            ->whereIn('role', ['admin', 'crm', 'mis'])
            ->get();

        // Send individual emails and notifications to all recipients
        foreach ($recipients as $user) {
            Mail::to($user->email)->queue(new NewBookingNotification($booking));
            $user->notify(new BookingCreated($booking));
        }

        // Create an announcement/admin notification for admin, CRM and MIS dashboard
        $this->createBookingAnnouncement($booking, $recipients);
    }

    /**
     * Create an announcement notification on the dashboard
     */
    protected function createBookingAnnouncement(Booking $booking, $recipients): void
    {
        $announcement = AdminNotification::create([
            'title' => 'New Booking Created',
            'message' => sprintf(
                'New booking %s created by %s for customer %s. Amount: %s %s',
                $booking->booking_reference,
                $booking->user->name ?? 'Unknown Agent',
                $booking->customer_name,
                $booking->currency,
                $booking->amount_charged
            ),
            'target_roles' => ['admin', 'crm', 'mis'],
            'target_type' => 'booking_created',
            'priority' => 'high',
            'is_active' => true,
            'can_dismiss' => true,
            'created_by' => auth()->id() ?? 1, // System user or authenticated user
        ]);

        // Associate announcement with all admin, CRM and MIS users
        $userIds = $recipients->pluck('id')->toArray();
        if (!empty($userIds)) {
            $announcement->readBy()->attach($userIds, ['read_at' => null]);
        }
    }
}
