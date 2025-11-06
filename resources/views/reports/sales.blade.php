@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Sales Report</h3>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-round">Back to Reports</a>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body">
          <form method="GET" action="{{ route('reports.sales') }}">
            <div class="row">
              <div class="col-md-4">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
              </div>
              <div class="col-md-4">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
              </div>
              <div class="col-md-4">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary btn-round">Filter</button>
                <a href="{{ route('reports.sales') }}" class="btn btn-secondary btn-round">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Total Sales</p>
            <h4 class="card-title">Birr {{ number_format($totalSales, 2) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Total Transactions</p>
            <h4 class="card-title">{{ number_format($totalCount) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Average Sale</p>
            <h4 class="card-title">Birr {{ number_format($averageSale, 2) }}</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Invoice #</th>
                  <th>Items</th>
                  <th>Total Amount</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($sales as $sale)
                  <tr>
                    <td>{{ $sale->created_at->format('M d, Y H:i') }}</td>
                    <td>#{{ $sale->id }}</td>
                    <td>{{ $sale->saleItems->count() }} items</td>
                    <td>Birr {{ number_format($sale->total_amount, 2) }}</td>
                    <td>
                      <span class="badge bg-success">{{ ucfirst($sale->status) }}</span>
                    </td>
                    <td>
                      <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i> View
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">No sales found for the selected period</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $sales->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

