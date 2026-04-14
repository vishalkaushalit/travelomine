<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\ChargeAssignment; // Changed from ChargingAssignment to ChargeAssignment
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChargingAssignment extends Notification
{
    use Queueable;

    public $booking;

    public $assignment;

    public function __construct(Booking $booking, ChargeAssignment $assignment) // Changed here too
    {
        $this->booking = $booking;
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Charging Assignment Available')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new charging assignment is available for review.')
            ->line('Booking Reference: ' . $this->booking->booking_reference)
            ->line('Customer Name: ' . ($this->booking->customer_name ?? optional($this->booking->user)->name ?? 'N/A'))
            ->line('Amount: $' . number_format($this->booking->amount_charged, 2))
            ->line('Merchant: ' . (optional($this->assignment->merchant)->name ?? 'N/A'))
            ->action('View Assignment', route('charge.assignments.details', $this->assignment->id))
            ->line('This booking is visible to all charge team members.');
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'assignment_id' => $this->assignment->id,
            'merchant_id' => $this->assignment->merchant_id,
            'message' => "New charging assignment: {$this->booking->booking_reference}",
            'url' => route('charge.assignments.details', $this->assignment->id),
            'amount' => $this->booking->amount_charged,
            'assigned_at' => optional($this->assignment->assigned_at)?->toDateTimeString(),
        ];
    }
}
