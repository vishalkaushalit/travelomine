<?php

namespace App\Http\Controllers\Mis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class MisBookingsController extends Controller
{
    /**
     * Display ALL bookings (not filtered by agent)
     */
     public function all(Request $request)
    {
        $query = Booking::with(['user', 'passengers', 'segments']);

        // ============ SEARCH ============
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('agent_custom_id', 'like', "%{$search}%")
                  ->orWhere('booking_reference', 'like', "%{$search}%")
                  ->orWhere('airline_pnr', 'like', "%{$search}%")
                  ->orWhere('gk_pnr', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // ============ DATE RANGE FILTER ============
        if ($request->filled('from_date')) {
            $query->whereDate('booking_date', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('booking_date', '<=', $request->to_date);
        }

        // ============ STATUS FILTER ============
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ============ SERVICE FILTER ============
        if ($request->filled('service')) {
            $query->where('service_provided', $request->service);
        }

        // ============ AGENT FILTER ============
        if ($request->filled('agent_id')) {
            $query->where('user_id', $request->agent_id);
        }

        // ============ SORTING ============
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = [
            'id', 'booking_reference', 'customer_name', 'customer_email', 
            'booking_date', 'created_at', 'status', 'amount_charged',
            'airline_pnr', 'service_provided'
        ];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // ============ PER PAGE ============
        $perPage = $request->get('per_page', 25);
        $allowedPerPage = [5, 10, 25, 50, 100, 250, 500, 1000, 5000];
        
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        // ============ EXECUTE QUERY ============
        $bookings = $query->paginate($perPage);
        $bookings->appends($request->except('page'));

        // ============ GET AGENTS FOR FILTER DROPDOWN ============
        $agents = User::where(function($q) {
            $q->where('email', 'like', '%@callinggenie.com')
              ->orWhere('email', 'like', '%@trafficpirates.com');
        })->orderBy('name')->get();

        // ============ STATS CARDS ============
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'charged' => Booking::where('status', 'charged')->count(),
            'ticketed' => Booking::where('status', 'ticketed')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'total_mco' => Booking::sum('total_mco'),
        ];

        return view('mis.bookings.all', compact('bookings', 'agents', 'stats'));
    }

    /**
     * Display bookings for a specific agent
     */
    public function index(Request $request)
    {
        $agentId = $request->query('agent_id');
        // Get the agent details
        $agent = User::findOrFail($agentId);
        // Fetch bookings with relationships
        $bookings = Booking::with(['passengers', 'segments', 'user'])
            ->where('user_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('mis.bookings.index', compact('bookings', 'agent'));
    }

    /**
     * Show single booking details
     */
    public function show($id)
    {
        $booking = Booking::with(['passengers', 'segments', 'user'])
            ->findOrFail($id);
        
        ActivityLogger::log(
            'booking',
            'view',
            'Viewed booking '.($booking->booking_reference ?? $booking->id).' (ID: '.$booking->id.')',
            Booking::class,
            $booking->id,
            ['booking_reference' => $booking->booking_reference]
        );

        return view('mis.bookings.show', compact('booking'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $booking = Booking::with(['passengers', 'segments'])
            ->findOrFail($id);
        
        return view('mis.bookings.edit', compact('booking'));
    }

    /**
     * Update booking
     */
    
    public function update(Request $request, $id)

    {
            $booking = Booking::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:pending,assigned_to_charging,auth_email_sent,payment_processing,confirmed,ticketed,failed,cancelled,hold,refund,charging_in_progress,Alert,RDR,retrieval,chargeback,charged',
                'mis_remarks' => 'nullable|string',
                'amount_charged' => 'required|numeric',
                'amount_paid_airline' => 'required|numeric',
                'total_mco' => 'required|numeric',
            ]);

            $booking->update($validated);

            return redirect()
                ->route('mis.bookings.show', $booking->id)
                ->with('success', 'Booking updated successfully!');
    }


    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        
        $booking->delete();
        
        return redirect()
            ->route('mis.bookings.all')
            ->with('success', 'Booking deleted successfully!');
    }
}
