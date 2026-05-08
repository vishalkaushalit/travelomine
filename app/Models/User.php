<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
=======
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7

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
<<<<<<< HEAD
        'email_verified_at',
=======
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
<<<<<<< HEAD
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_blocked' => 'boolean',
            'last_login' => 'datetime',
        ];
    }

    // Auto-generate agent_custom_id if not provided
=======
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_blocked'        => 'boolean',
            'last_login'        => 'datetime',
        ];
    }

    // ✅ Auto-generate agent_custom_id
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
    protected static function booted(): void
    {
        static::creating(function ($user) {
            if (empty($user->agent_custom_id)) {
                $user->agent_custom_id = 'AG' . rand(1000, 9999);
            }
        });
    }

<<<<<<< HEAD
    // Helper Methods for Role Checking
=======
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
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
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
<<<<<<< HEAD
        return $this->role === 'charging';
=======
        return $this->role === 'charge';
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
    }

    public function isSupport(): bool
    {
        return $this->role === 'support';
    }

    public function isMis(): bool
    {
        return $this->role === 'mis';
    }

<<<<<<< HEAD
    public function isMisManager(): bool
    {
        return $this->role === 'mis-manager';
    }
     public function isChanges(): bool
    {
        return $this->role === 'changes';
    }

=======
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }
<<<<<<< HEAD

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    // Relationships
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
=======
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
}