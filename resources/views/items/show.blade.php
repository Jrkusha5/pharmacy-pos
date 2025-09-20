@extends('layouts.app')

@section('title', 'Item Details - Pharmacy Management')

@section('content')

<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Item Details: {{ $item->name }}</h4>
                            <div>
                                <a href="{{ route('items.index') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Items
                                </a>
                                @can('item_edit')
                                <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </a>
                                @endcan
                            </div>
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
                                        @if($batches->count() > 0)
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
                                                        @foreach($batches as $batch)
                                                        <tr class="{{ $batch['is_expired'] ? 'table-danger' : ($batch['days_until_expiry'] <= 30 ? 'table-warning' : '') }}">
                                                            <td>{{ $batch['batch_no'] }}</td>
                                                            <td>{{ $batch['expires_at'] ? $batch['expires_at']->format('M d, Y') : 'N/A' }}</td>
                                                            <td>{{ $batch['quantity'] }}</td>
                                                            <td>{{ number_format($batch['unit_cost'], 2) }}</td>
                                                            <td>{{ number_format($batch['sell_price'], 2) }}</td>
                                                            <td>{{ number_format($batch['stock_value'], 2) }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $batch['is_expired'] ? 'danger' : 'success' }}">
                                                                    {{ $batch['is_expired'] ? 'Expired' : 'Active' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                @if($batch['expires_at'] && !$batch['is_expired'])
                                                                    {{ $batch['days_until_expiry'] }} days
                                                                @elseif($batch['is_expired'])
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
                                                        @foreach($item->stockMovements as $movement)
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
                                                        @foreach($item->saleItems as $saleItem)
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
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add any JavaScript needed for the show page
    });
</script>
@endpush
