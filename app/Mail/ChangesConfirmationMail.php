<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class ChangesConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject('Authorization for ' . ($this->booking->segments->first()?->airline_name ?? 'Airline') . ' Changes Confirmation # ' . ($this->booking->booking_reference ?? 'N/A'))
                    ->view('emails.charge.auth.changes-confirmation');
    }
}
