@extends('layouts.app')

@section('title', 'Supplier Management - Pharmacy Management')

@section('content')

<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Supplier Management</h4>
                            @can('supplier_create')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
                                <i class="fas fa-plus me-2"></i>Add New Supplier
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
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($suppliers as $supplier)
                                    <tr>
                                        <td>{{ $supplier->name }}</td>
                                        <td>{{ $supplier->email ?? 'N/A' }}</td>
                                        <td>{{ $supplier->phone ?? 'N/A' }}</td>
                                        <td>
                                            @if($supplier->active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                @can('supplier_view')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#showSupplierModal{{ $supplier->id }}"
                                                    class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @endcan
                                                @can('supplier_edit')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#editSupplierModal{{ $supplier->id }}"
                                                    class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan
                                                @can('supplier_delete')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#deleteSupplierModal{{ $supplier->id }}"
                                                    class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Show Modal -->
                                    <div class="modal fade" id="showSupplierModal{{ $supplier->id }}" tabindex="-1"
                                        aria-labelledby="showSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="showSupplierModalLabel{{ $supplier->id }}">
                                                        Supplier Details
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h3>{{ $supplier->name }}</h3>
                                                        <hr>
                                                        <h5 class="text-primary">Supplier Information</h5>
                                                        <p><strong>Name:</strong> {{ $supplier->name }}</p>
                                                        <p><strong>Email:</strong> {{ $supplier->email ?? 'N/A' }}</p>
                                                        <p><strong>Phone:</strong> {{ $supplier->phone ?? 'N/A' }}</p>
                                                        <p><strong>Address:</strong> {{ $supplier->address ?? 'N/A' }}</p>
                                                        <p><strong>Status:</strong>
                                                            @if($supplier->active)
                                                                <span class="badge bg-success">Active</span>
                                                            @else
                                                                <span class="badge bg-danger">Inactive</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1"
                                        aria-labelledby="editSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="editSupplierModalLabel{{ $supplier->id }}">
                                                            Edit Supplier
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="edit_name{{ $supplier->id }}" class="form-label">Name *</label>
                                                            <input type="text" class="form-control" id="edit_name{{ $supplier->id }}"
                                                                    name="name" value="{{ old('name', $supplier->name) }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_email{{ $supplier->id }}" class="form-label">Email</label>
                                                            <input type="email" class="form-control" id="edit_email{{ $supplier->id }}"
                                                                    name="email" value="{{ old('email', $supplier->email) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_phone{{ $supplier->id }}" class="form-label">Phone</label>
                                                            <input type="text" class="form-control" id="edit_phone{{ $supplier->id }}"
                                                                    name="phone" value="{{ old('phone', $supplier->phone) }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_address{{ $supplier->id }}" class="form-label">Address</label>
                                                            <textarea class="form-control" id="edit_address{{ $supplier->id }}"
                                                                name="address" rows="3">{{ old('address', $supplier->address) }}</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_active{{ $supplier->id }}" class="form-label">Status *</label>
                                                            <select class="form-control" id="edit_active{{ $supplier->id }}" name="active" required>
                                                                <option value="1" {{ $supplier->active ? 'selected' : '' }}>Active</option>
                                                                <option value="0" {{ !$supplier->active ? 'selected' : '' }}>Inactive</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning text-white">Update Supplier</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteSupplierModal{{ $supplier->id }}" tabindex="-1"
                                        aria-labelledby="deleteSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteSupplierModalLabel{{ $supplier->id }}">
                                                            Confirm Deletion
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                                                            <p>You are about to delete the supplier <strong>{{ $supplier->name }}</strong>.</p>
                                                            <p class="mb-0">This action cannot be undone.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Supplier</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>No suppliers found.
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

@can('supplier_create')
<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-black">
            <form action="{{ route('suppliers.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createSupplierModalLabel">Add New Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="active" class="form-label">Status *</label>
                        <select class="form-control @error('active') is-invalid @enderror" id="active" name="active" required>
                            <option value="1" {{ old('active', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('active') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('active')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Supplier</button>
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
                searchPlaceholder: "Search suppliers...",
            },
            columnDefs: [
                { orderable: false, targets: [4] }, // Actions column
                { responsivePriority: 1, targets: [0] },
                { responsivePriority: 2, targets: [1, 2, 3] }
            ],
            order: [[0, 'asc']], // Name column
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    });
</script>
@endpush
