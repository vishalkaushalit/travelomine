<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingAssignedToChangesTeam;

class AssignBookingController extends Controller
{
    /**
     * Show the form for assigning a booking to changes team
     */
    public function create(Booking $booking)
    {
        // Check if booking already has pending assignment
        $existingAssignment = $booking->assignments()
            ->where('status', 'pending')
            ->first();
            
        if ($existingAssignment) {
            return redirect()->route('agent.bookings.show', $booking)
                ->with('error', 'This booking already has a pending assignment to changes team.');
        }
        
        return view('agent.bookings.assign', compact('booking'));
    }
    
    /**
     * Store the assignment
     */
    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'message' => 'required|string|min:5|max:1000',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create assignment
            $assignment = BookingAssignment::create([
                'booking_id' => $booking->id,
                'assigned_by' => Auth::id(),
                'status' => 'pending',
                'message' => $request->message,
            ]);
            
            // Update booking status if needed
            if ($booking->status !== 'change_requested') {
                $booking->update(['status' => 'change_requested']);
            }
            
            // Notify changes team (all users with role 'change' or 'charge' as per your system)
            $changesTeam = User::whereIn('role', ['charge', 'admin'])->get();
            
            Notification::send($changesTeam, new BookingAssignedToChangesTeam($assignment));
            
            DB::commit();
            
            return redirect()->route('agent.bookings.show', $booking)
                ->with('success', 'Booking has been assigned to changes team successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to assign booking. Please try again.')->withInput();
        }
    }
    
    /**
     * Show all assignments for agent dashboard
     */
    public function index()
    {
        $assignments = BookingAssignment::where('assigned_by', Auth::id())
            ->with(['booking', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('agent.assignments.index', compact('assignments'));
    }
    
    /**
     * Show assignment details
     */
    public function show(BookingAssignment $assignment)
    {
        // Security check - only assigned by or assigned to can view
        if ($assignment->assigned_by != Auth::id() && $assignment->assigned_to != Auth::id()) {
            abort(403);
        }
        
        $assignment->load(['booking', 'booking.segments', 'booking.passengers', 'booking.cards', 'assignedBy', 'assignedTo']);
        
        return view('agent.assignments.show', compact('assignment'));
    }
}