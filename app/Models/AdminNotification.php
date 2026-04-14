<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    use HasFactory;

    protected $table = 'admin_notifications';

    protected $fillable = [
        'title',
        'message',
        'target_roles',
        'target_type',
        'start_date',
        'expiry_date',
        'priority',
        'is_active',
        'can_dismiss',
        'created_by'
    ];

    protected $casts = [
        'target_roles' => 'array',
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'can_dismiss' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function readBy()
    {
        return $this->belongsToMany(User::class, 'user_notification_reads', 'notification_id', 'user_id')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    public function isExpired()
    {
        return $this->expiry_date && now()->gt($this->expiry_date);
    }

    public function isValidForUser($user)
    {
        // Check if notification is active and not expired
        if (!$this->is_active || $this->isExpired()) {
            return false;
        }

        // Check start date
        if ($this->start_date && now()->lt($this->start_date)) {
            return false;
        }

        // Check target roles
        if ($this->target_type === 'all') {
            return true;
        }

        // For specific roles
        return in_array($user->role, $this->target_roles ?? []);
    }

    public function isReadByUser($userId)
    {
        return $this->readBy()->where('user_id', $userId)->exists();
    }
}