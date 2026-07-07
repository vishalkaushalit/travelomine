<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AllBookingImportController extends Controller
{
    /**
     * Export selected bookings (checkbox selected)
     */
    public function exportSelected(Request $request)
    {
        // Get the selected bookings from the request
        $selectedIds = $request->input('selected_bookings', []);
        
        // If it's a JSON string, decode it
        if (is_string($selectedIds)) {
            $selectedIds = json_decode($selectedIds, true);
        }
        
        // If it's still not an array or empty, return error
        if (!is_array($selectedIds) || empty($selectedIds)) {
            return back()->with('error', 'Please select at least one booking.');
        }

        // Convert all IDs to integers and filter out invalid ones
        $selectedIds = array_filter(array_map('intval', $selectedIds), function($id) {
            return $id > 0;
        });

        if (empty($selectedIds)) {
            return back()->with('error', 'Invalid booking IDs selected.');
        }

        $bookings = Booking::with([
            'user',
            'agent',
            'passengers',
            'cards',
            'segments',
            'agencyMerchant',
        ])
            ->whereIn('id', $selectedIds)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', 'No bookings found for export.');
        }

        return $this->generateCsvExport($bookings, 'selected-bookings');
    }
    /**
     * Export all bookings (filtered or all)
     */
    public function export(Request $request)
    {
        // Build query with filters
        $query = $this->buildFilteredQuery($request);

        $bookings = $query->with([
            'user',
            'agent',
            'passengers',
            'cards',
            'segments',
            'agencyMerchant',
        ])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', 'No bookings found matching the filters.');
        }

        return $this->generateCsvExport($bookings, 'all-bookings');
    }


    /**
     * Build the filtered query based on request parameters
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = Booking::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('agent_custom_id', 'like', "%{$search}%")
                  ->orWhere('booking_reference', 'like', "%{$search}%")
                  ->orWhere('airline_pnr', 'like', "%{$search}%")
                  ->orWhere('gk_pnr', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('booking_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('booking_date', '<=', $request->to_date);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Service filter
        if ($request->filled('service')) {
            $query->where('service_provided', $request->service);
        }

        // Agent filter
        if ($request->filled('agent_id')) {
            $query->where('user_id', $request->agent_id);
        }

        return $query;
    }

    /**
     * Generate CSV export for given bookings
     */
    protected function generateCsvExport($bookings, $filenamePrefix)
    {
        $filename = $filenamePrefix . '-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($bookings) {
            $handle = fopen('php://output', 'w');

            // Define CSV headers
            $csvHeaders = [
                'Timestamp',
                'Date',
                'Booking Reference',
                'Agent Name',
                'Call Type',
                'Manager',
                'Airline',
                'Sector',
                'GK PNR',
                'Airline PNR',
                'Travel Date',
                'Verticals',
                'Service Provided',
                'Booking Portal',
                'Card Holder Name',
                'Any Passenger Name',
                'Card Last 4 digit',
                'Calling Number',
                'Billing Phone Number',
                'Email Address',
                'Booking Status',
                'Email - Auth Taken',
                'Merchant',
                'Currency',
                'Total Quoted',
                'Amount Charged',
                'Amount paid to airline',
                'Total MCO',
                'Language',
                'Company card/VAN (if used with amount)',
                'Agent remarks if any',
                'Cabin',
                'Email ID Used for Airline Conf (If any)',
                'Return Date',
                'Trip Details',
                'Campaign',
                'Publisher',
                'Target',
                'Remarks By MIS',
                'Merchant Remarks by MIS',
                'Transaction Status',
                'Ticket Status',
                'Merchant Match',
                'Fare Type (NFXR / FXR)',
                'Amadeus Pseudo',
                'Issuance Fee ($10/PP)',
                'Refund // Void Amount',
                'MCO Match',
                'Charging E-Ticket',
                'Updated in Company Kitty',
                'Amount Charged in CAD//AUD',
            ];

            // Write headers
            fputcsv($handle, $csvHeaders);

            // Process each booking
            foreach ($bookings as $booking) {
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
                    ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
                    ->unique()
                    ->implode(', ');

                $row = [
                    optional($booking->created_at)->format('Y-m-d H:i:s'),
                    optional($booking->booking_date)->format('Y-m-d'),
                    $booking->booking_reference,
                    optional($booking->user)->name ?? optional($booking->agent)->name,
                    $booking->call_type,
                    $booking->manager ?? 'Duke',
                    $airlines,
                    $sectors,
                    $booking->gk_pnr ?: optional($firstSegment)->gk_pnr,
                    $booking->airline_pnr ?: optional($firstSegment)->airline_pnr,
                    $travelDates,
                    $booking->service_provided . ', ',
                    $booking->service_type,
                    $booking->booking_portal,
                    optional($firstCard)->card_holder_name,
                    $firstPassenger
                        ? trim(($firstPassenger->first_name ?? '') . ' ' . ($firstPassenger->last_name ?? ''))
                        : '',
                    optional($firstCard)->card_last_four ?? $booking->card_last_four,
                    $booking->customer_phone,
                    $booking->billing_phone,
                    $booking->customer_email,
                    $booking->status,
                    $booking->email_auth_taken ? 'Yes' : 'No',
                    $booking->agency_merchant_name ?? 'na',
                    $booking->agency_merchant_name,
                    $booking->amount_charged,
                    $booking->total_mco,
                    $booking->amount_paid_airline,
                    $booking->total_mco,
                    $booking->language,
                    '',
                    $booking->agent_remarks,
                    $booking->cabin_class,
                    '',
                    optional($booking->return_date)->format('Y-m-d'),
                    $booking->flight_type,
                    '',
                    '',
                    '',
                    $booking->mis_remarks,
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, $headers);
    }

}
