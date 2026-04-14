<?php
// app/Http/Controllers/Admin/AdminNotifyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminNotifyController extends Controller
{
    /**
     * Display list of notifications
     */
    public function index()
    {
        $notifications = AdminNotification::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show form to create new notification
     */
    public function create()
    {
        $roles = [
            'all' => 'All Users',
            'admin' => 'Admins',
            'manager' => 'Managers',
            'agent' => 'Agents',
            'charge' => 'Charge Team',
            'support' => 'Support Team',
            'mis' => 'MIS Team'
        ];
        
        $priorities = [
            'info' => 'Info (Blue)',
            'success' => 'Success (Green)',
            'warning' => 'Warning (Yellow)',
            'danger' => 'Danger (Red)'
        ];
        
        return view('admin.notifications.create', compact('roles', 'priorities'));
    }

    /**
     * Store new notification
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|in:all,specific_roles',
            'target_roles' => 'required_if:target_type,specific_roles|array',
            'target_roles.*' => 'string',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|in:info,success,warning,danger',
            'can_dismiss' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $notification = AdminNotification::create([
            'title' => $request->title,
            'message' => $request->message,
            'target_type' => $request->target_type,
            'target_roles' => $request->target_type === 'specific_roles' ? $request->target_roles : null,
            'start_date' => $request->start_date ? Carbon::parse($request->start_date) : null,
            'expiry_date' => $request->expiry_date ? Carbon::parse($request->expiry_date) : null,
            'priority' => $request->priority,
            'can_dismiss' => $request->has('can_dismiss'),
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id()
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification created successfully.');
    }

    /**
     * Show form to edit notification
     */
    public function edit($id)
    {
        $notification = AdminNotification::findOrFail($id);
        
        $roles = [
            'all' => 'All Users',
            'admin' => 'Admins',
            'manager' => 'Managers',
            'agent' => 'Agents',
            'charge' => 'Charge Team',
            'support' => 'Support Team',
            'mis' => 'MIS Team'
        ];
        
        $priorities = [
            'info' => 'Info (Blue)',
            'success' => 'Success (Green)',
            'warning' => 'Warning (Yellow)',
            'danger' => 'Danger (Red)'
        ];
        
        return view('admin.notifications.edit', compact('notification', 'roles', 'priorities'));
    }

    /**
     * Update notification
     */
    public function update(Request $request, $id)
    {
        $notification = AdminNotification::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|in:all,specific_roles',
            'target_roles' => 'required_if:target_type,specific_roles|array',
            'target_roles.*' => 'string',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|in:info,success,warning,danger',
            'can_dismiss' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $notification->update([
            'title' => $request->title,
            'message' => $request->message,
            'target_type' => $request->target_type,
            'target_roles' => $request->target_type === 'specific_roles' ? $request->target_roles : null,
            'start_date' => $request->start_date ? Carbon::parse($request->start_date) : null,
            'expiry_date' => $request->expiry_date ? Carbon::parse($request->expiry_date) : null,
            'priority' => $request->priority,
            'can_dismiss' => $request->has('can_dismiss'),
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification updated successfully.');
    }

    /**
     * Duplicate/Copy notification
     */
    public function duplicate($id)
    {
        $original = AdminNotification::findOrFail($id);
        
        $notification = AdminNotification::create([
            'title' => 'Copy: ' . $original->title,
            'message' => $original->message,
            'target_type' => $original->target_type,
            'target_roles' => $original->target_roles,
            'start_date' => $original->start_date,
            'expiry_date' => $original->expiry_date,
            'priority' => $original->priority,
            'can_dismiss' => $original->can_dismiss,
            'is_active' => false,
            'created_by' => auth()->id()
        ]);

        return redirect()->route('admin.notifications.edit', $notification->id)
            ->with('success', 'Notification duplicated. Please review and activate.');
    }

    /**
     * Toggle notification active status
     */
    public function toggleActive($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->is_active = !$notification->is_active;
        $notification->save();

        $status = $notification->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Notification {$status} successfully.");
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->readBy()->detach();
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully.');
    }

    /**
     * Get notification statistics
     */
    public function stats($id)
    {
        $notification = AdminNotification::with('readBy')->findOrFail($id);
        
        $totalUsers = $this->getTargetUserCount($notification);
        $readCount = $notification->readBy->count();
        $readPercentage = $totalUsers > 0 ? round(($readCount / $totalUsers) * 100, 2) : 0;
        
        $readByRole = $notification->readBy()
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role')
            ->toArray();

        return response()->json([
            'total_users' => $totalUsers,
            'read_count' => $readCount,
            'unread_count' => $totalUsers - $readCount,
            'read_percentage' => $readPercentage,
            'read_by_role' => $readByRole
        ]);
    }

    /**
     * Get target user count for notification
     */
    private function getTargetUserCount($notification)
    {
        if ($notification->target_type === 'all') {
            return User::where('is_active', true)
                ->where('is_blocked', false)
                ->count();
        }
        
        return User::where('is_active', true)
            ->where('is_blocked', false)
            ->whereIn('role', $notification->target_roles ?? [])
            ->count();
    }
}