<?php

namespace App\Mail;

use App\Models\BookingPaymentLink;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public BookingPaymentLink $link;
    public string $paymentUrl;
    public $booking;
    public $merchant;

    public function __construct(BookingPaymentLink $link, string $paymentUrl)
    {
        $this->link = $link;
        $this->paymentUrl = $paymentUrl;
        $this->booking = $link->booking;
        $this->merchant = $link->merchant;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Link for Booking #'.$this->link->booking_id.' - $'.number_format($this->link->amount, 2)
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payment-link',
            with: [
                'link' => $this->link,
                'booking' => $this->booking,
                'merchant' => $this->merchant,
                'paymentUrl' => $this->paymentUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
