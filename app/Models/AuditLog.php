<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * @property-read string|null $reason
 */
class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'admin_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the reason from details array
     */
    public function getReasonAttribute(): ?string
    {
        return $this->details['reason'] ?? null;
    }
}
