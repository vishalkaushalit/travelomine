<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'alias_name',
        'email',
        'extension_number',
        'alternate_number',
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

        static::created(function ($user) {
            // Requirement 5: Admin - when new user created
            try {
                $admins = \App\Models\User::where('role', 'admin')
                    ->where('is_active', true)
                    ->where('is_blocked', false)
                    ->get();

                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\CrmNotification(
                        'New User Created',
                        "A new user {$user->name} ({$user->role}) has been created.",
                        'fa-user-plus',
                        'success',
                        route('admin.users.index')
                    ));
                }
            } catch (\Throwable $e) {
                \Log::error('User created notification error: ' . $e->getMessage());
            }
        });

        static::updated(function ($user) {
            // Requirement 5: Admin - change anything in their name
            try {
                if ($user->wasChanged('name')) {
                    $originalName = $user->getOriginal('name');
                    $newName = $user->name;

                    $admins = \App\Models\User::where('role', 'admin')
                        ->where('is_active', true)
                        ->where('is_blocked', false)
                        ->get();

                    foreach ($admins as $admin) {
                        $admin->notify(new \App\Notifications\CrmNotification(
                            'User Name Changed',
                            "User name has been changed from \"{$originalName}\" to \"{$newName}\".",
                            'fa-user-edit',
                            'warning',
                            route('admin.users.index')
                        ));
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('User name update notification error: ' . $e->getMessage());
            }
        });
    }
    // Helper Methods for Role Checking
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => in_array($this->role, ['admin', 'manager']), 
            'agent' => $this->role === 'agent',
            'charge' => in_array($this->role, ['charge']), 
            'support' => in_array($this->role, ['support']),   
            'mis' => in_array($this->role, ['mis']),           
            'mis-manager' => in_array($this->role, ['mis-manager']),
            'changes' => in_array($this->role, ['changes']),        
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

    public function isCharge(): bool
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

    public function getNotificationRoute(): string
    {
        if ($this->role === 'admin' || $this->role === 'manager') {
            return route('admin.notifications.index');
        }

        $roleRoutes = [
            'agent' => 'agent.notifications',
            'charge' => 'charge.notifications',
            'mis' => 'mis.notifications',
            'mis-manager' => 'mis-manager.notifications',
            'changes' => 'changes.notifications'
        ];

        if (isset($roleRoutes[$this->role]) && \Route::has($roleRoutes[$this->role])) {
            return route($roleRoutes[$this->role]);
        }

        return '#';
    }
}