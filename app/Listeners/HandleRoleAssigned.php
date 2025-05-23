<?php

namespace App\Listeners;

use App\Enums\RolesEnum;
use App\Events\RoleAssigned;
use App\Enums\PermissionsEnum;
use App\Concerns\UserRoleTrait;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Permission\Models\Permission;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleRoleAssigned
{
    use UserRoleTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RoleAssigned $event): void
    {
        $this->handleUser($event->user, $event->roleName);
    }
}