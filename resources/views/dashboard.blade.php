@extends('layouts.app')

@push('styles')
<style>
  .dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 30px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
  }

  .stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: none;
    height: 100%;
    position: relative;
    overflow: hidden;
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: var(--card-color);
  }

  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  }

  .stat-card.primary { --card-color: #667eea; }
  .stat-card.info { --card-color: #17a2b8; }
  .stat-card.success { --card-color: #28a745; }
  .stat-card.danger { --card-color: #dc3545; }

  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    margin-bottom: 15px;
  }

  .stat-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
  .stat-icon.info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }
  .stat-icon.success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
  .stat-icon.danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }

  .stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #333;
    margin: 10px 0;
  }

  .stat-label {
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .modern-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    border: none;
    margin-bottom: 25px;
  }

  .modern-card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #e9ecef;
    padding: 20px 25px;
    border-radius: 15px 15px 0 0;
  }

  .modern-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .modern-card-body {
    padding: 25px;
  }

  .alert-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .table-modern {
    margin: 0;
  }

  .table-modern thead th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
    border: none;
    padding: 15px;
  }

  .table-modern tbody td {
    padding: 15px;
    vertical-align: middle;
    border-color: #f0f0f0;
  }

  .table-modern tbody tr:hover {
    background: #f8f9fa;
  }

  .summary-item {
    padding: 20px;
    border-radius: 12px;
    background: #f8f9fa;
    margin-bottom: 15px;
    transition: all 0.3s ease;
  }

  .summary-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
  }

  .summary-item:last-child {
    margin-bottom: 0;
  }

  .summary-label {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 8px;
    font-weight: 500;
  }

  .summary-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
  }

  .chart-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    padding: 25px;
  }

  .quick-action-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 12px 25px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
  }

  .quick-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
  }

  .quick-action-btn-secondary {
    background: white;
    border: 2px solid #667eea;
    color: #667eea;
    border-radius: 12px;
    padding: 12px 25px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .quick-action-btn-secondary:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
  }

  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
  }

  .empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
  }
</style>
@endpush

