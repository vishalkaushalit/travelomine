<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ChargeAssignment;
use App\Notifications\NewChargingAssignment;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ChargingController extends Controller
{
    /**
     * Display assigned charging bookings for current charge user/agent.
     */
    public function index()
    {
        $bookings = Booking::where('status', 'assigned_to_charging')
            ->get()
            ->filter(function ($booking) {
                $assignment = json_decode($booking->charging_remarks, true);

                return $assignment && ($assignment['charger_id'] ?? null) == auth()->id();
            });

        // Manual pagination
        $perPage = 20;
        $page = request()->get('page', 1);
        $paginated = new LengthAwarePaginator(
            $bookings->forPage($page, $perPage),
            $bookings->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('charge.bookings.index', compact('paginated'));
    }

    /**
     * Show specific assigned booking for charging.
     */
    public function show(Booking $booking)
    {
        $assignment = json_decode($booking->charging_remarks, true);

        if ($booking->status !== 'assigned_to_charging' ||
            ! $assignment ||
            ($assignment['charger_id'] ?? null) != auth()->id()) {
            abort(403, 'Not authorized to view this booking');
        }

        $booking->load(['user', 'passengers', 'segments', 'cards']);

        return view('charge.bookings.show', compact('booking'));
    }

    /**
     * Agent: Show charge assignment form for their booking.
     */
    // public function chargeByAgent(Booking $booking)
    // {
    //     // Only agent owner with pending status
    //     if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
    //         abort(403, 'Unauthorized');
    //     }

    //     $pendingCards = $booking->cards->where('payment_status', 'pending');
    //     if ($pendingCards->isEmpty()) {
    //         return back()->with('error', 'No pending payments to charge.');
    //     }

    //     $merchants = Merchant::where('is_active', true)->get();

    //     return view('agent.charging.charge', compact('booking', 'pendingCards', 'merchants'));
    // }

    public function chargeByAgent(Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            abort(403);
        }
        $merchants = Merchant::where('is_active', true)->get();

        return view('agent.charging.charge', compact('booking', 'merchants'));
    }

    /**
     * Agent: Assign booking to random charging team member.
     */
    public function assignForCharging(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id() || $booking->status !== 'pending') {
            abort(403, 'Unauthorized');
    }

    $request->validate([
        'merchant' => 'required|exists:merchants,id',
    ]);

    $merchant = Merchant::findOrFail($request->merchant);

    $charger = User::where('role', 'charge')->inRandomOrder()->first();

    if (!$charger) {
        return back()->withErrors(['merchant' => 'No charging team member available!']);
    }

    $assignmentData = [
        'charger_id' => $charger->id,
        'charger_name' => $charger->name,
        'assigned_at' => now()->toDateTimeString(),
        'merchant_id' => $merchant->id,
        'merchant_name' => $merchant->name,
        'status' => 'pending',
    ];

    $booking->update([
        'status' => 'assigned_to_charging',
        'charging_remarks' => json_encode($assignmentData),
        'merchant_name' => $merchant->name,
    ]);

    $assignment = ChargeAssignment::create([
        'booking_id' => $booking->id,
        'charger_id' => $charger->id,
        'agent_id' => auth()->id(),
        'merchant_id' => $merchant->id,
        'status' => 'pending',
        'assigned_at' => now(),
    ]);

    $charger->notify(new NewChargingAssignment($booking, $assignment));

    Cache::put("booking_assign_{$booking->id}", [
        'charger_id' => $charger->id,
        'assigned_at' => now(),
        'merchant_name' => $merchant->name,
    ], now()->addHours(24));

    return redirect()->route('agent.dashboard')
        ->with('success', "Booking #{$booking->booking_reference} sent to {$charger->name} | Merchant: {$merchant->name}");
}

}
