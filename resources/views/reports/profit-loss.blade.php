@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Profit & Loss Report</h3>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-round">Back to Reports</a>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body">
          <form method="GET" action="{{ route('reports.profit-loss') }}">
            <div class="row">
              <div class="col-md-4">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
              </div>
              <div class="col-md-4">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
              </div>
              <div class="col-md-4">
                <label>&nbsp;</label><br>
                <button type="submit" class="btn btn-primary btn-round">Filter</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Total Revenue</p>
            <h4 class="card-title text-success">Birr {{ number_format($salesRevenue, 2) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Total Costs</p>
            <h4 class="card-title text-danger">Birr {{ number_format($purchaseCosts, 2) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Gross Profit</p>
            <h4 class="card-title {{ $grossProfit >= 0 ? 'text-success' : 'text-danger' }}">
              Birr {{ number_format($grossProfit, 2) }}
            </h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Profit Margin</p>
            <h4 class="card-title {{ $profitMargin >= 0 ? 'text-success' : 'text-danger' }}">
              {{ number_format($profitMargin, 2) }}%
            </h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body">
          <h4 class="card-title mb-3">Monthly Breakdown</h4>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Month</th>
                  <th>Sales Revenue</th>
                  <th>Purchase Costs</th>
                  <th>Profit</th>
                  <th>Margin</th>
                </tr>
              </thead>
              <tbody>
                @forelse($monthlyData as $data)
                  <tr>
                    <td>{{ $data['month'] }}</td>
                    <td>Birr {{ number_format($data['sales'], 2) }}</td>
                    <td>Birr {{ number_format($data['purchases'], 2) }}</td>
                    <td class="{{ $data['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                      Birr {{ number_format($data['profit'], 2) }}
                    </td>
                    <td>
                      @php
                        $margin = $data['sales'] > 0 ? ($data['profit'] / $data['sales']) * 100 : 0;
                      @endphp
                      <span class="{{ $margin >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($margin, 2) }}%
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center">No data available</td>
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

