<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class GenerateNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:generate {--cleanup : Clean up old notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate notifications for expiring items and low stock alerts';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Generating notifications...');

        // Check expiring items
        $this->info('Checking expiring items...');
        $notificationService->checkExpiringItems();
        $this->info('✓ Expiry notifications generated');

        // Check low stock items
        $this->info('Checking low stock items...');
        $notificationService->checkLowStockItems();
        $this->info('✓ Low stock notifications generated');

        // Cleanup old notifications if requested
        if ($this->option('cleanup')) {
            $this->info('Cleaning up old notifications...');
            $notificationService->cleanupOldNotifications();
            $this->info('✓ Old notifications cleaned up');
        }

        $this->info('Notifications generation completed!');
        
        return Command::SUCCESS;
    }
}

