<?php
// app/Http/Controllers/SaleController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Apply role-based filtering: Super Admin sees all, others see only their own
        $sales = Sale::forUser()->with('items')->orderBy('sold_at', 'desc')->paginate(20);

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
$items = Item::all();
        return view('sales.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sold_at' => 'sometimes|date',
            'status' => 'sometimes|string|in:draft,completed,cancelled',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.batch_no' => 'nullable|string',
            'items.*.expires_at' => 'nullable|date',
            'items.*.purchase_item_id' => 'nullable|exists:purchase_items,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the sale
            $sale = Sale::create([
                'sold_at' => $request->sold_at ?? now(),
                'status' => $request->status ?? 'completed',
                'note' => $request->note,
                'subtotal' => $request->subtotal,
                'total' => $request->total,
            ]);

            // Create sale items
            foreach ($request->items as $itemData) {
                $saleItem = new SaleItem([
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expires_at' => $itemData['expires_at'] ?? null,
                    'purchase_item_id' => $itemData['purchase_item_id'] ?? null,
                ]);

                $sale->items()->save($saleItem);
            }

            // Calculate totals (this will update based on actual line totals)
            $sale->calculateTotals();

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Sale created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to create sale: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sale = Sale::forUser()->with('items.item', 'items.purchaseItem')->find($id);

        if (!$sale) {
            return redirect()->route('sales.index')
                ->with('error', 'Sale not found');
        }

        // Additional authorization check
        $this->authorizeRecordAccess($sale);

        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sale = Sale::forUser()->with('items.item')->find($id);
        $items = Item::where('is_active', true)->get();

        if (!$sale) {
            return redirect()->route('sales.index')
                ->with('error', 'Sale not found');
        }

        // Additional authorization check
        $this->authorizeRecordAccess($sale);

        return view('sales.edit', compact('sale', 'items'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sale = Sale::forUser()->find($id);

        if (!$sale) {
            return redirect()->route('sales.index')
                ->with('error', 'Sale not found');
        }

        // Additional authorization check
        $this->authorizeRecordAccess($sale);

        $validator = Validator::make($request->all(), [
            'sold_at' => 'sometimes|date',
            'status' => 'sometimes|string|in:draft,completed,cancelled',
            'note' => 'nullable|string',
            'items' => 'sometimes|array|min:1',
            'items.*.id' => 'nullable|exists:sale_items,id', // For existing items
            'items.*.item_id' => 'required_with:items|exists:items,id',
            'items.*.quantity' => 'required_with:items|numeric|min:0.01',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
            'items.*.batch_no' => 'nullable|string',
            'items.*.expires_at' => 'nullable|date',
            'items.*.purchase_item_id' => 'nullable|exists:purchase_items,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update sale details
            $sale->update([
                'sold_at' => $request->has('sold_at') ? $request->sold_at : $sale->sold_at,
                'status' => $request->has('status') ? $request->status : $sale->status,
                'note' => $request->has('note') ? $request->note : $sale->note,
                'subtotal' => $request->subtotal,
                'total' => $request->total,
            ]);

            // Handle items if provided
            if ($request->has('items')) {
                $existingItemIds = [];

                foreach ($request->items as $itemData) {
                    if (isset($itemData['id'])) {
                        // Update existing item
                        $saleItem = SaleItem::where('id', $itemData['id'])
                                            ->where('sale_id', $sale->id)
                                            ->first();

                        if ($saleItem) {
                            $saleItem->update([
                                'item_id' => $itemData['item_id'],
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                                'batch_no' => $itemData['batch_no'] ?? $saleItem->batch_no,
                                'expires_at' => $itemData['expires_at'] ?? $saleItem->expires_at,
                                'purchase_item_id' => $itemData['purchase_item_id'] ?? $saleItem->purchase_item_id,
                            ]);

                            $existingItemIds[] = $saleItem->id;
                        }
                    } else {
                        // Create new item
                        $saleItem = new SaleItem([
                            'item_id' => $itemData['item_id'],
                            'quantity' => $itemData['quantity'],
                            'unit_price' => $itemData['unit_price'],
                            'batch_no' => $itemData['batch_no'] ?? null,
                            'expires_at' => $itemData['expires_at'] ?? null,
                            'purchase_item_id' => $itemData['purchase_item_id'] ?? null,
                        ]);

                        $sale->items()->save($saleItem);
                        $existingItemIds[] = $saleItem->id;
                    }
                }

                // Delete items not in the request
                $sale->items()->whereNotIn('id', $existingItemIds)->delete();
            }

            // Recalculate totals
            $sale->calculateTotals();

            DB::commit();

            return redirect()->route('sales.show', $sale->id)
                ->with('success', 'Sale updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to update sale: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $sale = Sale::forUser()->find($id);

        if (!$sale) {
            return redirect()->route('sales.index')
                ->with('error', 'Sale not found');
        }

        // Additional authorization check
        $this->authorizeRecordAccess($sale);

        try {
            DB::beginTransaction();

            // Delete all related sale items
            $sale->items()->delete();

            // Delete the sale
            $sale->delete();

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Sale deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('sales.index')
                ->with('error', 'Failed to delete sale: ' . $e->getMessage());
        }
    }

    /**
     * Get item details for AJAX requests
     */
    public function getItemDetails($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json([
            'id' => $item->id,
            'name' => $item->name,
            'code' => $item->code,
            'price' => $item->sell_price,
            'in_stock' => $item->in_stock
        ]);
    }

    /**
     * Print invoice for a sale
     */
    public function printInvoice($id)
    {
        $sale = Sale::forUser()->with(['saleItems.item', 'customer'])->findOrFail($id);
        
        // Additional authorization check
        $this->authorizeRecordAccess($sale);
        
        return view('sales.print', compact('sale'));
    }

    /**
     * Search items for sales (AJAX endpoint)
     */
    public function searchItems(Request $request)
    {
        $search = $request->get('search', '');
        $limit = $request->get('limit', 20);
        
        $query = Item::with(['category', 'unit'])
            ->where('active', true);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        $items = $query->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'barcode' => $item->barcode,
                    'category' => $item->category->name ?? 'N/A',
                    'unit' => $item->unit->name ?? 'N/A',
                    'price' => $item->selling_price ?? $item->default_sell_price ?? 0,
                    'stock' => $item->qty_on_hand ?? 0,
                ];
            });
        
        return response()->json($items);
    }
}
