@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Reports</h3>
      <h6 class="op-7 mb-2">View and analyze your pharmacy data</h6>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card card-round">
        <div class="card-body text-center">
          <div class="icon-big text-center icon-primary mb-3">
            <i class="fas fa-chart-line fa-3x"></i>
          </div>
          <h4 class="card-title">Sales Report</h4>
          <p class="card-text">View detailed sales reports with date filters</p>
          <a href="{{ route('reports.sales') }}" class="btn btn-primary btn-round">View Report</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-round">
        <div class="card-body text-center">
          <div class="icon-big text-center icon-info mb-3">
            <i class="fas fa-shopping-cart fa-3x"></i>
          </div>
          <h4 class="card-title">Purchases Report</h4>
          <p class="card-text">Analyze purchase history and supplier data</p>
          <a href="{{ route('reports.purchases') }}" class="btn btn-info btn-round">View Report</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-round">
        <div class="card-body text-center">
          <div class="icon-big text-center icon-success mb-3">
            <i class="fas fa-boxes fa-3x"></i>
          </div>
          <h4 class="card-title">Inventory Report</h4>
          <p class="card-text">Current stock levels and inventory valuation</p>
          <a href="{{ route('reports.inventory') }}" class="btn btn-success btn-round">View Report</a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mt-3">
      <div class="card card-round">
        <div class="card-body text-center">
          <div class="icon-big text-center icon-warning mb-3">
            <i class="fas fa-dollar-sign fa-3x"></i>
          </div>
          <h4 class="card-title">Profit & Loss</h4>
          <p class="card-text">Financial overview and profitability analysis</p>
          <a href="{{ route('reports.profit-loss') }}" class="btn btn-warning btn-round">View Report</a>
        </div>
      </div>
    </div>

    <div class="col-md-4 mt-3">
      <div class="card card-round">
        <div class="card-body text-center">
          <div class="icon-big text-center icon-danger mb-3">
            <i class="fas fa-exclamation-triangle fa-3x"></i>
          </div>
          <h4 class="card-title">Expiry Report</h4>
          <p class="card-text">Track expiring and expired items</p>
          <a href="{{ route('reports.expiry') }}" class="btn btn-danger btn-round">View Report</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

