<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        try {
            DB::table('user_notification_reads')->updateOrInsert(
                [
                    'notification_id' => $id,
                    'user_id' => auth()->id()
                ],
                [
                    'read_at' => now(),
                    'updated_at' => now()
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get unread notifications for current user
     */
    public function getUnreadNotifications()
    {
        if (!auth()->check()) {
            return response()->json([]);
        }

        $user = auth()->user();
        
        $notifications = AdminNotification::where('is_active', true)
            ->where(function($query) use ($user) {
                $query->where('target_type', 'all')
                      ->orWhereJsonContains('target_roles', $user->role);
            })
            ->where(function($query) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $readNotifications = DB::table('user_notification_reads')
            ->where('user_id', $user->id)
            ->pluck('notification_id')
            ->toArray();

        $unreadNotifications = $notifications->filter(function($notification) use ($readNotifications) {
            return !in_array($notification->id, $readNotifications);
        })->values();

        return response()->json($unreadNotifications);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();
        
        $notifications = AdminNotification::where('is_active', true)
            ->where(function($query) use ($user) {
                $query->where('target_type', 'all')
                      ->orWhereJsonContains('target_roles', $user->role);
            })
            ->where(function($query) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', now());
            })
            ->pluck('id');

        foreach ($notifications as $notificationId) {
            DB::table('user_notification_reads')->updateOrInsert(
                [
                    'notification_id' => $notificationId,
                    'user_id' => $user->id
                ],
                [
                    'read_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        return response()->json(['success' => true]);
    }
}