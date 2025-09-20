@extends('layouts.app')

@section('title', 'Stock Movements - Pharmacy Management')

@section('content')

<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Stock Movements Management</h4>
                            @can('stock_movement_create')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStockMovementModal">
                                <i class="fas fa-plus me-2"></i>Add New Stock Movement
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
                                        <th>Item</th>
                                        <th>Batch</th>
                                        <th>Type</th>
                                        <th>Qty In</th>
                                        <th>Qty Out</th>
                                        <th>Reference</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($stockMovements as $movement)
                                    <tr>
                                        <td>{{ $movement->item->name }}</td>
                                        <td>{{ $movement->batch ? $movement->batch->batch_no : 'N/A' }}</td>
                                        <td>
                                            <span class="badge
                                                @if($movement->type == 'in') bg-success
                                                @elseif($movement->type == 'out') bg-danger
                                                @else bg-secondary @endif">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($movement->qty_in)
                                                <span class="text-success">{{ $movement->qty_in }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($movement->qty_out)
                                                <span class="text-danger">{{ $movement->qty_out }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($movement->reference_type && $movement->reference_id)
                                                {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                @can('stock_movement_view')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#showStockMovementModal{{ $movement->id }}"
                                                    class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @endcan
                                                @can('stock_movement_edit')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#editStockMovementModal{{ $movement->id }}"
                                                    class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan
                                                @can('stock_movement_delete')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#deleteStockMovementModal{{ $movement->id }}"
                                                    class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Show Modal -->
                                    <div class="modal fade" id="showStockMovementModal{{ $movement->id }}" tabindex="-1"
                                        aria-labelledby="showStockMovementModalLabel{{ $movement->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="showStockMovementModalLabel{{ $movement->id }}">
                                                        Stock Movement Details
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h5 class="text-primary">Movement Information</h5>
                                                        <p><strong>Item:</strong> {{ $movement->item->name }} ({{ $movement->item->code }})</p>
                                                        <p><strong>Batch:</strong> {{ $movement->batch ? $movement->batch->batch_no : 'N/A' }}</p>
                                                        <p><strong>Type:</strong>
                                                            <span class="badge
                                                                @if($movement->type == 'in') bg-success
                                                                @elseif($movement->type == 'out') bg-danger
                                                                @else bg-secondary @endif">
                                                                {{ ucfirst($movement->type) }}
                                                            </span>
                                                        </p>
                                                        <p><strong>Quantity In:</strong>
                                                            @if($movement->qty_in)
                                                                <span class="text-success">{{ $movement->qty_in }}</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </p>
                                                        <p><strong>Quantity Out:</strong>
                                                            @if($movement->qty_out)
                                                                <span class="text-danger">{{ $movement->qty_out }}</span>
                                                            @else
                                                                -
                                                            @endif
                                                        </p>
                                                        <p><strong>Reference:</strong>
                                                            @if($movement->reference_type && $movement->reference_id)
                                                                {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </p>
                                                        <p><strong>Reason:</strong> {{ $movement->reason ?? 'N/A' }}</p>
                                                        <p><strong>Created At:</strong> {{ $movement->created_at->format('M d, Y H:i') }}</p>
                                                        <p><strong>Updated At:</strong> {{ $movement->updated_at->format('M d, Y H:i') }}</p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editStockMovementModal{{ $movement->id }}" tabindex="-1"
                                        aria-labelledby="editStockMovementModalLabel{{ $movement->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('stock_movements.update', $movement->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="editStockMovementModalLabel{{ $movement->id }}">
                                                            Edit Stock Movement
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="edit_item_id{{ $movement->id }}" class="form-label">Item</label>
                                                            <select class="form-control" id="edit_item_id{{ $movement->id }}" name="item_id" required>
                                                                <option value="">Select Item</option>
                                                                @foreach($items as $item)
                                                                <option value="{{ $item->id }}" {{ old('item_id', $movement->item_id) == $item->id ? 'selected' : '' }}>
                                                                    {{ $item->name }} ({{ $item->code }})
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_batch_id{{ $movement->id }}" class="form-label">Batch</label>
                                                            <select class="form-control" id="edit_batch_id{{ $movement->id }}" name="batch_id">
                                                                <option value="">Select Batch</option>
                                                                @foreach($batches as $batch)
                                                                <option value="{{ $batch->id }}" {{ old('batch_id', $movement->batch_id) == $batch->id ? 'selected' : '' }}>
                                                                    {{ $batch->batch_no }} (Qty: {{ $batch->qty_on_hand }})
                                                                </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_type{{ $movement->id }}" class="form-label">Type</label>
                                                            <select class="form-control" id="edit_type{{ $movement->id }}" name="type" required>
                                                                <option value="in" {{ old('type', $movement->type) == 'in' ? 'selected' : '' }}>In</option>
                                                                <option value="out" {{ old('type', $movement->type) == 'out' ? 'selected' : '' }}>Out</option>
                                                                <option value="adjustment" {{ old('type', $movement->type) == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                                            </select>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_qty_in{{ $movement->id }}" class="form-label">Quantity In</label>
                                                                <input type="number" class="form-control" id="edit_qty_in{{ $movement->id }}"
                                                                        name="qty_in" value="{{ old('qty_in', $movement->qty_in) }}" min="0">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="edit_qty_out{{ $movement->id }}" class="form-label">Quantity Out</label>
                                                                <input type="number" class="form-control" id="edit_qty_out{{ $movement->id }}"
                                                                        name="qty_out" value="{{ old('qty_out', $movement->qty_out) }}" min="0">
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_reference_type{{ $movement->id }}" class="form-label">Reference Type</label>
                                                            <input type="text" class="form-control" id="edit_reference_type{{ $movement->id }}"
                                                                    name="reference_type" value="{{ old('reference_type', $movement->reference_type) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_reference_id{{ $movement->id }}" class="form-label">Reference ID</label>
                                                            <input type="number" class="form-control" id="edit_reference_id{{ $movement->id }}"
                                                                    name="reference_id" value="{{ old('reference_id', $movement->reference_id) }}" min="0">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_reason{{ $movement->id }}" class="form-label">Reason</label>
                                                            <textarea class="form-control" id="edit_reason{{ $movement->id }}"
                                                                name="reason" rows="3">{{ old('reason', $movement->reason) }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning text-white">Update Movement</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteStockMovementModal{{ $movement->id }}" tabindex="-1"
                                        aria-labelledby="deleteStockMovementModalLabel{{ $movement->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('stock_movements.destroy', $movement->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteStockMovementModalLabel{{ $movement->id }}">
                                                            Confirm Deletion
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                                                            <p>You are about to delete the stock movement record for <strong>{{ $movement->item->name }}</strong>.</p>
                                                            <p class="mb-0">This action cannot be undone and may affect your inventory records.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Movement</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>No stock movements found.
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

@can('stock_movement_create')
<!-- Create Stock Movement Modal -->
<div class="modal fade" id="createStockMovementModal" tabindex="-1" aria-labelledby="createStockMovementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-black">
            <form action="{{ route('stock_movements.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createStockMovementModalLabel">Add New Stock Movement</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
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
                    <div class="mb-3">
                        <label for="batch_id" class="form-label">Batch</label>
                        <select class="form-control @error('batch_id') is-invalid @enderror" id="batch_id" name="batch_id">
                            <option value="">Select Batch</option>
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_no }} (Qty: {{ $batch->qty_on_hand }})
                            </option>
                            @endforeach
                        </select>
                        @error('batch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                            <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>In</option>
                            <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Out</option>
                            <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qty_in" class="form-label">Quantity In</label>
                            <input type="number" class="form-control @error('qty_in') is-invalid @enderror" id="qty_in" name="qty_in" value="{{ old('qty_in') }}" min="0">
                            @error('qty_in')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="qty_out" class="form-label">Quantity Out</label>
                            <input type="number" class="form-control @error('qty_out') is-invalid @enderror" id="qty_out" name="qty_out" value="{{ old('qty_out') }}" min="0">
                            @error('qty_out')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reference_type" class="form-label">Reference Type</label>
                        <input type="text" class="form-control @error('reference_type') is-invalid @enderror" id="reference_type" name="reference_type" value="{{ old('reference_type') }}">
                        @error('reference_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="reference_id" class="form-label">Reference ID</label>
                        <input type="number" class="form-control @error('reference_id') is-invalid @enderror" id="reference_id" name="reference_id" value="{{ old('reference_id') }}" min="0">
                        @error('reference_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Movement</button>
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
                searchPlaceholder: "Search stock movements...",
            },
            columnDefs: [
                { orderable: false, targets: [7] }, // Actions column
                { responsivePriority: 1, targets: [0] },
                { responsivePriority: 2, targets: [1, 2, 3, 4, 5, 6] }
            ],
            order: [[6, 'desc']], // Date column (most recent first)
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    });
</script>
@endpush
