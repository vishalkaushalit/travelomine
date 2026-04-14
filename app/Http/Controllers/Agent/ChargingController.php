<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Mail\ChargeAssignmentBroadcastMail;
use App\Models\Booking;
use App\Models\ChargeAssignment;
use App\Models\Merchant;
use App\Models\User;
use App\Notifications\NewChargingAssignment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class ChargingController extends Controller
{
    /**
     * Display assigned + old charging bookings for all charge users.
     */
    public function index()
    {
        if (auth()->user()->role !== 'charge') {
            abort(403, 'Unauthorized');
        }

        $bookings = Booking::whereIn('status', [
                'assigned_to_charging',
                'charged',
                'approved',
                'rejected',
                'completed',
            ])
            ->latest()
            ->get();

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
     * Show charging booking details for all charge users.
     */
    public function show(Booking $booking)
    {
        if (auth()->user()->role !== 'charge') {
            abort(403, 'Unauthorized');
        }

        if (! in_array($booking->status, [
            'assigned_to_charging',
            'charged',
            'approved',
            'rejected',
            'completed',
        ])) {
            abort(403, 'Not authorized to view this booking');
        }

        $booking->load(['user', 'passengers', 'segments', 'cards']);

        return view('charge.bookings.show', compact('booking'));
    }

    /**
     * Agent: Show charge assignment form for their booking.
     */
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

        if (! $charger) {
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

        $chargeUsers = User::where('role', 'charge')->get();

        foreach ($chargeUsers as $chargeUser) {
            $chargeUser->notify(new NewChargingAssignment($booking, $assignment));
        }

        $chargeEmails = $chargeUsers
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();

        if (! empty($chargeEmails)) {
            Mail::to($chargeEmails)->send(
                new ChargeAssignmentBroadcastMail(
                    $booking,
                    $assignment,
                    $charger->name,
                    $merchant->name,
                    auth()->user()->name
                )
            );
        }

        Cache::put("booking_assign_{$booking->id}", [
            'charger_id' => $charger->id,
            'assigned_at' => now(),
            'merchant_name' => $merchant->name,
        ], now()->addHours(24));

        return redirect()->route('agent.dashboard')
            ->with('success', "Booking #{$booking->booking_reference} assigned to {$charger->name}, visible to all charge team members | Merchant: {$merchant->name}");
    }
}