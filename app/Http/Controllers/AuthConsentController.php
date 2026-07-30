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
        // These actions are exposed under both the agent and charge route groups.
        // Use the route that was actually authorized by middleware instead of
        // Spatie's role relation. The application stores the login role in the
        // users.role column, and the two can be out of sync for individual users.
        if (request()->routeIs('agent.*')) {
            return [
                'view_prefix' => 'agent.auth',
                'route_prefix' => 'agent',
                'redirect_route' => 'agent.bookings.index',
            ];
        }

        // Default for charge team and admin
        return [
            'view_prefix' => 'charge.auth',
            'route_prefix' => 'charge',
            'redirect_route' => 'charge.dashboard',
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

        // Get booking language
        $language = $this->getBookingLanguage($booking);

        // Get the appropriate template view based on service type and language
    $bodyView = $this->getTemplateView($booking->service_type, $language, $booking);

        // Default email body content (can be edited in UI)
        $emailContent = view($bodyView, compact('booking'))->render();

        // Handle Purchase Summary split for Spanish as well
        // Spanish: "Resumen de Compra" or keep English "Purchase Summary"
        $parts = preg_split(
            '/<h4[^>]*>\s*(?:Purchase Summary|Resumen de Compra)\s*:?\s*<\/h4>/i',
            $emailContent
        );

        $mainContent = $parts[0] ?? $emailContent;
        $purchaseSummary = '';

        if (isset($parts[1])) {
            // Keep the original heading from the template
            $purchaseSummary = '<h4>Purchase Summary:</h4>'.$parts[1];
        }

        // Get role-based configuration
        $config = $this->getRoleBasedConfig();

        return view(
            $config['view_prefix'].'.edit',
            compact(
                'booking',
                'mainContent',
                'purchaseSummary',
                'language'
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

        return redirect()->route($config['route_prefix'].'.authorize.preview.page', $id);
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

        return view($config['view_prefix'].'.preview', [
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

        // Allow sending/resending auth mail regardless of booking status

        $mainContent = $request->input('main_content') ?? session('main_content_'.$id);
        $purchaseSummary = $request->input('purchase_summary') ?? session('purchase_summary_'.$id);

        if (! $mainContent) {
            return redirect()
                ->route($config['route_prefix'].'.authorize.edit', $id)
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
                ->route($config['route_prefix'].'.authorize.preview.page', $id)
                ->with('error', 'Mail sending failed: '.$e->getMessage());

        } catch (\Exception $e) {
            Log::error('General mail send error', [
                'booking_id' => $booking->id,
                'merchant_id' => $booking->agency_merchant_id,
                'customer_email' => $booking->customer_email,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route($config['route_prefix'].'.authorize.preview.page', $id)
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
            // Get booking language
            $language = $this->getBookingLanguage($booking);

            // Get the appropriate template view with language
            $bodyView = $this->getTemplateView($booking->service_type, $language, $booking);

            $emailContent = view($bodyView, compact('booking'))->render();

            $parts = preg_split(
                '/<h4[^>]*>\s*(?:Purchase Summary|Resumen de Compra)\s*:?\s*<\/h4>/i',
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

        // Return html directly to preserve rich inline CSS and prevent layout distortion
        return $html;
    }

    /**
     * Get the language for email templates based on booking language field
     */
    private function getBookingLanguage($booking)
    {
        if (empty($booking->language)) {
            return 'en'; // Default to English
        }

        // Extract language from format like "English-Flight" or "Spanish-Flight"
        $languageMap = [
            'english-flight' => 'en',
            'spanish-flight' => 'es',
            // Add more languages as needed
            'french-flight' => 'fr',
            'german-flight' => 'de',
        ];

        $languageKey = strtolower($booking->language);

        return $languageMap[$languageKey] ?? 'en';
    }

    /**
     * Get the template view path based on service type and language
     */
    /**
     * Get the template view path based on service type and language
     */
    private function getTemplateView($serviceType, $language = 'en', $booking = null)
    {
        $templateMap = [
            'New Booking' => 'new-booking',
            'Exchange' => 'exchange',
            'Cancellation' => 'cancellation',
            'Refund' => 'refund',
            'Seat selection' => 'seat-assignment',
            'Baggage edition' => 'baggage-edition',
            'Pet edition' => 'pet-edition',
            'Others' => 'others',
            'Cancel & Refund' => 'cancel-and-refund',
            'Change' => 'change',
            'Exchange & Upgrade' => 'exchange-upgrade',
            'Changes Confirmation' => 'changes-confirmation',
            'Name Correction' => 'name-correction',
            'Pet Addition' => 'pet-addition',
        ];

        $templateName = $templateMap[$serviceType] ?? 'new-booking';

        // Build the view path based on language
        $languagePaths = [
            'es' => "emails.spanish.auth.{$templateName}",
            'en' => "emails.charge.auth.{$templateName}",
        ];

        $viewPath = $languagePaths[$language] ?? "emails.charge.auth.{$templateName}";

        // Check if the view exists, fallback to English if not
        if (! view()->exists($viewPath)) {
            // Get booking language for logging
            $bookingLanguage = $booking ? ($booking->language ?? 'not set') : 'not provided';

            Log::warning("Template not found for language: {$language}, service: {$serviceType}. Falling back to English.", [
                'booking_language' => $bookingLanguage,
                'requested_view' => $viewPath,
            ]);

            $viewPath = "emails.charge.auth.{$templateName}";
        }

        return $viewPath;
    }

    /**
     * Get supported languages configuration
     */
    private function getSupportedLanguages()
    {
        return [
            'en' => [
                'name' => 'English',
                'booking_value' => 'English-Flight',
                'email_view_path' => 'emails.charge.auth',
                'purchase_summary_heading' => 'Purchase Summary',
            ],
            'es' => [
                'name' => 'Spanish',
                'booking_value' => 'Spanish-Flight',
                'email_view_path' => 'emails.spanish.auth',
                'purchase_summary_heading' => 'Resumen de Compra',
            ],
            // Add more languages as needed
        ];
    }

    /**
     * Check if a language is supported
     */
    private function isLanguageSupported($languageCode)
    {
        return array_key_exists($languageCode, $this->getSupportedLanguages());
    }
}
