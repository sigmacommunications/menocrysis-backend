<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarberService;
use App\Models\User;

class ServiceController extends Controller
{
    /**
     * Display a listing of barber-created services.
     */
    public function index(Request $request)
    {
        try {
            $query = BarberService::with(['barber', 'service']);
            
            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                      ->orWhereHas('barber', function($q) use ($search) {
                          $q->where('first_name', 'like', "%$search%")
                            ->orWhere('last_name', 'like', "%$search%");
                      });
                });
            }
            
            // Filter by barber
            if ($request->has('barber_id') && $request->barber_id != '') {
                $query->where('user_id', $request->barber_id);
            }
            
            // Filter by main service
            if ($request->has('main_service') && $request->main_service != '') {
                $query->where('main_service', $request->main_service);
            }
            
            $services = $query->orderBy('created_at', 'desc')->paginate(10);
            $barbers = User::where('role', 'barber')->where('status', 'active')->get();
            
            return view('admin.services.index', compact('services', 'barbers'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading services: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     * REMOVED - Admin cannot create services
     */
    public function create()
    {
        return redirect()->route('services.index')->with('error', 'Admin cannot create services. Services are created by barbers.');
    }

    /**
     * Store a newly created resource in storage.
     * REMOVED - Admin cannot create services
     */
    public function store(Request $request)
    {
        return redirect()->route('services.index')->with('error', 'Admin cannot create services. Services are created by barbers.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $service = BarberService::with(['barber', 'service_info', 'bookings'])->findOrFail($id);
            return view('admin.services.show', compact('service'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Service not found: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     * REMOVED - Admin cannot edit services
     */
    public function edit(string $id)
    {
        return redirect()->route('services.index')->with('error', 'Admin cannot edit services. Services are managed by barbers.');
    }

    /**
     * Update the specified resource in storage.
     * REMOVED - Admin cannot edit services
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('services.index')->with('error', 'Admin cannot edit services. Services are managed by barbers.');
    }

    /**
     * Remove the specified resource from storage.
     * Admin can delete inappropriate services
     */
    public function destroy(string $id)
    {
        try {
            $service = BarberService::findOrFail($id);
            $service->delete();
            
            return redirect()->route('services.index')->with('success', 'Service deleted successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting service: ' . $e->getMessage());
        }
    }

    /**
     * Toggle service status (Active/Inactive)
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $service = BarberService::findOrFail($id);
            $service->update([
                'status' => $service->status == 'active' ? 'inactive' : 'active'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Service status updated successfully!',
                'new_status' => $service->status
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}