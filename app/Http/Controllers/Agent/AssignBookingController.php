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
            return redirect()->route('agent.assignments.show', $existingAssignment);
        }
        // Show the assign form to the agent
        return view('agent.bookings.assign', compact('booking'));
    }
    /**
     * Store the assignment
     */
    public function store(Request $request, Booking $booking)
    {
        \Log::info('Store assignment started for booking: ' . $booking->id);
        
        $request->validate([
            'message' => 'required|string|min:5|max:1000',
        ]);
        
        DB::beginTransaction();
        
        try {
            \Log::info('Creating assignment for booking: ' . $booking->id);
            
            // Create assignment
            $assignment = BookingAssignment::create([
                'booking_id' => $booking->id,
                'assigned_by' => Auth::id(),
                'status' => 'pending',
                'message' => $request->message,
            ]);
            
            \Log::info('Assignment created with ID: ' . $assignment->id);
            
            // Notify changes team (all users with role 'charge', 'admin', or 'changes')
            $changesTeam = User::whereIn('role', ['charge', 'admin', 'changes'])->get();
            
            \Log::info('Notifying ' . $changesTeam->count() . ' users');
            
            Notification::send($changesTeam, new BookingAssignedToChangesTeam($assignment));
            
            DB::commit();
            
            \Log::info('Assignment stored successfully, redirecting to assignments.show');
            
            return redirect()->route('agent.assignments.show', $assignment)
                ->with('success', 'Booking has been assigned to changes team successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Assignment creation failed: ' . $e->getMessage());
            \Log::error('Exception trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Failed to assign booking: ' . $e->getMessage())->withInput();
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