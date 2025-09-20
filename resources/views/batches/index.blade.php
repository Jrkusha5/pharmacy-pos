@extends('layouts.app')

@section('title', 'Batch Management - Pharmacy Management')

@section('content')
<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Batch Management</h4>
                            @can('batch_create')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBatchModal">
                                <i class="fas fa-plus me-2"></i>Add New Batch
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
                            <table id="multi-filter-select" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Batch No</th>
                                        <th>Item</th>
                                        <th>Expiry Date</th>
                                        <th>Quantity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($batches as $batch)
                                    <tr>
                                        <td>{{ $batch->batch_no }}</td>
                                        <td>{{ $batch->item->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($batch->expires_at)->format('M d, Y') }}</td>
                                        <td>{{ $batch->qty_on_hand }}</td>
                                        <td>
                                            @if($batch->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($batch->status == 'expired')
                                                <span class="badge bg-danger">Expired</span>
                                            @elseif($batch->status == 'sold_out')
                                                <span class="badge bg-warning">Sold Out</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($batch->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                @can('batch_view')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#showBatchModal{{ $batch->id }}"
                                                    class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @endcan
                                                @can('batch_edit')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#editBatchModal{{ $batch->id }}"
                                                    class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan
                                                @can('batch_delete')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#deleteBatchModal{{ $batch->id }}"
                                                    class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Show Modal -->
                                    <div class="modal fade" id="showBatchModal{{ $batch->id }}" tabindex="-1" aria-labelledby="showBatchModalLabel{{ $batch->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content text-black">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="showBatchModalLabel{{ $batch->id }}">Batch Details</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h3>{{ $batch->batch_no }}</h3>
                                                        <hr>
                                                        <h5 class="text-primary">Batch Information</h5>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>Batch Number:</strong> {{ $batch->batch_no }}</p>
                                                                <p><strong>Item:</strong> {{ $batch->item->name }}</p>
                                                                <p><strong>Expiry Date:</strong> {{ \Carbon\Carbon::parse($batch->expires_at)->format('M d, Y') }}</p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Cost Price:</strong> ${{ number_format($batch->cost_price, 2) }}</p>
                                                                <p><strong>Sell Price:</strong> ${{ number_format($batch->sell_price, 2) }}</p>
                                                                <p><strong>Quantity on Hand:</strong> {{ $batch->qty_on_hand }}</p>
                                                                <p><strong>Status:</strong>
                                                                    @if($batch->status == 'active')
                                                                        <span class="badge bg-success">Active</span>
                                                                    @elseif($batch->status == 'expired')
                                                                        <span class="badge bg-danger">Expired</span>
                                                                    @elseif($batch->status == 'sold_out')
                                                                        <span class="badge bg-warning">Sold Out</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">{{ ucfirst($batch->status) }}</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @if($batch->remark)
                                                        <div class="row mt-3">
                                                            <div class="col-md-12">
                                                                <p><strong>Remarks:</strong> {{ $batch->remark }}</p>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editBatchModal{{ $batch->id }}" tabindex="-1" aria-labelledby="editBatchModalLabel{{ $batch->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('batches.update', $batch->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="editBatchModalLabel{{ $batch->id }}">Edit Batch</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="edit_item_id{{ $batch->id }}" class="form-label">Item *</label>
                                                                    <select class="form-control" id="edit_item_id{{ $batch->id }}" name="item_id" required>
                                                                        @foreach($items as $item)
                                                                            <option value="{{ $item->id }}" {{ $batch->item_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_batch_no{{ $batch->id }}" class="form-label">Batch Number *</label>
                                                                    <input type="text" class="form-control" id="edit_batch_no{{ $batch->id }}"
                                                                            name="batch_no" value="{{ old('batch_no', $batch->batch_no) }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_expires_at{{ $batch->id }}" class="form-label">Expiry Date *</label>
                                                                    <input type="date" class="form-control" id="edit_expires_at{{ $batch->id }}"
                                                                            name="expires_at" value="{{ old('expires_at', $batch->expires_at) }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label for="edit_cost_price{{ $batch->id }}" class="form-label">Cost Price *</label>
                                                                    <input type="number" step="0.01" class="form-control" id="edit_cost_price{{ $batch->id }}"
                                                                            name="cost_price" value="{{ old('cost_price', $batch->cost_price) }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_sell_price{{ $batch->id }}" class="form-label">Sell Price *</label>
                                                                    <input type="number" step="0.01" class="form-control" id="edit_sell_price{{ $batch->id }}"
                                                                            name="sell_price" value="{{ old('sell_price', $batch->sell_price) }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_qty_on_hand{{ $batch->id }}" class="form-label">Quantity on Hand *</label>
                                                                    <input type="number" class="form-control" id="edit_qty_on_hand{{ $batch->id }}"
                                                                            name="qty_on_hand" value="{{ old('qty_on_hand', $batch->qty_on_hand) }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="edit_status{{ $batch->id }}" class="form-label">Status *</label>
                                                                    <select class="form-control" id="edit_status{{ $batch->id }}" name="status" required>
                                                                        <option value="active" {{ $batch->status == 'active' ? 'selected' : '' }}>Active</option>
                                                                        <option value="expired" {{ $batch->status == 'expired' ? 'selected' : '' }}>Expired</option>
                                                                        <option value="sold_out" {{ $batch->status == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                                                                        <option value="inactive" {{ $batch->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="mb-3">
                                                                    <label for="edit_remark{{ $batch->id }}" class="form-label">Remarks</label>
                                                                    <textarea class="form-control" id="edit_remark{{ $batch->id }}" name="remark" rows="3">{{ old('remark', $batch->remark) }}</textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning text-white">Update Batch</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteBatchModal{{ $batch->id }}" tabindex="-1"
                                        aria-labelledby="deleteBatchModalLabel{{ $batch->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('batches.destroy', $batch->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteBatchModalLabel{{ $batch->id }}">Confirm Deletion</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                                                            <p>You are about to delete the batch <strong>{{ $batch->batch_no }}</strong> for item <strong>{{ $batch->item->name }}</strong>.</p>
                                                            <p class="mb-0">This action cannot be undone.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Batch</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>No batches found.
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

@can('batch_create')
<!-- Create Batch Modal -->
<div class="modal fade" id="createBatchModal" tabindex="-1" aria-labelledby="createBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content text-black">
            <form action="{{ route('batches.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createBatchModalLabel">Add New Batch</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Item *</label>
                                <select class="form-control @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                                    <option value="">Select Item</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="batch_no" class="form-label">Batch Number *</label>
                                <input type="text" class="form-control @error('batch_no') is-invalid @enderror"
                                       id="batch_no" name="batch_no" value="{{ old('batch_no') }}" required>
                                @error('batch_no')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="expires_at" class="form-label">Expiry Date *</label>
                                <input type="date" class="form-control @error('expires_at') is-invalid @enderror"
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}" required>
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">Cost Price *</label>
                                <input type="number" step="0.01" class="form-control @error('cost_price') is-invalid @enderror"
                                       id="cost_price" name="cost_price" value="{{ old('cost_price') }}" required>
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="sell_price" class="form-label">Sell Price *</label>
                                <input type="number" step="0.01" class="form-control @error('sell_price') is-invalid @enderror"
                                       id="sell_price" name="sell_price" value="{{ old('sell_price') }}" required>
                                @error('sell_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="qty_on_hand" class="form-label">Quantity on Hand *</label>
                                <input type="number" class="form-control @error('qty_on_hand') is-invalid @enderror"
                                       id="qty_on_hand" name="qty_on_hand" value="{{ old('qty_on_hand') }}" required>
                                @error('qty_on_hand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="sold_out" {{ old('status') == 'sold_out' ? 'selected' : '' }}>Sold Out</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="remark" class="form-label">Remarks</label>
                                <textarea class="form-control @error('remark') is-invalid @enderror"
                                          id="remark" name="remark" rows="3">{{ old('remark') }}</textarea>
                                @error('remark')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Batch</button>
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
                        var select = $('<select class="form-select"><option value=""></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on("change", function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? "^" + val + "$" : "", true, false).draw();
                            });
                        column.data().unique().sort().each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + "</option>");
                        });
                    });
            },
            language: { search: "_INPUT_", searchPlaceholder: "Search batches..." },
            columnDefs: [
                { orderable: false, targets: [5] }, // Actions column
                { responsivePriority: 1, targets: [0] },
                { responsivePriority: 2, targets: [1, 2, 3, 4] }
            ],
            order: [[2, 'desc']], // Expiry Date column
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    });
</script>
@endpush
