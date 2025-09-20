@extends('layouts.app')

@section('title', 'Item Management - Pharmacy Management')

@section('content')

<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Item Management</h4>
                            @can('item_create')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createItemModal">
                                <i class="fas fa-plus me-2"></i>Add New Item
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">

                        @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="table-responsive">
                            <table
                                id="multi-filter-select"
                                class="display table table-striped table-hover"
                            >
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>SKU</th>
                                        <th>Category</th>
                                        <th>Unit</th>
                                        <th>Supplier</th>
                                        <th>Reorder Level</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($items as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->sku }}</td>
                                        <td>{{ $item->category->name ?? 'N/A' }}</td>
                                        <td>{{ $item->unit->abbreviation ?? 'N/A' }}</td>
                                        <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                                        <td>{{ $item->reorder_level }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->active ? 'success' : 'danger' }}">
                                                {{ $item->active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                @can('item_view')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#showItemModal{{ $item->id }}"
                                                    class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @endcan
                                                @can('item_edit')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}"
                                                    class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan
                                                @can('item_delete')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#deleteItemModal{{ $item->id }}"
                                                    class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Show Modal - Updated with detailed view -->
                                    <div class="modal fade" id="showItemModal{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="showItemModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content text-black">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="showItemModalLabel{{ $item->id }}">
                                                        Item Details: {{ $item->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <!-- Basic Information -->
                                                        <div class="col-md-6">
                                                            <div class="card mb-4">
                                                                <div class="card-header bg-primary text-white">
                                                                    <h5 class="card-title mb-0">Basic Information</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <table class="table table-borderless">
                                                                        <tr>
                                                                            <th width="30%">Name:</th>
                                                                            <td>{{ $item->name }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>SKU:</th>
                                                                            <td>{{ $item->sku ?? 'N/A' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Category:</th>
                                                                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Unit:</th>
                                                                            <td>{{ $item->unit->name ?? 'N/A' }} ({{ $item->unit->abbreviation ?? 'N/A' }})</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Primary Supplier:</th>
                                                                            <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Status:</th>
                                                                            <td>
                                                                                <span class="badge bg-{{ $item->active ? 'success' : 'danger' }}">
                                                                                    {{ $item->active ? 'Active' : 'Inactive' }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Inventory Information -->
                                                        <div class="col-md-6">
                                                            <div class="card mb-4">
                                                                <div class="card-header bg-info text-white">
                                                                    <h5 class="card-title mb-0">Inventory Information</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    <table class="table table-borderless">
                                                                        <tr>
                                                                            <th width="40%">Quantity on Hand:</th>
                                                                            <td>
                                                                                <span class="h5 {{ $item->qty_on_hand <= $item->reorder_level ? 'text-danger' : 'text-success' }}">
                                                                                    {{ $item->qty_on_hand }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Reorder Level:</th>
                                                                            <td>{{ $item->reorder_level }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Reorder Quantity:</th>
                                                                            <td>{{ $item->reorder_quantity }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Average Cost:</th>
                                                                            <td>{{ $item->average_cost ? number_format($item->average_cost, 2) : 'N/A' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Sell Price:</th>
                                                                            <td>{{ $item->sell_price ? number_format($item->sell_price, 2) : 'N/A' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>Stock Value:</th>
                                                                            <td class="fw-bold">{{ number_format($item->stock_value, 2) }}</td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Batch Information -->
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="card">
                                                                <div class="card-header bg-success text-white">
                                                                    <h5 class="card-title mb-0">Batch Information</h5>
                                                                </div>
                                                                <div class="card-body">
@if($item->purchaseItems->count() > 0)
                                                                        <div class="table-responsive">
                                                                            <table class="table table-striped">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Batch No</th>
                                                                                        <th>Expiry Date</th>
                                                                                        <th>Quantity</th>
                                                                                        <th>Unit Cost</th>
                                                                                        <th>Sell Price</th>
                                                                                        <th>Stock Value</th>
                                                                                        <th>Status</th>
                                                                                        <th>Days Until Expiry</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($item->batches as $batch)
                                                                                    @php
                                                                                        $isExpired = $batch->expires_at && $batch->expires_at->isPast();
                                                                                        $daysUntilExpiry = $batch->expires_at ? now()->diffInDays($batch->expires_at, false) : null;
                                                                                    @endphp
                                                                                    <tr class="{{ $isExpired ? 'table-danger' : ($daysUntilExpiry !== null && $daysUntilExpiry <= 30 ? 'table-warning' : '') }}">
                                                                                        <td>{{ $batch->batch_no }}</td>
                                                                                        <td>{{ $batch->expires_at ? $batch->expires_at->format('M d, Y') : 'N/A' }}</td>
                                                                                        <td>{{ $batch->qty_on_hand }}</td>
                                                                                        <td>{{ number_format($batch->unit_cost, 2) }}</td>
                                                                                        <td>{{ number_format($batch->sell_price, 2) }}</td>
                                                                                        <td>{{ number_format($batch->stock_value, 2) }}</td>
                                                                                        <td>
                                                                                            <span class="badge bg-{{ $isExpired ? 'danger' : 'success' }}">
                                                                                                {{ $isExpired ? 'Expired' : 'Active' }}
                                                                                            </span>
                                                                                        </td>
                                                                                        <td>
                                                                                            @if($batch->expires_at && !$isExpired)
                                                                                                {{ $daysUntilExpiry }} days
                                                                                            @elseif($isExpired)
                                                                                                Expired
                                                                                            @else
                                                                                                N/A
                                                                                            @endif
                                                                                        </td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @else
                                                                        <div class="alert alert-info">
                                                                            <i class="fas fa-info-circle me-2"></i>No batch information available.
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Recent Transactions -->
                                                    <div class="row mt-4">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Recent Stock Movements</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    @if($item->stockMovements->count() > 0)
                                                                        <div class="table-responsive">
                                                                            <table class="table table-sm">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Date</th>
                                                                                        <th>Type</th>
                                                                                        <th>Qty Change</th>
                                                                                        <th>Reason</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($item->stockMovements->take(5) as $movement)
                                                                                    <tr>
                                                                                        <td>{{ $movement->created_at->format('M d, Y') }}</td>
                                                                                        <td>{{ ucfirst($movement->movement_type) }}</td>
                                                                                        <td class="{{ $movement->quantity_change > 0 ? 'text-success' : 'text-danger' }}">
                                                                                            {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}
                                                                                        </td>
                                                                                        <td>{{ $movement->reason }}</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @else
                                                                        <p class="text-muted">No recent stock movements.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h5 class="card-title">Recent Sales</h5>
                                                                </div>
                                                                <div class="card-body">
                                                                    @if($item->saleItems->count() > 0)
                                                                        <div class="table-responsive">
                                                                            <table class="table table-sm">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Date</th>
                                                                                        <th>Quantity</th>
                                                                                        <th>Price</th>
                                                                                        <th>Total</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @foreach($item->saleItems->take(5) as $saleItem)
                                                                                    <tr>
                                                                                        <td>{{ $saleItem->created_at->format('M d, Y') }}</td>
                                                                                        <td>{{ $saleItem->quantity }}</td>
                                                                                        <td>{{ number_format($saleItem->unit_price, 2) }}</td>
                                                                                        <td>{{ number_format($saleItem->quantity * $saleItem->unit_price, 2) }}</td>
                                                                                    </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    @else
                                                                        <p class="text-muted">No recent sales.</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="editItemModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('items.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="editItemModalLabel{{ $item->id }}">
                                                            Edit Item
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="edit_name{{ $item->id }}" class="form-label">Name</label>
                                                                    <input type="text" class="form-control" id="edit_name{{ $item->id }}"
                                                                            name="name" value="{{ old('name', $item->name) }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_sku{{ $item->id }}" class="form-label">SKU</label>
                                                                    <input type="text" class="form-control" id="edit_sku{{ $item->id }}"
                                                                            name="sku" value="{{ old('sku', $item->sku) }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_category_id{{ $item->id }}" class="form-label">Category</label>
                                                                    <select class="form-select" id="edit_category_id{{ $item->id }}" name="category_id" required>
                                                                        <option value="">Select Category</option>
                                                                        @foreach($categories as $category)
                                                                        <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_unit_id{{ $item->id }}" class="form-label">Unit</label>
                                                                    <select class="form-select" id="edit_unit_id{{ $item->id }}" name="unit_id" required>
                                                                        <option value="">Select Unit</option>
                                                                        @foreach($units as $unit)
                                                                        <option value="{{ $unit->id }}" {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                                                            {{ $unit->name }} ({{ $unit->abbreviation }})
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="edit_primary_supplier_id{{ $item->id }}" class="form-label">Primary Supplier</label>
                                                                    <select class="form-select" id="edit_primary_supplier_id{{ $item->id }}" name="primary_supplier_id" required>
                                                                        <option value="">Select Supplier</option>
                                                                        @foreach($suppliers as $supplier)
                                                                        <option value="{{ $supplier->id }}" {{ $item->primary_supplier_id == $supplier->id ? 'selected' : '' }}>
                                                                            {{ $supplier->name }}
                                                                        </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_reorder_level{{ $item->id }}" class="form-label">Reorder Level</label>
                                                                    <input type="number" class="form-control" id="edit_reorder_level{{ $item->id }}"
                                                                            name="reorder_level" value="{{ old('reorder_level', $item->reorder_level) }}" min="0" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_reorder_quantity{{ $item->id }}" class="form-label">Reorder Quantity</label>
                                                                    <input type="number" class="form-control" id="edit_reorder_quantity{{ $item->id }}"
                                                                            name="reorder_quantity" value="{{ old('reorder_quantity', $item->reorder_quantity) }}" min="1" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Options</label>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" id="edit_active{{ $item->id }}"
                                                                            name="active" value="1" {{ $item->active ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="edit_active{{ $item->id }}">
                                                                            Active
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning text-white">Update Item</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteItemModal{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="deleteItemModalLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('items.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteItemModalLabel{{ $item->id }}">
                                                            Confirm Deletion
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                                                            <p>You are about to delete the item <strong>{{ $item->name }}</strong>.</p>
                                                            <p class="mb-0">This action cannot be undone and may affect related records.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Item</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>No items found.
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@can('item_create')
<!-- Create Item Modal -->
<div class="modal fade" id="createItemModal" tabindex="-1" aria-labelledby="createItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content text-black">
            <form action="{{ route('items.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createItemModalLabel">Add New Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku') }}" required>
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->abbreviation }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="primary_supplier_id" class="form-label">Primary Supplier</label>
                                <select class="form-select @error('primary_supplier_id') is-invalid @enderror" id="primary_supplier_id" name="primary_supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('primary_supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('primary_supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="reorder_level" class="form-label">Reorder Level</label>
                                <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', 0) }}" min="0" required>
                                @error('reorder_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="reorder_quantity" class="form-label">Reorder Quantity</label>
                                <input type="number" class="form-control @error('reorder_quantity') is-invalid @enderror" id="reorder_quantity" name="reorder_quantity" value="{{ old('reorder_quantity', 1) }}" min="1" required>
                                @error('reorder_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Options</label>
                                <div class="form-check">
                                    <input class="form-check-input @error('active') is-invalid @enderror" type="checkbox" id="active"
                                        name="active" value="1" {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $("#multi-filter-select").DataTable({
            pageLength: 5,
            initComplete: function () {
                this.api()
                    .columns()
                    .every(function () {
                        var column = this;
                        var select = $(
                            '<select class="form-select"><option value=""></option></select>'
                        )
                            .appendTo($(column.footer()).empty())
                            .on("change", function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column
                                    .search(val ? "^" + val + "$" : "", true, false)
                                    .draw();
                            });

                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function (d, j) {
                                select.append(
                                    '<option value="' + d + '">' + d + "</option>"
                                );
                            });
                    });
            },
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search items...",
            },
            columnDefs: [
                { orderable: false, targets: [7] }, // Actions column
                { responsivePriority: 1, targets: [0] },
                { responsivePriority: 2, targets: [1, 2, 3, 4, 5, 6] }
            ],
            order: [[0, 'asc']], // Name column
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    });
</script>
@endpush
