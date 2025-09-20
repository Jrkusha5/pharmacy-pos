<?php
// app/Models/PurchaseItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'item_id', 'batch_no', 'expires_at',
        'quantity', 'unit_cost', 'sell_price', 'line_total'
    ];

    protected $casts = [
        'expires_at' => 'date',
        'unit_cost' => 'decimal:4',
        'sell_price' => 'decimal:4',
        'line_total' => 'decimal:4'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseItem) {
            if (empty($purchaseItem->batch_no)) {
                $purchaseItem->batch_no = self::generateBatchNumber($purchaseItem->item);
            }
            $purchaseItem->line_total = $purchaseItem->quantity * $purchaseItem->unit_cost;
        });

        static::created(function ($purchaseItem) {
            StockMovement::create([
                'item_id' => $purchaseItem->item_id,
                'purchase_item_id' => $purchaseItem->id,
                'batch_no' => $purchaseItem->batch_no,
                'movement_type' => 'purchase',
                'quantity_change' => $purchaseItem->quantity,
                'quantity_after' => $purchaseItem->item->qty_on_hand,
                'unit_cost' => $purchaseItem->unit_cost,
                'reference_id' => $purchaseItem->purchase_id,
                'reference_type' => Purchase::class,
                'reason' => 'Purchase receipt'
            ]);
        });

        static::updated(function ($purchaseItem) {
            if ($purchaseItem->isDirty('quantity')) {
                $quantityChange = $purchaseItem->quantity - $purchaseItem->getOriginal('quantity');

                StockMovement::create([
                    'item_id' => $purchaseItem->item_id,
                    'purchase_item_id' => $purchaseItem->id,
                    'batch_no' => $purchaseItem->batch_no,
                    'movement_type' => 'adjustment',
                    'quantity_change' => $quantityChange,
                    'quantity_after' => $purchaseItem->item->qty_on_hand,
                    'unit_cost' => $purchaseItem->unit_cost,
                    'reference_id' => $purchaseItem->id,
                    'reference_type' => self::class,
                    'reason' => 'Quantity adjustment'
                ]);
            }
        });
    }

    private static function generateBatchNumber($item): string
    {
        $itemCode = strtoupper(substr($item->name, 0, 3));
        $date = now()->format('Ymd');
        $sequence = self::where('item_id', $item->id)
            ->whereDate('created_at', now())
            ->count() + 1;

        return "{$itemCode}-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getRemainingQuantityAttribute(): int
    {
        $soldQuantity = $this->saleItems()->sum('quantity');
        return max(0, $this->quantity - $soldQuantity);
    }

    public function getStockValueAttribute(): float
    {
        return $this->quantity * $this->unit_cost;
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeExpiringFirst($query)
    {
        return $query->orderBy('expires_at', 'asc');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
