<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PaymentStatus;
use App\Models\PaymentStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentStatusService
{
    /**
     * Update payment status for a booking
     */
    public function updateStatus(Booking $booking, $statusSlugOrId, $userId, $role, $remarks = null, $metadata = [])
    {
        DB::beginTransaction();

        try {
            // Get payment status
            $status = $this->getPaymentStatus($statusSlugOrId);
            
            if (!$status) {
                throw ValidationException::withMessages([
                    'payment_status' => 'Invalid payment status provided.'
                ]);
            }

            // Check if transition is allowed
            $oldStatus = $booking->paymentStatus;
            if ($oldStatus && !PaymentStatus::canTransition($oldStatus->slug, $status->slug)) {
                throw ValidationException::withMessages([
                    'payment_status' => "Cannot transition from '{$oldStatus->name}' to '{$status->name}'."
                ]);
            }

            // Update booking
            $booking->updatePaymentStatus(
                $status->id,
                $userId,
                $role,
                $remarks,
                $metadata
            );

            // If status is captured, update card payment statuses
            if ($status->slug === 'captured') {
                $this->updateCardPaymentStatuses($booking, $userId);
            }

            // Log activity
            $this->logActivity($booking, $oldStatus, $status, $userId, $role);

            DB::commit();

            return $booking;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get payment status by slug or ID
     */
    private function getPaymentStatus($slugOrId)
    {
        if (is_numeric($slugOrId)) {
            return PaymentStatus::find($slugOrId);
        }
        return PaymentStatus::getStatusBySlug($slugOrId);
    }

    /**
     * Update card payment statuses when booking payment is captured
     */
    private function updateCardPaymentStatuses(Booking $booking, $userId)
    {
        foreach ($booking->cards as $card) {
            $card->update([
                'payment_status' => 'captured',
                'payment_processed_at' => now(),
            ]);
        }
    }

    /**
     * Log activity for payment status change
     */
    private function logActivity($booking, $oldStatus, $newStatus, $userId, $role)
    {
        $oldStatusName = $oldStatus ? $oldStatus->name : 'None';
        $newStatusName = $newStatus ? $newStatus->name : 'None';

        ActivityLogger::log(
            'payment_status',
            'update',
            "Payment status changed from '{$oldStatusName}' to '{$newStatusName}' for booking #{$booking->booking_reference} by {$role}",
            Booking::class,
            $booking->id,
            [
                'old_status' => $oldStatusName,
                'new_status' => $newStatusName,
                'changed_by_role' => $role
            ]
        );
    }

    /**
     * Get payment status history for a booking
     */
    public function getHistory(Booking $booking)
    {
        return $booking->paymentStatusHistories()
            ->with(['paymentStatus', 'changedBy'])
            ->get();
    }

    /**
     * Get available payment statuses for a booking
     */
    public function getAvailableStatuses(Booking $booking)
    {
        $currentStatus = $booking->paymentStatus;
        
        if (!$currentStatus) {
            return PaymentStatus::active()->ordered()->get();
        }

        // Get all statuses
        $allStatuses = PaymentStatus::active()->ordered()->get();
        
        // Filter based on allowed transitions
        $available = $allStatuses->filter(function ($status) use ($currentStatus) {
            return PaymentStatus::canTransition($currentStatus->slug, $status->slug);
        });

        // Include current status
        $available->push($currentStatus);

        return $available->unique('id')->sortBy('order');
    }

    /**
     * Get status by slug with fallback
     */
    public function getStatusBySlug($slug)
    {
        return PaymentStatus::getStatusBySlug($slug) ?? PaymentStatus::getDefaultStatus();
    }
}