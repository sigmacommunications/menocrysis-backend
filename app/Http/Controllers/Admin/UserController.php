<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = User::query();
            
            // Search functionality
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhere('phone', 'like', "%$search%");
                });
            }
            
            // Filter by role
            if ($request->has('role') && $request->role != '') {
                $query->where('role', $request->role);
            }
            
            // Filter by status
            if ($request->has('status') && $request->status != '') {
                $query->where('status', $request->status);
            }
            
            $users = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return view('admin.users.index', compact('users'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading users: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'role' => 'required|in:user,barber',
                'password' => 'required|min:8|confirmed',
                'status' => 'required|in:active,inactive'
            ]);
            
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'email_verified_at' => now(),
            ]);
            
            return redirect()->route('users.index')->with('success', 'User created successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::with(['bookings', 'review', 'transaction'])->findOrFail($id);
            return view('admin.users.show', compact('user'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return view('admin.users.edit', compact('user'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'User not found: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'required|string|max:20',
                'role' => 'required|in:user,barber',
                'status' => 'required|in:active,inactive'
            ]);
            
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status,
            ]);
            
            // Update password if provided
            if ($request->password) {
                $request->validate([
                    'password' => 'min:8|confirmed'
                ]);
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }
            
            return redirect()->route('users.index')->with('success', 'User updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return redirect()->route('users.index')->with('success', 'User deleted successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update([
                'status' => $request->status
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}