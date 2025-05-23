<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckOrderJob implements ShouldQueue
{
    use Queueable;
    public $tries = 25;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Order::whereNull('closed_at')->get()->each(function (Order $order) {
            $validity = now()->timestamp - $order->exec_time;

            if ($validity >= $order->expiration) {
                $order->update([
                    'closed_at' => now()->timestamp,
                ]);
            }
        });
    }
}