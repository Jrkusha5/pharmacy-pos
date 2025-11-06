<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasRoleBasedAccess;

class StockMovement extends Model
{
    use HasRoleBasedAccess;

    protected $fillable = [
        'cis_code', 'item_id', 'purchase_item_id',
        'movement_type', 'quantity', 'reference_type', 'reference_id', 'note', 'created_by'
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($stockMovement) {
            // Automatically set created_by if not set
            if (empty($stockMovement->created_by) && auth()->check()) {
                $stockMovement->created_by = auth()->id();
            }
        });
    }
}
