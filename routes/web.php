<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use Illuminate\Support\Facades\Storage;

require_once __DIR__ . '/base.php';

registerSharedRoutes(Route::getFacadeRoot());

Route::middleware([
    'auth',
    config('jetstream.auth_session'),
    'role:user',
])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home')->withViewData(['getHomeScript' => true]);
    })->name('home');

    Route::get('/dashboard', function () {
        return redirect('/');
    })->name('dashboard');

    Route::inertia('/trade', 'Trade')->name('trade');

    Route::inertia('/news', 'News')->name('news');

    Route::inertia('/asset', 'Asset')->name('asset');

    Route::get('/swap', [AppController::class, 'swap'])->name('swap');

    Route::inertia('history', 'History')->name('history');

    Route::as('my.')->prefix('my')->group(function () {
        $prefix = 'My/';
        Route::inertia('/', $prefix . 'Index')->name('index');

        Route::get('/account', function () use ($prefix) {
            return Inertia::render($prefix . 'Account', [
                'countries' => Storage::json('countries.json')
            ]);
        })->name('account');

        Route::get('/verification/{type}', [AppController::class, 'verification'])->name('verification');
        Route::put('/verification', [AppController::class, 'verificationStore'])->name('verification.store');
    });
    Route::put('/wallet-address', [AppController::class, 'walletAddressUpdate'])->name('wallet-address.update');

    Route::get('/transaction/{type}', function (string $type) {
        return Inertia::render('Transaction', [
            'transactionType' => $type
        ]);
    })->name('transaction');
    Route::put('/transaction', [AppController::class, 'transactionStore'])->name('transaction.store');
    Route::get('/transaction', [AppController::class, 'transactionIndex'])->name('transaction.index');

    Route::get('/order', [AppController::class, 'orderIndex'])->name('order.index');
    Route::put('/order', [AppController::class, 'orderStore'])->name('order.store');
    Route::post('/order/{order:ref_id}', [AppController::class, 'orderClose'])->name('order.close');

    Route::put('/payment-proof', [AppController::class, 'paymentProof'])->name('payment-proof');
    Route::put('/swap', [AppController::class, 'swapStore'])->name('swap.store');
});
