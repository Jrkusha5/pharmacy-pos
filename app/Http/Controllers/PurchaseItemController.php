<?php

namespace App\Http\Controllers;

use App\Models\PurchaseItem;
use App\Models\Purchase;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PurchaseItemController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('permission:purchase_item_view')->only(['index', 'show']);
        $this->middleware('permission:purchase_item_create')->only(['create', 'store']);
        $this->middleware('permission:purchase_item_edit')->only(['edit', 'update']);
        $this->middleware('permission:purchase_item_delete')->only(['destroy']);
    }

  public function index()
{
    $purchaseItems = PurchaseItem::with(['purchase', 'item', 'batch'])->latest()->paginate(10);
    $purchases = Purchase::latest()->get();
    $items = Item::active()->get(); // Add this line
    $batches = Batch::latest()->get(); // Add this line

    return view('purchase-items.index', compact('purchaseItems', 'purchases', 'items', 'batches'));
}

    public function create()
    {
        $purchases = Purchase::latest()->get();
        $items = Item::active()->get();
        $batches = Batch::latest()->get();

        return view('purchase-items.create', compact('purchases', 'items', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'item_id' => 'required|exists:items,id',
            'batch_id' => 'nullable|exists:batches,id',
            'batch_no' => 'required|string|max:100',
            'expires_at' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'line_total' => 'required|numeric|min:0',
        ]);

        PurchaseItem::create($request->all());

        return redirect()->route('purchase-items.index')
            ->with('success', 'Purchase item created successfully.');
    }

    public function show(PurchaseItem $purchaseItem)
    {
        $purchaseItem->load(['purchase', 'item', 'batch']);
        return view('purchase-items.show', compact('purchaseItem'));
    }

    public function edit(PurchaseItem $purchaseItem)
    {
        $purchases = Purchase::latest()->get();
        $items = Item::active()->get();
        $batches = Batch::latest()->get();

        return view('purchase-items.edit', compact('purchaseItem', 'purchases', 'items', 'batches'));
    }

    public function update(Request $request, PurchaseItem $purchaseItem)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'item_id' => 'required|exists:items,id',
            'batch_id' => 'nullable|exists:batches,id',
            'batch_no' => 'required|string|max:100',
            'expires_at' => 'required|date',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'line_total' => 'required|numeric|min:0',
        ]);

        $purchaseItem->update($request->all());

        return redirect()->route('purchase-items.index')
            ->with('success', 'Purchase item updated successfully');
    }

    public function destroy(PurchaseItem $purchaseItem)
    {
        $purchaseItem->delete();

        return redirect()->route('purchase-items.index')
            ->with('success', 'Purchase item deleted successfully');
    }
}
