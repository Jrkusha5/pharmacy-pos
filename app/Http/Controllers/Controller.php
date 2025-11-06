<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Check if user can access a record
     * Super Admin can access all, others can only access their own
     */
    protected function canAccessRecord($record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Super Admin can access all records
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Others can only access their own records
        return isset($record->created_by) && $record->created_by == $user->id;
    }

    /**
     * Abort if user cannot access the record
     */
    protected function authorizeRecordAccess($record, $message = 'You do not have permission to access this record.')
    {
        if (!$this->canAccessRecord($record)) {
            abort(403, $message);
        }
    }
}
