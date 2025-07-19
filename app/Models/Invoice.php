<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceItem[] $items
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\User $user
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'issue_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'deletion_reason',
        'deleted_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelledBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->hasRole('Administrador');
    }

    public function canBeDeletedBy(User $user): bool
    {
        // Solo el creador de la factura o un administrador pueden eliminarla
        return $this->user_id === $user->id || $user->hasRole('Administrador');
    }

    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = self::latest('id')->first();
        $number = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return 'INV-' . str_pad((string) $number, 6, '0', STR_PAD_LEFT);
    }
}