@section('content')
<div class="page-inner">
  <!-- Dashboard Header -->
  <div class="dashboard-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
      <div>
        <h2 class="fw-bold mb-2" style="font-size: 2rem;">Dashboard Overview</h2>
        <p class="mb-0" style="opacity: 0.9;">Welcome back! Here's what's happening with your pharmacy today.</p>
      </div>
      <div class="mt-3 mt-md-0">
        <a href="{{ route('stock_movements.index') }}" class="quick-action-btn-secondary me-2">
          <i class="fas fa-warehouse me-2"></i>Manage Inventory
        </a>
        <a href="{{ route('items.create') }}" class="quick-action-btn">
          <i class="fas fa-plus me-2"></i>Add New Item
        </a>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-md-3">
      <div class="stat-card primary">
        <div class="stat-icon primary">
          <i class="fas fa-pills"></i>
        </div>
        <div class="stat-label">Total Items</div>
        <div class="stat-value">{{ number_format($totalItems) }}</div>
        <small class="text-muted">
          <i class="fas fa-arrow-up text-success me-1"></i>Active products in inventory
        </small>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="stat-card info">
        <div class="stat-icon info">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-label">Total Purchases</div>
        <div class="stat-value">{{ number_format($totalPurchases) }}</div>
        <small class="text-muted">
          <i class="fas fa-file-invoice me-1"></i>Purchase orders
        </small>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="stat-card success">
        <div class="stat-icon success">
          <i class="fas fa-luggage-cart"></i>
        </div>
        <div class="stat-label">Total Sales</div>
        <div class="stat-value">Birr {{ number_format($totalSalesAmount, 0) }}</div>
        <small class="text-muted">
          <i class="fas fa-chart-line text-success me-1"></i>Revenue generated
        </small>
      </div>
    </div>

    <div class="col-sm-6 col-md-3">
      <div class="stat-card danger">
        <div class="stat-icon danger">
          <i class="fas fa-undo"></i>
        </div>
        <div class="stat-label">Sales Returns</div>
        <div class="stat-value">{{ number_format($totalSalesReturns) }}</div>
        <small class="text-muted">
          <i class="fas fa-exchange-alt me-1"></i>Returned items
        </small>
      </div>
    </div>
  </div>

  <!-- Alerts and Today's Summary -->
  <div class="row g-4 mb-4">
    <!-- Stock Alerts -->
    <div class="col-md-6">
      <div class="modern-card">
        <div class="modern-card-header">
          <h5 class="modern-card-title">
            <i class="fas fa-exclamation-triangle text-warning"></i>
            Stock Alerts
          </h5>
        </div>
        <div class="modern-card-body">
          <div class="table-responsive">
            <table class="table table-modern">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>Stock</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($lowStockItems as $stock)
                  <tr>
                    <td>
                      <strong>{{ $stock->purchaseItem->item->name ?? 'N/A' }}</strong>
                    </td>
                    <td>
                      <span class="fw-bold">{{ $stock->quantity }}</span> units
                    </td>
                    <td>
                      @if($stock->quantity == 0)
                        <span class="alert-badge bg-danger text-white">Out of Stock</span>
                      @else
                        <span class="alert-badge bg-warning text-dark">Low Stock</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3">
                      <div class="empty-state">
                        <i class="fas fa-check-circle text-success"></i>
                        <p class="mb-0">All items are well stocked!</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($lowStockItems->count() > 0)
            <div class="mt-3 text-center">
              <a href="{{ route('reports.inventory') }}?low_stock=1" class="btn btn-sm btn-outline-primary">
                View All Alerts <i class="fas fa-arrow-right ms-1"></i>
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Expired Items -->
    <div class="col-md-6">
      <div class="modern-card">
        <div class="modern-card-header">
          <h5 class="modern-card-title">
            <i class="fas fa-calendar-times text-danger"></i>
            Expiring Items
          </h5>
        </div>
        <div class="modern-card-body">
          <div class="table-responsive">
            <table class="table table-modern">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Batch No.</th>
                  <th>Expiry Date</th>
                </tr>
              </thead>
              <tbody>
                @forelse($expiredItems as $item)
                  @php
                    $expiryDate = \Carbon\Carbon::parse($item->expiry_date);
                    $isExpired = $expiryDate->isPast();
                    $daysUntil = $expiryDate->diffInDays(now(), false);
                  @endphp
                  <tr>
                    <td><strong>{{ $item->item_name }}</strong></td>
                    <td><code>{{ $item->batch_number }}</code></td>
                    <td>
                      @if($isExpired)
                        <span class="text-danger fw-bold">
                          <i class="fas fa-exclamation-circle me-1"></i>
                          Expired {{ abs($daysUntil) }}d ago
                        </span>
                      @else
                        <span class="text-warning">
                          <i class="fas fa-clock me-1"></i>
                          {{ $expiryDate->format('M d, Y') }}
                          <small class="text-muted">({{ abs($daysUntil) }} days)</small>
                        </span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="3">
                      <div class="empty-state">
                        <i class="fas fa-check-circle text-success"></i>
                        <p class="mb-0">No expiring items found!</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($expiredItems->count() > 0)
            <div class="mt-3 text-center">
              <a href="{{ route('reports.expiry') }}" class="btn btn-sm btn-outline-danger">
                View Expiry Report <i class="fas fa-arrow-right ms-1"></i>
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Charts and Today's Summary -->
  <div class="row g-4">
    <!-- Sales Chart -->
    <div class="col-md-8">
      <div class="chart-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h5 class="fw-bold mb-1">Monthly Sales Overview</h5>
            <p class="text-muted mb-0 small">Sales performance for {{ now()->format('Y') }}</p>
          </div>
          <div>
            <a href="{{ route('reports.sales') }}" class="btn btn-sm btn-outline-primary">
              <i class="fas fa-chart-bar me-1"></i>View Report
            </a>
          </div>
        </div>
        <div class="chart-container" style="min-height: 350px; position: relative;">
          <canvas id="salesChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Today's Summary -->
    <div class="col-md-4">
      <div class="modern-card">
        <div class="modern-card-header">
          <h5 class="modern-card-title">
            <i class="fas fa-calendar-day text-primary"></i>
            Today's Summary
          </h5>
        </div>
        <div class="modern-card-body">
          <div class="summary-item">
            <div class="summary-label">
              <i class="fas fa-dollar-sign text-success me-2"></i>Total Sales
            </div>
            <div class="summary-value text-success">
              Birr {{ number_format($todaySales, 2) }}
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-label">
              <i class="fas fa-shopping-bag text-info me-2"></i>New Purchases
            </div>
            <div class="summary-value text-info">
              {{ $todayPurchases }} orders
            </div>
          </div>

          <div class="summary-item">
            <div class="summary-label">
              <i class="fas fa-undo text-danger me-2"></i>Items Returned
            </div>
            <div class="summary-value text-danger">
              {{ $todayReturns }} items
            </div>
          </div>

          <div class="mt-3 pt-3 border-top">
            <a href="{{ route('sales.index') }}" class="btn btn-primary btn-sm w-100">
              <i class="fas fa-eye me-2"></i>View All Sales
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
      const chartData = {
        labels: @json($chartLabels),
        datasets: [{
          label: 'Sales (Birr)',
          data: @json($chartData),
          borderColor: 'rgb(102, 126, 234)',
          backgroundColor: 'rgba(102, 126, 234, 0.1)',
          borderWidth: 3,
          tension: 0.4,
          fill: true,
          pointBackgroundColor: 'rgb(102, 126, 234)',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7
        }]
      };

      new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top',
              labels: {
                usePointStyle: true,
                padding: 15,
                font: {
                  size: 12,
                  weight: '600'
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 15,
              titleFont: {
                size: 14,
                weight: '600'
              },
              bodyFont: {
                size: 13
              },
              callbacks: {
                label: function(context) {
                  return 'Sales: Birr ' + context.parsed.y.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                  });
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false
              },
              ticks: {
                callback: function(value) {
                  return 'Birr ' + value.toLocaleString();
                },
                font: {
                  size: 11
                },
                color: '#666'
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 11
                },
                color: '#666'
              }
            }
          },
          interaction: {
            intersect: false,
            mode: 'index'
          }
        }
      });
    }
  });
</script>
@endpush
@endsection
