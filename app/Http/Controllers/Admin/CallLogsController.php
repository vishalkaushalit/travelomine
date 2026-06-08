<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\User;
use Illuminate\Http\Request;

class CallLogsController extends Controller
{
    /**
     * Display a listing of all call logs with agent details
     */
    public function index(Request $request)
    {
        $query = CallLog::with('agent'); // Eager load agent relationship
        
        // Filter by agent
        if ($request->has('agent_id') && $request->agent_id !== '') {
            $query->where('agent_id', $request->agent_id);
        }
        
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
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhereHas('agent', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Date range filter
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $callLogs = $query->latest()->paginate(10);
        
        // Get all agents for filter dropdown
        $agents = User::where('role', 'agent')->orderBy('name')->get();
        
        return view('admin.call-log.index', compact('callLogs', 'agents'));
    }

    /**
     * Display the specified call log.
     */
    public function show(CallLog $callLog)
    {
        // Load agent relationship
        $callLog->load('agent');
        
        return view('admin.call-log.show', compact('callLog'));
    }

    /**
     * Export call logs to CSV
     */
    public function export(Request $request)
    {
        $query = CallLog::with('agent');
        
        // Apply same filters as index
        if ($request->has('agent_id') && $request->agent_id !== '') {
            $query->where('agent_id', $request->agent_id);
        }
        
        if ($request->has('follow_up') && $request->follow_up !== '') {
            $query->where('follow_up', $request->follow_up);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $callLogs = $query->latest()->get();
        
        // Generate CSV
        $filename = 'call_logs_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add CSV headers
        fputcsv($handle, [
            'ID', 
            'Agent Name', 
            'Agent Email',
            'Customer First Name', 
            'Customer Last Name', 
            'Phone Number', 
            'Email', 
            'City', 
            'Follow Up', 
            'Call Detail', 
            'Remark', 
            'Created At'
        ]);
        
        // Add data rows
        foreach ($callLogs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->agent->name ?? 'Unknown',
                $log->agent->email ?? 'Unknown',
                $log->first_name,
                $log->last_name,
                $log->phone_number,
                $log->email ?? '',
                $log->city,
                $log->follow_up ? 'Yes' : 'No',
                $log->call_detail,
                $log->remark ?? '',
                $log->created_at->format('Y-m-d H:i:s')
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}