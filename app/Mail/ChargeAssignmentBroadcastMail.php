<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\ChargeAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChargeAssignmentBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $assignment;
    public $assignedChargerName;
    public $merchantName;
    public $agentName;

    public function __construct(Booking $booking, ChargeAssignment $assignment, string $assignedChargerName, string $merchantName, string $agentName)
    {
        $this->booking = $booking;
        $this->assignment = $assignment;
        $this->assignedChargerName = $assignedChargerName;
        $this->merchantName = $merchantName;
        $this->agentName = $agentName;
    }

    public function build()
    {
        return $this->subject('New Booking Assigned To Charging Team')
            ->view('emails.charge.assignment-broadcast');
    }
}