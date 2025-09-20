@extends('layouts.app')

@section('title', 'Unit Management - Pharmacy Management')

@section('content')

<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Unit Management</h4>
                            @can('unit_create')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUnitModal">
                                <i class="fas fa-plus me-2"></i>Add New Unit
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
                                        <th>Abbreviation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                               
                                <tbody>
                                    @forelse($units as $unit)
                                    <tr>
                                        <td>{{ $unit->name }}</td>
                                        <td>{{ $unit->abbreviation }}</td>
                                        <td>
                                            <div class="form-button-action">
                                                @can('unit_view')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#showUnitModal{{ $unit->id }}"
                                                    class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                @endcan
                                                @can('unit_edit')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#editUnitModal{{ $unit->id }}"
                                                    class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                @endcan
                                                @can('unit_delete')
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#deleteUnitModal{{ $unit->id }}"
                                                    class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Show Modal -->
                                    <div class="modal fade" id="showUnitModal{{ $unit->id }}" tabindex="-1"
                                        aria-labelledby="showUnitModalLabel{{ $unit->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title" id="showUnitModalLabel{{ $unit->id }}">
                                                        Unit Details
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h3>{{ $unit->name }}</h3>
                                                        <hr>
                                                        <h5 class="text-primary">Unit Information</h5>
                                                        <p><strong>Name:</strong> {{ $unit->name }}</p>
                                                        <p><strong>Abbreviation:</strong> {{ $unit->abbreviation }}</p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1"
                                        aria-labelledby="editUnitModalLabel{{ $unit->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('units.update', $unit->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-warning text-white">
                                                        <h5 class="modal-title" id="editUnitModalLabel{{ $unit->id }}">
                                                            Edit Unit
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="edit_name{{ $unit->id }}" class="form-label">Name</label>
                                                            <input type="text" class="form-control" id="edit_name{{ $unit->id }}"
                                                                    name="name" value="{{ old('name', $unit->name) }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="edit_abbreviation{{ $unit->id }}" class="form-label">Abbreviation</label>
                                                            <input type="text" class="form-control" id="edit_abbreviation{{ $unit->id }}"
                                                                    name="abbreviation" value="{{ old('abbreviation', $unit->abbreviation) }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning text-white">Update Unit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteUnitModal{{ $unit->id }}" tabindex="-1"
                                        aria-labelledby="deleteUnitModalLabel{{ $unit->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-black">
                                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title" id="deleteUnitModalLabel{{ $unit->id }}">
                                                            Confirm Deletion
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-danger">
                                                            <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                                                            <p>You are about to delete the unit <strong>{{ $unit->name }}</strong>.</p>
                                                            <p class="mb-0">This action cannot be undone.</p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete Unit</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>No units found.
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

@can('unit_create')
<!-- Create Unit Modal -->
<div class="modal fade" id="createUnitModal" tabindex="-1" aria-labelledby="createUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-black">
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createUnitModalLabel">Add New Unit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="abbreviation" class="form-label">Abbreviation</label>
                        <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" id="abbreviation" name="abbreviation" value="{{ old('abbreviation') }}" required>
                        @error('abbreviation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Unit</button>
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
                searchPlaceholder: "Search units...",
            },
            columnDefs: [
                { orderable: false, targets: [3] }, // Actions column
                { responsivePriority: 1, targets: [0] },
                { responsivePriority: 2, targets: [1, 2] }
            ],
            order: [[0, 'asc']], // Name column
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    });
</script>
@endpush
