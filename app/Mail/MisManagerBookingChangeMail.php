<?php

namespace App\Mail;

use App\Models\BookingChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MisManagerBookingChangeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public BookingChange $change
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Booking #{$this->change->booking_id} Changed by MIS Manager - {$this->change->customer_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mis-manager-booking-change',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
