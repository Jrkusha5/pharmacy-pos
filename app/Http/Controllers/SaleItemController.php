<?php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class SaleItemController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('permission:sale_item_view')->only(['index', 'show']);
        $this->middleware('permission:sale_item_create')->only(['create', 'store']);
        $this->middleware('permission:sale_item_edit')->only(['edit', 'update']);
        $this->middleware('permission:sale_item_delete')->only(['destroy']);
    }

    public function index()
    {
        $saleItems = SaleItem::with(['sale', 'item', 'batch'])->latest()->paginate(10);
        return view('sale_items.index', compact('saleItems'));
    }

    public function create()
    {
        $sales = Sale::latest()->get();
        $items = Item::active()->get();
        $batches = Batch::where('qty_on_hand', '>', 0)->get();

        return view('sale_items.create', compact('sales', 'items', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'item_id' => 'required|exists:items,id',
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'line_total' => 'required|numeric|min:0',
        ]);

        SaleItem::create($request->all());

        return redirect()->route('sale_items.index')
            ->with('success', 'Sale item created successfully.');
    }

    public function show(SaleItem $saleItem)
    {
        $saleItem->load(['sale', 'item', 'batch']);
        return view('sale_items.show', compact('saleItem'));
    }

    public function edit(SaleItem $saleItem)
    {
        $sales = Sale::latest()->get();
        $items = Item::active()->get();
        $batches = Batch::where('qty_on_hand', '>', 0)->get();

        return view('sale_items.edit', compact('saleItem', 'sales', 'items', 'batches'));
    }

    public function update(Request $request, SaleItem $saleItem)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'item_id' => 'required|exists:items,id',
            'batch_id' => 'required|exists:batches,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'line_total' => 'required|numeric|min:0',
        ]);

        $saleItem->update($request->all());

        return redirect()->route('sale_items.index')
            ->with('success', 'Sale item updated successfully');
    }

    public function destroy(SaleItem $saleItem)
    {
        $saleItem->delete();

        return redirect()->route('sale_items.index')
            ->with('success', 'Sale item deleted successfully');
    }
}
