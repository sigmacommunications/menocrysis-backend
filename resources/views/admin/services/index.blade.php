@extends('admin.layouts.master')
@section('title')
Categories Management
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Categories Management</h1>
            <p class="text-muted">Manage service categories with hierarchy</p>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Category
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Categories List</h3>
            
            <!-- Filter Form -->
            <div class="card-tools">
                <form action="{{ route('categories.index') }}" method="GET" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <select name="level" class="form-control form-control-sm">
                                <option value="">All Levels</option>
                                <option value="main" {{ request('level') == 'main' ? 'selected' : '' }}>Main Categories</option>
                                <option value="sub" {{ request('level') == 'sub' ? 'selected' : '' }}>Sub Categories</option>
                                <option value="child" {{ request('level') == 'child' ? 'selected' : '' }}>Child Categories</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="product" {{ request('type') == 'product' ? 'selected' : '' }}>Product</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
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
                        <th>Category Path</th>
                        <th>Type</th>
                        <th>Sort Order</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            @if(($category->child_category))
                                <span class="badge badge-info ml-1">{{ ($category->child_category) ? $category->child_category->count() : null}} children</span>
                            @endif
                        </td>
                        <td>
                            @if($category->parent)
                                @if($category->parent->parent)
                                    <small class="text-primary">{{ $category->parent->parent->name }}</small>
                                    <br>
                                    <small class="text-success">→ {{ $category->parent->name }}</small>
                                    <br>
                                    <small class="text-muted">→ → {{ $category->name }}</small>
                                @else
                                    <small class="text-primary">{{ $category->parent->name }}</small>
                                    <br>
                                    <small class="text-muted">→ {{ $category->name }}</small>
                                @endif
                            @else
                                <span class="text-primary"><strong>{{ $category->name }}</strong> (Main)</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ ucfirst($category->type) }}</span>
                        </td>
                        <td>{{ $category->sort_order }}</td>
                        <td>
                            <span class="badge badge-{{ $category->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                        <td>{{ $category->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="btn-group">
                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button type="button" class="btn btn-{{ $category->status == 'active' ? 'warning' : 'success' }} btn-sm toggle-status" 
                                        data-id="{{ $category->id }}" title="{{ $category->status == 'active' ? 'Deactivate' : 'Activate' }}">
                                    <i class="fas fa-{{ $category->status == 'active' ? 'pause' : 'play' }}"></i>
                                </button>
                                
                                <button type="button" class="btn btn-danger btn-sm delete-category" data-id="{{ $category->id }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No categories found.</p>
                            @if(request()->anyFilled(['search', 'level', 'type', 'status']))
                                <a href="{{ route('categories.index') }}" class="btn btn-primary">Clear Filters</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer clearfix">
            {{ $categories->links('custom-pagination-links') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle category status
        $('.toggle-status').click(function() {
            var categoryId = $(this).data('id');
            var button = $(this);
            
            $.ajax({
                url: "{{ url('categories') }}/" + categoryId + "/toggle-status",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Update button appearance
                        if (response.new_status == 'active') {
                            button.removeClass('btn-success').addClass('btn-warning');
                            button.find('i').removeClass('fa-play').addClass('fa-pause');
                            button.attr('title', 'Deactivate');
                        } else {
                            button.removeClass('btn-warning').addClass('btn-success');
                            button.find('i').removeClass('fa-pause').addClass('fa-play');
                            button.attr('title', 'Activate');
                        }
                        
                        // Update status badge
                        var statusBadge = button.closest('tr').find('.badge');
                        statusBadge.removeClass('badge-success badge-danger');
                        statusBadge.addClass('badge-' + (response.new_status == 'active' ? 'success' : 'danger'));
                        statusBadge.text(response.new_status.charAt(0).toUpperCase() + response.new_status.slice(1));
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        // Delete category
        $('.delete-category').click(function() {
            var categoryId = $(this).data('id');
            
            if (confirm('Are you sure you want to delete this category?')) {
                $.ajax({
                    url: "{{ url('categories') }}/" + categoryId,
                    method: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    }
                });
            }
        });
    });
</script>
@endpush