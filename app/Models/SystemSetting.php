<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SystemSetting extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'deposit_wallets' => 'array',
    ];


    protected function depositWallets(): Attribute
    {
        return Attribute::get(function (): array {
            return PaymentMethod::all()->toArray();
        });
    }
}