<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'alias_name',
        'email',
        'phone',
        'password',
        'agent_custom_id',
        'role',
        'is_active',
        'is_blocked',
        'last_login',
        'created_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_blocked'        => 'boolean',
            'last_login'        => 'datetime',
        ];
    }

    // ✅ Auto-generate agent_custom_id
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->agent_custom_id)) {
                $user->agent_custom_id = 'AG' . rand(1000, 9999);
            }
        });
    }

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
        return $this->role === 'charge';
    }

    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    public function isMis(): bool
    {
        return $this->role === 'mis';
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
}