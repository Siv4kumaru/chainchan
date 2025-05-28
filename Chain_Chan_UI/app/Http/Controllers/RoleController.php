<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // Define core roles that should not be easily modified/deleted
    protected $coreRoles = ['admin', 'public']; // 'user' might be redundant if 'public' is the base

    public function index()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.roles.index', compact('roles'))->with('coreRoles', $this->coreRoles);
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:50|regex:/^[a-z0-9_]+$/',
            'description' => 'nullable|string|max:100',
        ]);

        // Prevent creating a role with a core name if it somehow bypassed validation (unlikely but defensive)
        if (in_array(strtolower($request->name), $this->coreRoles)) {
            return back()->withErrors(['name' => 'SYSTEM_ALERT: Cannot_recreate_a_core_role_name.'])->withInput();
        }

        Role::create($request->all());
        return redirect()->route('admin.roles.index')->with('success', 'ROLE_CREATED_SUCCESSFULLY.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'))->with('coreRoles', $this->coreRoles);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:50', 'regex:/^[a-z0-9_]+$/',
                Rule::unique('roles')->ignore($role->roleid, 'roleid'), // Ensure using roleid here
            ],
            'description' => 'nullable|string|max:100',
        ]);

        // Prevent editing the name of core roles
        if (in_array($role->name, $this->coreRoles) && $role->name !== $request->name) {
             return back()->withErrors(['name' => 'SYSTEM_ALERT: Name_of_core_roles_cannot_be_changed.'])->withInput();
        }
        // Prevent changing a non-core role's name TO a core role name
        if (!in_array($role->name, $this->coreRoles) && in_array($request->name, $this->coreRoles)) {
            return back()->withErrors(['name' => 'SYSTEM_ALERT: Cannot_rename_role_to_a_core_role_name.'])->withInput();
        }

        $role->update($request->all());
        return redirect()->route('admin.roles.index')->with('success', 'ROLE_UPDATED_SUCCESSFULLY.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, $this->coreRoles)) {
            return redirect()->route('admin.roles.index')->with('error', 'SYSTEM_LOCK: Core_roles_('.implode(', ', $this->coreRoles).')_cannot_be_deleted.');
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with(
                'error',
                'SYSTEM_LOCK: Cannot_delete_role "' . ($role->description ?: $role->name) . '". It_is_currently_assigned_to_users.'
            );
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success', 'ROLE_DELETED_SUCCESSFULLY.');
    }
}