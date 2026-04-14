<?php
// app/Mail/CardViewedNotification.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CardViewedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $bookingId;
    public $userName;
    public $cardId;

    public function __construct($bookingId, $userName, $cardId = null)
    {
        $this->bookingId = $bookingId;
        $this->userName = $userName;
        $this->cardId = $cardId;
    }

    public function build()
    {
        return $this->subject('Card Details Viewed - Booking #' . $this->bookingId)
            ->view('emails.card-viewed')
            ->with(['bookingId' => $this->bookingId, 'userName' => $this->userName, 'cardId' => $this->cardId]);
    }
}
