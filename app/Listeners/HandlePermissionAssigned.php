<?php

namespace App\Listeners;

use App\Events\PermissionAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePermissionAssigned
{
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
    public function handle(PermissionAssigned $event): void
    {
        //
    }
}