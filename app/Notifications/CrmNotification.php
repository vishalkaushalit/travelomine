<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CrmNotification extends Notification
{
    use Queueable;

    public $title;
    public $message;
    public $icon;
    public $color;
    public $actionUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $icon = 'fa-info-circle', $color = 'primary', $actionUrl = '#')
    {
        $this->title = $title;
        $this->message = $message;
        $this->icon = $icon;
        $this->color = $color;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
            'color' => $this->color,
            'action_url' => $this->actionUrl,
        ];
    }
}
