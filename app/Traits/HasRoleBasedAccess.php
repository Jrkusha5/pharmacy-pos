<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasRoleBasedAccess
{
    /**
     * Check if the current user is a Super Admin
     */
    public static function isSuperAdmin(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('Super Admin');
    }

    /**
     * Scope to filter records based on user role
     * Super Admin sees all, others see only their own records
     */
    public function scopeForUser(Builder $query, $userId = null): Builder
    {
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0'); // No access if not authenticated
        }

        // Super Admin can see all data
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        // Other users see only their own data
        $userId = $userId ?? $user->id;
        return $query->where('created_by', $userId);
    }

    /**
     * Get the user ID for the current authenticated user
     */
    protected static function getCurrentUserId(): ?int
    {
        return auth()->id();
    }
}

