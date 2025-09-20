<?php

// app/Services/InventoryService.php
namespace App\Services;

use App\Models\{Item,Batch,StockMovement,Sale,SaleItem,Purchase,PurchaseItem};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InventoryService
{
  /** Increases stock by creating/using a batch (on purchase). */
  public function receivePurchase(Purchase $purchase): void
  {
    DB::transaction(function () use ($purchase) {
      foreach ($purchase->items as $pi) {
        $batch = Batch::firstOrCreate(
          ['item_id'=>$pi->item_id, 'batch_no'=>$pi->batch_no],
          [
            'expires_at'=>$pi->expires_at,
            'cost_price'=>$pi->unit_cost,
            'sell_price'=>$pi->sell_price ?? $pi->item->batches()->latest()->value('sell_price') ?? $pi->unit_cost,
            'qty_on_hand'=>0,
            'status'=>'active'
          ]
        );
        $batch->increment('qty_on_hand', $pi->quantity);

        StockMovement::create([
          'item_id'=>$pi->item_id,
          'batch_id'=>$batch->id,
          'type'=>'purchase',
          'qty_in'=>$pi->quantity,
          'qty_out'=>0,
          'reference_type'=>Purchase::class,
          'reference_id'=>$purchase->id,
          'reason'=>'Purchase received'
        ]);
      }
    });
  }

  /** Deducts stock using FIFO across non-expired, active batches. */
  public function fulfillSale(Sale $sale, array $cartLines /* [ ['item_id'=>, 'qty'=>, 'unit_price'=>] ] */): void
  {
    DB::transaction(function () use ($sale, $cartLines) {
      $subtotal = 0;
      foreach ($cartLines as $line) {
        $item = Item::findOrFail($line['item_id']);
        $qtyToIssue = (int)$line['qty'];

        // FIFO batches: active, not expired, with stock
        $batches = $item->batches()
          ->where('status','active')
          ->where(function($q){ $q->whereNull('expires_at')->orWhere('expires_at','>=',now()); })
          ->where('qty_on_hand','>',0)
          ->orderBy('expires_at') // soonest to expire first (FEFO)
          ->orderBy('id')
          ->lockForUpdate()
          ->get();

        foreach ($batches as $batch) {
          if ($qtyToIssue <= 0) break;
          $take = min($qtyToIssue, $batch->qty_on_hand);

          $batch->decrement('qty_on_hand', $take);

          SaleItem::create([
            'sale_id'=>$sale->id,
            'item_id'=>$item->id,
            'batch_id'=>$batch->id,
            'quantity'=>$take,
            'unit_price'=>$line['unit_price'] ?? $batch->sell_price,
            'line_total'=> bcmul($line['unit_price'] ?? $batch->sell_price, $take, 4),
          ]);

          StockMovement::create([
            'item_id'=>$item->id,
            'batch_id'=>$batch->id,
            'type'=>'sale',
            'qty_in'=>0,
            'qty_out'=>$take,
            'reference_type'=>Sale::class,
            'reference_id'=>$sale->id,
            'reason'=>'Sale issued'
          ]);

          $subtotal += ($line['unit_price'] ?? $batch->sell_price) * $take;
          $qtyToIssue -= $take;
        }

        if ($qtyToIssue > 0) {
          throw new \RuntimeException("Insufficient stock for item {$item->name}");
        }
      }

      $sale->update([
        'subtotal'=>$subtotal,
        'tax'=>0,
        'discount'=>0,
        'total'=>$subtotal
      ]);
    });
  }

  /** Reverses (restores) stock for a sale, keeping full audit. */
  public function reverseSale(Sale $sale, string $reason = 'Sale reversal'): void
  {
    if ($sale->status === 'reversed') return;

    DB::transaction(function () use ($sale, $reason) {
      foreach ($sale->items as $si) {
        if ($si->batch) {
          $si->batch->increment('qty_on_hand', $si->quantity);
        }
        StockMovement::create([
          'item_id'=>$si->item_id,
          'batch_id'=>$si->batch_id,
          'type'=>'reversal',
          'qty_in'=>$si->quantity,
          'qty_out'=>0,
          'reference_type'=>Sale::class,
          'reference_id'=>$sale->id,
          'reason'=>$reason
        ]);
      }
      $sale->update(['status'=>'reversed']);
    });
  }
}
