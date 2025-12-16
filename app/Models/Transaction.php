<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'user_id',
        'customer_name',
        'total_amount',
        'payment_method',
        'status',
        'paid_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->uuid)) {
                $transaction->uuid = Str::uuid()->toString();
            }
        });
    }

    /**
     * Get the user (cashier) that handles this transaction.
     * Include soft-deleted users to preserve history.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the items for this transaction.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Recalculate and update the total amount based on all items.
     */
    public function recalculateTotal(): self
    {
        $this->total_amount = $this->items()->sum('subtotal');
        $this->save();

        return $this;
    }

    /**
     * Get items that haven't been printed to kitchen yet (add-on orders).
     */
    public function unprintedItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class)->where('is_printed', false);
    }

    /**
     * Mark the transaction as paid.
     */
    public function markAsPaid(string $paymentMethod): self
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark the transaction as cancelled.
     */
    public function markAsCancelled(): self
    {
        $this->update([
            'status' => 'cancelled',
        ]);

        return $this;
    }

    /**
     * Scope to get unpaid transactions.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * Scope to get paid transactions.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope to get today's transactions.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
