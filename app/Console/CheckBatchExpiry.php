<?php

// app/Console/Commands/CheckBatchExpiry.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batch;
use App\Notifications\BatchExpiryAlert;
use App\Models\User; // choose recipients (e.g., admins)

class CheckBatchExpiry extends Command {
  protected $signature = 'pharmacy:check-expiry {--threshold=30}';
  protected $description = 'Notify when batches are expired or close to expiry';

  public function handle(): int {
    $threshold = (int)$this->option('threshold');
    $today = now()->startOfDay();
    $limit = now()->addDays($threshold)->endOfDay();

    $batches = Batch::with('item')
      ->where('status','active')
      ->whereNotNull('expires_at')
      ->whereBetween('expires_at', [$today, $limit])
      ->orWhere(function($q) use ($today){
        $q->where('expires_at','<',$today)->where('status','active');
      })
      ->get();

    $admins = User::role('admin')->get(); // or however you select recipients

    foreach ($batches as $b) {
      $daysLeft = $b->expires_at?->diffInDays($today, false) ?? 0;
      foreach ($admins as $admin) $admin->notify(new BatchExpiryAlert($b, $daysLeft));
    }

    $this->info("Checked ".count($batches)." batches.");
    return self::SUCCESS;
  }
}

