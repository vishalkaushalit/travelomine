<?php
// app/Models/ActivityLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'module',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'meta',
        'activity_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'activity_at' => 'datetime',
    ];
}
