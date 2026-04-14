<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\MerchantMailerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthConsentController extends Controller
{
    public function __construct(
        protected MerchantMailerService $merchantMailerService
    ) {}

    public function edit($id)
    {
        $booking = Booking::with([
            'passengers',
            'cards.merchant',
            'segments',
            'agencyMerchant',
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
            'agencyMerchant',
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
            'agencyMerchant',
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
        // $booking = Booking::with('agencyMerchant')->findOrFail($id);
        $booking = Booking::with(['agencyMerchant', 'cards'])->findOrFail($id);


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
            $this->merchantMailerService->sendAuthMail($booking, $finalHtml);

            $booking->update([
                'status' => 'auth_email_sent',
                'auth_email_sent_at' => now(),
            ]);

            session()->forget('authorize_preview_'.$id);

            return redirect()->route('charge.dashboard')
                ->with('success', 'Acknowledgement mail sent successfully.');
        } catch (TransportExceptionInterface $e) {
            Log::error('Mail transport failed', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('charge.authorize.preview.page', $id)
                ->with('error', 'Mail sending failed: '.$e->getMessage());
        } catch (\Exception $e) {
            Log::error('General mail send error', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('charge.authorize.preview.page', $id)
                ->with('error', 'Unexpected error while sending mail: '.$e->getMessage());
        }
    }

    public function resend(Request $request, $id)
    {
        // $booking = Booking::with('agencyMerchant')->findOrFail($id);
        $booking = Booking::with(['agencyMerchant', 'cards'])->findOrFail($id);

        $emailBody = $request->input('final_content') ?? session('authorize_preview_'.$id);

        if (! $emailBody) {
            $emailBody = view('emails.booking-auth-template', compact('booking'))->render();
        }

        $finalHtml = view('emails.customer-final-auth', [
            'booking' => $booking,
            'emailBody' => $emailBody,
        ])->render();

        try {
            $this->merchantMailerService->sendAuthMail($booking, $finalHtml);

            $booking->update([
                'auth_email_sent_at' => now(),
                'auth_email_resend_count' => ($booking->auth_email_resend_count ?? 0) + 1,
            ]);

            Log::info('Authorization email re-sent', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
            ]);

            return redirect()->back()
                ->with('success', 'Acknowledgement mail re-sent successfully.');

        } catch (TransportExceptionInterface $e) {
            Log::error('Resend mail transport failed', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Resend failed: '.$e->getMessage());

        } catch (\Exception $e) {
            Log::error('Resend mail general error', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Unexpected error: '.$e->getMessage());
        }
    }

    public function markAuthDone($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->email_auth_taken = 1;
        $booking->save();

        return redirect()->back()->with('success', 'Email Auth updated to Yes successfully.');
    }
}