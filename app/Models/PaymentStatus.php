<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    // Relationships
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PaymentStatusHistory::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Helper Methods
    public static function getStatusBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

    public static function getDefaultStatus()
    {
        return self::where('slug', 'pending')->first();
    }

    public static function getCapturedStatus()
    {
        return self::where('slug', 'captured')->first();
    }

    // Check if transition is allowed
    public static function canTransition($fromSlug, $toSlug)
    {
        $allowedTransitions = [
            'pending' => ['captured', 'failed', 'hold', 'declined'],
            'captured' => ['refund', 'void'],
            'hold' => ['captured', 'failed', 'declined'],
            'failed' => ['pending'],
            'refund' => [],
            'void' => [],
            'declined' => ['pending']
        ];

        return in_array($toSlug, $allowedTransitions[$fromSlug] ?? []);
    }
}