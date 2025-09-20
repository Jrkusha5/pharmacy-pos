<?php
// app/Models/Purchase.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'cis_code','supplier_id', 'invoice_no', 'purchased_at', 'subtotal', 'total',
        'paid_amount', 'due_amount', 'payment_status', 'payment_method',
        'due_date', 'status', 'note'
    ];

    protected $casts = [
        'purchased_at' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:4',
        'total' => 'decimal:4',
        'paid_amount' => 'decimal:4',
        'due_amount' => 'decimal:4'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->invoice_no)) {
                $latestId = self::max('id') + 1;
                $purchase->invoice_no = 'INV-' . now()->format('Y') . '-' . str_pad($latestId, 6, '0', STR_PAD_LEFT);
            }

            if ($purchase->paid_amount > 0) {
                $purchase->due_amount = $purchase->total - $purchase->paid_amount;
                $purchase->payment_status = $purchase->due_amount > 0 ? 'partial' : 'paid';
            } else {
                $purchase->due_amount = $purchase->total;
                $purchase->payment_status = 'pending';
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
