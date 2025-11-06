@extends('layouts.app')

@section('content')
<div class="page-inner">
  <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
    <div>
      <h3 class="fw-bold mb-3">Notifications</h3>
      <h6 class="op-7 mb-2">Manage your system notifications</h6>
    </div>
    <div class="ms-md-auto py-2 py-md-0">
      <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary btn-round">
          <i class="fa fa-check"></i> Mark All as Read
        </button>
      </form>
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
                  <th>Type</th>
                  <th>Title</th>
                  <th>Message</th>
                  <th>Level</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($notifications as $notification)
                  <tr class="{{ $notification->isRead() ? '' : 'table-warning' }}">
                    <td>
                      @if($notification->type == 'expiry')
                        <span class="badge bg-danger">
                          <i class="fa fa-calendar-times"></i> Expiry
                        </span>
                      @elseif($notification->type == 'low_stock')
                        <span class="badge bg-warning text-dark">
                          <i class="fa fa-exclamation-triangle"></i> Low Stock
                        </span>
                      @else
                        <span class="badge bg-info">{{ ucfirst($notification->type) }}</span>
                      @endif
                    </td>
                    <td>{{ $notification->title }}</td>
                    <td>{{ $notification->message }}</td>
                    <td>
                      @if($notification->level == 'critical')
                        <span class="badge bg-danger">Critical</span>
                      @elseif($notification->level == 'warning')
                        <span class="badge bg-warning text-dark">Warning</span>
                      @else
                        <span class="badge bg-info">Info</span>
                      @endif
                    </td>
                    <td>{{ $notification->created_at->format('M d, Y H:i') }}</td>
                    <td>
                      @if($notification->isRead())
                        <span class="badge bg-success">Read</span>
                      @else
                        <span class="badge bg-warning text-dark">Unread</span>
                      @endif
                    </td>
                    <td>
                      @if(!$notification->isRead())
                        <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success" title="Mark as read">
                            <i class="fa fa-check"></i>
                          </button>
                        </form>
                      @endif
                      <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                          <i class="fa fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center">No notifications found</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $notifications->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

