<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBalance extends Model
{
    protected $fillable = ['purchase_item_id', 'quantity'];

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function item()
    {
        return $this->purchaseItem->item;
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->purchaseItem->item->reorder_level;
    }
}
