@extends('admin.layouts.master')
@section('title')
Edit Service
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Service</h1>
            <p class="text-muted">Update category details and hierarchy</p>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Categories
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Service Details</h3>
        </div>
        
        <form action="{{ route('categories.update', $category->id) }}" method="POST" id="categoryForm">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="row">
                    <!-- Category Name -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Category Name *</label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $category->name) }}" 
                                   placeholder="Enter category name" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- Parent Category -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="parent_id">Parent Category</label>
                            <select name="parent_id" id="parent_id" class="form-control select2 @error('parent_id') is-invalid @enderror">
                                <option value="">No Parent (Main Category)</option>
                                
                                <!-- Main Categories -->
                                <optgroup label="Main Categories">
                                    @foreach($mainCategories as $cat)
                                        @if($cat->id != $category->id) {{-- Prevent self selection --}}
                                        <option value="{{ $cat->id }}" 
                                                {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                        @endif
                                    @endforeach
                                </optgroup>
                                
                                <!-- Sub Categories -->
                                <optgroup label="Sub Categories">
                                    @foreach($subCategories as $cat)
                                        @if($cat->id != $category->id) {{-- Prevent self selection --}}
                                        <option value="{{ $cat->id }}" 
                                                {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;→ {{ $cat->name }}
                                        </option>
                                        @endif
                                    @endforeach
                                </optgroup>
                            </select>
                            <small class="form-text text-muted">
                                Leave empty to make this a main category. Select parent for sub/child categories.
                            </small>
                            @error('parent_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>


                    <!-- Status -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Current Category Info -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Current Category Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Current Level:</strong> 
                                    @if(!$category->parent_id)
                                        <span class="badge badge-success">Main Category</span>
                                    @elseif($category->parent && !$category->parent->parent_id)
                                        <span class="badge badge-info">Sub Category</span>
                                    @else
                                        <span class="badge badge-warning">Child Category</span>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <strong>Children Count:</strong> 
                                    <span class="badge badge-primary">{{ $category->children_count ?? 0 }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Created:</strong> 
                                    {{ $category->created_at->format('M d, Y') }}
                                </div>
                            </div>
                            @if($category->parent)
                                <div class="mt-2">
                                    <strong>Current Parent:</strong> 
                                    <span class="text-primary">{{ $category->parent->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Category Level Preview -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning" id="categoryLevelPreview">
                            <strong>New Category Level:</strong> 
                            <span class="text-muted">Change parent to see new level</span>
                        </div>
                    </div>
                </div>

                <!-- Warning for existing children -->
                @if(($category->children_count ?? 0) > 0 && $category->parent_id)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> Important Note</h6>
                                <p class="mb-0">
                                    This category has <strong>{{ $category->children_count }} children</strong>. 
                                    If you change its parent level, it may affect the hierarchy of its children categories.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
                
                @if(($category->children_count ?? 0) == 0)
                <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#deleteModal">
                    <i class="fas fa-trash"></i> Delete Category
                </button>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
@if(($category->children_count ?? 0) == 0)
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the category <strong>"{{ $category->name }}"</strong>?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Category</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    optgroup {
        font-weight: bold;
        font-style: normal;
    }
    optgroup option {
        padding-left: 20px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();

        // Update category level preview
        function updateCategoryLevelPreview() {
            var parentId = $('#parent_id').val();
            var parentText = $('#parent_id option:selected').text().trim();
            var currentName = $('#name').val() || '{{ $category->name }}';
            
            var level = '';
            var levelClass = 'alert-warning';
            
            if (!parentId) {
                level = '<span class="text-success"><strong>Main Category</strong></span> - This will become a top-level category: <strong>' + currentName + '</strong>';
                levelClass = 'alert-success';
            } else {
                // Check if parent is main or sub category
                var parentOption = $('#parent_id option:selected');
                var optgroupLabel = parentOption.parent('optgroup').attr('label');
                
                if (optgroupLabel === 'Main Categories') {
                    level = '<span class="text-info"><strong>Sub Category</strong></span> - Child of: <span class="text-primary">' + parentText + '</span>';
                    levelClass = 'alert-info';
                } else if (optgroupLabel === 'Sub Categories') {
                    level = '<span class="text-warning"><strong>Child Category</strong></span> - Grandchild of: <span class="text-primary">' + parentText.replace('→', '').trim() + '</span>';
                    levelClass = 'alert-warning';
                } else {
                    level = '<span class="text-muted">Unknown level</span>';
                    levelClass = 'alert-secondary';
                }
            }
            
            $('#categoryLevelPreview')
                .removeClass('alert-success alert-info alert-warning alert-secondary')
                .addClass(levelClass)
                .html('<strong>New Category Level:</strong> ' + level);
        }

        // Initialize level preview
        updateCategoryLevelPreview();

        // Update preview when parent changes
        $('#parent_id').change(updateCategoryLevelPreview);

        // Update preview when name changes
        $('#name').on('input', updateCategoryLevelPreview);

        // Form validation
        $('#categoryForm').submit(function(e) {
            var categoryName = $('#name').val().trim();
            var categoryType = $('#type').val();
            var parentId = $('#parent_id').val();
            
            if (!categoryName) {
                e.preventDefault();
                alert('Please enter category name');
                $('#name').focus();
                return false;
            }
            
            if (!categoryType) {
                e.preventDefault();
                alert('Please select category type');
                $('#type').focus();
                return false;
            }

            // Prevent self selection
            if (parentId == '{{ $category->id }}') {
                e.preventDefault();
                alert('Category cannot be its own parent');
                $('#parent_id').focus();
                return false;
            }
        });
    });
</script>
@endpush