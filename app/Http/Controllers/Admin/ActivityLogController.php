<?php
// app/Http/Controllers/Admin/ActivityLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::latest('activity_at')->paginate(20);

        return view('admin.activity-logs.index', compact('logs'));
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
                'activity_at'
            ]);

        return response()->json($logs);
    }
}
