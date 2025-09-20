@extends('layouts.app')

@section('title', 'Purchase Items Report - Pharmacy Management')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1 class="text-black">Purchase Items Report</h1>
        <div class="btn-group">
            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left me-2"></i> Back to Purchases
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 shadow-sm rounded-3">
        <div class="card-body">
            <form action="{{ route('purchases.items-report') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="item_id" class="form-select">
                        <option value="">All Items</option>
                        @foreach($allItems as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }} ({{ $item->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="supplier_id" class="form-select">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="To Date">
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
                    <h6>Total Items</h6>
                    <h4>{{ $items->total() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm rounded-3">
                <div class="card-body text-center">
                    <h6>Total Quantity</h6>
                    <h4>{{ number_format($items->sum('quantity')) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info shadow-sm rounded-3">
                <div class="card-body text-center">
                    <h6>Total Value</h6>
                    <h4>${{ number_format($items->sum('line_total'), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm rounded-3">
                <div class="card-body text-center">
                    <h6>Avg. Cost</h6>
                    <h4>${{ $items->count() > 0 ? number_format($items->avg('unit_cost'), 4) : '0.0000' }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card shadow-sm rounded-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Purchase Date</th>
                            <th>Invoice No</th>
                            <th>Supplier</th>
                            <th>Item</th>
                            <th>Batch No</th>
                            <th>Qty</th>
                            <th>Unit Cost</th>
                            <th>Sell Price</th>
                            <th>Line Total</th>
                            <th>Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->purchase->purchased_at->format('Y-m-d') }}</td>
                            <td>{{ $item->purchase->invoice_no }}</td>
                            <td>{{ $item->purchase->supplier->name }}</td>
                            <td>{{ $item->item->name }} ({{ $item->item->sku }})</td>
                            <td>{{ $item->batch_no }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->unit_cost, 4) }}</td>
                            <td>${{ number_format($item->sell_price, 2) }}</td>
                            <td>${{ number_format($item->line_total, 2) }}</td>
                            <td>{{ $item->expires_at ? $item->expires_at->format('Y-m-d') : 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
