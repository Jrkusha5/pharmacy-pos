<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasRoleBasedAccess;

class Customer extends Model
{
    use HasRoleBasedAccess;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'customer_type', // 'retail', 'wholesale', 'institutional'
        'credit_limit',
        'payment_terms',
        'tax_id',
        'notes',
        'status', // 'active', 'inactive'
        'created_by'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
    ];

    /**
     * Get all sales for this customer
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get total purchases amount
     */
    public function getTotalPurchasesAttribute()
    {
        return $this->sales()->where('status', 'completed')->sum('total_amount');
    }

    /**
     * Get outstanding balance
     */
    public function getOutstandingBalanceAttribute()
    {
        return $this->sales()
            ->where('status', 'completed')
            ->where('payment_status', '!=', 'paid')
            ->sum('total_amount');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            // Automatically set created_by if not set
            if (empty($customer->created_by) && auth()->check()) {
                $customer->created_by = auth()->id();
            }
        });
    }
}

