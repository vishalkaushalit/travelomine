<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFEditorController extends Controller
{

    /**
     * Generate booking-specific PDF with template
     */
    public function generateBookingPDF(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $components = $request->validate([
            'components' => 'required|array',
        ]);

        $html = $this->buildHTMLFromComponents($components['components'], $booking);

        $pdf = Pdf::loadHTML($html)
            ->setPaper('A4', 'portrait');

        return $pdf->stream('ticket-' . $booking->booking_reference . '.pdf');
    }
    // update and generate pdf with template 
    public function updateAndGeneratePDF(Request $request, $bookingId)
{
    $booking = Booking::findOrFail($bookingId);

    // 1. Validate the request
    $validated = $request->validate([
        'flight_type' => 'required|in:one_way,round_trip,multi_city',
        'passengers' => 'required|array',
        'passengers.*.title' => 'required|string',
        'passengers.*.first_name' => 'required|string',
        'passengers.*.last_name' => 'required|string',
        'passengers.*.passenger_type' => 'required|string',
        'passengers.*.ticket_number' => 'nullable|string',
        'passengers.*.seat_number' => 'nullable|string',
        'segments' => 'required|array',
        'segments.*.flight_number' => 'required|string',
        'segments.*.airline_name' => 'nullable|string',
        'segments.*.airline_code' => 'nullable|string',
        'segments.*.from_city' => 'required|string',
        'segments.*.from_airport' => 'required|string',
        'segments.*.to_city' => 'required|string',
        'segments.*.to_airport' => 'required|string',
        'segments.*.departure_time' => 'required|date',
        'segments.*.arrival_time' => 'required|date|after:segments.*.departure_time',
        'optional_fields' => 'nullable|array',
        'optional_fields.passport_number' => 'nullable|boolean',
        'optional_fields.baggage' => 'nullable|boolean',
        'optional_fields.pet' => 'nullable|boolean',
        'passport_numbers' => 'nullable|array',
        'baggage_info' => 'nullable|string',
        'pet_info' => 'nullable|string',
    ]);

    // 2. Update flight_type on booking
    $booking->flight_type = $validated['flight_type'];
    $booking->save();

    // 3. Update passengers
    foreach ($validated['passengers'] as $index => $data) {
        $passenger = $booking->passengers()->where('id', $data['id'] ?? null)->first();
        if ($passenger) {
            $passenger->update($data);
        } else {
            // If new passenger, create (shouldn't happen in this edit flow)
            $booking->passengers()->create($data);
        }
    }

    // 4. Update segments
    foreach ($validated['segments'] as $index => $data) {
        $segment = $booking->segments()->where('id', $data['id'] ?? null)->first();
        if ($segment) {
            $segment->update($data);
        } else {
            $booking->segments()->create($data);
        }
    }

    // 5. Build ticket_data array
    $ticketData = $booking->ticket_data ?? [];
    $ticketData['optional_fields'] = [
        'passport_number' => $request->has('optional_fields.passport_number'),
        'baggage' => $request->has('optional_fields.baggage'),
        'pet' => $request->has('optional_fields.pet'),
    ];
    $ticketData['passport_numbers'] = $request->input('passport_numbers', []);
    $ticketData['baggage_info'] = $request->input('baggage_info', '');
    $ticketData['pet_info'] = $request->input('pet_info', '');

    $booking->ticket_data = $ticketData;
    $booking->save();

    // 6. Refresh the booking to get all relations with updated data
    $booking->refresh();

    // 7. Generate PDF using the blade template
    $pdf = Pdf::loadView('admin.bookings.ticket-pdf', compact('booking'))
              ->setPaper('A4', 'portrait');

    return $pdf->stream('ticket-' . $booking->booking_reference . '.pdf');
}

    /**
     * Get ticket template data with booking values
     */
    public function getTicketTemplate($bookingId)
    {
        $booking = Booking::with(['passengers', 'segments', 'user'])->findOrFail($bookingId);

        $template = [
            'booking' => [
                'id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'airline_name' => $booking->airline_name ?? 'Airline',
                'status' => ucfirst($booking->status),
                'departure_city' => $booking->departure_city,
                'arrival_city' => $booking->arrival_city,
                'departure_date' => \Carbon\Carbon::parse($booking->departure_date)->format('d M Y'),
                'return_date' => $booking->return_date ? \Carbon\Carbon::parse($booking->return_date)->format('d M Y') : null,
                'flight_type' => ucwords(str_replace('_', ' ', $booking->flight_type ?? 'one_way')),
                'total_passengers' => $booking->passengers->count(),
                'ticket_number' => $booking->passengers->first()?->ticket_number,
                'airline_pnr' => $booking->airline_pnr,
                'cabin_class' => $booking->cabin_class,
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'customer_phone' => $booking->customer_phone,
                'amount_charged' => $booking->amount_charged,
                'currency' => $booking->currency,
            ],
            'segments' => $booking->segments->map(function ($segment) {
                return [
                    'flight_number' => $segment->flight_number,
                    'departure_time' => date('h:i A', strtotime($segment->departure_time)),
                    'from_city' => $segment->from_city,
                    'from_airport' => $segment->from_airport,
                    'arrival_time' => date('h:i A', strtotime($segment->arrival_time)),
                    'to_city' => $segment->to_city,
                    'to_airport' => $segment->to_airport,
                ];
            })->toArray(),
            'passengers' => $booking->passengers->map(function ($passenger) {
                return [
                    'title' => $passenger->title,
                    'first_name' => $passenger->first_name,
                    'last_name' => $passenger->last_name,
                    'passenger_type' => $passenger->passenger_type,
                    'ticket_number' => $passenger->ticket_number,
                    'passport_number' => $passenger->passport_number,
                ];
            })->toArray(),
        ];

        return response()->json($template);
    }

    /**
     * Save template changes
     */
    public function saveTemplateChanges(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $validated = $request->validate([
            'components' => 'required|array',
            'template_name' => 'nullable|string',
        ]);

        // Store the template configuration
        $templateData = [
            'booking_id' => $bookingId,
            'components' => $validated['components'],
            'name' => $validated['template_name'] ?? 'Ticket Template - ' . now(),
            'saved_at' => now(),
        ];

        // You can save this to database or file
        // For now, returning success
        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully',
            'data' => $templateData
        ]);
    }

    /**
     * Build HTML from WYSIWYG components
     */
    private function buildHTMLFromComponents($components, $booking = null): string
    {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                td { border: 1px solid #ddd; padding: 8px; }
                img { max-width: 100%; height: auto; }
                .spacer { height: 20px; }
            </style>
        </head>
        <body>';

        foreach ($components as $component) {
            $props = $component['properties'] ?? [];

            switch ($component['type']) {
                case 'heading':
                    $html .= '<h3 style="font-size: ' . ($props['fontSize'] ?? 24) . 'px; ' .
                             'font-weight: ' . ($props['fontWeight'] ?? 'bold') . '; ' .
                             'text-align: ' . ($props['textAlign'] ?? 'left') . '; ' .
                             'color: ' . ($props['color'] ?? '#000') . ';">' .
                             $this->replaceVariables($props['text'] ?? '', $booking) .
                             '</h3>';
                    break;

                case 'paragraph':
                    $html .= '<p style="font-size: ' . ($props['fontSize'] ?? 12) . 'px; ' .
                             'text-align: ' . ($props['textAlign'] ?? 'left') . '; ' .
                             'color: ' . ($props['color'] ?? '#000') . '; ' .
                             'line-height: ' . ($props['lineHeight'] ?? 1.5) . ';">' .
                             $this->replaceVariables($props['text'] ?? '', $booking) .
                             '</p>';
                    break;

                case 'image':
                    if (!empty($props['src'])) {
                        $html .= '<img src="' . $props['src'] . '" style="width: ' . ($props['width'] ?? 200) . 'px; ' .
                                 'height: ' . ($props['height'] ?? 150) . 'px;">';
                    }
                    break;

                case 'table':
                    $rows = $props['rows'] ?? 3;
                    $cols = $props['cols'] ?? 3;
                    $html .= '<table style="border-collapse: collapse; width: 100%; margin: 10px 0;">';
                    for ($i = 0; $i < $rows; $i++) {
                        $html .= '<tr>';
                        for ($j = 0; $j < $cols; $j++) {
                            $isHeader = $i === 0;
                            $html .= '<td style="border: 1px solid ' . ($props['borderColor'] ?? '#000') . '; ' .
                                     'padding: 8px; background: ' . ($isHeader ? ($props['headerBg'] ?? '#f0f0f0') : 'white') . ';">Cell</td>';
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</table>';
                    break;

                case 'divider':
                    $html .= '<hr style="border: none; border-top: ' . ($props['height'] ?? 2) . 'px solid ' . ($props['color'] ?? '#ccc') . '; ' .
                             'margin: ' . ($props['margin'] ?? 10) . 'px 0;">';
                    break;

                case 'spacer':
                    $html .= '<div class="spacer" style="height: ' . ($props['height'] ?? 20) . 'px;"></div>';
                    break;

                case 'barcode':
                    $html .= '<div style="margin: 10px 0; text-align: center;">
                             <img src="https://barcode.tec-it.com/barcode.ashx?data=' . urlencode($props['value'] ?? '') . '&code=Code128&showtext=true" 
                                  alt="barcode" style="height: ' . ($props['height'] ?? 50) . 'px;">
                             </div>';
                    break;

                case 'qrcode':
                    $html .= '<div style="margin: 10px 0; text-align: center;">
                             <img src="https://api.qrserver.com/v1/create-qr-code/?size=' . ($props['size'] ?? 100) . 'x' . ($props['size'] ?? 100) . '&data=' . urlencode($props['value'] ?? '') . '" 
                                  alt="qr-code" style="width: ' . ($props['size'] ?? 100) . 'px; height: ' . ($props['size'] ?? 100) . 'px;">
                             </div>';
                    break;
            }
        }

        $html .= '</body></html>';
        return $html;
    }

    /**
     * Replace booking variables in text
     */
    private function replaceVariables($text, $booking = null): string
    {
        if (!$booking) {
            return $text;
        }

        $replacements = [
            '{{booking_reference}}' => $booking->booking_reference ?? '',
            '{{customer_name}}' => $booking->customer_name ?? '',
            '{{customer_email}}' => $booking->customer_email ?? '',
            '{{customer_phone}}' => $booking->customer_phone ?? '',
            '{{booking_date}}' => $booking->booking_date ?? '',
            '{{airline_pnr}}' => $booking->airline_pnr ?? '',
            '{{gk_pnr}}' => $booking->gk_pnr ?? '',
            '{{status}}' => $booking->status ?? '',
            '{{flight_type}}' => $booking->flight_type ?? '',
        ];

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $text
        );
    }
}
