<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AuthConsentController extends Controller
{
    protected $merchantMailerService;

    public function __construct(\App\Services\MerchantMailerService $merchantMailerService)
    {
        $this->merchantMailerService = $merchantMailerService;
    }

    /**
     * Step 1: Open editor with default content based on service_type.
     */
    public function edit($id)
    {
        $booking = Booking::with([
            'segments',
            'cards.merchant',
            'passengers',
            'agencyMerchant',
        ])->findOrFail($id);

        // Map service_type to a specific body template
        $templateMap = [
            'New Booking'           => 'emails.charge.auth.new-booking',
            'Exchange'              => 'emails.charge.auth.exchange',
            'Exchange & Upgrade'    => 'emails.charge.auth.exchange-upgrade',
            'Cancellation'          => 'emails.charge.auth.cancellation',
            'Refund'                => 'emails.charge.auth.refund',
            'Seat selection'        => 'emails.charge.auth.seat-assignment',
            'Baggage edition'       => 'emails.charge.auth.baggage-edition',
            'Pet edition'           => 'emails.charge.auth.pet-edition',
        ];

        $bodyView = $templateMap[$booking->service_type] ?? 'emails.charge.auth.new-booking';

        // Default email body content (can be edited in UI)
        $emailContent = view($bodyView, compact('booking'))->render();

        return view('charge.auth.edit', compact('booking', 'emailContent'));
    }

    /**
     * Step 2: Save edited content in session and go to preview route.
     */
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

    /**
     * Step 3: Show preview using customer-final-auth layout.
     */
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
            return redirect()
                ->route('charge.authorize.edit', $id)
                ->with('error', 'Preview content not found. Please edit again.');
        }

        // This view should show the full email (layout + body) in-browser
        return view('charge.auth.preview', [
            'booking'     => $booking,
            'finalContent'=> $finalContent,
        ]);
    }

    /**
     * Step 4: Send final email to customer.
     */
    public function send(Request $request, $id)
    {
        $booking = Booking::with(['agencyMerchant', 'cards'])->findOrFail($id);

        if ($booking->auth_email_sent_at || $booking->status === 'auth_email_sent') {
            return redirect()
                ->route('charge.dashboard')
                ->with('error', 'Auth mail has already been sent for this booking.');
        }

        $emailBody = $request->input('final_content') ?? session('authorize_preview_'.$id);

        if (! $emailBody) {
            return redirect()
                ->route('charge.authorize.edit', $id)
                ->with('error', 'Email content missing. Please preview again.');
        }

        // Wrap body into full layout
        $finalHtml = view('emails.customer-final-auth', [
            'booking'   => $booking,
            'emailBody' => $emailBody,
        ])->render();

        try {
            $this->merchantMailerService->sendAuthMail($booking, $finalHtml);

            $booking->update([
                'status'            => 'auth_email_sent',
                'auth_email_sent_at'=> now(),
            ]);

            session()->forget('authorize_preview_'.$id);

            return redirect()
                ->route('charge.dashboard')
                ->with('success', 'Acknowledgement mail sent successfully.');

        } catch (TransportExceptionInterface $e) {
            Log::error('Mail transport failed', [
                'booking_id'     => $booking->id,
                'merchant_id'    => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
                'error'          => $e->getMessage(),
            ]);

            return redirect()
                ->route('charge.authorize.preview.page', $id)
                ->with('error', 'Mail sending failed: '.$e->getMessage());

        } catch (\Exception $e) {
            Log::error('General mail send error', [
                'booking_id'     => $booking->id,
                'merchant_id'    => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
                'error'          => $e->getMessage(),
            ]);

            return redirect()
                ->route('charge.authorize.preview.page', $id)
                ->with('error', 'Unexpected error while sending mail: '.$e->getMessage());
        }
    }

    /**
     * Optional: Resend auth mail.
     */
    public function resend(Request $request, $id)
    {
        $booking = Booking::with(['agencyMerchant', 'cards'])->findOrFail($id);

        $emailBody = $request->input('final_content') ?? session('authorize_preview_'.$id);

        if (! $emailBody) {
            // fallback to default body for current service_type
            $templateMap = [
                'New Booking'           => 'emails.charge.auth.new-booking',
                'Exchange'              => 'emails.charge.auth.exchange',
                'Exchange & Upgrade'    => 'emails.charge.auth.exchange-upgrade',
                'Cancellation' => 'emails.charge.auth.cancellation',
                'Refund' => 'emails.charge.auth.refund',
                'Changes Confirmation' => 'emails.charge.auth.changes-confirmation',
                'Name Correction' => 'emails.charge.auth.name-correction',
                'Pet Addition' => 'emails.charge.auth.pet-addition',
            ];

            $bodyView = $templateMap[$booking->service_type] ?? 'emails.charge.auth.new-booking';
            $emailBody = view($bodyView, compact('booking'))->render();
        }

        $finalHtml = view('emails.customer-final-auth', [
            'booking'   => $booking,
            'emailBody' => $emailBody,
        ])->render();

        try {
            $this->merchantMailerService->sendAuthMail($booking, $finalHtml);

            $booking->update([
                'auth_email_sent_at'      => now(),
                'auth_email_resend_count' => ($booking->auth_email_resend_count ?? 0) + 1,
            ]);

            Log::info('Authorization email re-sent', [
                'booking_id'     => $booking->id,
                'merchant_id'    => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Acknowledgement mail re-sent successfully.');

        } catch (TransportExceptionInterface $e) {
            Log::error('Resend mail transport failed', [
                'booking_id'  => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'error'       => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Resend failed: '.$e->getMessage());

        } catch (\Exception $e) {
            Log::error('Resend mail general error', [
                'booking_id'  => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'error'       => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Unexpected error: '.$e->getMessage());
        }
    }

    /**
     * Mark that email auth has been taken.
     */
    public function markAuthDone($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->email_auth_taken = 1;
        $booking->save();

        return redirect()->back()->with('success', 'Email Auth updated to Yes successfully.');
    }
}