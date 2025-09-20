<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'reference_no', 'sold_at', 'status', 'subtotal', 'total', 'note'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->reference_no)) {
                $latestId = self::max('id') + 1;
                $sale->reference_no = 'SALE-' . now()->format('Y') . '-' . str_pad($latestId, 5, '0', STR_PAD_LEFT);
            }
            if (empty($sale->sold_at)) {
                $sale->sold_at = now();
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('line_total');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal
        ]);
    }
}
