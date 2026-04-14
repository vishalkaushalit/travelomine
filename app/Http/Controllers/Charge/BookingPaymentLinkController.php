<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Mail\PaymentLinkMail;
use App\Models\Booking;
use App\Models\BookingPaymentLink;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class BookingPaymentLinkController extends Controller
{
    /**
     * Show the "Charge Now" form for a booking
     * Route: GET /charge/bookings/{booking}/payment-link/create
     */
    public function create(Booking $booking)
    {
        $invalidStatuses = ['confirmed', 'ticketed', 'failed', 'cancelled'];
        // there are muttiple status
        if (in_array($booking->status, $invalidStatuses)) {
            return redirect()
                ->route('charge.bookings.show', $booking->id)
                ->with('error', 'This booking is already processed and cannot be charged again.');
        }

        $alreadyPaid = \App\Models\BookingPaymentLink::where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->exists();

        if ($alreadyPaid) {
            return redirect()
                ->route('charge.bookings.show', $booking->id)
                ->with('error', 'A payment has already been completed for this booking.');
        }

        $defaultAmount = $booking->total_mco;

        return view('charge.payment-links.create', [
            'booking' => $booking,
            'defaultAmount' => $defaultAmount,
        ]);
    }

    /**
     * Store payment link for a booking
     * Route: POST /charge/bookings/{booking}/payment-link
     */
    public function store(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        // ✅ Use assignment's merchant_id (agency_merchant_name is null on bookings)
        $assignment = \App\Models\ChargeAssignment::where('booking_id', $booking->id)
            ->latest()
            ->first();

        $merchant = $assignment ? Merchant::find($assignment->merchant_id) : null;

        if (! $merchant) {
            return back()->with('error', 'No merchant found for this booking. Please contact admin.');
        }

        $link = BookingPaymentLink::create([
            'booking_id' => $booking->id,
            'merchant_id' => $merchant->id,
            'customer_name' => $booking->customer_name,
            'billing_email' => $booking->customer_email,
            'billing_phone' => $booking->billing_phone,
            'billing_address' => $booking->billing_address,
            'amount' => $data['amount'],
            'currency' => 'USD',
            'status' => 'pending',
            'expires_at' => now()->addDays(3),
            'created_by' => Auth::id(),
            'notes' => $data['notes'] ?? null,
        ]);

        $paymentUrl = route('public.pay.show', $link->token);

        return view('charge.payment-links.created', [
            'booking' => $booking,
            'link' => $link,
            'merchant' => $merchant,
            'paymentUrl' => $paymentUrl,
        ]);
    }

    // send email logic
    public function sendMail(Booking $booking, $linkId)
    {
        $link = BookingPaymentLink::findOrFail($linkId);

        // Make sure the link has a billing email
        if (! $link->billing_email) {
            return back()->with('error', 'No email address found for this payment link.');
        }

        // Make sure link is still pending
        if ($link->isPaid()) {
            return back()->with('error', 'This link has already been paid.');
        }

        if ($link->isExpired()) {
            return back()->with('error', 'This link has expired. Please create a new one.');
        }

        // Generate the public URL
        $paymentUrl = route('public.pay.show', $link->token);

        // Send the email (Requires you to have created App\Mail\PaymentLinkMail)
        Mail::to($link->billing_email)->send(new PaymentLinkMail($link, $paymentUrl));

        return back()->with('mail_sent', 'Payment link sent to '.$link->billing_email.' successfully!');
    }
}
