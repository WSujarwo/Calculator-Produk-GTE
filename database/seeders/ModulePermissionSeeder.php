<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ModulePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = $this->managedPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function managedPermissions(): array
    {
        $groupedModules = config('module_permissions.groups', []);
        $modules = [];

        if (! empty($groupedModules)) {
            foreach ($groupedModules as $items) {
                $modules = array_merge($modules, array_keys($items));
            }
        } else {
            $modules = array_keys(config('module_permissions.modules', []));
        }

        $actions = array_keys(config('module_permissions.actions', []));

        $permissions = [];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissions[] = $module . '.' . $action;
            }
        }

        return $permissions;
    }
}
