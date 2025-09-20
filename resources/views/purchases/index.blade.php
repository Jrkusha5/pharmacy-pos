@extends('layouts.app')

@section('title', 'Purchase Management - Pharmacy Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="text-black">Purchases</h1>
        @can('purchase_create')
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">+ New Purchase</a>
        @endcan
    </div>

    <!-- Payment Summary Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm rounded-3">
                <div class="card-body">
                    <h6>Total Amount</h6>
                    <h4>{{ number_format($purchases->sum('total'), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm rounded-3">
                <div class="card-body">
                    <h6>Paid Amount</h6>
                    <h4>{{ number_format($purchases->sum('paid_amount'), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm rounded-3">
                <div class="card-body">
                    <h6>Due Amount</h6>
                    <h4>{{ number_format($purchases->sum('due_amount'), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm rounded-3">
                <div class="card-body">
                    <h6>Purchases Count</h6>
                    <h4>{{ $purchases->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm rounded-3">
        <div class="card-body">
            <form action="{{ route('purchases.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search invoice or supplier..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                        <option value="void" {{ request('status') == 'void' ? 'selected' : '' }}>Void</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="payment_status" class="form-select">
                        <option value="">All Payments</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="To Date">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <table id="purchasesTable" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Supplier</th>
                        <th>Invoice No</th>
                        <th>Total Amount</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->id }}</td>
                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                            <td>{{ $purchase->invoice_no }}</td>
                            <td>{{ number_format($purchase->total, 2) }}</td>
                            <td>{{ number_format($purchase->paid_amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $purchase->due_amount > 0 ? 'warning' : 'success' }}">
                                    {{ number_format($purchase->due_amount, 2) }}
                                </span>
                            </td>
                            <td>{{ $purchase->purchased_at->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge bg-{{ $purchase->status == 'posted' ? 'success' : ($purchase->status == 'draft' ? 'secondary' : 'danger') }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $purchase->payment_status == 'paid' ? 'success' : ($purchase->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($purchase->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-info" title="View">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @can('purchase_edit')
                                    @if($purchase->status == 'draft')
                                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @endif
                                    @endcan
                                    @can('purchase_delete')
                                    @if($purchase->status == 'draft')
                                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this purchase?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- DataTables + Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script>
$(document).ready(function() {
    $('#purchasesTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csv',
                className: 'btn btn-sm btn-outline-primary',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-outline-danger',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-outline-secondary',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                }
            }
        ],
        pageLength: 10,
        responsive: true,
        ordering: true,
        order: [[0, 'desc']],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)"
        }
    });
});
</script>
@endpush
