@extends('admin.layouts.master')
@section('title')
Users Management
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Users Management</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Users List</h3>
            
            <!-- Search and Filter Form -->
            <div class="card-tools">
                <form action="{{ route('users.index') }}" method="GET">
                    <div class="input-group input-group-sm" style="width: 300px;">
                        <input type="text" name="search" class="form-control float-right" placeholder="Search..." value="{{ request('search') }}">
                        
                        <select name="role" class="form-control" style="width: 120px;">
                            <option value="">All Roles</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>Customer</option>
                            <option value="barber" {{ request('role') == 'barber' ? 'selected' : '' }}>Barber</option>
                        </select>
                        
                        <select name="status" class="form-control" style="width: 120px;">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-{{ $user->role == 'barber' ? 'success' : 'info' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $user->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{ $users->links('custom-pagination-links') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Delete user confirmation
        $('.delete-user').click(function() {
            var userId = $(this).data('id');
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: "{{ url('admin/users') }}/" + userId,
                    type: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error deleting user');
                        }
                    }
                });
            }
        });

        // Status update
        $('.status-toggle').change(function() {
            var userId = $(this).data('id');
            var status = $(this).is(':checked') ? 'active' : 'inactive';
            
            $.ajax({
                url: "{{ url('admin/users') }}/" + userId + "/status",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'status': status
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        });
    });
</script>
@endpush