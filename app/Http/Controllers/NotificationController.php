<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        try {
            $userId = auth()->id();
            $now = now();

            DB::table('user_notification_reads')->updateOrInsert(
                [
                    'notification_id' => $id,
                    'user_id' => $userId,
                ],
                [
                    'read_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to mark notification as read',
                'message' => $e->getMessage(),
            ], 500);
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
        $now = now();

        $readNotificationIds = DB::table('user_notification_reads')
            ->where('user_id', $user->id)
            ->pluck('notification_id');

        $unreadNotifications = AdminNotification::query()
            ->where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->where('target_type', 'all')
                      ->orWhereJsonContains('target_roles', $user->role);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('expiry_date')
                      ->orWhere('expiry_date', '>=', $now);
            })
            ->whereNotIn('id', $readNotificationIds)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($unreadNotifications);
    }

    /**
     * Mark all visible notifications as read
     */
    public function markAllAsRead()
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        try {
            $user = auth()->user();
            $now = now();

            $notificationIds = AdminNotification::query()
                ->where('is_active', true)
                ->where(function ($query) use ($user) {
                    $query->where('target_type', 'all')
                          ->orWhereJsonContains('target_roles', $user->role);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('start_date')
                          ->orWhere('start_date', '<=', $now);
                })
                ->where(function ($query) use ($now) {
                    $query->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', $now);
                })
                ->pluck('id');

            foreach ($notificationIds as $notificationId) {
                DB::table('user_notification_reads')->updateOrInsert(
                    [
                        'notification_id' => $notificationId,
                        'user_id' => $user->id,
                    ],
                    [
                        'read_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Failed to mark all notifications as read',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}