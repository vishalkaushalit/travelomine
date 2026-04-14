<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusUpdated extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail']; // Only sending email for now
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Booking Status Updated')
                    ->line('Booking #'.$this->booking->id.' status changed to '.str_replace('_', ' ', $this->booking->status).'.')
                    ->action('View Booking', url('/bookings/'.$this->booking->id))
                    ->line('Thank you for using our system!');
    }
}
