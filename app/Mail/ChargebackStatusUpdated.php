<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\ChargebackRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChargebackStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $chargebackRecord;
    public $recipientType; // 'admin', 'mis', 'mis_manager', 'agent'

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, ChargebackRecord $chargebackRecord, string $recipientType)
    {
        $this->booking = $booking;
        $this->chargebackRecord = $chargebackRecord;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $statusEmoji = $this->getStatusEmoji($this->chargebackRecord->status);
        
        return new Envelope(
            subject: "{$statusEmoji} Dispute Status Updated: {$this->chargebackRecord->status} - Booking #{$this->booking->id}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.chargeback.status-updated',
            with: [
                'booking' => $this->booking,
                'chargebackRecord' => $this->chargebackRecord,
                'recipientType' => $this->recipientType,
                'statusEmoji' => $this->getStatusEmoji($this->chargebackRecord->status),
                'statusColor' => $this->getStatusColor($this->chargebackRecord->status),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get emoji for status
     */
    private function getStatusEmoji(string $status): string
    {
        return match($status) {
            'Alert' => '🚨',
            'RDR' => '🔄',
            'Retrieval' => '🔍',
            'Chargeback' => '❌',
            'Refund' => '💰',
            'Resolved' => '✅',
            default => '📋',
        };
    }

    /**
     * Get color for status
     */
    private function getStatusColor(string $status): string
    {
        return match($status) {
            'Alert' => '#f39c12',
            'RDR' => '#3498db',
            'Retrieval' => '#9b59b6',
            'Chargeback' => '#e74c3c',
            'Refund' => '#95a5a6',
            'Resolved' => '#27ae60',
            default => '#34495e',
        };
    }
}