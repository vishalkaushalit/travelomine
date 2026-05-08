<?php

namespace App\Notifications;

use App\Models\BookingChange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class MisManagerBookingChangeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public BookingChange $change
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => "Booking #{$this->change->booking_id} Updated",
            'message' => "MIS Manager {$this->change->mis_manager_name} updated booking for {$this->change->customer_name}",
            'booking_id' => $this->change->booking_id,
            'booking_change_id' => $this->change->id,
            'agent_name' => $this->change->agent_name,
            'customer_name' => $this->change->customer_name,
            'changed_fields' => implode(', ', $this->change->changed_fields ?? []),
            'manager_remark' => $this->change->manager_remark,
            'timestamp' => $this->change->created_at,
        ];
    }
}
