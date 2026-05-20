<?php
// app/Http/Controllers/Agent/CallLogController.php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallLogController extends Controller
{
    /**
     * Display a listing of call logs for the authenticated agent.
     */
    public function index(Request $request)
    {
        $query = CallLog::where('agent_id', Auth::id());
        
        // Filter by follow_up
        if ($request->has('follow_up') && $request->follow_up !== '') {
            $query->where('follow_up', $request->follow_up);
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }
        
        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $callLogs = $query->latest()->paginate(15);
        
        return view('agent.call-log.index', compact('callLogs'));
    }

    /**
     * Show the form for creating a new call log.
     */
    public function create()
    {
        return view('agent.call-log.create');
    }

    /**
     * Store a newly created call log in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'required|string|max:255',
            'follow_up' => 'boolean',
            'call_detail' => 'required|string',
            'remark' => 'nullable|string'
        ]);
        
        $validated['agent_id'] = Auth::id();
        $validated['follow_up'] = $request->has('follow_up');
        
        CallLog::create($validated);
        
        return redirect()
            ->route('agent.call-log.index')
            ->with('success', 'Call log created successfully!');
    }

    /**
     * Display the specified call log.
     */
    public function show(CallLog $callLog)
    {
        // Ensure agent can only view their own call logs
        if ($callLog->agent_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('agent.call-log.show', compact('callLog'));
    }
}