<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class NewBookingNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $booking;
    public $agent;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->agent = $booking->user;
    }

    public function build()
    {
        return $this->subject('New Booking Created - ' . $this->booking->booking_reference)
                    ->markdown('emails.new-booking-notification');
    }
}
