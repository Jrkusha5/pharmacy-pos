<?php
// app/Models/Item.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    protected $fillable = [
        'name', 'category_id', 'unit_id',  'sku',
        'default_sell_price',
        'reorder_level', 'reorder_quantity', 'active'
    ];

    protected $casts = [
         'default_sell_price' => 'decimal:4',
        'active' => 'boolean',

    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

  public function needsReorder()
    {
        return $this->stock_on_hand <= $this->reorder_level;
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getQtyOnHandAttribute(): int
    {
        return (int) $this->purchaseItems()->sum('quantity');
    }

    public function getStockValueAttribute(): float
    {
        return $this->purchaseItems->sum(function ($purchaseItem) {
            return $purchaseItem->quantity * $purchaseItem->unit_cost;
        });
    }

    public function getAverageCostAttribute(): float
    {
        $totalQuantity = $this->qty_on_hand;
        if ($totalQuantity === 0) return 0;

        return $this->stock_value / $totalQuantity;
    }

    public function getBatchStock($batchNo): ?array
    {
        $purchaseItem = $this->purchaseItems()
            ->where('batch_no', $batchNo)
            ->first();

        if (!$purchaseItem) return null;

        return [
            'batch_no' => $purchaseItem->batch_no,
            'expires_at' => $purchaseItem->expires_at,
            'quantity' => $purchaseItem->quantity,
            'unit_cost' => $purchaseItem->unit_cost,
            'sell_price' => $purchaseItem->sell_price,
            'stock_value' => $purchaseItem->stock_value,
            'is_expired' => $purchaseItem->isExpired()
        ];
    }

    public function getAllBatchesStock()
    {
        return $this->purchaseItems()
            ->orderBy('expires_at', 'asc')
            ->get()
            ->map(function ($purchaseItem) {
                return [
                    'batch_no' => $purchaseItem->batch_no,
                    'expires_at' => $purchaseItem->expires_at,
                    'quantity' => $purchaseItem->quantity,
                    'unit_cost' => $purchaseItem->unit_cost,
                    'sell_price' => $purchaseItem->sell_price,
                    'stock_value' => $purchaseItem->stock_value,
                    'is_expired' => $purchaseItem->isExpired(),
                    'days_until_expiry' => $purchaseItem->expires_at ? $purchaseItem->expires_at->diffInDays(now()) : null
                ];
            });
    }

    public function getExpiringBatches($days = 30)
    {
        return $this->purchaseItems()
            ->where('expires_at', '<=', now()->addDays($days))
            ->where('quantity', '>', 0)
            ->orderBy('expires_at', 'asc')
            ->get();
    }

    public function getLowStockBatches()
    {
        return $this->purchaseItems()
            ->where('quantity', '<=', $this->reorder_level)
            ->where('quantity', '>', 0)
            ->orderBy('quantity', 'asc')
            ->get();
    }

    public function adjustStock($batchNo, $quantityChange, $reason = 'Stock adjustment')
    {
        $purchaseItem = $this->purchaseItems()
            ->where('batch_no', $batchNo)
            ->firstOrFail();

        $oldQuantity = $purchaseItem->quantity;
        $purchaseItem->quantity += $quantityChange;
        $purchaseItem->save();

        StockMovement::create([
            'item_id' => $this->id,
            'purchase_item_id' => $purchaseItem->id,
            'batch_no' => $batchNo,
            'movement_type' => 'adjustment',
            'quantity_change' => $quantityChange,
            'quantity_after' => $purchaseItem->quantity,
            'unit_cost' => $purchaseItem->unit_cost,
            'reference_id' => $purchaseItem->id,
            'reference_type' => PurchaseItem::class,
            'reason' => $reason
        ]);

        return $purchaseItem;
    }
}
