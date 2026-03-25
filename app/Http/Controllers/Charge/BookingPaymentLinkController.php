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
        // Only amount + notes are coming from the form
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        // Find merchant based on booking's agency_merchant_name
        // Adjust column names if different in your merchants table
        $merchant = Merchant::where('name', $booking->agency_merchant_name)->first();

        if (! $merchant) {
            return back()->with('error', 'Merchant "'.$booking->agency_merchant_name.'" not found. Please configure it in merchants table.');
        }

        // Create the payment link record using data from booking
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

        // Public URL customer will use to pay
        $paymentUrl = route('public.pay.show', $link->token);

        // Show the "link created" page
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