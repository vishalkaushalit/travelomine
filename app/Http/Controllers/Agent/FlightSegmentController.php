<?php

namespace App\Http\Controllers\Agent\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FlightSegmentController extends Controller
{
    /**
     * Validate incoming flight data from booking form.
     *
     * Expected request keys:
     * - flight_type
     * - segments[]
     */
    public function validateSegments(Request $request): array
    {
        $validated = $request->validate([
            'flight_type' => 'nullable|in:oneway,roundtrip,multicity',

            'segments' => 'required_if:service_provided,Flight|array|min:1',
            'segments.*.from_city' => 'required|string|max:100',
            'segments.*.to_city' => 'required|string|max:100',
            'segments.*.departure_date' => 'required|date',
            'segments.*.return_date' => 'nullable|date',
            'segments.*.airline_name' => 'nullable|string|max:100',
            'segments.*.flight_number' => 'nullable|string|max:50',
            'segments.*.segment_pnr' => 'nullable|string|max:50',
            'segments.*.cabin_class' => 'nullable|string|max:50',
        ]);

        $this->applySegmentRules($validated);

        return $validated;
    }

    /**
     * Business rules:
     * - oneway: exactly 1 segment, no return_date required
     * - roundtrip: at least 1 segment, first return_date required
     * - multicity: 2 to 10 segments allowed
     */
    protected function applySegmentRules(array $validated): void
    {
        $flightType = $validated['flight_type'] ?? null;
        $segments = $validated['segments'] ?? [];
        $count = count($segments);

        if (!$flightType && $count > 0) {
            throw ValidationException::withMessages([
                'flight_type' => 'Please select a flight type.',
            ]);
        }

        if ($flightType === 'oneway') {
            if ($count !== 1) {
                throw ValidationException::withMessages([
                    'segments' => 'One way booking must contain exactly 1 flight segment.',
                ]);
            }
        }

        if ($flightType === 'roundtrip') {
            if ($count < 1) {
                throw ValidationException::withMessages([
                    'segments' => 'Round trip booking requires at least 1 segment.',
                ]);
            }

            if (empty($segments[0]['return_date'])) {
                throw ValidationException::withMessages([
                    'segments.0.return_date' => 'Return date is required for round trip booking.',
                ]);
            }
        }

        if ($flightType === 'multicity') {
            if ($count < 2) {
                throw ValidationException::withMessages([
                    'segments' => 'Multi city booking requires at least 2 flight segments.',
                ]);
            }

            if ($count > 10) {
                throw ValidationException::withMessages([
                    'segments' => 'Maximum 10 flight segments are allowed for multi city booking.',
                ]);
            }
        }

        foreach ($segments as $index => $segment) {
            if (
                !empty($segment['departure_date']) &&
                !empty($segment['return_date']) &&
                $segment['return_date'] < $segment['departure_date']
            ) {
                throw ValidationException::withMessages([
                    "segments.$index.return_date" => 'Return date cannot be earlier than departure date.',
                ]);
            }
        }
    }

    /**
     * Return booking summary fields from first flight segment.
     * These can be merged into Booking::create([...]).
     */
    public function getBookingFlightSummary(array $segments, ?string $flightType = null): array
    {
        $firstSegment = $segments[0] ?? null;

        if (!$firstSegment) {
            return [
                'flight_type' => $flightType,
                'departure_city' => null,
                'arrival_city' => null,
                'departure_date' => null,
                'return_date' => null,
                'airline_name' => null,
                'flight_number' => null,
                'cabin_class' => null,
            ];
        }

        return [
            'flight_type' => $flightType,
            'departure_city' => $firstSegment['from_city'] ?? null,
            'arrival_city' => $firstSegment['to_city'] ?? null,
            'departure_date' => $firstSegment['departure_date'] ?? null,
            'return_date' => $flightType === 'roundtrip'
                ? ($firstSegment['return_date'] ?? null)
                : null,
            'airline_name' => $firstSegment['airline_name'] ?? null,
            'flight_number' => $firstSegment['flight_number'] ?? null,
            'cabin_class' => $firstSegment['cabin_class'] ?? null,
        ];
    }

    /**
     * Store all segments in flight_segments table.
     *
     * Mapping:
     * - from_airport = from_city
     * - to_airport   = to_city
     * - pnr          = segment_pnr
     * - airline_code = airline_name (temporary mapping, until separate code field exists)
     */
    public function storeForBooking(Booking $booking, array $segments): void
    {
        foreach ($segments as $segment) {
            $booking->flightSegments()->create([
                'from_city' => $segment['from_city'] ?? null,
                'to_city' => $segment['to_city'] ?? null,

                'from_airport' => $segment['from_city'],
                'to_airport' => $segment['to_city'],

                'departure_date' => $segment['departure_date'],
                'return_date' => $segment['return_date'] ?? null,

                'airline_name' => $segment['airline_name'] ?? null,
                'flight_number' => $segment['flight_number'] ?? null,
                'segment_pnr' => $segment['segment_pnr'] ?? null,
                'cabin_class' => $segment['cabin_class'] ?? null,

                'pnr' => $segment['segment_pnr'] ?? null,
                'airline_code' => $segment['airline_name'] ?? '',
                'cabin_type' => $segment['cabin_class'] ?? null,
            ]);
        }
    }
}
