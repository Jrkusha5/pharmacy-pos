<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'active'
    ];

    // Option 1: Remove the type hint (recommended for simplicity)
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }



    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    
}
