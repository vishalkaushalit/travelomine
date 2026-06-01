<?php
// app/Http/Controllers/Admin/ActivityLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::latest('activity_at')->paginate(20);
        $onlineUsers = $this->getOnlineUsers();
        return view('admin.activity-logs.index', compact('logs', 'onlineUsers'));
    }

    public function latest()
    {
        $logs = ActivityLog::latest('activity_at')
            ->take(30)
            ->get([
                'id',
                'user_name',
                'role',
                'module',
                'action',
                'description',
                'activity_at',
                'ip_address',
                'subject_type',
                'subject_id',
                'meta',
            ])->map(function ($log) {
                $bookingReference = $log->meta['booking_reference'] ?? null;

                return [
                    'id' => $log->id,
                    'user_name' => $log->user_name,
                    'role' => $log->role,
                    'module' => $log->module,
                    'action' => $log->action,
                    'description' => $log->description,
                    'booking_reference' => $bookingReference,
                    'booking_id' => $log->subject_type === \App\Models\Booking::class ? $log->subject_id : null,
                    'activity_at' => Carbon::parse($log->activity_at)->format('d-m-Y H:i:s'),
                    'ip_address' => $log->ip_address,
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'logs' => $logs
        ]);
    }

    /**
     * Get currently online users (logged in but not logged out)
     */
    public function getOnlineUsers()
    {
        // Get the latest login event for each user
        $onlineUsers = DB::table('activity_logs as al')
            ->select(
                'al.user_id',
                'al.user_name',
                'al.role',
                'al.ip_address',
                'al.activity_at as last_login',
                DB::raw('MAX(al.id) as last_activity_id')
            )
            ->where('al.action', 'login')
            ->where('al.module', 'user')
            ->groupBy('al.user_id', 'al.user_name', 'al.role', 'al.ip_address', 'al.activity_at')
            ->get()
            ->filter(function ($user) {
                // Check if user has a subsequent logout
                $hasLogout = ActivityLog::where('user_id', $user->user_id)
                    ->where('action', 'logout')
                    ->where('module', 'user')
                    ->where('activity_at', '>', $user->last_login)
                    ->exists();

                return !$hasLogout; // Include only if no logout after this login
            })
            ->map(function ($user) {
                // Convert last_login string to Carbon instance
                $user->last_login = Carbon::parse($user->last_login);
                return $user;
            })
            ->sortByDesc('last_login')
            ->values();

        return $onlineUsers;
    }

    public function onlineUsers()
    {
        $onlineUsers = $this->getOnlineUsers();

        return response()->json([
            'success' => true,
            'users' => $onlineUsers
        ]);
    }
}
