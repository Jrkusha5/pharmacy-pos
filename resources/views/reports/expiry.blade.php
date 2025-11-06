@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Expiry Report</h3>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-round">Back to Reports</a>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body">
          <form method="GET" action="{{ route('reports.expiry') }}">
            <div class="row">
              <div class="col-md-4">
                <select name="filter" class="form-control">
                  <option value="">All Items</option>
                  <option value="expired" {{ request('filter') == 'expired' ? 'selected' : '' }}>Expired</option>
                  <option value="expiring_soon" {{ request('filter') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (3 months)</option>
                  <option value="expiring_this_month" {{ request('filter') == 'expiring_this_month' ? 'selected' : '' }}>Expiring This Month</option>
                </select>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-round">Filter</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-6">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Expired Items</p>
            <h4 class="card-title text-danger">{{ number_format($expiredCount) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Expiring Soon</p>
            <h4 class="card-title text-warning">{{ number_format($expiringSoonCount) }}</h4>
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
                  <th>Item Name</th>
                  <th>Batch Number</th>
                  <th>Quantity</th>
                  <th>Expiry Date</th>
                  <th>Days Until Expiry</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($expiringItems as $item)
                  @php
                    $expiryDate = \Carbon\Carbon::parse($item->expires_at);
                    $daysUntilExpiry = $expiryDate->diffInDays(now(), false);
                    $isExpired = $expiryDate->isPast();
                  @endphp
                  <tr class="{{ $isExpired ? 'table-danger' : ($daysUntilExpiry <= 90 ? 'table-warning' : '') }}">
                    <td>{{ $item->item->name ?? 'N/A' }}</td>
                    <td>{{ $item->batch_no ?? 'N/A' }}</td>
                    <td>{{ $item->quantity ?? 0 }}</td>
                    <td>{{ $expiryDate->format('M d, Y') }}</td>
                    <td>
                      @if($isExpired)
                        <span class="text-danger">Expired {{ abs($daysUntilExpiry) }} days ago</span>
                      @else
                        {{ $daysUntilExpiry }} days
                      @endif
                    </td>
                    <td>
                      @if($isExpired)
                        <span class="badge bg-danger">Expired</span>
                      @elseif($daysUntilExpiry <= 30)
                        <span class="badge bg-danger">Expiring Soon</span>
                      @elseif($daysUntilExpiry <= 90)
                        <span class="badge bg-warning text-dark">Warning</span>
                      @else
                        <span class="badge bg-success">OK</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center">No expiring items found</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $expiringItems->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

