@extends('layouts.app')

@section('title', 'User Management - Pharmacy Management')

@section('content')

 <div class="container-fluid px-4">
          <div class="page-inner">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                      <h4 class="card-title">User Management</h4>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="fas fa-plus me-2"></i>Add New User
                      </button>
                    </div>
                  </div>
                  <div class="card-body">

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                      <table
                        id="multi-filter-select"
                        class="display table table-striped table-hover"
                      >
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tfoot>
                          <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                          </tr>
                        </tfoot>
                        <tbody>
                          @forelse($users as $user)
                          <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="form-button-action">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#showUserModal{{ $user->id }}"
                                        class="btn btn-link btn-primary" data-bs-toggle="tooltip" title="View">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"
                                        class="btn btn-link btn-warning" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}"
                                        class="btn btn-link btn-danger" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </td>
                          </tr>

                          <!-- Show Modal -->
                          <div class="modal fade" id="showUserModal{{ $user->id }}" tabindex="-1"
                              aria-labelledby="showUserModalLabel{{ $user->id }}" aria-hidden="true">
                              <div class="modal-dialog">
                                  <div class="modal-content text-black">
                                      <div class="modal-header bg-info text-white">
                                          <h5 class="modal-title" id="showUserModalLabel{{ $user->id }}">
                                              User Details
                                          </h5>
                                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                  aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                          <div class="mb-3">
                                              <h3>{{ $user->name }}</h3>
                                              <hr>
                                              <h5 class="text-primary">User Information</h5>
                                              <p><strong>Email:</strong> {{ $user->email }}</p>
                                              <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                                              <p><strong>Address:</strong> {{ $user->address ?? 'N/A' }}</p>
                                              <p><strong>Status:</strong>
                                                  <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'inactive' ? 'warning' : 'danger') }}">
                                                      {{ ucfirst($user->status) }}
                                                  </span>
                                              </p>
                                              <p><strong>Role(s):</strong>
                                                  @foreach($user->roles as $role)
                                                      <span class="badge bg-primary">{{ $role->name }}</span>
                                                  @endforeach
                                              </p>
                                              <p><strong>Created At:</strong> {{ $user->created_at->format('M d, Y H:i') }}</p>
                                              <p><strong>Updated At:</strong> {{ $user->updated_at->format('M d, Y H:i') }}</p>
                                          </div>
                                      </div>
                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                      </div>
                                  </div>
                              </div>
                          </div>

                          <!-- Edit Modal -->
                          <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
                              aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-lg">
                                  <div class="modal-content text-black">
                                      <form action="{{ route('users.update', $user->id) }}" method="POST">
                                          @csrf
                                          @method('PUT')
                                          <div class="modal-header bg-warning text-white">
                                              <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">
                                                  Edit User
                                              </h5>
                                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                      aria-label="Close"></button>
                                          </div>
                                          <div class="modal-body">
                                              <div class="row">
                                                  <div class="col-md-6 mb-3">
                                                      <label for="edit_name{{ $user->id }}" class="form-label">Name</label>
                                                      <input type="text" class="form-control" id="edit_name{{ $user->id }}"
                                                            name="name" value="{{ old('name', $user->name) }}" required>
                                                  </div>
                                                  <div class="col-md-6 mb-3">
                                                      <label for="edit_email{{ $user->id }}" class="form-label">Email</label>
                                                      <input type="email" class="form-control" id="edit_email{{ $user->id }}"
                                                            name="email" value="{{ old('email', $user->email) }}" required>
                                                  </div>

                                                  <div class="col-md-6 mb-3">
                                                      <label for="edit_phone{{ $user->id }}" class="form-label">Phone</label>
                                                      <input type="text" class="form-control" id="edit_phone{{ $user->id }}"
                                                            name="phone" value="{{ old('phone', $user->phone) }}">
                                                  </div>
                                                  <div class="col-md-6 mb-3">
                                                      <label for="edit_status{{ $user->id }}" class="form-label">Status</label>
                                                      <select class="form-select" id="edit_status{{ $user->id }}" name="status" required>
                                                          <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                                                          <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                          <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                      </select>
                                                  </div>

                                                  <div class="col-md-6 mb-3">
                                                      <label for="edit_role{{ $user->id }}" class="form-label">Role</label>
                                                      <select class="form-select" id="edit_role{{ $user->id }}" name="role" required>
                                                          @foreach(\Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')->get() as $role)
                                                              <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                                  {{ $role->name }}
                                                              </option>
                                                          @endforeach
                                                      </select>
                                                  </div>
                                                  <div class="col-md-6 mb-3">
                                                      <label for="edit_password{{ $user->id }}" class="form-label">Password (Leave blank to keep current)</label>
                                                      <input type="password" class="form-control" id="edit_password{{ $user->id }}"
                                                            name="password">
                                                  </div>

                                                  <div class="col-12 mb-3">
                                                      <label for="edit_address{{ $user->id }}" class="form-label">Address</label>
                                                      <textarea class="form-control" id="edit_address{{ $user->id }}" name="address">{{ old('address', $user->address) }}</textarea>
                                                  </div>

                                                  <div class="col-12 mb-3">
                                                      <label for="edit_password_confirmation{{ $user->id }}" class="form-label">Confirm Password</label>
                                                      <input type="password" class="form-control" id="edit_password_confirmation{{ $user->id }}"
                                                            name="password_confirmation">
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                              <button type="submit" class="btn btn-warning text-white">Update User</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                          </div>

                          <!-- Delete Modal -->
                          <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1"
                              aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                              <div class="modal-dialog">
                                  <div class="modal-content text-black">
                                      <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                          @csrf
                                          @method('DELETE')
                                          <div class="modal-header bg-danger text-white">
                                              <h5 class="modal-title" id="deleteUserModalLabel{{ $user->id }}">
                                                  Confirm Deletion
                                              </h5>
                                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                                      aria-label="Close"></button>
                                          </div>
                                          <div class="modal-body">
                                              <div class="alert alert-danger">
                                                  <h5><i class="fas fa-exclamation-triangle me-2"></i>Warning!</h5>
                                                  <p>You are about to delete the user <strong>{{ $user->name }}</strong>.</p>
                                                  <p class="mb-0">This action cannot be undone.</p>
                                              </div>
                                          </div>
                                          <div class="modal-footer">
                                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                              <button type="submit" class="btn btn-danger">Delete User</button>
                                          </div>
                                      </form>
                                  </div>
                              </div>
                          </div>
                          @empty
                          <tr>
                              <td colspan="5" class="text-center py-4">
                                  <div class="alert alert-info">
                                      <i class="fas fa-info-circle me-2"></i>No users found.
                                  </div>
                              </td>
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
        </div>

        <!-- Create User Modal -->
        <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content text-black">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="createUserModalLabel">Add New User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="">Select Role</option>
                                        @foreach(\Spatie\Permission\Models\Role::where('name', '!=', 'Super Admin')->get() as $role)
                                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endsection

@push('scripts')
        <script>
      $(document).ready(function () {
        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api()
              .columns()
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search users...",
          },
          columnDefs: [
            { orderable: false, targets: [4] }, // Actions column
            { responsivePriority: 1, targets: [0] },
            { responsivePriority: 2, targets: [1, 2, 3] }
          ],
          order: [[0, 'asc']], // Name column
          lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
      });
    </script>
@endpush
