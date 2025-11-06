@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Inventory Report</h3>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-round">Back to Reports</a>
    </div>
  </div>

  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body">
          <form method="GET" action="{{ route('reports.inventory') }}">
            <div class="row">
              <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search items..." value="{{ request('search') }}">
              </div>
              <div class="col-md-3">
                <select name="category_id" class="form-control">
                  <option value="">All Categories</option>
                  @foreach(\App\Models\Category::all() as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                      {{ $category->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="low_stock" {{ request('low_stock') ? 'checked' : '' }}>
                  <label class="form-check-label" for="low_stock">Low Stock Only</label>
                </div>
              </div>
              <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-round">Filter</button>
                <a href="{{ route('reports.inventory') }}" class="btn btn-secondary btn-round">Reset</a>
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
            <p class="card-category">Total Items</p>
            <h4 class="card-title">{{ number_format($totalItems) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card card-stats card-round">
        <div class="card-body">
          <div class="numbers">
            <p class="card-category">Total Inventory Value</p>
            <h4 class="card-title">Birr {{ number_format($totalValue, 2) }}</h4>
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
                  <th>Category</th>
                  <th>SKU</th>
                  <th>Stock Quantity</th>
                  <th>Unit Price</th>
                  <th>Total Value</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($inventory as $stock)
                  <tr>
                    <td>{{ $stock->purchaseItem->item->name ?? 'N/A' }}</td>
                    <td>{{ $stock->purchaseItem->item->category->name ?? 'N/A' }}</td>
                    <td>{{ $stock->purchaseItem->item->sku ?? 'N/A' }}</td>
                    <td>{{ $stock->quantity }}</td>
                    <td>Birr {{ number_format($stock->purchaseItem->item->selling_price ?? $stock->purchaseItem->item->default_sell_price ?? 0, 2) }}</td>
                    <td>Birr {{ number_format($stock->quantity * ($stock->purchaseItem->item->selling_price ?? $stock->purchaseItem->item->default_sell_price ?? 0), 2) }}</td>
                    <td>
                      @php
                        $item = $stock->purchaseItem->item ?? null;
                        $minStock = $item->min_stock_level ?? $item->reorder_level ?? 0;
                      @endphp
                      @if($stock->quantity == 0)
                        <span class="badge bg-danger">Out of Stock</span>
                      @elseif($stock->quantity <= $minStock)
                        <span class="badge bg-warning text-dark">Low Stock</span>
                      @else
                        <span class="badge bg-success">OK</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center">No inventory items found</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $inventory->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

