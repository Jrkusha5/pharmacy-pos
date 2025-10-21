@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Dashboard</h3>
      <h6 class="op-7 mb-2">Manage items, purchases, sales, and inventory</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="#" class="btn btn-label-info btn-round me-2">Manage Inventory</a>
      <a href="#" class="btn btn-primary btn-round">Add New Item</a>
    </div>
  </div>

  {{-- Dashboard Cards --}}
  <div class="row">
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-primary bubble-shadow-small">
                <i class="fas fa-pills"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                <p class="card-category">Total Items</p>
                <h4 class="card-title">210</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-info bubble-shadow-small">
                <i class="fas fa-shopping-cart"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                <p class="card-category">Purchases</p>
                <h4 class="card-title">315</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-success bubble-shadow-small">
                          <i class="fas fa-luggage-cart"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                <p class="card-category">Sales</p>
                <h4 class="card-title">Birr 12,450</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-md-3">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col-icon">
              <div class="icon-big text-center icon-danger bubble-shadow-small">
                <i class="fas fa-undo"></i>
              </div>
            </div>
            <div class="col col-stats ms-3 ms-sm-0">
              <div class="numbers">
                <p class="card-category">Sales Returns</p>
                <h4 class="card-title">22</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Stock Alerts and Expired Items --}}
  <div class="row mt-4">
    <div class="col-sm-6 col-md-6">
      <div class="card card-round">
        <div class="card-body">
          <div class="card-head-row">
            <div class="card-title">Stock Alerts</div>
          </div>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Stock</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Paracetamol 500mg</td>
                  <td>8</td>
                  <td><span class="badge bg-warning text-dark">Low Stock</span></td>
                </tr>
                <tr>
                  <td>Amoxicillin 250mg</td>
                  <td>0</td>
                  <td><span class="badge bg-danger">Out of Stock</span></td>
                </tr>
                <tr>
                  <td>Vitamin C Tablets</td>
                  <td>15</td>
                  <td><span class="badge bg-success">OK</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-md-6">
      <div class="card card-round">
        <div class="card-body">
          <div class="card-head-row">
            <div class="card-title">Expired Items</div>
          </div>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Batch No.</th>
                  <th>Expiry Date</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Ibuprofen 200mg</td>
                  <td>#B1245</td>
                  <td>Aug 2025</td>
                </tr>
                <tr>
                  <td>Cough Syrup</td>
                  <td>#B0983</td>
                  <td>Sep 2025</td>
                </tr>
                <tr>
                  <td>Multivitamin</td>
                  <td>#C4312</td>
                  <td>Expired</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Charts Section --}}
  <div class="row mt-4">
    <div class="col-md-8">
      <div class="card card-round">
        <div class="card-header">
          <div class="card-head-row">
            <div class="card-title">Monthly Sales Overview</div>
            <div class="card-tools">
              <a href="#" class="btn btn-label-info btn-round btn-sm me-2">
                <span class="btn-label"><i class="fa fa-download"></i></span> Export
              </a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="chart-container" style="min-height: 375px">
            <canvas id="salesChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card card-round">
        <div class="card-body">
          <h4 class="card-title mb-3">Today’s Summary</h4>
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Total Sales
              <span class="badge bg-success">$1,230</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              New Purchases
              <span class="badge bg-info">12</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Items Returned
              <span class="badge bg-danger">3</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
