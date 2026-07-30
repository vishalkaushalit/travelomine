<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentAuthorizationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $mainContent;
    public $purchaseSummary;

    public function __construct($booking, $mainContent, $purchaseSummary)
    {
        $this->booking = $booking;
        $this->mainContent = $mainContent;
        $this->purchaseSummary = $purchaseSummary;
    }

    public function build()
    {
        $email = $this->subject('Payment Authorization & Booking Confirmation')
                      ->view('emails.payment-authorization');

        // Attach itinerary image inline if exists
        if ($this->booking->itinerary_image) {
            $imagePath = $this->findImagePath();
            
            if ($imagePath && file_exists($imagePath)) {
                $email->embed($imagePath, 'flight-itinerary-image');
            }
        }

        return $email;
    }

    private function findImagePath()
    {
        $paths = [
            storage_path('app/public/' . $this->booking->itinerary_image),
            storage_path('app/' . $this->booking->itinerary_image),
            public_path('storage/' . $this->booking->itinerary_image),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}