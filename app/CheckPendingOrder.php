<?php

namespace App;

use App\Jobs\CheckOrderJob;

class CheckPendingOrder
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Invoke the class instance.
     */
    public function __invoke(): void
    {
        dispatch_sync(new CheckOrderJob());
    }
}
