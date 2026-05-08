<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Booking Created - ' . $this->booking->booking_reference)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('A new booking has been created in the system.')
                    ->line('**Booking Reference**: ' . $this->booking->booking_reference)
                    ->line('**Customer Name**: ' . $this->booking->customer_name)
                    ->line('**Customer Email**: ' . $this->booking->customer_email)
                    ->line('**Service Type**: ' . $this->booking->service_type)
                    ->line('**Booking Status**: ' . ucfirst(str_replace('_', ' ', $this->booking->status)))
                    ->line('**Amount Charged**: ' . $this->booking->currency . ' ' . $this->booking->amount_charged)
                    ->action('View Booking', url('/bookings/' . $this->booking->id))
                    ->line('Please review the booking details in the system.');
    }
}
