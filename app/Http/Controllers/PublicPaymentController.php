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
        $link = BookingPaymentLink::with(['booking', 'merchant'])->where('token', $token)->firstOrFail();

        if ($link->isPaid()) {
            return view('public-payments.already-paid', compact('link'));
        }

        if ($link->isExpired()) {
            return view('public-payments.expired', compact('link'));
        }

        $booking = $link->booking;

        // Here you can show:
        // - Booking personal details
        // - Passenger details
        // - Flight details
        // - Amount breakup (from bookings or related tables)

        return view('public-payments.pay', compact('link', 'booking'));
    }

    /**
     * Customer submits card and pays
     * Route: POST /pay/{token}
     */
    public function process(string $token, Request $request, NmiService $nmi)
    {
        $link = BookingPaymentLink::with(['booking', 'merchant'])->where('token', $token)->firstOrFail();

        if ($link->isPaid()) {
            return back()->with('error', 'This payment has already been completed.');
        }

        if ($link->isExpired()) {
            return back()->with('error', 'This payment link has expired.');
        }

        $validated = $request->validate([
            'ccnumber' => 'required|string',
            'ccexp' => 'required|string|size:4',
            'cvv' => 'required|string|min:3|max:4',
        ]);

        $booking = $link->booking;

        $cardData = array_merge($validated, [
            'amount' => $link->amount,
            'first_name' => $booking->customername,      // adjust if you have separate first/last
            'last_name' => '',
            'email' => $link->billing_email,
            'address1' => $link->billing_address,
            'country' => null,                        // if you have it, pass it
            'order_id' => 'BOOKING-'.$booking->id.'-LINK-'.$link->id,
        ]);

        // Use selected merchant from link
        if ($link->merchant) {
            $nmi->useMerchant($link->merchant);
        }

        $response = $nmi->sale($cardData);

        // Log transaction with booking + link
        $txn = $nmi->logTransaction(
            array_merge($cardData, ['booking_id' => $booking->id]),
            $response,
            $link->id
        );

        if (isset($response['response']) && $response['response'] == 1) {
            $link->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Mark booking as confirmed / paid
            $booking->update([
                'status' => 'confirmed', // adjust to your actual booking status column
            ]);

            return redirect()->route('public.pay.success', $link->token)
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
        $link = BookingPaymentLink::with('booking')->where('token', $token)->firstOrFail();

        return view('public-payments.success', compact('link'));
    }
}
