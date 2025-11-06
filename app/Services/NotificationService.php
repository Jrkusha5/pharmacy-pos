<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\PurchaseItem;
use App\Models\StockBalance;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Check and create notifications for expired/expiring items
     */
    public function checkExpiringItems(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Get items that are expiring or expired for this user's purchases
            $expiringItems = PurchaseItem::whereHas('purchase', function($query) use ($user) {
                // Super Admin sees all, others see only their own
                if (!$user->hasRole('Super Admin')) {
                    $query->where('created_by', $user->id);
                }
            })
            ->whereNotNull('expires_at')
            ->where('quantity', '>', 0)
            ->with('item', 'purchase')
            ->get();

            foreach ($expiringItems as $purchaseItem) {
                $expiryDate = Carbon::parse($purchaseItem->expires_at);
                $daysUntilExpiry = $expiryDate->diffInDays(now(), false);
                
                // Check if notification already exists
                $existingNotification = Notification::where('user_id', $user->id)
                    ->where('item_id', $purchaseItem->item_id)
                    ->where('type', 'expiry')
                    ->whereNull('read_at')
                    ->where('data->purchase_item_id', $purchaseItem->id)
                    ->first();

                if ($existingNotification) {
                    // Update existing notification if days changed significantly
                    $existingDays = $existingNotification->data['days_until_expiry'] ?? 999;
                    if (abs($existingDays - $daysUntilExpiry) > 1) {
                        $this->updateExpiryNotification($existingNotification, $purchaseItem, $daysUntilExpiry, $expiryDate);
                    }
                    continue;
                }

                // Create notification based on expiry status
                if ($daysUntilExpiry < 0) {
                    // Already expired
                    $this->createExpiryNotification($user, $purchaseItem, 'expired', abs($daysUntilExpiry), $expiryDate);
                } elseif ($daysUntilExpiry <= 7) {
                    // Expiring within 7 days - critical
                    $this->createExpiryNotification($user, $purchaseItem, 'critical', $daysUntilExpiry, $expiryDate);
                } elseif ($daysUntilExpiry <= 30) {
                    // Expiring within 30 days - warning
                    $this->createExpiryNotification($user, $purchaseItem, 'warning', $daysUntilExpiry, $expiryDate);
                } elseif ($daysUntilExpiry <= 90) {
                    // Expiring within 90 days - info
                    $this->createExpiryNotification($user, $purchaseItem, 'info', $daysUntilExpiry, $expiryDate);
                }
            }
        }
    }

    /**
     * Create expiry notification
     */
    private function createExpiryNotification(User $user, PurchaseItem $purchaseItem, string $level, int $days, Carbon $expiryDate): void
    {
        $item = $purchaseItem->item;
        $isExpired = $days < 0;
        
        $title = $isExpired 
            ? "Item Expired: {$item->name}"
            : "Item Expiring Soon: {$item->name}";
        
        $message = $isExpired
            ? "The item '{$item->name}' (Batch: {$purchaseItem->batch_no}) expired {$days} day(s) ago on {$expiryDate->format('M d, Y')}. Quantity: {$purchaseItem->quantity}"
            : "The item '{$item->name}' (Batch: {$purchaseItem->batch_no}) will expire in {$days} day(s) on {$expiryDate->format('M d, Y')}. Quantity: {$purchaseItem->quantity}";

        Notification::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'type' => 'expiry',
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'data' => [
                'purchase_item_id' => $purchaseItem->id,
                'batch_no' => $purchaseItem->batch_no,
                'expiry_date' => $expiryDate->toDateString(),
                'days_until_expiry' => $days,
                'quantity' => $purchaseItem->quantity,
                'is_expired' => $isExpired,
            ],
        ]);
    }

    /**
     * Update existing expiry notification
     */
    private function updateExpiryNotification(Notification $notification, PurchaseItem $purchaseItem, int $days, Carbon $expiryDate): void
    {
        $item = $purchaseItem->item;
        $isExpired = $days < 0;
        
        $level = $isExpired ? 'critical' : ($days <= 7 ? 'critical' : ($days <= 30 ? 'warning' : 'info'));
        
        $title = $isExpired 
            ? "Item Expired: {$item->name}"
            : "Item Expiring Soon: {$item->name}";
        
        $message = $isExpired
            ? "The item '{$item->name}' (Batch: {$purchaseItem->batch_no}) expired {$days} day(s) ago on {$expiryDate->format('M d, Y')}. Quantity: {$purchaseItem->quantity}"
            : "The item '{$item->name}' (Batch: {$purchaseItem->batch_no}) will expire in {$days} day(s) on {$expiryDate->format('M d, Y')}. Quantity: {$purchaseItem->quantity}";

        $notification->update([
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'data' => array_merge($notification->data ?? [], [
                'days_until_expiry' => $days,
                'quantity' => $purchaseItem->quantity,
                'is_expired' => $isExpired,
            ]),
        ]);
    }

    /**
     * Check and create notifications for low stock items
     */
    public function checkLowStockItems(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Get low stock items for this user
            $lowStockItems = StockBalance::whereHas('purchaseItem.purchase', function($query) use ($user) {
                // Super Admin sees all, others see only their own
                if (!$user->hasRole('Super Admin')) {
                    $query->where('created_by', $user->id);
                }
            })
            ->with(['purchaseItem.item', 'purchaseItem.purchase'])
            ->get()
            ->filter(function($stock) {
                $item = $stock->purchaseItem->item ?? null;
                if (!$item) return false;
                
                $minStockLevel = $item->min_stock_level ?? $item->reorder_level ?? 0;
                return $stock->quantity <= $minStockLevel;
            });

            foreach ($lowStockItems as $stock) {
                $item = $stock->purchaseItem->item;
                $minStockLevel = $item->min_stock_level ?? $item->reorder_level ?? 0;
                
                // Check if notification already exists
                $existingNotification = Notification::where('user_id', $user->id)
                    ->where('item_id', $item->id)
                    ->where('type', 'low_stock')
                    ->whereNull('read_at')
                    ->where('data->stock_balance_id', $stock->id)
                    ->first();

                if ($existingNotification) {
                    // Update if stock level changed significantly
                    $existingQuantity = $existingNotification->data['current_quantity'] ?? 0;
                    if (abs($existingQuantity - $stock->quantity) > 0) {
                        $this->updateLowStockNotification($existingNotification, $stock, $item, $minStockLevel);
                    }
                    continue;
                }

                // Determine notification level
                $level = $stock->quantity == 0 ? 'critical' : ($stock->quantity <= ($minStockLevel * 0.5) ? 'critical' : 'warning');

                $this->createLowStockNotification($user, $stock, $item, $minStockLevel, $level);
            }
        }
    }

    /**
     * Create low stock notification
     */
    private function createLowStockNotification(User $user, StockBalance $stock, Item $item, int $minStockLevel, string $level): void
    {
        $title = $stock->quantity == 0 
            ? "Out of Stock: {$item->name}"
            : "Low Stock Alert: {$item->name}";
        
        $message = $stock->quantity == 0
            ? "The item '{$item->name}' is out of stock. Minimum stock level: {$minStockLevel}"
            : "The item '{$item->name}' is running low. Current stock: {$stock->quantity}, Minimum stock level: {$minStockLevel}";

        Notification::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'type' => 'low_stock',
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'data' => [
                'stock_balance_id' => $stock->id,
                'current_quantity' => $stock->quantity,
                'min_stock_level' => $minStockLevel,
                'is_out_of_stock' => $stock->quantity == 0,
            ],
        ]);
    }

    /**
     * Update existing low stock notification
     */
    private function updateLowStockNotification(Notification $notification, StockBalance $stock, Item $item, int $minStockLevel): void
    {
        $level = $stock->quantity == 0 ? 'critical' : ($stock->quantity <= ($minStockLevel * 0.5) ? 'critical' : 'warning');
        
        $title = $stock->quantity == 0 
            ? "Out of Stock: {$item->name}"
            : "Low Stock Alert: {$item->name}";
        
        $message = $stock->quantity == 0
            ? "The item '{$item->name}' is out of stock. Minimum stock level: {$minStockLevel}"
            : "The item '{$item->name}' is running low. Current stock: {$stock->quantity}, Minimum stock level: {$minStockLevel}";

        $notification->update([
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'data' => array_merge($notification->data ?? [], [
                'current_quantity' => $stock->quantity,
                'is_out_of_stock' => $stock->quantity == 0,
            ]),
        ]);
    }

    /**
     * Clean up old read notifications (older than 30 days)
     */
    public function cleanupOldNotifications(): void
    {
        Notification::whereNotNull('read_at')
            ->where('read_at', '<', Carbon::now()->subDays(30))
            ->delete();
    }
}

