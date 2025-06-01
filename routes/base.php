<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProxyController;

function registerSharedRoutes($router, array $userResourceExcept = [])
{
    $router->get('/proxy/binance', [ProxyController::class, 'proxyToBinance']);

    $router->middleware('auth')->group(function () use ($router) {
        $router->post('/fatal-error', 'App\Http\Controllers\BaseController@fatalError')->name('fatal-error');

        $router->resources([
            'notification' => NotificationController::class,
        ]);
        $router->patch('/notifications', [NotificationController::class, 'markAllAsRead'])->name('notifications.read');
        $router->delete('/notifications', [NotificationController::class, 'deleteAll'])->name('notifications.delete');

        $router->prefix('user')->as('user.')->controller(UserController::class)->group(function () use ($router) {
            $router->put('/profile-photo', 'updateProfilePhoto')->name('profile-photo.update');
            $router->put('/personal-info', 'updatePersonalInfo')->name('personal-info.update');
            $router->put('/mail-notif', 'updateMailNotif')->name('mail-notif');
            $router->put('/password/{user:email}', 'updatePassword')->name('password.update');
            $router->delete('/{user:email}', 'destroy')->name('destroy');
        });

        $router->resource('user', UserController::class)->only([
            'index',
        ]);

        $router->resource('session', SessionController::class)->only([
            'index',
            'destroy'
        ]);
    });
}
