<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasRoleBasedAccess;

class Sale extends Model
{
    use HasRoleBasedAccess;

    protected $fillable = [
        'reference_no', 'customer_id', 'sold_at', 'status', 'payment_status',
        'subtotal', 'total', 'total_amount', 'note', 'created_by'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4',
        'total_amount' => 'decimal:4'
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

            // Automatically set created_by if not set
            if (empty($sale->created_by) && auth()->check()) {
                $sale->created_by = auth()->id();
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('line_total');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal,
            'total_amount' => $subtotal
        ]);
    }
}
