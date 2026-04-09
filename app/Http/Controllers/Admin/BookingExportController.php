<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Response;

class BookingExportController extends Controller
{
    public function exportSingle(Booking $booking)
    {
        $booking->load([
            'user',
            'agent',
            'passengers',
            'cards',
            'segments',
            'agencyMerchant',
        ]);

        $firstPassenger = $booking->passengers->first();
        $firstCard = $booking->cards->sortBy('card_order')->first();
        $firstSegment = $booking->segments->first();

        $airlines = $booking->segments
            ->pluck('airline_code')
            ->filter()
            ->unique()
            ->implode(', ');

        $sectors = $booking->segments
            ->map(function ($segment) {
                $from = $segment->from_airport ?? $segment->from_city ?? '';
                $to = $segment->to_airport ?? $segment->to_city ?? '';
                return trim($from . ' - ' . $to, ' -');
            })
            ->filter()
            ->unique()
            ->implode(', ');

        $travelDates = $booking->segments
            ->pluck('departure_date')
            ->filter()
            ->map(fn ($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
            ->unique()
            ->implode(', ');

        $row = [
            'Timestamp' => optional($booking->created_at)->format('Y-m-d H:i:s'),
            'Date' => optional($booking->booking_date)->format('Y-m-d'),
            'Booking Reference' => $booking->booking_reference,
            'Agent Name' => optional($booking->user)->name ?? optional($booking->agent)->name,
            'Call Type' => $booking->call_type,
            'Manager' => $booking->manager ?? 'Duke', 
            'Airline' => $airlines,
            'Sector' => $sectors,
            'GK PNR' => $booking->gk_pnr ?: optional($firstSegment)->gk_pnr,
            'Airline PNR' => $booking->airline_pnr ?: optional($firstSegment)->airline_pnr,
            'Travel Date' => $travelDates,
            'Verticals' => $booking->service_provided . ', ', 
            'Service Provided' => $booking->service_type,
            'Booking Portal' => $booking->booking_portal,
            'Card Holder Name' => optional($firstCard)->card_holder_name,
            'Any Passenger Name' => $firstPassenger
                ? trim(($firstPassenger->first_name ?? '') . ' ' . ($firstPassenger->last_name ?? ''))
                : '',
            'Card Last 4 digit' => optional($firstCard)->card_last_four ?? $booking->card_last_four,
            'Calling Number' => $booking->customer_phone,
            'Billing Phone Number' => $booking->billing_phone,
            'Email Address' => $booking->customer_email,
            'Booking Status' => $booking->status,
            'Email - Auth Taken' => $booking->email_auth_taken ? 'Yes' : 'No',
            'Merchant' => $booking->agency_merchant_name ?? 'na',
            // 'Currency' => $booking->currency,
            'Currency' => $booking->agency_merchant_name,
            'Total Quoted' => $booking->amount_charged,
            'Amount Charged' => $booking->total_mco,
            'Amount paid to airline' => $booking->amount_paid_airline,
            'Total MCO' => $booking->total_mco,
            'Language' => $booking->language,
            'Company card/VAN (if used with amount)' => '',
            'Agent remarks if any' => $booking->agent_remarks,
            'Cabin' => $booking->cabin_class,
            'Email ID Used for Airline Conf (If any)' => '',
            'Return Date' => optional($booking->return_date)->format('Y-m-d'),
            'Trip Details' => $booking->flight_type,
            'Campaign' => '',
            'Publisher' => '',
            'Target' => '',
            'Remarks By MIS' => $booking->mis_remarks,
            'Merchant Remarks by MIS' => '',
            'Transaction Status' => '',
            'Ticket Status' => '',
            'Merchant Match' => '',
            'Fare Type (NFXR / FXR)' => '',
            'Amadeus Pseudo' => '',
            'Issuance Fee ($10/PP)' => '',
            'Refund // Void Amount' => '',
            'MCO Match' => '',
            'Charging E-Ticket' => '',
            'Updated in Company Kitty' => '',
            'Amount Charged in CAD//AUD' => '',
        ];

        $filename = 'booking-' . ($booking->booking_reference ?? $booking->id) . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($row) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, array_keys($row));
            fputcsv($handle, array_values($row));

            fclose($handle);
        }, 200, $headers);
    }
}