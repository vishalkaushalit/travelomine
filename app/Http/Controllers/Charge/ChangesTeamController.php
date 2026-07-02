<?php

namespace App\Http\Controllers\Charge;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChangesTeamController extends Controller
{
    /**
     * Show changes team dashboard with pending assignments
     */
    public function dashboard()
    {
        $pendingAssignments = BookingAssignment::where('status', 'pending')
            ->with(['booking', 'assignedBy'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);
            
        $acceptedAssignments = BookingAssignment::where('status', 'accepted')
            ->where('assigned_to', Auth::id())
            ->with(['booking', 'assignedBy'])
            ->orderBy('accepted_at', 'desc')
            ->paginate(15);
            
        $rejectedAssignments = BookingAssignment::where('status', 'rejected')
            ->where('assigned_to', Auth::id())
            ->with(['booking', 'assignedBy'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        $processingAssignments = BookingAssignment::where('status', 'Processing')
            ->where('assigned_to', Auth::id())
            ->with(['booking', 'assignedBy'])
            ->orderBy('completed_at', 'desc')
            ->paginate(15);
        $completedAssignments = BookingAssignment::where('status', 'completed')
            ->where('assigned_to', Auth::id())
            ->with(['booking', 'assignedBy'])
            ->orderBy('completed_at', 'desc')
            ->paginate(15);
            
        $stats = [
            'pending' => BookingAssignment::where('status', 'pending')->count(),
            'accepted' => BookingAssignment::where('status', 'accepted')->where('assigned_to', Auth::id())->count(),
            'rejected' => BookingAssignment::where('status', 'rejected')->where('assigned_to', Auth::id())->count(),
            'processing' => BookingAssignment::where('status', 'Processing')->where('assigned_to', Auth::id())->count(),
            'completed' => BookingAssignment::where('status', 'completed')->where('assigned_to', Auth::id())->count(),
        ];
        
        return view('charge.assignments.dashboard', compact('pendingAssignments', 'acceptedAssignments', 'rejectedAssignments', 'processingAssignments', 'completedAssignments', 'stats'));
    }
    
    /**
     * Show assignment details with booking information
     */
    public function show(BookingAssignment $assignment)
    {
        // Security check - only changes team can view
        if (!in_array(Auth::user()->role, ['charge', 'admin'])) {
            abort(403);
        }
        
        $assignment->load([
            'booking', 
            'booking.segments', 
            'booking.passengers', 
            'booking.cards',
            'booking.remarks',
            'assignedBy',
            'assignedTo'
        ]);
        
        return view('charge.assignments.show', compact('assignment'));
    }
    
    /**
     * Accept the assignment
     */
    public function accept(Request $request, BookingAssignment $assignment)
    {
        $request->validate([
            'response_message' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $assignment->accept(Auth::id(), $request->response_message);
            
            // Update booking status
            $assignment->booking->update(['status' => 'change_in_progress']);
            
            DB::commit();
            
            return redirect()->route('charge.assignments.dashboard')
                ->with('success', 'Assignment accepted successfully. You can now make changes to this booking.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to accept assignment. Please try again.');
        }
    }
    
    /**
     * Reject the assignment
     */
    public function reject(Request $request, BookingAssignment $assignment)
    {
        $request->validate([
            'response_message' => 'required|string|min:5|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $assignment->reject(Auth::id(), $request->response_message);
            
            // Update booking status back to previous
            $assignment->booking->update(['status' => 'change_rejected']);
            
            DB::commit();
            
            return redirect()->route('charge.assignments.dashboard')
                ->with('info', 'Assignment rejected. Reason has been sent to the agent.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject assignment. Please try again.');
        }
    }
    
    /**
     * Mark assignment as completed after changes are done
     */
    public function complete(Request $request, BookingAssignment $assignment)
    {
        $request->validate([
            'completion_message' => 'nullable|string|max:500',
        ]);
        
        DB::beginTransaction();
        
        try {
            $assignment->complete();
            
            // Add completion remark
            if ($request->completion_message) {
                $assignment->booking->remarks()->create([
                    'agent_id' => Auth::id(),
                    'remark_text' => $request->completion_message,
                    'remark_type' => 'changes_completed',
                ]);
            }
            
            // Update booking status
            $assignment->booking->update(['status' => 'changes_completed']);
            
            DB::commit();
            
            return redirect()->route('charge.assignments.dashboard')
                ->with('success', 'Changes completed successfully. Booking has been updated.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete changes. Please try again.');
        }
    }

    /**
     * Add a remark to the booking from the changes team.
     */
    public function addRemark(Request $request, BookingAssignment $assignment)
    {
        $request->validate([
            'remark_text' => 'required|string|min:3|max:1000',
        ]);

        $assignment->booking->remarks()->create([
            'agent_id' => Auth::id(),
            'remark_text' => $request->remark_text,
            'remark_type' => 'changes_team',
        ]);

        return redirect()->route('charge.assignments.show', $assignment)
            ->with('success', 'Remark added successfully.');
    }
}