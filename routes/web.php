<?php

// routes/web.php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Units
    Route::resource('units', UnitController::class);

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Items
    Route::resource('items', ItemController::class);

    // Batches
    Route::resource('batches', BatchController::class);

    // Purchases
    Route::resource('purchases', PurchaseController::class);
    Route::post('purchases/{purchase}/update-status', [PurchaseController::class, 'updateStatus'])->name('purchases.update-status');
    Route::post('purchases/{purchase}/add-payment', [PurchaseController::class, 'addPayment'])->name('purchases.add-payment');
    Route::get('purchases/items/report', [PurchaseController::class, 'itemsReport'])->name('purchases.items-report');
    Route::get('purchases/batch/report', [PurchaseController::class, 'batchReport'])->name('purchases.batch-report');
    Route::get('purchases/{purchaseItem}/adjust-stock', [PurchaseController::class, 'showAdjustStock'])->name('purchases.show-adjust-stock');
    Route::post('purchases/{purchaseItem}/adjust-stock', [PurchaseController::class, 'adjustStock'])->name('purchases.adjust-stock');
    Route::get('purchases/{purchase}/print-invoice', [PurchaseController::class, 'printInvoice'])->name('purchases.print-invoice');
    Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
    Route::get('purchases/statistics', [PurchaseController::class, 'statistics'])->name('purchases.statistics');

    // Purchase Items
    Route::resource('purchase-items', PurchaseItemController::class);

    // Sales
    Route::resource('sales', SaleController::class);
    Route::get('sales/{sale}/print', [SaleController::class, 'printInvoice'])->name('sales.print');

    // Sale Items
    Route::resource('sale-items', SaleItemController::class);

    // Stock Movements
    Route::resource('stock_movements', StockMovementController::class);

    // Customers
    Route::resource('customers', CustomerController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/expiry', [ReportController::class, 'expiry'])->name('expiry');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Roles and Permissions
    Route::resource('roles', RoleController::class)->middleware('permission:role_management');
    Route::resource('permissions', PermissionController::class)->middleware('permission:permission_management');
});
