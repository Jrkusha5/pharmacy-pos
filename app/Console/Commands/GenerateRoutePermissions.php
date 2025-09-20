<?php

// app/Console/Commands/GenerateRoutePermissions.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Route;

class GenerateRoutePermissions extends Command
{
protected $signature = 'permission:create-permissions-route';
    protected $description = 'Generate permissions for all routes';

    public function handle()
    {
        $routes = Route::getRoutes()->getRoutes();

        foreach ($routes as $route) {
            $name = $route->getName();

            if ($name && !Permission::where('name', $name)->exists()) {
                Permission::create(['name' => $name]);
                $this->info("Permission created: $name");
            }
        }

        $this->info('All route permissions generated successfully');
    }
}
