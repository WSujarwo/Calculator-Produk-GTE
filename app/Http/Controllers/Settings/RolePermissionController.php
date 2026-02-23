<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $users = User::with('roles')->orderBy('name')->get();

        $selectedRoleName = $request->query('role');
        $selectedRole = $roles->firstWhere('name', $selectedRoleName) ?? $roles->first();

        return view('setting', [
            'roles' => $roles,
            'users' => $users,
            'selectedRole' => $selectedRole,
            'permissionRows' => $this->permissionRows(),
            'actionLabels' => config('module_permissions.actions', []),
        ]);
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $managedPermissions = $this->managedPermissions();

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', $managedPermissions)],
        ]);

        $selected = collect($validated['permissions'] ?? [])->values()->all();

        $unmanagedPermissions = $role->permissions()
            ->whereNotIn('name', $managedPermissions)
            ->pluck('name')
            ->all();

        $role->syncPermissions(array_values(array_unique(array_merge($unmanagedPermissions, $selected))));

        return redirect()
            ->route('setting', ['role' => $role->name])
            ->with('success', "Permission untuk role '{$role->name}' berhasil diupdate.");
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->syncRoles([$validated['role_name']]);

        return redirect()->route('setting')->with('success', "Role user '{$user->name}' berhasil diupdate.");
    }

    private function permissionRows(): array
    {
        $groupedModules = $this->groupedModules();
        $actions = array_keys(config('module_permissions.actions', []));

        $rows = [];
        foreach ($groupedModules as $groupLabel => $modules) {
            foreach ($modules as $moduleKey => $moduleLabel) {
                $permissions = [];
                foreach ($actions as $action) {
                    $permissions[$action] = $moduleKey . '.' . $action;
                }

                $rows[] = [
                    'group_label' => $groupLabel,
                    'module_key' => $moduleKey,
                    'module_label' => $moduleLabel,
                    'permissions' => $permissions,
                ];
            }
        }

        return $rows;
    }

    private function groupedModules(): array
    {
        $groups = config('module_permissions.groups', []);
        if (! empty($groups)) {
            return $groups;
        }

        $modules = config('module_permissions.modules', []);
        return ['General' => $modules];
    }

    private function managedPermissions(): array
    {
        return collect($this->permissionRows())
            ->flatMap(fn ($row) => array_values($row['permissions']))
            ->values()
            ->all();
    }
}
