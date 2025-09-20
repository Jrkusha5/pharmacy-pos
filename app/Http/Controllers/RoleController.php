<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('permission:role_management');
    }

    public function index()
{
    $roles = Role::where('name', '!=', 'Super Admin')->get();
    $permissions = Permission::all(); // Add this line
    return view('roles.index', compact('roles', 'permissions')); // Add permissions to compact
}

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

   public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:roles,name',
        'permissions' => 'required|array',
        'permissions.*' => 'exists:permissions,id', // This ensures permissions exist
    ]);

    // Convert permission IDs to permission names
    $permissionNames = Permission::whereIn('id', $request->permissions)
                                ->pluck('name')
                                ->toArray();

    $role = Role::create(['name' => $request->name]);
    $role->givePermissionTo($permissionNames); // Use names instead of IDs

    return redirect()->route('roles.index')
        ->with('success', 'Role created successfully.');
}
    public function edit(Role $role)
    {
        if ($role->name === 'Super Admin') {
            abort(403, 'Super Admin role cannot be edited.');
        }

        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
{
    if ($role->name === 'Super Admin') {
        abort(403, 'Super Admin role cannot be updated.');
    }

    $request->validate([
        'name' => 'required|string|unique:roles,name,' . $role->id,
        'permissions' => 'required|array',
        'permissions.*' => 'exists:permissions,id',
    ]);

    // Convert permission IDs to permission names
    $permissionNames = Permission::whereIn('id', $request->permissions)
                                ->pluck('name')
                                ->toArray();

    $role->update(['name' => $request->name]);
    $role->syncPermissions($permissionNames); // Use names instead of IDs

    return redirect()->route('roles.index')
        ->with('success', 'Role updated successfully.');
}

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            abort(403, 'Super Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
