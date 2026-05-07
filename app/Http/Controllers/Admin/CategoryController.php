<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display categories with hierarchy
     */
    public function index(Request $request)
    {

            $query = Category::with(['parent', 'children.children']);

            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('type', 'like', "%$search%");
                });
            }

            // Filter by type
            if ($request->has('type') && $request->type != '') {
                $query->where('type', $request->type);
            }

            // Filter by level
            if ($request->has('level') && $request->level != '') {
                switch ($request->level) {
                    case 'main':
                        $query->whereNull('parent_id');
                        break;
                    case 'sub':
                        $query->whereNotNull('parent_id')
                              ->whereHas('parent', function($q) {
                                  $q->whereNull('parent_id');
                              });
                        break;
                    case 'child':
                        $query->whereNotNull('parent_id')
                              ->whereHas('parent', function($q) {
                                  $q->whereNotNull('parent_id');
                              });
                        break;
                }
            }

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }

            $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(20);

            return view('admin.services.index', compact('categories'));
            
       
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        try {
            $mainCategories = Category::whereNull('parent_id')->active()->get();
            $subCategories = Category::whereNotNull('parent_id')
                                   ->whereHas('parent', function($q) {
                                       $q->whereNull('parent_id');
                                   })
                                   ->active()
                                   ->get();

            return view('admin.services.create', compact('mainCategories', 'subCategories'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'status' => 'required|in:active,inactive'
            ]);

            // Auto-determine level based on parent_id
            $level = 'main';
            if ($request->parent_id) {
                $parent = Category::find($request->parent_id);
                $level = $parent->parent_id ? 'child' : 'sub';
            }

            $category = Category::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'status' => $request->status,
            ]);

            return redirect()->route('categories.index')->with('success', 'Category created successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating category: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit($id)
    {

            $category = Category::findOrFail($id);
            $mainCategories = Category::whereNull('parent_id')->active()->get();
            $subCategories = Category::whereNotNull('parent_id')
                                   ->whereHas('parent', function($q) {
                                       $q->whereNull('parent_id');
                                   })
                                   ->active()
                                   ->get();

            return view('admin.services.edit', compact('category', 'mainCategories', 'subCategories'));
            
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id',
                'type' => 'required|string|max:50',
                'sort_order' => 'nullable|integer|min:0',
                'status' => 'required|in:active,inactive'
            ]);

            // Prevent circular reference
            if ($request->parent_id == $category->id) {
                return redirect()->back()->with('error', 'Category cannot be its own parent.')->withInput();
            }

            $category->update([
                'name' => $request->name,
                'parent_id' => $request->parent_id,
                'type' => $request->type,
                'sort_order' => $request->sort_order ?? 0,
                'status' => $request->status,
            ]);

            return redirect()->route('categories.index')->with('success', 'Category updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating category: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = Category::with(['children'])->findOrFail($id);
        // // Check if category has children
        // if ($category->children->count() > 0) {
        //     response()->json(['success'=>false,'error'=> 'Cannot delete category. It has sub-categories.']);
        // }
        
        // // Check if category has services
        // if ($category->services->count() > 0) {
        //     response()->json(['success'=>false,'error'=> 'Cannot delete category. It has associated services..']);
        // }
            
        $category->delete();
        return response()->json(['success'=>true,'message'=> 'Category deleted successfully!']);
        
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $category = Category::with('children')->findOrFail($id);

            $newStatus = $category->status == 'active' ? 'inactive' : 'active';
            $category->update(['status' => $newStatus]);

            // If deactivating a main category, also deactivate its children
            if ($newStatus == 'inactive' && !$category->parent_id) {
                Category::where('parent_id', $id)->update(['status' => 'inactive']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Category status updated successfully!',
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for AJAX requests
     */
    public function getCategories(Request $request)
    {
        try {
            $parentId = $request->get('parent_id');
            $type = $request->get('type', 'service');

            $query = Category::active();

            if ($parentId) {
                $query->where('parent_id', $parentId);
            } else {
                $query->whereNull('parent_id');
            }

            if ($type) {
                $query->where('type', $type);
            }

            $categories = $query->orderBy('sort_order')->orderBy('name')->get();

            return response()->json([
                'success' => true,
                'categories' => $categories
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading categories: ' . $e->getMessage()
            ], 500);
        }
    }
}