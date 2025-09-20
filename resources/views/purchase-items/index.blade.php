@extends('layouts.app')

@section('title', 'Purchase Item Management - Pharmacy Management')

@section('content')

<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Purchase Item Management</h4>
                            @can('purchase_item_create')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPurchaseItemModal">
                                <i class="fas fa-plus me-2"></i>Add New Purchase Item
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
                                        <th>Purchase Invoice</th>
                                        <th>Item</th>
                                        <th>Batch No</th>
                                        <th>Expiry Date</th>
                                        <th>Quantity</th>
                                        <th>Unit Cost</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($purchaseItems as $purchaseItem)
                                    <tr>
                                        <td>{{ $purchaseItem->purchase->invoice_no }}</td>
                                        <td>{{ $purchaseItem->item->name }}</td>
                                        <td>{{ $purchaseItem->batch_no }}</td>
                                        <td>{{ \Carbon\Carbon::parse($purchaseItem->expires_at)->format('M d, Y') }}</td>
                                        <td>{{ $purchaseItem->quantity }}</td>
                                        <td>${{ number_format($purchaseItem->unit_cost, 2) }}</td>
                                        <td>${{ number_format($purchaseItem->line_total, 2) }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                @can('purchase_item_view')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#showPurchaseItemModal{{ $purchaseItem->id }}"
                                                    class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @endcan
                                                @can('purchase_item_edit')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#editPurchaseItemModal{{ $purchaseItem->id }}"
                                                    class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan
                                                @can('purchase_item_delete')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#deletePurchaseItemModal{{ $purchaseItem->id }}"
                                                    class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Show Modal -->
                                    <div class="modal fade" id="showPurchaseItemModal{{ $purchaseItem->id }}" tabindex="-1"
                                        aria-labelledby="showPurchaseItemModalLabel{{ $purchaseItem->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content text-black">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="showPurchaseItemModalLabel{{ $purchaseItem->id }}">
                                                        Purchase Item Details
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-4">
                                                        <div class="col-md-6">
                                                            <h5 class="text-primary">Purchase Information</h5>
                                                            <p><strong>Invoice No:</strong> {{ $purchaseItem->purchase->invoice_no }}</p>
                                                            <p><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($purchaseItem->purchase->purchased_at)->format('M d, Y') }}</p>
                                                            <p><strong>Supplier:</strong> {{ $purchaseItem->purchase->supplier->name }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h5 class="text-primary">Item Information</h5>
                                                            <p><strong>Item Name:</strong> {{ $purchaseItem->item->name }}</p>
                                                            <p><strong>Item Code:</strong> {{ $purchaseItem->item->code ?? 'N/A' }}</p>
                                                            <p><strong>Category:</strong> {{ $purchaseItem->item->category->name ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-4">
                                                        <div class="col-md-6">
                                                            <h5 class="text-primary">Batch Details</h5>
                                                            <p><strong>Batch No:</strong> {{ $purchaseItem->batch_no }}</p>
                                                            <p><strong>Expiry Date:</strong> {{ \Carbon\Carbon::parse($purchaseItem->expires_at)->format('M d, Y') }}</p>
                                                            @if($purchaseItem->batch)
                                                            <p><strong>Batch Name:</strong> {{ $purchaseItem->batch->name ?? 'N/A' }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h5 class="text-primary">Pricing Information</h5>
                                                            <p><strong>Quantity:</strong> {{ $purchaseItem->quantity }}</p>
                                                            <p><strong>Unit Cost:</strong> ${{ number_format($purchaseItem->unit_cost, 2) }}</p>
                                                            <p><strong>Sell Price:</strong> ${{ number_format($purchaseItem->sell_price, 2) }}</p>
                                                            <p><strong class="h5">Line Total:</strong> ${{ number_format($purchaseItem->line_total, 2) }}</p>
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
                                    <div class="modal fade" id="editPurchaseItemModal{{ $purchaseItem->id }}" tabindex="-1"
                                        aria-labelledby="editPurchaseItemModalLabel{{ $purchaseItem->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('purchase-items.update', $purchaseItem->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="editPurchaseItemModalLabel{{ $purchaseItem->id }}">
                                                            Edit Purchase Item
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_purchase_id{{ $purchaseItem->id }}" class="form-label">Purchase</label>
                                                                <select class="form-control" id="edit_purchase_id{{ $purchaseItem->id }}" name="purchase_id" required>
                                                                    <option value="">Select Purchase</option>
                                                                    @foreach($purchases as $purchase)
                                                                    <option value="{{ $purchase->id }}" {{ $purchaseItem->purchase_id == $purchase->id ? 'selected' : '' }}>
                                                                        {{ $purchase->invoice_no }} - {{ $purchase->supplier->name }}
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_item_id{{ $purchaseItem->id }}" class="form-label">Item</label>
                                                                <select class="form-control" id="edit_item_id{{ $purchaseItem->id }}" name="item_id" required>
                                                                    <option value="">Select Item</option>
                                                                    @foreach($items as $item)
                                                                    <option value="{{ $item->id }}" {{ $purchaseItem->item_id == $item->id ? 'selected' : '' }}>
                                                                        {{ $item->name }} ({{ $item->code }})
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_batch_id{{ $purchaseItem->id }}" class="form-label">Batch</label>
                                                                <select class="form-control" id="edit_batch_id{{ $purchaseItem->id }}" name="batch_id">
                                                                    <option value="">Select Batch (Optional)</option>
                                                                    @foreach($batches as $batch)
                                                                    <option value="{{ $batch->id }}" {{ $purchaseItem->batch_id == $batch->id ? 'selected' : '' }}>
                                                                        {{ $batch->name }} ({{ $batch->code }})
                                                                    </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_batch_no{{ $purchaseItem->id }}" class="form-label">Batch No</label>
                                                                <input type="text" class="form-control" id="edit_batch_no{{ $purchaseItem->id }}"
                                                                        name="batch_no" value="{{ old('batch_no', $purchaseItem->batch_no) }}" required>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_expires_at{{ $purchaseItem->id }}" class="form-label">Expiry Date</label>
                                                                <input type="date" class="form-control" id="edit_expires_at{{ $purchaseItem->id }}"
                                                                        name="expires_at" value="{{ old('expires_at', $purchaseItem->expires_at->format('Y-m-d')) }}" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_quantity{{ $purchaseItem->id }}" class="form-label">Quantity</label>
                                                                <input type="number" class="form-control" id="edit_quantity{{ $purchaseItem->id }}"
                                                                        name="quantity" value="{{ old('quantity', $purchaseItem->quantity) }}" required min="1">
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label for="edit_unit_cost{{ $purchaseItem->id }}" class="form-label">Unit Cost</label>
                                                                <input type="number" step="0.01" class="form-control" id="edit_unit_cost{{ $purchaseItem->id }}"
                                                                        name="unit_cost" value="{{ old('unit_cost', $purchaseItem->unit_cost) }}" required min="0">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="edit_sell_price{{ $purchaseItem->id }}" class="form-label">Sell Price</label>
                                                                <input type="number" step="0.01" class="form-control" id="edit_sell_price{{ $purchaseItem->id }}"
                                                                        name="sell_price" value="{{ old('sell_price', $purchaseItem->sell_price) }}" required min="0">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label for="edit_line_total{{ $purchaseItem->id }}" class="form-label">Line Total</label>
                                                                <input type="number" step="0.01" class="form-control" id="edit_line_total{{ $purchaseItem->id }}"
                                                                        name="line_total" value="{{ old('line_total', $purchaseItem->line_total) }}" required min="0" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning text-white">Update Purchase Item</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deletePurchaseItemModal{{ $purchaseItem->id }}" tabindex="-1"
                                        aria-labelledby="deletePurchaseItemModalLabel{{ $purchaseItem->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('purchase-items.destroy', $purchaseItem->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deletePurchaseItemModalLabel{{ $purchaseItem->id }}">
                                                            Confirm Deletion
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                                                            <p>You are about to delete the purchase item for <strong>{{ $purchaseItem->item->name }}</strong> from purchase <strong>{{ $purchaseItem->purchase->invoice_no }}</strong>.</p>
                                                            <p class="mb-0">This action cannot be undone.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Purchase Item</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>No purchase items found.
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

@can('purchase_item_create')
<!-- Create Purchase Item Modal -->
<div class="modal fade" id="createPurchaseItemModal" tabindex="-1" aria-labelledby="createPurchaseItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content text-black">
            <form action="{{ route('purchase-items.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createPurchaseItemModalLabel">Add New Purchase Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_id" class="form-label">Purchase</label>
                            <select class="form-control @error('purchase_id') is-invalid @enderror" id="purchase_id" name="purchase_id" required>
                                <option value="">Select Purchase</option>
                                @foreach($purchases as $purchase)
                                <option value="{{ $purchase->id }}" {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                    {{ $purchase->invoice_no }} - {{ $purchase->supplier->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('purchase_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="item_id" class="form-label">Item</label>
                            <select class="form-control @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} ({{ $item->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-control @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id">
                                <option value="">Select Batch (Optional)</option>
                                @foreach($batches as $batch)
                                <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                    {{ $batch->name }} ({{ $batch->code }})
                                </option>
                                @endforeach
                            </select>
                            @error('batch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="batch_no" class="form-label">Batch No</label>
                            <input type="text" class="form-control @error('batch_no') is-invalid @enderror" id="batch_no" name="batch_no" value="{{ old('batch_no') }}" required>
                            @error('batch_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label">Expiry Date</label>
                            <input type="date" class="form-control @error('expires_at') is-invalid @enderror" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" required>
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" required min="1">
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="unit_cost" class="form-label">Unit Cost</label>
                            <input type="number" step="0.01" class="form-control @error('unit_cost') is-invalid @enderror" id="unit_cost" name="unit_cost" value="{{ old('unit_cost', 0) }}" required min="0">
                            @error('unit_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sell_price" class="form-label">Sell Price</label>
                            <input type="number" step="0.01" class="form-control @error('sell_price') is-invalid @enderror" id="sell_price" name="sell_price" value="{{ old('sell_price', 0) }}" required min="0">
                            @error('sell_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="line_total" class="form-label">Line Total</label>
                            <input type="number" step="0.01" class="form-control @error('line_total') is-invalid @enderror" id="line_total" name="line_total" value="{{ old('line_total', 0) }}" required min="0" readonly>
                            @error('line_total')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Purchase Item</button>
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
                searchPlaceholder: "Search purchase items...",
            },
            columnDefs: [
                { orderable: false, targets: [7] }, // Actions column
                { responsivePriority: 1, targets: [0, 1] },
                { responsivePriority: 2, targets: [2, 3, 4, 5, 6] }
            ],
            order: [[0, 'desc']], // Purchase Invoice column
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });

        // Calculate line total when quantity or unit cost changes
        function calculateLineTotal() {
            const quantity = parseFloat($('#quantity').val()) || 0;
            const unitCost = parseFloat($('#unit_cost').val()) || 0;
            const lineTotal = quantity * unitCost;
            $('#line_total').val(lineTotal.toFixed(2));
        }

        $('#quantity, #unit_cost').on('input', calculateLineTotal);

        // For edit modals
        $('[id^="edit_quantity"], [id^="edit_unit_cost"]').on('input', function() {
            const id = this.id.replace('edit_', '').replace('quantity', '').replace('unit_cost', '');
            const quantity = parseFloat($('#edit_quantity' + id).val()) || 0;
            const unitCost = parseFloat($('#edit_unit_cost' + id).val()) || 0;
            const lineTotal = quantity * unitCost;
            $('#edit_line_total' + id).val(lineTotal.toFixed(2));
        });
    });
</script>
@endpush
