@extends('layouts.app')

@section('title', 'Batch Inventory Report - Pharmacy Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="text-black">Batch Inventory Report</h1>
        <div class="btn-group">
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to Purchases
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm rounded-3">
        <div class="card-body">
            <form action="{{ route('purchases.batch-report') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="batch_no" class="form-control" value="{{ request('batch_no') }}" placeholder="Search batch no...">
                </div>
                <div class="col-md-3">
                    <select name="item_id" class="form-select">
                        <option value="">All Items</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} ({{ $item->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="expiring_soon" class="form-select">
                        <option value="">All Batches</option>
                        <option value="1" {{ request('expiring_soon') == '1' ? 'selected' : '' }}>Expiring Soon (30 days)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="expired" class="form-select">
                        <option value="">All Status</option>
                        <option value="1" {{ request('expired') == '1' ? 'selected' : '' }}>Expired Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm rounded-3">
                <div class="card-body text-center">
                    <h6>Total Batches</h6>
                    <h4>{{ $batches->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm rounded-3">
                <div class="card-body text-center">
                    <h6>Total Quantity</h6>
                    <h4>{{ number_format($batches->sum('quantity')) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm rounded-3">
                <div class="card-body text-center">
                    <h6>Stock Value</h6>
                    <h4>${{ number_format($batches->sum(function($batch) { return $batch->quantity * $batch->unit_cost; }), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm rounded-3">
                <div class="card-body text-center">
                    <h6>Expiring Soon</h6>
                    <h4>{{ $batches->where('expires_at', '<=', now()->addDays(30))->where('expires_at', '>=', now())->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Batch No</th>
                            <th>Item</th>
                            <th>Supplier</th>
                            <th>Quantity</th>
                            <th>Unit Cost</th>
                            <th>Stock Value</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batches as $batch)
                        <tr>
                            <td>{{ $batch->batch_no }}</td>
                            <td>{{ $batch->item->name }} ({{ $batch->item->sku }})</td>
                            <td>{{ $batch->purchase->supplier->name }}</td>
                            <td>{{ $batch->quantity }}</td>
                            <td>${{ number_format($batch->unit_cost, 4) }}</td>
                            <td>${{ number_format($batch->quantity * $batch->unit_cost, 2) }}</td>
                            <td>{{ $batch->expires_at ? $batch->expires_at->format('Y-m-d') : 'N/A' }}</td>
                            <td>
                                @if($batch->expires_at)
                                    @if($batch->expires_at->isPast())
                                        <span class="badge bg-danger">Expired</span>
                                    @elseif($batch->expires_at->diffInDays(now()) <= 30)
                                        <span class="badge bg-warning">Expiring Soon</span>
                                    @else
                                        <span class="badge bg-success">Good</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">No Expiry</span>
                                @endif
                            </td>
                            <td>
                                @can('purchase_edit')
                                <a href="{{ route('purchases.show-adjust-stock', $batch->id) }}" class="btn btn-sm btn-warning" title="Adjust Stock">
                                    <i class="fa fa-adjust"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $batches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
