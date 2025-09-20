<?php
// app/Http/Controllers/PurchaseController.php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:purchase_view')->only(['index', 'show']);
        $this->middleware('permission:purchase_create')->only(['create', 'store']);
        $this->middleware('permission:purchase_edit')->only(['edit', 'update']);
        $this->middleware('permission:purchase_delete')->only(['destroy']);
    }
    
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'items.item']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('purchased_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('purchased_at', '<=', $request->end_date);
        }

        $purchases = $query->orderBy('purchased_at', 'desc')->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::with('category')->where('active', true)->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'items', 'categories'));
    }

    /**
     * Store a newly created purchase in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchased_at' => 'required|date',
            'payment_method' => 'required|in:cash,credit,bank_transfer,mobile_money',
            'paid_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date|required_if:payment_method,credit',
            'status' => 'required|in:draft,posted,void',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.sell_price' => 'required|numeric|min:0',
            'items.*.expires_at' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'purchased_at' => $validated['purchased_at'],
                'subtotal' => $subtotal,
                'tax' => 0,
                'total' => $subtotal,
                'paid_amount' => $validated['paid_amount'],
                'due_amount' => $subtotal - $validated['paid_amount'],
                'payment_method' => $validated['payment_method'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null
            ]);

            // Create purchase items
            foreach ($validated['items'] as $itemData) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'sell_price' => $itemData['sell_price'],
                    'expires_at' => $itemData['expires_at'] ?? null,
                    'line_total' => $itemData['quantity'] * $itemData['unit_cost']
                ]);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', 'Purchase created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating purchase: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.item', 'items.item.category', 'items.item.unit']);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit(Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            return redirect()->route('purchases.show', $purchase->id)
                ->with('error', 'Only draft purchases can be edited.');
        }

        $purchase->load('items.item');
        $suppliers = Supplier::orderBy('name')->get();
        $items = Item::with('category')->where('active', true)->orderBy('name')->get();

        return view('purchases.edit', compact('purchase', 'suppliers', 'items'));
    }

    /**
     * Update the specified purchase in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            return redirect()->route('purchases.show', $purchase->id)
                ->with('error', 'Only draft purchases can be edited.');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchased_at' => 'required|date',
            'payment_method' => 'required|in:cash,credit,bank_transfer,mobile_money',
            'paid_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date|required_if:payment_method,credit',
            'status' => 'required|in:draft,posted,void',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.sell_price' => 'required|numeric|min:0',
            'items.*.expires_at' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            // Delete existing items
            $purchase->items()->delete();

            // Calculate new totals
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_cost'];
            }

            // Update purchase
            $purchase->update([
                'supplier_id' => $validated['supplier_id'],
                'purchased_at' => $validated['purchased_at'],
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'paid_amount' => $validated['paid_amount'],
                'due_amount' => $subtotal - $validated['paid_amount'],
                'payment_method' => $validated['payment_method'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null
            ]);

            // Create new items
            foreach ($validated['items'] as $itemData) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'sell_price' => $itemData['sell_price'],
                    'expires_at' => $itemData['expires_at'] ?? null,
                    'line_total' => $itemData['quantity'] * $itemData['unit_cost']
                ]);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase->id)
                ->with('success', 'Purchase updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase from storage.
     */
    public function destroy(Purchase $purchase)
    {
        if ($purchase->status !== 'draft') {
            return redirect()->route('purchases.show', $purchase->id)
                ->with('error', 'Only draft purchases can be deleted.');
        }

        try {
            DB::beginTransaction();

            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting purchase: ' . $e->getMessage());
        }
    }

    /**
     * Update purchase status.
     */
    public function updateStatus(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,posted,void'
        ]);

        try {
            $purchase->update(['status' => $validated['status']]);

            return back()->with('success', 'Purchase status updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating status: ' . $e->getMessage());
        }
    }

    /**
     * Add payment to purchase.
     */
    public function addPayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $purchase->due_amount,
            'payment_method' => 'required|in:cash,credit,bank_transfer,mobile_money'
        ]);

        try {
            DB::beginTransaction();

            $purchase->increment('paid_amount', $validated['amount']);
            $purchase->decrement('due_amount', $validated['amount']);

            if ($purchase->due_amount <= 0) {
                $purchase->update(['payment_status' => 'paid']);
            } elseif ($purchase->paid_amount > 0) {
                $purchase->update(['payment_status' => 'partial']);
            }

            DB::commit();

            return back()->with('success', 'Payment added successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding payment: ' . $e->getMessage());
        }
    }

    /**
     * Get purchase items report.
     */
    public function itemsReport(Request $request)
    {
        $query = PurchaseItem::with(['purchase.supplier', 'item.category', 'item.unit']);

        // Filters
        if ($request->has('item_id') && $request->item_id != '') {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('supplier_id') && $request->supplier_id != '') {
            $query->whereHas('purchase', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereHas('purchase', function($q) use ($request) {
                $q->whereDate('purchased_at', '>=', $request->start_date);
            });
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereHas('purchase', function($q) use ($request) {
                $q->whereDate('purchased_at', '<=', $request->end_date);
            });
        }

        $items = $query->orderBy('created_at', 'desc')->paginate(25);
        $allItems = Item::where('active', true)->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.items-report', compact('items', 'allItems', 'suppliers'));
    }

    /**
     * Get batch-wise inventory report.
     */
    public function batchReport(Request $request)
    {
        $query = PurchaseItem::with(['item.category', 'item.unit', 'purchase.supplier'])
            ->where('quantity', '>', 0);

        if ($request->has('batch_no') && $request->batch_no != '') {
            $query->where('batch_no', 'like', "%{$request->batch_no}%");
        }

        if ($request->has('item_id') && $request->item_id != '') {
            $query->where('item_id', $request->item_id);
        }

        if ($request->has('expiring_soon') && $request->expiring_soon == '1') {
            $query->where('expires_at', '<=', now()->addDays(30))
                  ->where('expires_at', '>=', now());
        }

        if ($request->has('expired') && $request->expired == '1') {
            $query->where('expires_at', '<', now());
        }

        $batches = $query->orderBy('expires_at', 'asc')->paginate(25);
        $items = Item::where('active', true)->orderBy('name')->get();

        return view('purchases.batch-report', compact('batches', 'items'));
    }

    /**
     * Show form for stock adjustment.
     */
    public function showAdjustStock(PurchaseItem $purchaseItem)
    {
        return view('purchases.adjust-stock', compact('purchaseItem'));
    }

    /**
     * Adjust stock for a specific purchase item.
     */
    public function adjustStock(Request $request, PurchaseItem $purchaseItem)
    {
        $validated = $request->validate([
            'quantity_change' => 'required|integer',
            'reason' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $oldQuantity = $purchaseItem->quantity;
            $purchaseItem->quantity += $validated['quantity_change'];

            if ($purchaseItem->quantity < 0) {
                throw new \Exception('Quantity cannot be negative.');
            }

            $purchaseItem->save();

            // Record stock movement
            StockMovement::create([
                'item_id' => $purchaseItem->item_id,
                'purchase_item_id' => $purchaseItem->id,
                'batch_no' => $purchaseItem->batch_no,
                'movement_type' => 'adjustment',
                'quantity_change' => $validated['quantity_change'],
                'quantity_after' => $purchaseItem->quantity,
                'unit_cost' => $purchaseItem->unit_cost,
                'reference_id' => $purchaseItem->id,
                'reference_type' => PurchaseItem::class,
                'reason' => $validated['reason']
            ]);

            DB::commit();

            return redirect()->route('purchases.batch-report')
                ->with('success', 'Stock adjusted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adjusting stock: ' . $e->getMessage());
        }
    }
}
