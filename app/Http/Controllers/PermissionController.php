<?php

namespace App\Http\Controllers;

use App\AccountsPermissions;
use App\ControlAccount;
use App\MainAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\role_permission;
use App\SubControlAccount;
use App\TransactionAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{

    // Create New Permission
    public function index()
    {
        $permissions = Permission::with('parent')->get(); // Assuming a parent-child relationship
        return view('permission.per_create.index', compact('permissions'));
    }

    // Show create form
    public function create()
    {
        $modules = Permission::where('parent_id', 0)->get(); // Fetch main modules
        return view('permission.per_create.create', compact('modules'));
    }

    public function getSubModules(Request $request)
    {
        $parentId = $request->input('parent_id');
        $subModules = Permission::where('parent_id', $parentId)->get();

        return response()->json([
            'subModules' => $subModules,
        ]);
    }

    // Store new permission
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:permissions,id',
            'sub_module_id' => 'nullable|exists:permissions,id',
        ]);

        // Determine the parent ID
        $parentId = $request->sub_module_id ?? $request->parent_id ?? 0; // Default to 0 for top-level modules

        // Create the permission
        Permission::create([
            'name' => $request->name,
            'parent_id' => $parentId, // Store the appropriate parent
            'guard_name' => 'web', // Assuming you use 'web' as the guard name
        ]);

        return redirect()->route('permission.index')->with('success', 'Permission created successfully.');
    }


    // Show edit form
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $modules = Permission::where('parent_id', 0)->get(); // Parent modules
        $subModules = Permission::where('parent_id', '!=', 0)->get(); // Sub-modules

        return view('permission.per_create.edit', compact('permission', 'modules', 'subModules'));
    }

    // Update permission
    public function update(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:permissions,id',
            'sub_module_id' => 'nullable|integer|exists:permissions,id',
        ]);

        // Find the permission by its ID
        $permission = Permission::findOrFail($id);

        // Update the permission's details
        $permission->name = $request->name;

        // If a parent module is selected, update the parent_id
        if ($request->has('parent_id')) {
            $permission->parent_id = $request->parent_id;
        } else {
            $permission->parent_id = 0; // Set to 0 if no parent is selected
        }

        // If a sub-module is selected, update the sub_module_id
        if ($request->has('sub_module_id')) {
            $permission->sub_module_id = $request->sub_module_id;
        } else {
            $permission->sub_module_id = null; // Set to null if no sub-module is selected
        }

        // Save the updated permission
        $permission->save();

        // Redirect to the permission list with a success message
        return redirect()->route('permission.index')->with('success', 'Permission updated successfully.');
    }


    // Delete permission
    public function destroy(Permission $permission)
    {
        if ($permission->children()->exists()) {
            return redirect()->route('permission.index')->with('error', 'Cannot delete a module with sub-modules.');
        }

        $permission->delete();
        return redirect()->route('permission.index')->with('success', 'Permission deleted successfully.');
    }










    // Assin Permissions to Roles and users

    public function permission_index(Request $request)
    {
        $roles = Role::all();
        $role_id = $request->id ?? 0;

        $permissions = Permission::all();


        $assigned_permissions = $role_id ? Role::find($role_id)->permissions->pluck('id')->toArray() : [];

        return view('permission.index', compact('roles', 'role_id', 'permissions', 'assigned_permissions'));
    }


    public function assignPermissions(Request $request, $role_id)
    {
        // Validate the request
        $validated = $request->validate([
            'permissions' => 'array',
            'permission.*' => 'integer|exists:permissions,id',
        ]);

        // Find the role
        $role = Role::find($role_id);
        if (!$role) {
            return redirect()->back()->with('alert', 'Role not found')->with('alert-class', 'danger');
        }

        // Filter permissions for the 'web' guard
        $permissions = Permission::whereIn('id', $validated['permissions'] ?? [])
            ->where('guard_name', 'web')
            ->pluck('name') // Get the names of the permissions
            ->toArray();

        // Sync permissions to the role (adds and removes accordingly)
        $role->syncPermissions($permissions);

        try {
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        } catch (\Throwable $e) {
        }

        // Set success response
        return redirect()->back()->with('alert', 'Permissions updated successfully for role: ' . $role->name)->with('alert-class', 'success');
    }
}
