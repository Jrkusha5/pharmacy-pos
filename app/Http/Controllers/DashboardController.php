<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleReversal;
use App\Models\StockBalance;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Items
        $totalItems = Item::count();

        // Total Purchases - Apply role-based filtering
        $totalPurchases = Purchase::forUser()->count();

        // Total Sales Amount (sum of all sales) - Apply role-based filtering
        $totalSalesAmount = Sale::forUser()
            ->where('status', 'completed')
            ->sum(DB::raw('COALESCE(total_amount, total, 0)'));

        // Total Sales Returns
        $totalSalesReturns = SaleReversal::count();

        // Stock Alerts - Items with low stock
        $lowStockItems = StockBalance::with(['item', 'purchaseItem.item'])
            ->whereHas('purchaseItem.item', function($query) {
                $query->whereNotNull('min_stock_level');
            })
            ->get()
            ->filter(function($stock) {
                $item = $stock->purchaseItem->item ?? null;
                if (!$item) return false;
                return $stock->quantity <= ($item->min_stock_level ?? 0);
            })
            ->take(10);

        // Expired Items - Items expiring soon or expired
        $expiredItems = DB::table('purchase_items')
            ->join('items', 'purchase_items.item_id', '=', 'items.id')
            ->where('purchase_items.expires_at', '<=', Carbon::now()->addMonths(3))
            ->whereNotNull('purchase_items.expires_at')
            ->select('items.name as item_name', 'purchase_items.batch_no as batch_number', 'purchase_items.expires_at as expiry_date')
            ->orderBy('purchase_items.expires_at', 'asc')
            ->take(10)
            ->get();

        // Monthly Sales Data for Chart - Apply role-based filtering
        $monthlySales = Sale::forUser()
            ->where('status', 'completed')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(COALESCE(total_amount, total, 0)) as total')
            )
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month', 'year')
            ->orderBy('month', 'asc')
            ->get();

        // Today's Summary - Apply role-based filtering
        $todaySales = Sale::forUser()
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum(DB::raw('COALESCE(total_amount, total, 0)'));

        $todayPurchases = Purchase::forUser()
            ->whereDate('created_at', Carbon::today())
            ->count();

        $todayReturns = SaleReversal::whereDate('created_at', Carbon::today())
            ->count();

        // Prepare chart data
        $chartLabels = [];
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartLabels[] = Carbon::create(null, $i, 1)->format('M');
            $monthData = $monthlySales->firstWhere('month', $i);
            $chartData[] = $monthData ? $monthData->total : 0;
        }

        return view('dashboard', compact(
            'totalItems',
            'totalPurchases',
            'totalSalesAmount',
            'totalSalesReturns',
            'lowStockItems',
            'expiredItems',
            'todaySales',
            'todayPurchases',
            'todayReturns',
            'chartLabels',
            'chartData'
        ));
    }
}
