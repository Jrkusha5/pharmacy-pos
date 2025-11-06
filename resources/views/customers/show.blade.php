@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Customer Details</h3>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary btn-round me-2">Edit</a>
      <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-round">Back to List</a>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card card-round">
        <div class="card-body">
          <h4 class="card-title mb-3">{{ $customer->name }}</h4>
          <table class="table table-borderless">
            <tr>
              <td><strong>Email:</strong></td>
              <td>{{ $customer->email ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Phone:</strong></td>
              <td>{{ $customer->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td><strong>Type:</strong></td>
              <td><span class="badge bg-info">{{ ucfirst($customer->customer_type ?? 'Retail') }}</span></td>
            </tr>
            <tr>
              <td><strong>Status:</strong></td>
              <td>
                <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'secondary' }}">
                  {{ ucfirst($customer->status) }}
                </span>
              </td>
            </tr>
            <tr>
              <td><strong>Credit Limit:</strong></td>
              <td>Birr {{ number_format($customer->credit_limit ?? 0, 2) }}</td>
            </tr>
            <tr>
              <td><strong>Total Purchases:</strong></td>
              <td>Birr {{ number_format($customer->total_purchases, 2) }}</td>
            </tr>
            <tr>
              <td><strong>Outstanding:</strong></td>
              <td>Birr {{ number_format($customer->outstanding_balance, 2) }}</td>
            </tr>
          </table>

          @if($customer->address)
            <hr>
            <h5>Address</h5>
            <p>{{ $customer->address }}<br>
            {{ $customer->city }}, {{ $customer->state }} {{ $customer->postal_code }}<br>
            {{ $customer->country }}</p>
          @endif

          @if($customer->notes)
            <hr>
            <h5>Notes</h5>
            <p>{{ $customer->notes }}</p>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card card-round">
        <div class="card-body">
          <h4 class="card-title mb-3">Sales History</h4>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Invoice #</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($customer->sales as $sale)
                  <tr>
                    <td>{{ $sale->created_at->format('M d, Y') }}</td>
                    <td>#{{ $sale->id }}</td>
                    <td>Birr {{ number_format($sale->total_amount, 2) }}</td>
                    <td>
                      <span class="badge bg-{{ $sale->status == 'completed' ? 'success' : 'warning' }}">
                        {{ ucfirst($sale->status) }}
                      </span>
                    </td>
                    <td>
                      <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i> View
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center">No sales found</td>
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
@endsection

