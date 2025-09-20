@extends('layouts.app')

@section('title', 'Sale Details')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-receipt me-2"></i>Sale Details</h1>
                <div>
                    {{-- <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a> --}}
                    <a href="{{ route('sales.create') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Sales
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Sale Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Sale ID:</strong> #{{ $sale->id }}</p>
                                    <p><strong>Date:</strong> {{ $sale->sold_at->format('M d, Y h:i A') }}</p>
                                    <p><strong>Status:</strong>
                                        <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'draft' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($sale->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Subtotal:</strong> Birr{{ number_format($sale->subtotal, 2) }}</p>
                                    <p><strong>Total:</strong> Birr{{ number_format($sale->total, 2) }}</p>
                                </div>
                                @if($sale->note)
                                <div class="col-12 mt-3">
                                    <p><strong>Notes:</strong></p>
                                    <p>{{ $sale->note }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Sale Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                            <th>Batch No</th>
                                            <th>Expiry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sale->items as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->item->name }}</strong><br>
                                                <small class="text-muted">{{ $item->item->code }}</small>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>Birr{{ number_format($item->unit_price, 2) }}</td>
                                            <td>Birr{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                            <td>{{ $item->batch_no ?? 'N/A' }}</td>
                                            <td>{{ $item->expires_at ? $item->expires_at->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">Subtotal:</td>
                                            <td>Birr {{ number_format($sale->subtotal, 2) }}</td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td colspan="3" class="text-end">Total:</td>
                                            <td>Birr {{ number_format($sale->total, 2) }}</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card summary-card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i> Edit Sale
                                </a>
                                <form action="{{ route('sales.destroy', $sale->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to delete this sale?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i> Delete Sale
                                    </button>
                                </form>
                                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> New Sale
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
