<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Travelomine!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.user_registered_by_admin',
            with: [
                'user' => $this->user,
                'loginUrl' => $this->loginUrlForRole($this->user->role),
            ],
        );
    }

    private function loginUrlForRole(string $role): string
    {
        $routes = [
            'agent' => '/agent/login',
            'support' => '/support/login',
            'charge' => '/charge/login',
            'mis' => '/mis/login',
            'admin' => '/admin/login',
            'manager' => '/admin/login',
        ];

        return url($routes[strtolower($role)] ?? '/login');
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
