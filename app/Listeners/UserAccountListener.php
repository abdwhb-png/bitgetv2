<?php

namespace App\Listeners;

use App\Models\Balance;
use App\Models\PaymentMethod;
use App\Events\UserAccountEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserAccountListener
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
    public function handle(UserAccountEvent $event): void
    {
        $account = $event->account;

        if ($event->state == 'created' && $event->section == 'account') {
            PaymentMethod::all()->each(function ($paymentMethod) use ($account) {
                $account->paymentMethods()->attach($paymentMethod->id);

                Balance::create([
                    'user_account_id' => $account->id,
                    'asset_id' => $paymentMethod->id,
                    'wallet' => $paymentMethod->name,
                ]);
            });
        }
    }
}