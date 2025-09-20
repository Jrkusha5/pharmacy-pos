<?php

// app/Console/Commands/CheckReorderLevels.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Notifications\ReorderAlert;
use App\Models\User;

class CheckReorderLevels extends Command {
  protected $signature = 'pharmacy:check-reorder';
  protected $description = 'Notify when stock on hand is at/below reorder level';

  public function handle(): int {
    $items = Item::withSum('batches as on_hand','qty_on_hand')->get();
    $admins = User::role('admin')->get();

    foreach ($items as $it) {
      $on = (int)($it->on_hand ?? 0);
      if ($it->reorder_level > 0 && $on <= $it->reorder_level) {
        foreach ($admins as $admin) $admin->notify(new ReorderAlert($it, $on));
      }
    }
    $this->info('Reorder checks complete.');
    return self::SUCCESS;
  }
}

