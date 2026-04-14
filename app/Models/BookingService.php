<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Unified manager for all optional booking services.
 * Use individual models (BookingHotel, BookingCab, BookingInsurance)
 * for Eloquent queries, and this class as a service layer.
 */
class BookingService extends Model
{
    /**
     * Attach hotel details to a booking.
     */
    public static function attachHotel(int $bookingId, array $data): BookingHotel
    {
        // Mark hotel_required = true on the booking
        Booking::where('id', $bookingId)->update(['hotel_required' => true]);

        return BookingHotel::updateOrCreate(
            ['booking_id' => $bookingId],
            array_merge($data, ['booking_id' => $bookingId])
        );
    }

    /**
     * Attach cab details to a booking.
     */
    public static function attachCab(int $bookingId, array $data): BookingCab
    {
        Booking::where('id', $bookingId)->update(['cab_required' => true]);

        return BookingCab::updateOrCreate(
            ['booking_id' => $bookingId],
            array_merge($data, ['booking_id' => $bookingId])
        );
    }

    /**
     * Attach insurance details to a booking.
     */
    public static function attachInsurance(int $bookingId, array $data): BookingInsurance
    {
        Booking::where('id', $bookingId)->update(['insurance_required' => true]);

        return BookingInsurance::updateOrCreate(
            ['booking_id' => $bookingId],
            array_merge($data, ['booking_id' => $bookingId])
        );
    }

    /**
     * Detach / remove a service from a booking.
     */
    public static function detach(int $bookingId, string $service): void
    {
        match($service) {
            'hotel' => [
                BookingHotel::where('booking_id', $bookingId)->delete(),
                Booking::where('id', $bookingId)->update(['hotel_required' => false]),
            ],
            'cab' => [
                BookingCab::where('booking_id', $bookingId)->delete(),
                Booking::where('id', $bookingId)->update(['cab_required' => false]),
            ],
            'insurance' => [
                BookingInsurance::where('booking_id', $bookingId)->delete(),
                Booking::where('id', $bookingId)->update(['insurance_required' => false]),
            ],
            default => null,
        };
    }

    /**
     * Get all optional services summary for a booking.
     */
    public static function summary(int $bookingId): array
    {
        return [
            'hotel'     => BookingHotel::where('booking_id', $bookingId)->first(),
            'cab'       => BookingCab::where('booking_id', $bookingId)->first(),
            'insurance' => BookingInsurance::where('booking_id', $bookingId)->first(),
        ];
    }

    /**
     * Get total optional services cost for a booking.
     */
    public static function totalCost(int $bookingId): float
    {
        $hotel     = BookingHotel::where('booking_id', $bookingId)->value('hotel_cost') ?? 0;
        $cab       = BookingCab::where('booking_id', $bookingId)->value('cab_cost') ?? 0;
        $insurance = BookingInsurance::where('booking_id', $bookingId)->value('insurance_cost') ?? 0;

        return (float) ($hotel + $cab + $insurance);
    }
}
