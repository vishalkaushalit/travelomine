<?php

// app/Services/ActivityLogger.php
namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $module,
        string $action,
        string $description,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $meta = []
    ): void {
        $user = Auth::user();
        $request = request();

        ActivityLog::create([
            'user_id'      => $user?->id,
            'user_name'    => $user?->name,
            'role'         => $user?->role ?? null,
            'module'       => $module,
            'action'       => $action,
            'description'  => $description,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'ip_address'   => $request?->ip(),
            'user_agent'   => $request?->userAgent(),
            'url'          => $request?->fullUrl(),
            'method'       => $request?->method(),
            'meta'         => $meta,
            'activity_at'  => now(),
        ]);
    }
}
