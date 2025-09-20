@extends('layouts.app')

@section('title', 'Purchase Details - Pharmacy Management')

@section('content')
<div class="container-fluid px-4">
    <div class="page-inner">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Purchase Details - {{ $purchase->invoice_no }}</h4>
                        <div class="btn-group">
                            <a href="{{ route('purchases.print-invoice', $purchase->id) }}" class="btn btn-sm btn-info" target="_blank">
                                <i class="fa fa-print"></i> Print Invoice
                            </a>
                            @can('purchase_edit')
                            @if($purchase->status == 'draft')
                            <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            @endif
                            @endcan
                            <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Purchase Header -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>Supplier Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="120">Supplier:</th>
                                        <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Invoice No:</th>
                                        <td>{{ $purchase->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Purchase Date:</th>
                                        <td>{{ $purchase->purchased_at->format('M d, Y') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Payment Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <th width="120">Status:</th>
                                        <td>
                                            <span class="badge bg-{{ $purchase->status == 'posted' ? 'success' : ($purchase->status == 'draft' ? 'secondary' : 'danger') }}">
                                                {{ ucfirst($purchase->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Payment Status:</th>
                                        <td>
                                            <span class="badge bg-{{ $purchase->payment_status == 'paid' ? 'success' : ($purchase->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($purchase->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method:</th>
                                        <td>{{ ucfirst($purchase->payment_method) }}</td>
                                    </tr>
                                    @if($purchase->due_date)
                                    <tr>
                                        <th>Due Date:</th>
                                        <td>{{ $purchase->due_date->format('M d, Y') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- Purchase Items -->
                        <h6>Purchase Items</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Batch No</th>
                                        <th>Expiry Date</th>
                                        <th>Qty</th>
                                        <th>Unit Cost</th>
                                        <th>Sell Price</th>
                                        <th>Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase->items as $item)
                                    <tr>
                                        <td>{{ $item->item->name }} ({{ $item->item->sku }})</td>
                                        <td>{{ $item->batch_no }}</td>
                                        <td>{{ $item->expires_at ? $item->expires_at->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->unit_cost, 4) }}</td>
                                        <td>${{ number_format($item->sell_price, 2) }}</td>
                                        <td>${{ number_format($item->line_total, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="row">
                            <div class="col-md-4 offset-md-8">
                                <table class="table table-sm">
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-end">${{ number_format($purchase->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tax:</th>
                                        <td class="text-end">${{ number_format($purchase->tax, 2) }}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <th>Total:</th>
                                        <td class="text-end">${{ number_format($purchase->total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Paid Amount:</th>
                                        <td class="text-end">${{ number_format($purchase->paid_amount, 2) }}</td>
                                    </tr>
                                    <tr class="fw-bold text-{{ $purchase->due_amount > 0 ? 'danger' : 'success' }}">
                                        <th>Due Amount:</th>
                                        <td class="text-end">${{ number_format($purchase->due_amount, 2) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Notes -->
                        @if($purchase->note)
                        <div class="mt-4">
                            <h6>Notes</h6>
                            <div class="border rounded p-3">
                                {{ $purchase->note }}
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        @can('purchase_edit')
                        <div class="mt-4">
                            @if($purchase->due_amount > 0)
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                                <i class="fa fa-money-bill me-2"></i> Add Payment
                            </button>
                            @endif

                            @if($purchase->status == 'draft')
                            <form action="{{ route('purchases.update-status', $purchase->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="posted">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Mark this purchase as posted?')">
                                    <i class="fa fa-check me-2"></i> Mark as Posted
                                </button>
                            </form>
                            @endif
                        </div>
                        @endcan

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
@can('purchase_edit')
@if($purchase->due_amount > 0)
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('purchases.add-payment', $purchase->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Amount to Pay</label>
                        <input type="number" step="0.01" name="amount" class="form-control"
                               max="{{ $purchase->due_amount }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endcan

@endsection
