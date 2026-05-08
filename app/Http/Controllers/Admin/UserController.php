<?php

namespace App\Http\Controllers\Admin;
<<<<<<< HEAD

=======
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
<<<<<<< HEAD

class UserController extends Controller
{
    // Define allowed roles as class constant
    const ALLOWED_ROLES = ['admin', 'manager', 'agent', 'charging', 'support', 'mis', 'mis-manager'];

=======
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with('createdBy')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form for creating a new user
     */
    public function create()
    {
<<<<<<< HEAD
        $roles = self::ALLOWED_ROLES;
=======
        $roles = Role::all()->pluck('name');
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'alias_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'extension_number' => 'nullable|alpha_num|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
<<<<<<< HEAD
            'role' => 'required|string|in:' . implode(',', self::ALLOWED_ROLES),
=======
            'role' => 'required|string|exists:roles,name',
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'alias_name' => $request->alias_name,
            'email' => $request->email,
            'extension_number' => $request->role === 'agent' ? $request->extension_number : null,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'agent_custom_id' => $this->generateAgentId($request->role),
            'role' => $request->role,
<<<<<<< HEAD
            'is_active' => $request->has('is_active'),
            'is_blocked' => false,
            'created_by' => auth()->id(),
            'email_verified_at' => now(),
        ]);

        // Send welcome email
        if ($user && $user->email) {
=======
            'is_active' => $request->has('is_active') ? true : false,
            'is_blocked' => false,
            'created_by' => auth()->id(),
            'email_verified_at' => now(), // Auto verify
        ]);

        // Assign Spatie role
        $user->assignRole($request->role);

        if($user && $user->email) {
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
            Mail::to($user->email)->send(new WelcomeMail($user));
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show form for editing user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
<<<<<<< HEAD
        $roles = self::ALLOWED_ROLES;
=======
        $roles = Role::all()->pluck('name');
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'alias_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'extension_number' => 'nullable|alpha_num|max:20',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
<<<<<<< HEAD
            'role' => 'required|string|in:' . implode(',', self::ALLOWED_ROLES),
=======
            'role' => 'required|string|exists:roles,name',
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
            'is_active' => 'sometimes|boolean',
            'is_blocked' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'alias_name' => $request->alias_name,
            'email' => $request->email,
            'extension_number' => $request->role === 'agent' ? $request->extension_number : null,
            'phone' => $request->phone,
            'role' => $request->role,
<<<<<<< HEAD
            'is_active' => $request->has('is_active'),
            'is_blocked' => $request->has('is_blocked'),
=======
            'is_active' => $request->has('is_active') ? true : false,
            'is_blocked' => $request->has('is_blocked') ? true : false,
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

<<<<<<< HEAD
        // Update agent_custom_id if role changed to agent
=======
        // Update agent_custom_id if role changed and it's an agent
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
        if ($user->role !== $request->role && $request->role === 'agent') {
            $updateData['agent_custom_id'] = $this->generateAgentId($request->role);
        }

        $user->update($updateData);

<<<<<<< HEAD
=======
        // Sync Spatie role
        $user->syncRoles([$request->role]);

>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Toggle user active status
     */
    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Toggle user block status
     */
    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent blocking yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot block your own account.');
        }

        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'blocked' : 'unblocked';
        
        return redirect()->back()
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

<<<<<<< HEAD
        // Prevent deleting admin user
        if ($user->role === 'admin') {
            return redirect()->back()
                ->with('error', 'Cannot delete admin user.');
        }

=======
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
        // Check if user has related records
        if ($user->bookings()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete user with existing bookings.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Generate agent custom ID
     */
    private function generateAgentId($role)
    {
        $prefix = match($role) {
            'admin' => 'ADM',
            'manager' => 'MGR',
            'agent' => 'AGT',
<<<<<<< HEAD
            'charging' => 'CHG',
            'support' => 'SUP',
            'mis' => 'MIS',
            'changes' => 'CHANGES',
            'mis-manager' => 'MIS_MGR',
            default => 'USR'
        };
        
        // Add timestamp for uniqueness
        return $prefix . '_' . time() . '_' . strtoupper(substr(uniqid(), -6));
=======
            'charge' => 'CHG',
            'support' => 'SUP',
            'mis' => 'MIS',
            default => 'USR'
        };
        
        return $prefix . '_' . strtoupper(uniqid());
>>>>>>> 06924c1a30d5822418525d51da97f03dc316d9f7
    }
}