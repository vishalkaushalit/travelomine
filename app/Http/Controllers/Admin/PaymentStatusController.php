<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PaymentStatus;
use App\Services\PaymentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentStatusController extends Controller
{
    protected $paymentStatusService;

    public function __construct(PaymentStatusService $paymentStatusService)
    {
        $this->paymentStatusService = $paymentStatusService;
    }

    /**
     * Update payment status via AJAX (For all teams)
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_status_id' => 'required|exists:payment_statuses,id',
            'remarks' => 'nullable|string|max:500'
        ]);

        try {
            $status = PaymentStatus::find($request->payment_status_id);
            
            // Get user role
            $role = $this->getUserRole();
            
            // Update payment status
            $this->paymentStatusService->updateStatus(
                $booking,
                $status->id,
                Auth::id(),
                $role,
                $request->remarks,
                ['updated_via' => 'web_interface']
            );

            return response()->json([
                'success' => true,
                'message' => "Payment status updated to '{$status->name}' successfully.",
                'data' => [
                    'new_status' => $status->name,
                    'new_status_color' => $status->color,
                    'history' => $this->paymentStatusService->getHistory($booking)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get available statuses for a booking
     */
    public function getAvailableStatuses(Booking $booking)
    {
        try {
            $statuses = $this->paymentStatusService->getAvailableStatuses($booking);
            
            return response()->json([
                'success' => true,
                'data' => $statuses
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get payment status history
     */
    public function getHistory(Booking $booking)
    {
        try {
            $history = $this->paymentStatusService->getHistory($booking);
            
            return response()->json([
                'success' => true,
                'data' => $history
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get user role for logging
     */
    private function getUserRole()
    {
        if (Auth::user()->hasRole('admin')) {
            return 'admin';
        } elseif (Auth::user()->hasRole('mis')) {
            return 'mis';
        } elseif (Auth::user()->hasRole('chargeback')) {
            return 'chargeback';
        } elseif (Auth::user()->hasRole('changes')) {
            return 'changes';
        } elseif (Auth::user()->hasRole('agent')) {
            return 'agent';
        }
        return 'user';
    }
}