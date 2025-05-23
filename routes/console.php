<?php

use App\Models\User;
use App\CheckPendingOrder;
use App\Concerns\UserRoleTrait;
use App\Models\Balance;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Permission\Models\Role;

Schedule::call(new CheckPendingOrder)->everyThirtySeconds();

Schedule::command(\Spatie\Health\Commands\RunHealthChecksCommand::class)->everyFiveMinutes();

Schedule::command("queue:work --stop-when-empty --queue=emails")->everyFiveMinutes();
Schedule::command("queue:work --stop-when-empty")->everyThirtyMinutes();

Artisan::command('give:role {id} {role}', function ($id, $role) {
    $user = User::findOrfail($id);
    $role = Role::where('name', $role)->firstOrfail();

    $user->syncRoles($role);

    $this->info("'{$role->name}' role has been granted to user with ID: {$id} -> {$user->email}");
})->purpose('Give role permissions to a user');


Artisan::command('update:balances', function () {
    $count = 0;

    Balance::all()->each(function ($balance) use (&$count) {
        if ($balance->asset) {
            $balance->updateQuietly([
                'wallet' => $balance->asset->name,
            ]);

            $count++;
        }
    });

    $this->info($count . " balances have been updated");
})->purpose('Set all balances wallet with asset to the asset name');
