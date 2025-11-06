<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Item;
use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class StockMovementController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('permission:stock_movement_view')->only(['index', 'show']);
        $this->middleware('permission:stock_movement_create')->only(['create', 'store']);
        $this->middleware('permission:stock_movement_edit')->only(['edit', 'update']);
        $this->middleware('permission:stock_movement_delete')->only(['destroy']);
    }

    public function index()
{
    // Apply role-based filtering: Super Admin sees all, others see only their own
    $stockMovements = StockMovement::forUser()->with(['item', 'batch'])->latest()->paginate(10);
    $items = Item::active()->get();
    $batches = Batch::latest()->get();
    
    return view('stock_movements.index', compact('stockMovements', 'items', 'batches'));
}

    public function create()
    {
        $items = Item::active()->get();
        $batches = Batch::where('qty_on_hand', '>', 0)->get();

        return view('stock_movements.create', compact('items', 'batches'));
    }

 // In your controller store method
public function store(Request $request)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'batch_id' => 'nullable|exists:batches,id',
        'type' => 'required|string|max:50',
        'qty_in' => 'nullable|integer|min:0',
        'qty_out' => 'nullable|integer|min:0',
        'reference_type' => 'nullable|string|max:255',
        'reference_id' => 'nullable|integer',
        'reason' => 'nullable|string',
    ]);

    // Ensure at least one quantity field is provided
    if (empty($request->qty_in) && empty($request->qty_out)) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['qty_in' => 'Either quantity in or quantity out must be provided.']);
    }

    // Set default values for null quantities
    $data = $request->all();
    $data['qty_in'] = $data['qty_in'] ?? 0;
    $data['qty_out'] = $data['qty_out'] ?? 0;

    // Map simple types to class names if needed
    if ($data['reference_type']) {
        $typeMap = [
            'purchase' => \App\Models\Purchase::class,
            'sale' => \App\Models\Sale::class,
            'adjustment' => \App\Models\Adjustment::class,
            // Add other mappings as needed
        ];
        
        $data['reference_type'] = $typeMap[strtolower($data['reference_type'])] ?? $data['reference_type'];
    }

    StockMovement::create($data);

    return redirect()->route('stock_movements.index')
        ->with('success', 'Stock movement created successfully.');
}

    public function show(StockMovement $stockMovement)
    {
        $stockMovement->load(['item', 'batch', 'reference']);
        return view('stock_movements.show', compact('stockMovement'));
    }

    public function edit(StockMovement $stockMovement)
    {
        $items = Item::active()->get();
        $batches = Batch::where('qty_on_hand', '>', 0)->get();

        return view('stock_movements.edit', compact('stockMovement', 'items', 'batches'));
    }

    public function update(Request $request, StockMovement $stockMovement)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'batch_id' => 'nullable|exists:batches,id',
            'type' => 'required|string|max:50',
            'qty_in' => 'nullable|integer|min:0',
            'qty_out' => 'nullable|integer|min:0',
            'reference_type' => 'nullable|string|max:255',
            'reference_id' => 'nullable|integer',
            'reason' => 'nullable|string',
        ]);

        // Ensure at least one quantity field is provided
        if (empty($request->qty_in) && empty($request->qty_out)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['qty_in' => 'Either quantity in or quantity out must be provided.']);
        }

        $stockMovement->update($request->all());

        return redirect()->route('stock_movements.index')
            ->with('success', 'Stock movement updated successfully');
    }

    public function destroy(StockMovement $stockMovement)
    {
        $stockMovement->delete();

        return redirect()->route('stock_movements.index')
            ->with('success', 'Stock movement deleted successfully');
    }
}
