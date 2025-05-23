<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProxyController;

Route::get('/proxy/binance', [ProxyController::class, 'proxyToBinance']);

Route::middleware('auth')->group(function () {
    Route::post('/fatal-error', 'App\Http\Controllers\BaseController@fatalError')->name('fatal-error');

    Route::resources([
        'notification' => NotificationController::class,
    ]);
    Route::patch('/notifications', [NotificationController::class, 'markAllAsRead'])->name('notifications.read');
    Route::delete('/notifications', [NotificationController::class, 'deleteAll'])->name('notifications.delete');

    Route::prefix('user')->as('user.')->controller(UserController::class)->group(function () {
        Route::put('/profile-photo', 'updateProfilePhoto')->name('profile-photo.update');
        Route::put('/personal-info', 'updatePersonalInfo')->name('personal-info.update');
        Route::put('/mail-notif', 'updateMailNotif')->name('mail-notif');
        Route::put('/password/{user:email}', 'updatePassword')->name('password.update');
        Route::delete('/{user:email}', 'destroy')->name('destroy');
    });
    Route::resource('user', UserController::class)->only([
        'index',
    ]);

    Route::resource('session', SessionController::class)->only([
        'index',
        'destroy'
    ]);
});
