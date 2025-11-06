<?php
// app/Http/Controllers/ItemController.php
namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ItemController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('permission:item_view')->only(['index', 'show']);
        $this->middleware('permission:item_create')->only(['create', 'store']);
        $this->middleware('permission:item_edit')->only(['edit', 'update']);
        $this->middleware('permission:item_delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by active status
        if ($request->has('active') && $request->active != '') {
            $query->where('active', $request->active);
        }

        $items = $query->orderBy('name')->paginate(20);
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('items.index', compact('items', 'categories', 'units', 'suppliers'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('items.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'sku' => 'nullable|string|max:100|unique:items,sku',
            'default_sell_price' => 'nullable|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::create($validated);

            DB::commit();

            return redirect()->route('items.show', $item->id)
                ->with('success', 'Item created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating item: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item)
    {
        $item->load(['category', 'unit', 'purchaseItems', 'saleItems' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }, 'stockMovements' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        $batches = $item->getAllBatchesStock();
        $expiringBatches = $item->getExpiringBatches(30);
        $lowStockBatches = $item->getLowStockBatches();

        return view('items.show', compact('item', 'batches', 'expiringBatches', 'lowStockBatches'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();

        return view('items.edit', compact('item', 'categories', 'units'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'sku' => 'nullable|string|max:100|unique:items,sku,' . $item->id,
            'default_sell_price' => 'nullable|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $item->update($validated);

            DB::commit();

            return redirect()->route('items.show', $item->id)
                ->with('success', 'Item updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating item: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item)
    {
        try {
            DB::beginTransaction();

            // Check if item has any transactions before deleting
            if ($item->purchaseItems()->exists() || $item->saleItems()->exists()) {
                return back()->with('error', 'Cannot delete item with existing transactions.');
            }

            $item->delete();

            DB::commit();

            return redirect()->route('items.index')
                ->with('success', 'Item deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting item: ' . $e->getMessage());
        }
    }

    /**
     * Show item inventory report.
     */
    public function inventoryReport()
    {
        $items = Item::with(['purchaseItems' => function($query) {
            $query->where('quantity', '>', 0);
        }])->whereHas('purchaseItems', function($query) {
            $query->where('quantity', '>', 0);
        })->orderBy('name')->get();

        return view('items.inventory-report', compact('items'));
    }

    /**
     * Show low stock items.
     */
    public function lowStock()
    {
        $items = Item::with(['purchaseItems' => function($query) {
            $query->where('quantity', '>', 0);
        }])->get()->filter(function($item) {
            return $item->needsReorder() && $item->qty_on_hand > 0;
        });

        return view('items.low-stock', compact('items'));
    }

    /**
     * Show expiring items.
     */
    public function expiringSoon()
    {
        $items = Item::with(['purchaseItems' => function($query) {
            $query->where('quantity', '>', 0)
                  ->where('expires_at', '<=', now()->addDays(30))
                  ->orderBy('expires_at', 'asc');
        }])->whereHas('purchaseItems', function($query) {
            $query->where('quantity', '>', 0)
                  ->where('expires_at', '<=', now()->addDays(30));
        })->get();

        return view('items.expiring-soon', compact('items'));
    }

    /**
     * Adjust stock for a specific batch.
     */
    public function adjustStock(Request $request, Item $item)
    {
        $validated = $request->validate([
            'batch_no' => 'required|string|max:100',
            'quantity_change' => 'required|integer',
            'reason' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $item->adjustStock(
                $validated['batch_no'],
                $validated['quantity_change'],
                $validated['reason']
            );

            DB::commit();

            return back()->with('success', 'Stock adjusted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adjusting stock: ' . $e->getMessage());
        }
    }
}
