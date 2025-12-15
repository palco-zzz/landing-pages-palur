<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaction_id',
        'menu_id',
        'quantity',
        'price',
        'subtotal',
        'is_printed',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'integer',
            'subtotal' => 'integer',
            'is_printed' => 'boolean',
        ];
    }

    /**
     * Get the transaction that owns this item.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the menu associated with this item.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Mark this item as printed to kitchen.
     */
    public function markAsPrinted(): self
    {
        $this->update(['is_printed' => true]);

        return $this;
    }

    /**
     * Scope to get unprinted items (add-ons waiting for kitchen).
     */
    public function scopeUnprinted($query)
    {
        return $query->where('is_printed', false);
    }

    /**
     * Scope to get printed items.
     */
    public function scopePrinted($query)
    {
        return $query->where('is_printed', true);
    }

    /**
     * Calculate and set the subtotal before saving.
     */
    public function calculateSubtotal(): self
    {
        $this->subtotal = $this->price * $this->quantity;

        return $this;
    }
}
