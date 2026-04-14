<?php

namespace App\Http\Controllers;

use App\Models\BookingPaymentLink;
use App\Services\NmiService;
use Illuminate\Http\Request;

class PublicPaymentController extends Controller
{
    /**
     * Customer views the payment page
     * Route: GET /pay/{token}
     */
    public function show(string $token)
{
    $link = BookingPaymentLink::with(['booking.agencyMerchant', 'merchant'])
    ->where('token', $token)
    ->firstOrFail();

    if ($link->isPaid()) {
        return view('public-payments.already-paid', compact('link'));
    }

    if ($link->isExpired()) {
        return view('public-payments.expired', compact('link'));
    }

    $booking = $link->booking;

    return view('public-payments.pay', compact('link', 'booking'));
}

    /**
     * Customer submits payment token and pays
     * Route: POST /pay/{token}
     */
    public function process(string $token, Request $request, NmiService $nmi)
    {
        $link = BookingPaymentLink::with(['booking.agencyMerchant', 'merchant'])
            ->where('token', $token)
            ->firstOrFail();

        if ($link->isPaid()) {
            return back()->with('error', 'This payment has already been completed.');
        }

        if ($link->isExpired()) {
            return back()->with('error', 'This payment link has expired.');
        }

        $validated = $request->validate([
            'payment_token' => ['required', 'string'],
        ]);

        $booking = $link->booking;
        $merchant = $booking->agencyMerchant ?? $link->merchant ?? null;        if (! $merchant) {
            return back()->with('error', 'Merchant configuration not found for this payment link.');
        }

        if (empty($merchant->security_key)) {
            return back()->with('error', 'Merchant security key is missing.');
        }

        if (empty($merchant->api_url)) {
            return back()->with('error', 'Merchant API URL is missing.');
        }

        if (empty($merchant->tokenization_key)) {
            return back()->with('error', 'Merchant tokenization key is missing.');
        }

        $paymentData = [
            'amount' => $link->amount,
            'payment_token' => $validated['payment_token'],
            'first_name' => $booking->customername ?? $link->customer_name ?? '',
            'last_name' => '',
            'email' => $link->billing_email,
            'address1' => $link->billing_address,
            'country' => null,
            'order_id' => 'BOOKING-'.$booking->id.'-LINK-'.$link->id,
        ];

        $nmi->useMerchant($merchant);

        $response = $nmi->saleWithToken($paymentData);

        $nmi->logTransactionFromLink(
            $paymentData,
            $response,
            $link->id
        );

        if (isset($response['response']) && (string) $response['response'] === '1') {
            $link->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $booking->update([
                'status' => 'confirmed',
            ]);

            return redirect()
                ->route('public.pay.success', $link->token)
                ->with('success', 'Payment successful!');
        }

        $message = $response['responsetext'] ?? 'Payment failed. Please try again.';

        return back()->with('error', $message);
    }

    /**
     * Success page
     */
    public function success(string $token)
    {
        $link = BookingPaymentLink::with('booking')
            ->where('token', $token)
            ->firstOrFail();

        return view('public-payments.success', compact('link'));
    }
}
