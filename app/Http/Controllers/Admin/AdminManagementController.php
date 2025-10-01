<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminRole;
use App\Models\AdminPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    public function __construct()
    {
        // Middleware is handled at the route level
    }
    
    /**
     * Display a listing of admins.
     */
    public function index()
    {
        $admins = User::whereHas('adminRoles')
                     ->with(['adminRoles.permissions'])
                     ->paginate(15);
        
        $roles = AdminRole::active()->with('permissions')->get();
        
        return view('admin.admins.index', compact('admins', 'roles'));
    }
    
    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $roles = AdminRole::active()->get();
        return view('admin.admins.create', compact('roles'));
    }
    
    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:admin_roles,id',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
        ]);
        
        $user->adminRoles()->attach($request->role_id, [
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);
        
        return redirect()->route('admin.admins.index')
                        ->with('success', 'Admin created successfully.');
    }
    
    /**
     * Display the specified admin.
     */
    public function show(User $admin)
    {
        $admin->load(['adminRoles.permissions', 'assignedTickets.report', 'activeTickets.report']);
        
        return view('admin.admins.show', compact('admin'));
    }
    
    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin)
    {
        $roles = AdminRole::active()->get();
        $admin->load('adminRoles');
        
        return view('admin.admins.edit', compact('admin', 'roles'));
    }
    
    /**
     * Update the specified admin.
     */
    public function update(Request $request, User $admin)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($admin->id)],
            'password' => 'nullable|min:8|confirmed',
            'is_active' => 'boolean',
        ]);
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->boolean('is_active'),
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $admin->update($updateData);
        
        return redirect()->route('admin.admins.index')
                        ->with('success', 'Admin updated successfully.');
    }
    
    /**
     * Assign a role to an admin.
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:admin_roles,id',
            'expires_at' => 'nullable|date|after:now',
        ]);
        
        // Check if user already has this role
        if ($user->adminRoles()->where('role_id', $request->role_id)->exists()) {
            return back()->with('error', 'User already has this role.');
        }
        
        $user->adminRoles()->attach($request->role_id, [
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);
        
        return back()->with('success', 'Role assigned successfully.');
    }
    
    /**
     * Remove a role from an admin.
     */
    public function removeRole(User $user, AdminRole $role)
    {
        $user->adminRoles()->updateExistingPivot($role->id, [
            'is_active' => false,
        ]);
        
        return back()->with('success', 'Role removed successfully.');
    }
    
    /**
     * Get admin workload statistics.
     */
    public function workload()
    {
        $admins = User::whereHas('adminRoles')
                     ->withCount(['activeTickets', 'completedTickets'])
                     ->with('adminRoles')
                     ->get();
        
        return response()->json($admins);
    }
    
    /**
     * Get admin performance metrics.
     */
    public function performance(User $admin)
    {
        $metrics = [
            'total_tickets' => $admin->assignedTickets()->count(),
            'active_tickets' => $admin->activeTickets()->count(),
            'completed_tickets' => $admin->completedTickets()->count(),
            'avg_resolution_time' => $this->getAdminAverageResolutionTime($admin),
            'roles' => $admin->adminRoles()->pluck('name'),
            'permissions' => $admin->getAdminPermissions(),
        ];
        
        return response()->json($metrics);
    }
    
    /**
     * Get admin average resolution time (SQLite-compatible).
     */
    private function getAdminAverageResolutionTime(User $admin)
    {
        $assignments = $admin->completedTickets()
                            ->select('assigned_at', 'completed_at')
                            ->get();
        
        if ($assignments->isEmpty()) {
            return 0;
        }
        
        $totalHours = $assignments->sum(function ($assignment) {
            $assigned = \Carbon\Carbon::parse($assignment->assigned_at);
            $completed = \Carbon\Carbon::parse($assignment->completed_at);
            return $assigned->diffInHours($completed);
        });
        
        return round($totalHours / $assignments->count(), 2);
    }
}
