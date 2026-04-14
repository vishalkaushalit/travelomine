<?php

namespace App\Http\Controllers\Agent\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PassengerController extends Controller
{
    /**
     * Validate passenger payload from booking form.
     *
     * Expected structure:
     * passengers => [
     *   [
     *     passenger_type, title, first_name, middle_name, last_name,
     *     gender, dob, passport_number, passport_expiry, nationality,
     *     seat_preference, meal_preference, special_assistance
     *   ]
     * ]
     */
    public function validatePassengers(Request $request): array
    {
        $validated = $request->validate([
            'adults' => 'required|integer|min:1|max:9',
            'children' => 'required|integer|min:0|max:9',
            'infants' => 'required|integer|min:0|max:9',

            'passengers' => 'required|array|min:1|max:9',
            'passengers.*.passenger_type' => 'required|string|in:ADT,CHD,INF,INL',
            'passengers.*.title' => 'required|string|in:Mr,Mrs,Ms,Miss,Dr,Master',
            'passengers.*.first_name' => 'required|string|max:100',
            'passengers.*.middle_name' => 'nullable|string|max:100',
            'passengers.*.last_name' => 'required|string|max:100',
            'passengers.*.gender' => 'required|string|in:male,female,other',
            'passengers.*.dob' => 'required|date',
            'passengers.*.passport_number' => 'nullable|string|max:50',
            'passengers.*.passport_expiry' => 'nullable|date',
            'passengers.*.nationality' => 'nullable|string|max:100',
            'passengers.*.seat_preference' => 'nullable|string|max:100',
            'passengers.*.meal_preference' => 'nullable|string|max:100',
            'passengers.*.special_assistance' => 'nullable|string|max:255',
        ]);

        $this->applyPassengerRules($validated);

        return $validated;
    }

    /**
     * Business rules:
     * - Total passengers cannot be more than 9
     * - At least 1 adult is required
     * - Infant count cannot exceed adult count
     * - Passenger rows count should match adults + children + infants
     */
    protected function applyPassengerRules(array $validated): void
    {
        $adults = (int) ($validated['adults'] ?? 0);
        $children = (int) ($validated['children'] ?? 0);
        $infants = (int) ($validated['infants'] ?? 0);
        $passengers = $validated['passengers'] ?? [];

        $total = $adults + $children + $infants;

        if ($adults < 1) {
            throw ValidationException::withMessages([
                'adults' => 'At least 1 adult is required for booking.',
            ]);
        }

        if ($total > 9) {
            throw ValidationException::withMessages([
                'passengers' => 'Total passengers cannot be more than 9.',
            ]);
        }

        if ($infants > $adults) {
            throw ValidationException::withMessages([
                'infants' => 'Number of infants cannot exceed number of adults.',
            ]);
        }

        if (count($passengers) !== $total) {
            throw ValidationException::withMessages([
                'passengers' => 'Passenger details count does not match total passenger count.',
            ]);
        }

        $typeCounts = [
            'ADT' => 0,
            'CHD' => 0,
            'INF' => 0,
            'INL' => 0,
        ];

        foreach ($passengers as $row) {
            $type = $row['passenger_type'] ?? null;
            if ($type && isset($typeCounts[$type])) {
                $typeCounts[$type]++;
            }
        }

        $actualAdultCount = $typeCounts['ADT'];
        $actualChildCount = $typeCounts['CHD'];
        $actualInfantCount = $typeCounts['INF'] + $typeCounts['INL'];

        if ($actualAdultCount !== $adults) {
            throw ValidationException::withMessages([
                'passengers' => 'Adult passenger rows do not match adults count.',
            ]);
        }

        if ($actualChildCount !== $children) {
            throw ValidationException::withMessages([
                'passengers' => 'Child passenger rows do not match children count.',
            ]);
        }

        if ($actualInfantCount !== $infants) {
            throw ValidationException::withMessages([
                'passengers' => 'Infant passenger rows do not match infants count.',
            ]);
        }
    }

    /**
     * Store validated passengers against a booking.
     */
    public function storeForBooking(Booking $booking, array $passengers): void
    {
        foreach ($passengers as $index => $passenger) {
            $booking->passengers()->create([
                'passenger_type' => $passenger['passenger_type'],
                'title' => $passenger['title'],
                'first_name' => $passenger['first_name'],
                'middle_name' => $passenger['middle_name'] ?? null,
                'last_name' => $passenger['last_name'],
                'gender' => $passenger['gender'],
                'dob' => $passenger['dob'],
                'passport_number' => $passenger['passport_number'] ?? null,
                'passport_expiry' => $passenger['passport_expiry'] ?? null,
                'nationality' => $passenger['nationality'] ?? null,
                'seat_preference' => $passenger['seat_preference'] ?? null,
                'meal_preference' => $passenger['meal_preference'] ?? null,
                'special_assistance' => $passenger['special_assistance'] ?? null,
            ]);
        }
    }



}
