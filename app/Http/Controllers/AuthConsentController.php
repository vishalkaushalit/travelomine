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
     * Get the view path and route prefix based on user role
     */
    private function getRoleBasedConfig()
    {
        $user = auth()->user();
        
        if ($user->hasRole('agent')) {
            return [
                'view_prefix' => 'agent.auth',
                'route_prefix' => 'agent',
                'redirect_route' => 'agent.bookings.index'
            ];
        }
        
        // Default for charge team and admin
        return [
            'view_prefix' => 'charge.auth',
            'route_prefix' => 'charge',
            'redirect_route' => 'charge.dashboard'
        ];
    }

    /**
     * Step 1: Open editor with default content based on service_type.
     */
    public function edit($id)
    {
        $booking = Booking::with([
            'segments.airline',
            'cards.merchant',
            'passengers',
            'agencyMerchant',
        ])->findOrFail($id);

        // Map service_type to a specific body template
        $templateMap = [
            'New Booking' => 'emails.charge.auth.new-booking',
            'Exchange' => 'emails.charge.auth.exchange',
            'Cancellation' => 'emails.charge.auth.cancellation',
            'Refund' => 'emails.charge.auth.refund',
            'Seat selection' => 'emails.charge.auth.seat-assignment',
            'Baggage edition' => 'emails.charge.auth.baggage-edition',
            'Pet edition' => 'emails.charge.auth.pet-edition',
            'Others' => 'emails.charge.auth.others',
            'Cancel & Refund' => 'emails.charge.auth.cancel-and-refund',
            'Change' => 'emails.charge.auth.change',
            'Exchange & Upgrade' => 'emails.charge.auth.exchange-upgrade',
        ];

        $bodyView = $templateMap[$booking->service_type] ?? 'emails.charge.auth.new-booking';

        // Default email body content (can be edited in UI)
        $emailContent = view($bodyView, compact('booking'))->render();

        $parts = preg_split(
            '/<h4[^>]*>\s*Purchase Summary\s*:?\s*<\/h4>/i',
            $emailContent
        );

        $mainContent = $parts[0] ?? $emailContent;

        $purchaseSummary = '';

        if (isset($parts[1])) {
            $purchaseSummary = '<h4>Purchase Summary:</h4>'.$parts[1];
        }

        // Get role-based configuration
        $config = $this->getRoleBasedConfig();

        return view(
            $config['view_prefix'] . '.edit', 
            compact(
                'booking',
                'mainContent',
                'purchaseSummary'
            )
        );
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

        session([
            'main_content_'.$id => $request->main_content,
            'purchase_summary_'.$id => $request->purchase_summary,
        ]);

        // Get role-based configuration
        $config = $this->getRoleBasedConfig();

        return redirect()->route($config['route_prefix'] . '.authorize.preview.page', $id);
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

        $mainContent = session('main_content_'.$id);
        $purchaseSummary = session('purchase_summary_'.$id);

        // Format for preview as well to maintain consistency
        $mainContent = $this->formatEmailContent($mainContent);
        $purchaseSummary = $this->formatEmailContent($purchaseSummary);

        // Get role-based configuration
        $config = $this->getRoleBasedConfig();

        return view($config['view_prefix'] . '.preview', [
            'booking' => $booking,
            'mainContent' => $mainContent,
            'purchaseSummary' => $purchaseSummary,
        ]);
    }

    /**
     * Step 4: Send final email to customer.
     */
    public function send(Request $request, $id)
    {
        $booking = Booking::with([
            'agencyMerchant',
            'cards',
            'segments.airline',
            'passengers',
            'user',
        ])->findOrFail($id);

        // Get role-based configuration
        $config = $this->getRoleBasedConfig();

        if ($booking->auth_email_sent_at || $booking->status === 'auth_email_sent') {
            return redirect()
                ->route($config['redirect_route'])
                ->with('error', 'Auth mail has already been sent for this booking.');
        }

        $mainContent = $request->input('main_content') ?? session('main_content_'.$id);
        $purchaseSummary = $request->input('purchase_summary') ?? session('purchase_summary_'.$id);

        if (! $mainContent) {
            return redirect()
                ->route($config['route_prefix'] . '.authorize.edit', $id)
                ->with('error', 'Email content missing. Please preview again.');
        }

        // Apply formatting to individual content parts
        $mainContent = $this->formatEmailContent($mainContent);
        $purchaseSummary = $this->formatEmailContent($purchaseSummary);

        $finalHtml = view('emails.customer-final-auth', [
            'booking' => $booking,
            'mainContent' => $mainContent,
            'purchaseSummary' => $purchaseSummary,
        ])->render();

        try {
            $this->merchantMailerService->sendAuthMail($booking, $finalHtml);

            $booking->update([
                'status' => 'auth_email_sent',
                'auth_email_sent_at' => now(),
            ]);

            session()->forget(['main_content_'.$id, 'purchase_summary_'.$id]);

            return redirect()
                ->route($config['redirect_route'])
                ->with('success', 'Acknowledgement mail sent successfully.');

        } catch (TransportExceptionInterface $e) {
            Log::error('Mail transport failed', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route($config['route_prefix'] . '.authorize.preview.page', $id)
                ->with('error', 'Mail sending failed: '.$e->getMessage());

        } catch (\Exception $e) {
            Log::error('General mail send error', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route($config['route_prefix'] . '.authorize.preview.page', $id)
                ->with('error', 'Unexpected error while sending mail: '.$e->getMessage());
        }
    }

    /**
     * Optional: Resend auth mail.
     */
    public function resend(Request $request, $id)
    {
        $booking = Booking::with([
            'agencyMerchant',
            'cards',
            'segments.airline',
            'passengers',
            'user',
        ])->findOrFail($id);

        $mainContent = $request->input('main_content') ?? session('main_content_'.$id);
        $purchaseSummary = $request->input('purchase_summary') ?? session('purchase_summary_'.$id);

        if (! $mainContent) {
            // fallback to default body for current service_type
            $templateMap = [
                'New Booking' => 'emails.charge.auth.new-booking',
                'Exchange' => 'emails.charge.auth.exchange',
                'Exchange & Upgrade' => 'emails.charge.auth.exchange-upgrade',
                'Cancellation' => 'emails.charge.auth.cancellation',
                'Refund' => 'emails.charge.auth.refund',
                'Changes Confirmation' => 'emails.charge.auth.changes-confirmation',
                'Name Correction' => 'emails.charge.auth.name-correction',
                'Pet Addition' => 'emails.charge.auth.pet-addition',
            ];

            $bodyView = $templateMap[$booking->service_type] ?? 'emails.charge.auth.new-booking';
            $emailContent = view($bodyView, compact('booking'))->render();

            $parts = preg_split(
                '/<h4[^>]*>\s*Purchase Summary\s*:?\s*<\/h4>/i',
                $emailContent
            );

            $mainContent = $parts[0] ?? $emailContent;
            $purchaseSummary = isset($parts[1]) ? '<h4>Purchase Summary:</h4>'.$parts[1] : '';
        }

        // Apply formatting
        $mainContent = $this->formatEmailContent($mainContent);
        $purchaseSummary = $this->formatEmailContent($purchaseSummary);

        $finalHtml = view('emails.customer-final-auth', [
            'booking' => $booking,
            'mainContent' => $mainContent,
            'purchaseSummary' => $purchaseSummary,
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

            return redirect()
                ->back()
                ->with('success', 'Acknowledgement mail re-sent successfully.');

        } catch (TransportExceptionInterface $e) {
            Log::error('Resend mail transport failed', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Resend failed: '.$e->getMessage());

        } catch (\Exception $e) {
            Log::error('Resend mail general error', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'error' => $e->getMessage(),
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

    private function formatEmailContent($html)
    {
        if (empty($html)) {
            return $html;
        }

        // Style tables - ensure they have proper width
        $html = preg_replace(
            '/<table(.*?)>/i',
            '<table $1 style="width:100%; border-collapse:collapse; border:1px solid #dcdcdc; margin:15px 0;">',
            $html
        );

        // Style TH
        $html = preg_replace(
            '/<th(.*?)>/i',
            '<th $1 style="border:1px solid #dcdcdc; padding:10px; background:#f8f9fa; font-weight:bold; text-align:left;">',
            $html
        );

        // Style TD
        $html = preg_replace(
            '/<td(.*?)>/i',
            '<td $1 style="border:1px solid #dcdcdc; padding:10px;">',
            $html
        );

        // Add responsive wrapper for tables
        $html = preg_replace(
            '/<table/i',
            '<div style="overflow-x:auto;"><table',
            $html
        );

        $html = preg_replace(
            '/<\/table>/i',
            '</table></div>',
            $html
        );

        // Headings
        $html = preg_replace(
            '/<h4(.*?)>/i',
            '<h4 $1 style="margin-top:25px; color:#1e3a8a;">',
            $html
        );

        // Fix any nested table issues
        $html = preg_replace(
            '/<div style="overflow-x:auto;"><div style="overflow-x:auto;">/i',
            '<div style="overflow-x:auto;">',
            $html
        );

        return $html;
    }
}