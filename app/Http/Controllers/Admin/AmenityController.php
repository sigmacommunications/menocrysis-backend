<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Amenity;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Amenity::query();
            
            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhere('description', 'like', "%$search%");
                });
            }
            
            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            $amenities = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return view('admin.amenities.index', compact('amenities'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading amenities: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.amenities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:amenities,name',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive'
            ]);
            
            $amenity = Amenity::create([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
                'status' => $request->status,
            ]);
            
            return redirect()->route('amenities.index')->with('success', 'Amenity created successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating amenity: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $amenity = Amenity::findOrFail($id);
            return view('admin.amenities.show', compact('amenity'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Amenity not found: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $amenity = Amenity::findOrFail($id);
            return view('admin.amenities.edit', compact('amenity'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Amenity not found: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $amenity = Amenity::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255|unique:amenities,name,' . $id,
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'status' => 'required|in:active,inactive'
            ]);
            
            $amenity->update([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon,
                'status' => $request->status,
            ]);
            
            return redirect()->route('amenities.index')->with('success', 'Amenity updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating amenity: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $amenity = Amenity::findOrFail($id);
            $amenity->delete();
            
            return redirect()->route('amenities.index')->with('success', 'Amenity deleted successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting amenity: ' . $e->getMessage());
        }
    }

    /**
     * Toggle amenity status
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $amenity = Amenity::findOrFail($id);
            $newStatus = $amenity->status == 'active' ? 'inactive' : 'active';
            
            $amenity->update([
                'status' => $newStatus
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Amenity status updated successfully!',
                'new_status' => $newStatus,
                'status_text' => ucfirst($newStatus)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get amenities statistics
     */
    public function statistics()
    {
        try {
            $totalAmenities = Amenity::count();
            $activeAmenities = Amenity::where('status', 'active')->count();
            $inactiveAmenities = Amenity::where('status', 'inactive')->count();
            
            $recentAmenities = Amenity::orderBy('created_at', 'desc')->take(5)->get();
            
            return response()->json([
                'success' => true,
                'statistics' => [
                    'total_amenities' => $totalAmenities,
                    'active_amenities' => $activeAmenities,
                    'inactive_amenities' => $inactiveAmenities,
                ],
                'recent_amenities' => $recentAmenities
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}