<?php

namespace App\Listeners;

use App\NotifData;
use App\Events\FatalErrorEvent;
use App\Http\Helpers\UtilsHelper;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FatalErrorListener
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
    public function handle(FatalErrorEvent $event): void
    {
        $notifData = new NotifData('<span style="color: red;">' . $event->error . '</span>');
        $notifData->setSubject('Fatal Error');
        if ($th = $event->th) {
            $notifData->setBody($th->getMessage() . '<br>File: ' . $th->getFile() . '<br>Line: ' . $th->getLine());
        }

        UtilsHelper::notifySuperAdmins($notifData);
    }
}
