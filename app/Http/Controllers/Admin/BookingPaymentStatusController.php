<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PaymentStatus;
use App\Services\PaymentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingPaymentStatusController extends Controller
{
    protected $paymentStatusService;

    public function __construct(PaymentStatusService $paymentStatusService)
    {
        $this->paymentStatusService = $paymentStatusService;
    }

    /**
     * Show payment status management page
     */
    public function index(Booking $booking)
    {
        $this->authorize('manage-payment-status', $booking);
        
        $booking->load(['paymentStatus', 'paymentStatusHistories.changedBy', 'cards']);
        $availableStatuses = $this->paymentStatusService->getAvailableStatuses($booking);
        $statusHistory = $this->paymentStatusService->getHistory($booking);

        return view('admin.bookings.payment-status', compact(
            'booking',
            'availableStatuses',
            'statusHistory'
        ));
    }

    /**
     * Update payment status (POST)
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('manage-payment-status', $booking);

        $request->validate([
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'remarks' => 'nullable|string|max:500'
        ]);

        try {
            $status = PaymentStatus::find($request->payment_status_id);
            $role = $this->getUserRole();

            $this->paymentStatusService->updateStatus(
                $booking,
                $status->id,
                Auth::id(),
                $role,
                $request->remarks,
                [
                    'updated_via' => 'web_interface',
                    'team' => $role
                ]
            );

            return redirect()
                ->route('admin.bookings.payment-status', $booking->id)
                ->with('success', "Payment status updated to '{$status->name}' successfully.");

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Bulk update payment statuses (For team leads)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array|min:1',
            'booking_ids.*' => 'exists:bookings,id',
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'remarks' => 'nullable|string|max:500'
        ]);

        try {
            $status = PaymentStatus::find($request->payment_status_id);
            $role = $this->getUserRole();
            $updated = 0;
            $failed = 0;

            foreach ($request->booking_ids as $bookingId) {
                try {
                    $booking = Booking::find($bookingId);
                    
                    // Check if transition is allowed
                    if ($booking->paymentStatus) {
                        if (!PaymentStatus::canTransition($booking->paymentStatus->slug, $status->slug)) {
                            $failed++;
                            continue;
                        }
                    }

                    $this->paymentStatusService->updateStatus(
                        $booking,
                        $status->id,
                        Auth::id(),
                        $role,
                        $request->remarks,
                        ['bulk_update' => true]
                    );
                    $updated++;
                } catch (\Exception $e) {
                    $failed++;
                }
            }

            return redirect()
                ->back()
                ->with('success', "Bulk update completed: {$updated} updated, {$failed} failed.");

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function getUserRole()
    {
        if (Auth::user()->hasRole('admin')) return 'admin';
        if (Auth::user()->hasRole('mis')) return 'mis';
        if (Auth::user()->hasRole('chargeback')) return 'chargeback';
        if (Auth::user()->hasRole('changes')) return 'changes';
        if (Auth::user()->hasRole('agent')) return 'agent';
        return 'user';
    }
}