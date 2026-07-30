<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Merchant;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MerchantMailerService
{
    public function sendAuthMail(Booking $booking, string $html): void
    {
        $booking->loadMissing('agencyMerchant');

        /** @var Merchant|null $merchant */
        $merchant = $booking->agencyMerchant;

        if (! $merchant) {
            throw new \RuntimeException('No merchant is assigned to this booking.');
        }

        if (! $merchant->is_active) {
            throw new \RuntimeException("Merchant {$merchant->name} is inactive.");
        }

        if (! $merchant->is_smtp_active) {
            throw new \RuntimeException("SMTP is inactive for merchant {$merchant->name}.");
        }

        foreach (['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password'] as $field) {
            if (blank($merchant->{$field})) {
                throw new \RuntimeException("Merchant SMTP field '{$field}' is missing for {$merchant->name}.");
            }
        }

        $fromEmail = $merchant->mail_from_address;
        // $fromName = $merchant->mail_from_name;
        $fromName = 'Reservation Desk';
        $replyToEmail = $merchant->mail_reply_to_address;
        $replyToName = $merchant->mail_reply_to_name;

        if (blank($fromEmail)) {
            throw new \RuntimeException("From email is missing for merchant {$merchant->name}.");
        }

        $transport = Transport::fromDsn($this->buildDsn($merchant));

        $mailer = new Mailer(
            'merchant_dynamic',
            app('view'),
            $transport,
            app('events')
        );

        $airlineName = $booking->airline_name ?? ($booking->segments->first()?->airline_name ?? 'the airline');
        $pnr = $booking->airline_pnr ?: ($booking->gk_pnr ?: 'N/A');
        $subject = "Authorization for {$airlineName} New Booking Confirmation #{$pnr}";

        $imagePath = null;
        if ($booking->itinerary_image) {
            $rawPath = ltrim($booking->itinerary_image, '/\\');
            $cleanPath = preg_replace('/^(public\/|storage\/|app\/public\/)+/i', '', $rawPath);
            $candidatePaths = [
                storage_path('app/public/' . $cleanPath),
                storage_path('app/public/' . $rawPath),
                storage_path('app/' . $cleanPath),
                storage_path('app/' . $rawPath),
                public_path('storage/' . $cleanPath),
                public_path($rawPath),
                base_path($rawPath),
            ];
            foreach ($candidatePaths as $path) {
                if ($path && file_exists($path) && !is_dir($path)) {
                    $imagePath = $path;
                    break;
                }
            }
        }

        $mailer->send([], [], function (Message $message) use (
            $booking,
            $html,
            $imagePath,
            $subject,
            $fromEmail,
            $fromName,
            $replyToEmail,
            $replyToName
        ) {
            $message->to($booking->customer_email)
                ->subject($subject)
                ->from($fromEmail, $fromName);

            if (! blank($replyToEmail)) {
                $message->replyTo($replyToEmail, $replyToName);
            }

            $bodyHtml = $html;
            if ($imagePath && file_exists($imagePath)) {
                try {
                    $cid = $message->embed($imagePath);
                    $bodyHtml = preg_replace('/src="data:image\/[^;]+;base64,[^"]+"/', 'src="' . $cid . '"', $html);
                } catch (\Exception $e) {
                    Log::warning('Failed to embed itinerary image inline: ' . $e->getMessage());
                }
            }

            $message->html($bodyHtml);
        });

        Log::info('Merchant auth email sent.', [
            'booking_id' => $booking->id,
            'merchant_id' => $merchant->id,
            'merchant_name' => $merchant->name,
            'customer_email' => $booking->customer_email,
            'from_email' => $fromEmail,
        ]);
    }

    protected function buildDsn(Merchant $merchant): string
    {
        $scheme = strtolower((string) $merchant->smtp_encryption) === 'ssl' ? 'smtps' : 'smtp';

        $username = rawurlencode((string) $merchant->smtp_username);
        $password = rawurlencode((string) $merchant->smtp_password);
        $host = $merchant->smtp_host;
        $port = $merchant->smtp_port;

        return "{$scheme}://{$username}:{$password}@{$host}:{$port}";
    }
}