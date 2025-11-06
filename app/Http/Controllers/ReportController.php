<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\StockBalance;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function sales(Request $request)
    {
        // Apply role-based filtering: Super Admin sees all, others see only their own
        $query = Sale::forUser()->with('saleItems.item')->where('status', 'completed');

        // Date filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Default to current month if no dates provided
        if (!$request->has('start_date') && !$request->has('end_date')) {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        $sales = $query->latest()->paginate(20);

        // Summary statistics
        $totalSales = $query->sum(DB::raw('COALESCE(total_amount, total, 0)'));
        $totalCount = $query->count();
        $averageSale = $totalCount > 0 ? $totalSales / $totalCount : 0;

        return view('reports.sales', compact('sales', 'totalSales', 'totalCount', 'averageSale'));
    }

    public function purchases(Request $request)
    {
        // Apply role-based filtering: Super Admin sees all, others see only their own
        $query = Purchase::forUser()->with('purchaseItems.item');

        // Date filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Default to current month if no dates provided
        if (!$request->has('start_date') && !$request->has('end_date')) {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        $purchases = $query->latest()->paginate(20);

        // Summary statistics
        $totalPurchases = $query->sum(DB::raw('COALESCE(total_amount, total, 0)'));
        $totalCount = $query->count();
        $averagePurchase = $totalCount > 0 ? $totalPurchases / $totalCount : 0;

        return view('reports.purchases', compact('purchases', 'totalPurchases', 'totalCount', 'averagePurchase'));
    }

    public function inventory(Request $request)
    {
        $query = StockBalance::with('item.category', 'item.unit');

        // Search filter
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category_id')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Low stock filter
        if ($request->has('low_stock') && $request->low_stock) {
            $query->whereHas('item', function($q) {
                $q->whereNotNull('min_stock_level');
            })->get()->filter(function($stock) {
                return $stock->quantity <= ($stock->item->min_stock_level ?? 0);
            });
        }

        $inventory = $query->latest()->paginate(20);

        // Summary
        $totalItems = StockBalance::count();
        $totalValue = StockBalance::with('item')->get()->sum(function($stock) {
            return $stock->quantity * ($stock->item->selling_price ?? 0);
        });

        return view('reports.inventory', compact('inventory', 'totalItems', 'totalValue'));
    }

    public function profitLoss(Request $request)
    {
        // Date filters
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        // Sales revenue - Apply role-based filtering
        $salesRevenue = Sale::forUser()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(total_amount, total, 0)'));

        // Purchase costs - Apply role-based filtering
        $purchaseCosts = Purchase::forUser()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum(DB::raw('COALESCE(total_amount, total, 0)'));

        // Calculate profit
        $grossProfit = $salesRevenue - $purchaseCosts;
        $profitMargin = $salesRevenue > 0 ? ($grossProfit / $salesRevenue) * 100 : 0;

        // Monthly breakdown
        $monthlyData = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthSales = Sale::forUser()
                ->where('status', 'completed')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum(DB::raw('COALESCE(total_amount, total, 0)'));

            $monthPurchases = Purchase::forUser()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum(DB::raw('COALESCE(total_amount, total, 0)'));

            $monthlyData[] = [
                'month' => $current->format('M Y'),
                'sales' => $monthSales,
                'purchases' => $monthPurchases,
                'profit' => $monthSales - $monthPurchases
            ];

            $current->addMonth();
        }

        return view('reports.profit-loss', compact(
            'salesRevenue',
            'purchaseCosts',
            'grossProfit',
            'profitMargin',
            'monthlyData',
            'startDate',
            'endDate'
        ));
    }

    public function expiry(Request $request)
    {
        $query = PurchaseItem::with('item')
            ->whereNotNull('expires_at');

        // Expiry filter
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'expired':
                    $query->where('expires_at', '<', Carbon::now());
                    break;
                case 'expiring_soon':
                    $query->whereBetween('expires_at', [Carbon::now(), Carbon::now()->addMonths(3)]);
                    break;
                case 'expiring_this_month':
                    $query->whereMonth('expires_at', Carbon::now()->month)
                          ->whereYear('expires_at', Carbon::now()->year);
                    break;
            }
        }

        $expiringItems = $query->orderBy('expires_at', 'asc')->paginate(20);

        // Summary
        $expiredCount = PurchaseItem::where('expires_at', '<', Carbon::now())->count();
        $expiringSoonCount = PurchaseItem::whereBetween('expires_at', [Carbon::now(), Carbon::now()->addMonths(3)])->count();

        return view('reports.expiry', compact('expiringItems', 'expiredCount', 'expiringSoonCount'));
    }

    public function export($type, Request $request)
    {
        // This would typically export to PDF or Excel
        // For now, we'll redirect to the report view
        return redirect()->route("reports.{$type}", $request->all())
            ->with('info', 'Export functionality will be implemented with PDF/Excel libraries');
    }
}

