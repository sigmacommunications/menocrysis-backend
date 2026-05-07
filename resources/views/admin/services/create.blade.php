@extends('admin.layouts.master')
@section('title')
Add New Category
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Add New Category</h1>
            <p class="text-muted">Create new category with hierarchical structure</p>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Categories
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Category Details</h3>
        </div>
        
        <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
            @csrf
            
            <div class="card-body">
                <div class="row">
                    <!-- Category Name -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Category Name *</label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" 
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
                                    @foreach($mainCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                
                                <!-- Sub Categories -->
                                <optgroup label="Sub Categories">
                                    @foreach($subCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;→ {{ $category->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <small class="form-text text-muted">
                                Leave empty to create main category. Select parent for sub/child categories.
                            </small>
                            @error('parent_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>


                    <!-- Status -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Category Level Preview -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info" id="categoryLevelPreview">
                            <strong>Category Level:</strong> 
                            <span class="text-muted">Select parent category to see level</span>
                        </div>
                    </div>
                </div>

                <!-- Hierarchy Explanation -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-info-circle"></i> Category Hierarchy</h6>
                            <ul class="mb-0 pl-3">
                                <li><strong>Main Category:</strong> No parent selected</li>
                                <li><strong>Sub Category:</strong> Parent is a Main Category</li>
                                <li><strong>Child Category:</strong> Parent is a Sub Category</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Category
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
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
            
            var level = '';
            var levelClass = '';
            
            if (!parentId) {
                level = '<span class="text-success"><strong>Main Category</strong></span> - This will be a top-level category';
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
                .html('<strong>Category Level:</strong> ' + level);
        }

        // Initialize level preview
        updateCategoryLevelPreview();

        // Update preview when parent changes
        $('#parent_id').change(updateCategoryLevelPreview);

        // Form validation
        $('#categoryForm').submit(function(e) {
            var categoryName = $('#name').val().trim();
            
            if (!categoryName) {
                e.preventDefault();
                alert('Please enter category name');
                $('#name').focus();
                return false;
            }
            
        });

        // Auto-update preview on name change
        $('#name').on('input', function() {
            updateCategoryLevelPreview();
        });
    });
</script>
@endpush