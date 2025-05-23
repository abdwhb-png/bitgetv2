<?php

namespace App\Observers;

use App\Enums\PermissionsEnum;
use App\Concerns\UserRoleTrait;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleObserver
{
    use UserRoleTrait;

    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        $this->handleRole($role);
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "restored" event.
     */
    public function restored(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     */
    public function forceDeleted(Role $role): void
    {
        //
    }
}