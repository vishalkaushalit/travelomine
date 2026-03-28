<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthConsentController extends Controller
{
    public function edit($id)
    {
        $booking = Booking::with([
            'passengers',
            'cards.merchant',
            'segments',
        ])->findOrFail($id);

        $emailContent = view('emails.booking-auth-template', compact('booking'))->render();

        return view('charge.auth.edit', compact('booking', 'emailContent'));
    }

    public function preview(Request $request, $id)
    {
        $booking = Booking::with([
            'segments',
            'cards.merchant',
            'passengers',
        ])->findOrFail($id);

        $finalContent = $request->input('email_body');

        session(['authorize_preview_'.$id => $finalContent]);

        return redirect()->route('charge.authorize.preview.page', $id);
    }

    public function previewPage($id)
    {
        $booking = Booking::with([
            'segments',
            'cards.merchant',
            'passengers',
        ])->findOrFail($id);

        $finalContent = session('authorize_preview_'.$id);

        if (! $finalContent) {
            return redirect()->route('charge.authorize.edit', $id)
                ->with('error', 'Preview content not found. Please edit again.');
        }

        return view('charge.auth.preview', compact('booking', 'finalContent'));
    }

    public function send(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->auth_email_sent_at || $booking->status === 'auth_email_sent') {
            return redirect()->route('charge.dashboard')
                ->with('error', 'Auth mail has already been sent for this booking.');
        }

        $emailBody = $request->input('final_content') ?? session('authorize_preview_'.$id);

        if (! $emailBody) {
            return redirect()->route('charge.authorize.edit', $id)
                ->with('error', 'Email content missing. Please preview again.');
        }

        $finalHtml = view('emails.customer-final-auth', [
            'booking' => $booking,
            'emailBody' => $emailBody,
        ])->render();

        try {
            Mail::html($finalHtml, function ($message) use ($booking) {
                $message->to($booking->customer_email)
                    ->subject('Booking Acknowledgement Required: '.$booking->booking_reference)
                    ->from(config('mail.from.address'), 'Travelomile Reservation');
            });

            $booking->update([
                'status' => 'auth_email_sent',
                'auth_email_sent_at' => now(),
            ]);

            session()->forget('authorize_preview_'.$id);

            Log::info('Authorization email sent successfully', [
                'booking_id' => $booking->id,
                'customer_email' => $booking->customer_email,
            ]);

            return redirect()->route('charge.dashboard')
                ->with('success', 'Acknowledgement mail sent successfully.');
        } catch (TransportExceptionInterface $e) {
            Log::error('Mail transport failed', [
                'booking_id' => $booking->id,
                'customer_email' => $booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('charge.authorize.preview.page', $id)
                ->with('error', 'Mail sending failed: '.$e->getMessage());
        } catch (\Exception $e) {
            Log::error('General mail send error', [
                'booking_id' => $booking->id,
                'customer_email' => $booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('charge.authorize.preview.page', $id)
                ->with('error', 'Unexpected error while sending mail: '.$e->getMessage());
        }
    }

    public function resend(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Get content from request or session
        $emailBody = $request->input('final_content') ?? session('authorize_preview_'.$id);

        // If no session content, re-render the default template
        if (! $emailBody) {
            $emailBody = view('emails.booking-auth-template', compact('booking'))->render();
        }

        $finalHtml = view('emails.customer-final-auth', [
            'booking' => $booking,
            'emailBody' => $emailBody,
        ])->render();

        try {
            Mail::html($finalHtml, function ($message) use ($booking) {
                $message->to($booking->customer_email)
                    ->subject('Booking Acknowledgement Required: '.$booking->booking_reference)
                    ->from(config('mail.from.address'), 'Travelomile Reservation');
            });

            // Update resend count and timestamp only — don't change status
            $booking->update([
                'auth_email_sent_at' => now(),
                'auth_email_resend_count' => ($booking->auth_email_resend_count ?? 0) + 1,
            ]);

            Log::info('Authorization email re-sent', [
                'booking_id' => $booking->id,
                'customer_email' => $booking->customer_email,
                'resend_count' => $booking->auth_email_resend_count,
            ]);

            return redirect()->back()
                ->with('success', 'Acknowledgement mail re-sent successfully.');

        } catch (TransportExceptionInterface $e) {
            Log::error('Resend mail transport failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Resend failed: '.$e->getMessage());

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unexpected error: '.$e->getMessage());
        }
    }

    public function markAuthDone($id)
    {
        // Find the booking or fail with 404
        $booking = Booking::findOrFail($id);

        // Update the auth status
        $booking->email_auth_taken = 1;
        $booking->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Email Auth updated to Yes successfully.');
    }
}
