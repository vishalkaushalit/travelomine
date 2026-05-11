<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'alias_name',
        'email',
        'extension_number',
        'phone',
        'password',
        'agent_custom_id',
        'role',
        'is_active',
        'is_blocked',
        'last_login',
        'created_by',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    // Auto-generate agent_custom_id if not provided
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->agent_custom_id)) {
                $user->agent_custom_id = 'AG' . rand(1000, 9999);
            }
        });
    }
    // Helper Methods for Role Checking
    // ✅ FILAMENT PANEL ACCESS - Updated for multiple roles per panel
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => in_array($this->role, ['admin', 'manager']), // Managers assist admins
            'agent' => $this->role === 'agent',
            'charge' => in_array($this->role, ['charge']), // Multiple charging team members
            'support' => in_array($this->role, ['support']),   // Multiple support team members
            'mis' => in_array($this->role, ['mis']),           // Multiple MIS team members
            default => false,
        };
    }

    // ✅ RELATIONSHIPS
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    // ✅ HELPER METHODS - Added new role checkers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    public function isCharging(): bool
    {
        return $this->role === 'charging';
    }

    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    public function isMis(): bool
    {
        return $this->role === 'mis';
    }

    public function isMisManager(): bool
    {
        return $this->role === 'mis-manager';
    }
     public function isChanges(): bool
    {
        return $this->role === 'changes';
    }
    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }
}