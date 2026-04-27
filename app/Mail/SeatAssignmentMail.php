<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class SeatAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $confirmationNumber;
    public $seatAssignments;
    public $totalCost;
    public $cardLastFour;
    public $cardType;

    public function __construct(
        Booking $booking,
        string $confirmationNumber,
        array $seatAssignments,
        float $totalCost,
        string $cardLastFour,
        string $cardType = 'VISA'
    ) {
        $this->booking = $booking;
        $this->confirmationNumber = $confirmationNumber;
        $this->seatAssignments = $seatAssignments;
        $this->totalCost = $totalCost;
        $this->cardLastFour = $cardLastFour;
        $this->cardType = $cardType;
    }

    public function build()
    {
        return $this->subject('Authorization for Seat Assignment Confirmation # ' . $this->confirmationNumber)
                    ->markdown('emails.charge.auth.seat-assignment');
    }
}
