@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Customers</h3>
      <h6 class="op-7 mb-2">Manage your customers</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <a href="{{ route('customers.create') }}" class="btn btn-primary btn-round">
        <i class="fa fa-plus"></i> Add New Customer
      </a>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="card card-round">
        <div class="card-body">
          <form method="GET" action="{{ route('customers.index') }}" class="mb-3">
            <div class="row">
              <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
              </div>
              <div class="col-md-3">
                <select name="status" class="form-control">
                  <option value="">All Status</option>
                  <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                  <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-round">Search</button>
              </div>
              <div class="col-md-3 text-end">
                @if(request('search') || request('status'))
                  <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-round">Clear</a>
                @endif
              </div>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Type</th>
                  <th>Total Purchases</th>
                  <th>Outstanding</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($customers as $customer)
                  <tr>
                    <td>{{ $customer->name }}</td>
                    <td>
                      <div>{{ $customer->phone }}</div>
                      <small class="text-muted">{{ $customer->email }}</small>
                    </td>
                    <td>
                      <span class="badge bg-info">{{ ucfirst($customer->customer_type ?? 'Retail') }}</span>
                    </td>
                    <td>Birr {{ number_format($customer->total_purchases, 2) }}</td>
                    <td>Birr {{ number_format($customer->outstanding_balance, 2) }}</td>
                    <td>
                      <span class="badge bg-{{ $customer->status == 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($customer->status) }}
                      </span>
                    </td>
                    <td>
                      <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-info">
                        <i class="fa fa-eye"></i>
                      </a>
                      <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-edit"></i>
                      </a>
                      <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center">No customers found</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $customers->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

