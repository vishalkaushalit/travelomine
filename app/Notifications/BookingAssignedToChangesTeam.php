<?php

namespace App\Notifications;

use App\Models\BookingAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingAssignedToChangesTeam extends Notification implements ShouldQueue
{
    use Queueable;

    protected $assignment;

    public function __construct(BookingAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Booking Assignment for Changes')
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new booking has been assigned to the changes team by ' . $this->assignment->assignedBy->name)
            ->line('Booking Reference: ' . $this->assignment->booking->booking_reference)
            ->line('Message from agent: ' . $this->assignment->message)
            ->action('View Assignment', url('/charge/assignments/' . $this->assignment->id))
            ->line('Please review and take necessary action.');
    }

    public function toArray($notifiable)
    {
        return [
            'assignment_id' => $this->assignment->id,
            'booking_id' => $this->assignment->booking_id,
            'booking_reference' => $this->assignment->booking->booking_reference,
            'assigned_by' => $this->assignment->assignedBy->name,
            'message' => $this->assignment->message,
            'status' => $this->assignment->status,
        ];
    }
}