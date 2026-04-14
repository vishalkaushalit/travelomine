<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\BookingStatusUpdated;

class ChargeBookingStatusController extends Controller
{
    public function update(Request $request, $id)
    {
        $allowedStatuses = [
            'payment_processing',
            'confirmed',
            'ticketed',
            'failed',
            'cancelled',
            'hold',
            'refund',
            'charging_in_progress',
        ];

        $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);

        $booking = Booking::findOrFail($id);

        if (!in_array($booking->status, ['auth_email_sent', 'payment_processing', 'charging_in_progress', 'confirmed', 'ticketed', 'failed', 'cancelled', 'hold', 'refund'])) {
            return redirect()->back()->with('error', 'This booking is not ready for charge status update.');
        }

        $booking->update([
            'status' => $request->status,
        ]);

        // SEND EMAIL USING MAIL FACADE (most reliable method)
        $agent = $booking->user; // Adjust if your relationship is different
        Mail::to($agent->email)->send(new BookingStatusUpdated($booking));

        // CREATE NOTIFICATION IN YOUR EXISTING admin_notifications TABLE
        $agentRole = $agent->role; // Adjust if you store roles differently
        $adminRoles = ['admin', 'superadmin']; // Adjust to your actual admin roles
        $targetRoles = array_unique(array_merge([$agentRole], $adminRoles));

        DB::table('admin_notifications')->insert([
            'title' => 'Booking Status Updated',
            'message' => "Booking #{$booking->id} status updated to " . str_replace('_', ' ', $booking->status),
            'target_roles' => json_encode($targetRoles),
            'target_type' => 'specific_roles',
            'start_date' => now(),
            'expiry_date' => now()->addDays(7),
            'priority' => 'info',
            'is_active' => true,
            'can_dismiss' => true,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Booking status updated successfully to ' . str_replace('_', ' ', $request->status) . '.');
    }
}
