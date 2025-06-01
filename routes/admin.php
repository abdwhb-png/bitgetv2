<?php

use App\Enums\PermissionsEnum;
use App\Enums\RolesEnum;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\OrdersController;
use App\Http\Controllers\Admin\TransactionsController;
use App\Http\Controllers\Admin\VerificationsController;
use App\Http\Controllers\RolePermissionController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

require_once __DIR__ . '/base.php';

registerSharedRoutes(Route::getFacadeRoot());

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    $roles = [RolesEnum::ADMIN->value, RolesEnum::SUPERADMIN->value, RolesEnum::ROOT->value];
    Route::middleware('role:' . implode('|', $roles))
        ->group(function () {
            Route::inertia('/notifications', 'Notifications')->name('notifications');

            // Admin dashboard and settings routes
            Route::controller(IndexController::class)->group(function () {
                Route::get('/dashboard', 'index')->name('dashboard');

                // Payment methods routes
                Route::post('/pmethod', 'pmethodStore')->name('pmethod.store');
                Route::delete('/pmethod/{item}', 'pmethodDestroy')->name('pmethod.destroy');
                Route::put('/pmethod/{item}', 'pmethodUpdate')->name('pmethod.update');

                // Customer service routes
                Route::put('/cservice/{item}', 'cserviceUpdate')->name('cservice.update');

                // System settings routes
                Route::put('/setting/{item}', 'settingUpdate')->name('setting.update');
            });

            // Users routes
            Route::controller(UsersController::class)->group(function () {
                Route::get('/users', 'index')->name('users.index');
                Route::get('/users-get', 'get')->name('users.get');
                Route::put('/users/{id}', 'update')->name('users.update');
            });

            // Transactions routes
            Route::controller(TransactionsController::class)->group(function () {
                Route::get('/transactions', 'index')->name('transactions.index');
                Route::get('/transactions-get', 'get')->name('transactions.get');
                Route::put('/transactions/{transaction}', 'update')->name('transactions.update');
            });

            // Orders routes
            Route::controller(OrdersController::class)->group(function () {
                Route::get('/orders', 'index')->name('orders.index');
                Route::get('/orders-get', 'get')->name('orders.get');
                Route::put('/orders/{order}', 'update')->name('orders.update');
            });

            // Verifications routes
            Route::controller(VerificationsController::class)->group(function () {
                Route::get('/verifications', 'index')->name('verifications.index');
                Route::get('/verifications-get', 'get')->name('verifications.get');
                Route::put('/verifications/{id}', 'update')->name('verifications.update');
            });
        });

    Route::middleware(['role_or_permission:' . RolesEnum::ROOT->value . '|' . PermissionsEnum::MANAGEADMIN->value])->group(function () {
        Route::get('/admins', [UsersController::class, 'adminsIndex'])->name('admins.index');
        Route::put('/admins/{user:email}', [UsersController::class, 'adminsUpdate'])->name('admins.update');

        Route::resource('roles-perms', RolePermissionController::class)->only([
            'index',
            'store',
        ]);
        Route::put('/roles-perms/{user:email}', [RolePermissionController::class, 'update'])->name('roles-perms.update');
    });
});

Route::get('/app-health', HealthCheckResultsController::class)->name('app.health');
