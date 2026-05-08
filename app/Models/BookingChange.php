<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'booking_status',
        'agent_id',
        'agent_name',
        'customer_name',
        'mis_manager_id',
        'mis_manager_name',
        'changed_fields',
        'old_values',
        'new_values',
        'manager_remark',
    ];

    protected $casts = [
        'changed_fields' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $table = 'booking_changes';

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function misManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mis_manager_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }
}
