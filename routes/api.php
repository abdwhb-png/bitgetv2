<?php

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BinanceController;
use App\Http\Resources\SystemSettingResource;
use App\Http\Controllers\NotificationController;

// Route::post('/notification', [NotificationController::class, 'store'])->name('notification.store');

Route::controller(BinanceController::class)->group(function () {
    Route::get('/exchange-info', 'getExchangeInfo')->name('exchange-info');
    Route::get('/ticker-price', 'getTickerPrice')->name('ticker-price');
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/config', function () {
    $setting = SystemSetting::first();
    return new SystemSettingResource($setting);
});