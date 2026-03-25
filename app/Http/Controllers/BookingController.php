<?php

// app/Http/Controllers/BookingController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CallType;
use App\Models\ChargeAssignment;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    // AGENT: List own bookings
    public function agentIndex()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['user', 'passengers', 'cards'])
            ->latest()
            ->paginate(20);

        return view('agent.bookings.index', compact('bookings'));
    }

    /**
     * Display the Charging Team Dashboard.
     */
    public function chargeIndex()
    {
        // Returning to the view you mentioned in your dashboard snippet
        return view('charge.dashboard');
    }

    // AGENT: Create form
    public function agentCreate()
    {
        $callTypes = CallType::where('is_active', true)->get();
        $merchants = Merchant::where('is_active', true)->get();

        return view('agent.bookings.create', compact('callTypes', 'merchants'));
    }

    // AGENT: Store
    public function agentStore(Request $request)
    {

        $validated = $request->validate([
            'calltype' => 'required|string|exists:call_types,name',
            'serviceprovided' => 'required|string|in:Flight,Hotel,Package',
            'servicetype' => 'required|string|in:New Booking,Modification,Cancellation',
            'bookingportal' => 'required|string|in:amadeus,sabre,worldspan,gds,website',
            'customername' => 'required|string|max:255',
            'customeremail' => 'required|email',
            'customerphone' => 'required|string',
            'billingphone' => 'required|string',
            'billingaddress' => 'required|string',
            'adults' => 'integer|min:1|max:9',
            'children' => 'integer|min:0|max:9',
            'infants' => 'integer|min:0|max:9',
            'flighttype' => 'required_if:serviceprovided,Flight|in:oneway,roundtrip,multicity',
            'segments' => 'required_if:serviceprovided,Flight|array|min:1',
            'segments.*.from_city' => 'required|string',
            'segments.*.to_city' => 'required|string',
            'segments.*.departure_date' => 'required|date',
            'segments.*.cabin_class' => 'required|string',
            // Cards/Passengers dynamic: use sometimes/required_unless
            'cards' => 'array|min:1',
            'cards.*.merchant_id' => 'required|exists:merchants,id',
            'cards.*.charge_amount' => 'required|numeric|min:0.01',
            'passengers' => 'array|min:1',
            // Hotel/Cab/Insurance conditional
            'hotelrequired' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $booking = Booking::create(array_merge($validated, [
                'userid' => auth()->id(),
                'agentcustomid' => auth()->user()->agentcustomid ?? 'AG'.auth()->id(),
                'status' => 'pending',
                'bookingreference' => 'BTK-'.strtoupper(substr(uniqid(), -5)),
            ]));

            // Create related records
            if (isset($validated['segments'])) {
                foreach ($validated['segments'] as $seg) {
                    $booking->segments()->create($seg);
                }
            }
            if (isset($validated['passengers'])) {
                foreach ($validated['passengers'] as $pass) {
                    $booking->passengers()->create($pass);
                }
            }
            if (isset($validated['cards'])) {
                foreach ($validated['cards'] as $card) {
                    $booking->cards()->create($card);
                }
            }
            // Hotel/Cab/Insurance similarly

            DB::commit();

            return redirect()->route('agent.bookings.show', $booking->id)
                ->with('success', 'Booking created! Ref: '.$booking->bookingreference);
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    // AGENT: Show booking
    public function agentShow(Booking $booking)
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->load(['passengers', 'segments', 'cards.merchant', 'hotel', 'cab', 'insurance']);

        return view('agent.bookings.show', compact('booking'));
    }

    public function chargeShow(Booking $booking)
    {
        // Charge access rule: either charge can see all,
        // OR only those "assignedtocharging"/assigned to them—apply your real rule here.

        $booking->load(['passengers', 'segments', 'cards.merchant', 'hotel', 'cab', 'insurance']);

        // If you want it EXACTLY same UI:
        return view('agent.bookings.show', compact('booking')); // reuse same blade
        // OR if you want same content but charging layout:
        // return view('charge.bookings.show', compact('booking'));
    }

    // ADMIN/MIS: List all/filter (from AdminBookingsController)
    public function adminIndex(Request $request)
    {
        $agentId = $request->query('agent_id');
        $agent = $agentId ? User::findOrFail($agentId) : null;

        $bookings = Booking::with(['user', 'passengers', 'segments'])
            ->when($agentId, fn ($q) => $q->where('user_id', $agentId))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings', 'agent'));
    }

    // ADMIN/MIS: Show
    public function adminShow(Booking $booking)
    {
        $booking->load(['user', 'passengers', 'segments', 'cards']);

        return view('admin.bookings.show', compact('booking'));
    }

    // ADMIN/MIS: Edit/Update (from AdminBookingsController)
    public function adminEdit(Booking $booking)
    {
        return view('admin.bookings.edit', compact('booking'));
    }

    public function adminUpdate(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,charged,refunded',
            'mis_remarks' => 'nullable|string',
            'amount_charged' => 'required|numeric',
            // ... other fields
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index', ['agent_id' => $booking->user_id])
            ->with('success', 'Updated!');
    }

    // assign bookings to charging team
    public function assignForCharging(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'This booking is already assigned to the charging team (Status: '.$booking->status.').');
        }

        $request->validate([
            'merchant' => 'required|exists:merchants,id',
        ]);

        $merchant = Merchant::findOrFail($request->merchant);

        // Find a random charge user
        $charger = User::where('role', 'charge')->inRandomOrder()->first();

        if (! $charger) {
            return back()->withErrors(['merchant' => 'No charging team member is available!']);
        }

        // Create assignment record
        $assignment = ChargeAssignment::create([
            'booking_id' => $booking->id,
            'charger_id' => $charger->id,
            'agent_id' => auth()->id(),
            'merchant_id' => $merchant->id,
            'status' => 'pending',
            'assigned_at' => now(),
        ]);

        // Update booking status only - REMOVE merchant_name
        $booking->update([
            'status' => 'assigned_to_charging',
            // 'merchant_name' => $merchant->name,  // ← REMOVE THIS LINE
        ]);

        return back()->with('success', 'Booking sent to '.$charger->name.' for charging.');
    }
}
