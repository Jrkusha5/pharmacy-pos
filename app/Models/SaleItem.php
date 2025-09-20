<?php
// app/Models/SaleItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'item_id', 'purchase_item_id',
        'quantity', 'unit_price', 'line_total', 'meta'
    ];

    protected $casts = [
        'unit_price' => 'decimal:4',
        'line_total' => 'decimal:4',
        'meta' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($saleItem) {
            $saleItem->line_total = $saleItem->quantity * $saleItem->unit_price;

            if (empty($saleItem->batch_no)) {
                $saleItem->assignFEFOBatch();
            }
        });

        static::created(function ($saleItem) {
            $saleItem->deductFromBatches();
            $saleItem->sale->calculateTotals();
        });
    }

    private function assignFEFOBatch()
    {
        $remainingQuantity = $this->quantity;
        $allocatedBatches = [];

        $availableBatches = PurchaseItem::where('item_id', $this->item_id)
            ->where('quantity', '>', 0)
            ->orderBy('expires_at', 'asc')
            ->orderBy('unit_cost', 'asc')
            ->get();

        foreach ($availableBatches as $purchaseItem) {
            if ($remainingQuantity <= 0) break;

            $allocatable = min($remainingQuantity, $purchaseItem->quantity);

            if ($allocatable > 0) {
                $allocatedBatches[] = [
                    'purchase_item_id' => $purchaseItem->id,
                    'batch_no' => $purchaseItem->batch_no,
                    'expires_at' => $purchaseItem->expires_at,
                    'quantity' => $allocatable,
                    'unit_cost' => $purchaseItem->unit_cost,
                    'sell_price' => $purchaseItem->sell_price
                ];

                $remainingQuantity -= $allocatable;
            }
        }

        if ($remainingQuantity === 0) {
            $firstBatch = $allocatedBatches[0];
            $this->batch_no = $firstBatch['batch_no'];
            $this->expires_at = $firstBatch['expires_at'];
            $this->purchase_item_id = $firstBatch['purchase_item_id'];
            $this->meta = ['allocations' => $allocatedBatches];
        } else {
            throw new \Exception("Insufficient stock. Available: " .
                ($this->quantity - $remainingQuantity) . ", Requested: " . $this->quantity);
        }
    }

    private function deductFromBatches()
    {
        if (isset($this->meta['allocations'])) {
            foreach ($this->meta['allocations'] as $allocation) {
                $purchaseItem = PurchaseItem::find($allocation['purchase_item_id']);
                $purchaseItem->decrement('quantity', $allocation['quantity']);

                StockMovement::create([
                    'item_id' => $this->item_id,
                    'purchase_item_id' => $purchaseItem->id,
                    'batch_no' => $purchaseItem->batch_no,
                    'movement_type' => 'sale',
                    'quantity_change' => -$allocation['quantity'],
                    'quantity_after' => $purchaseItem->quantity,
                    'unit_cost' => $purchaseItem->unit_cost,
                    'reference_id' => $this->sale_id,
                    'reference_type' => Sale::class,
                    'reason' => 'Sale deduction'
                ]);
            }
        } elseif ($this->purchase_item_id) {
            $purchaseItem = PurchaseItem::find($this->purchase_item_id);
            $purchaseItem->decrement('quantity', $this->quantity);

            StockMovement::create([
                'item_id' => $this->item_id,
                'purchase_item_id' => $purchaseItem->id,
                'batch_no' => $purchaseItem->batch_no,
                'movement_type' => 'sale',
                'quantity_change' => -$this->quantity,
                'quantity_after' => $purchaseItem->quantity,
                'unit_cost' => $purchaseItem->unit_cost,
                'reference_id' => $this->sale_id,
                'reference_type' => Sale::class,
                'reason' => 'Sale deduction'
            ]);
        }
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function getAverageCost(): float
    {
        if (isset($this->meta['allocations'])) {
            $totalCost = 0;
            foreach ($this->meta['allocations'] as $allocation) {
                $totalCost += $allocation['quantity'] * $allocation['unit_cost'];
            }
            return $totalCost / $this->quantity;
        }

        return $this->purchaseItem->unit_cost ?? 0;
    }

    public function getProfitMargin(): float
    {
        $averageCost = $this->getAverageCost();
        if ($averageCost > 0) {
            return (($this->unit_price - $averageCost) / $this->unit_price) * 100;
        }
        return 0;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
