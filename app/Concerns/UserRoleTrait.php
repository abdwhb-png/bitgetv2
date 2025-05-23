<?php

namespace App\Concerns;

use App\Models\User;
use App\Enums\PermissionsEnum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait UserRoleTrait
{
    /**
     * Assigns admin permissions to the given user or role.
     *
     * @param User|Role $item User or Role object to assign permissions to
     */
    protected function adminPerms(User | Role $item): void
    {
        // Sync permissions to only allow inviting users for admin role
        $item->syncPermissions(PermissionsEnum::INVITEUSERS->value);
    }

    /**
     * Assigns super admin permissions to the given user or role.
     *
     * @param User|Role $item User or Role object to assign permissions to
     */
    protected function superAdminPerms(User | Role $item): void
    {
        // Sync permissions allowing all except editing permissions for super admin
        $item->syncPermissions(Permission::where('name', '!=', PermissionsEnum::EDITPERMISSIONS->value)->pluck('name'));
    }

    /**
     * Grants all permissions to the given user or role.
     *
     * @param User|Role $item User or Role object to assign permissions to
     */
    protected function rootPerms(User | Role $item): void
    {
        // Give all permissions to root role
        $item->givePermissionTo(Permission::all()->pluck('name'));
    }

    /**
     * Handles the assignment of permissions to a user based on their role.
     *
     * @param User $user The user object to assign permissions to
     * @param string|null $role Optional role name to assign
     */
    public function handleUser(User $user, ?string $role = ''): void
    {
        // Check if user or specified role is root and assign permissions accordingly
        if ($user->hasRole('root') || $role == 'root') {
            $this->rootPerms($user);
        }

        // Check if user or specified role is super-admin and assign permissions accordingly
        if ($user->hasRole('super-admin') || $role == 'super-admin') {
            $this->superAdminPerms($user);
        }

        // Check if user or specified role is admin and assign permissions accordingly
        if ($user->hasRole('admin') || $role == 'admin') {
            $this->adminPerms($user);
        }
    }

    /**
     * Handles the assignment of permissions to a role.
     *
     * @param Role $role The role object to assign permissions to
     */
    public function handleRole(Role $role): void
    {
        // If the role is root, assign root permissions
        if ($role->name == 'root') {
            $this->rootPerms($role);
        }

        // If the role is super-admin, assign super admin permissions
        if ($role->name == 'super-admin') {
            $this->superAdminPerms($role);
        }

        // If the role is admin, assign admin permissions
        if ($role->name == 'admin') {
            $this->adminPerms($role);
        }
    }
}