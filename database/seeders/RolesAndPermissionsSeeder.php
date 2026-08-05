<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates every role and permission declared in config/access.php and syncs
 * each role's permission set. Safe to re-run: roles/permissions are
 * findOrCreate'd and role permissions are fully synced (not appended), so the
 * config file stays the single source of truth.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('access.guard');

        $allPermissions = collect(config('access.permissions'))->flatten()->unique()->values();

        $allPermissions->each(
            fn (string $name) => Permission::findOrCreate($name, $guard)
        );

        foreach (config('access.roles') as $slug => $label) {
            $role = Role::findOrCreate($slug, $guard);

            $permissions = config("access.role_permissions.{$slug}", []);

            $role->syncPermissions(
                in_array('*', $permissions, true) ? $allPermissions : $permissions
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
