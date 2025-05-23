<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PaymentMethod extends Model
{
    protected $guarded = [
        'id',
    ];

    static public function booted()
    {
        static::created(function ($paymentMethod) {
            UserAccount::all()->each(function ($account) use ($paymentMethod) {
                $account->paymentMethods()->attach($paymentMethod->id);
                $account->balances()->updateOrCreate([
                    'user_account_id' => $account->id,
                    'asset_id' => $paymentMethod->id
                ], [
                    'amount' => 0,
                    'wallet' => $paymentMethod->name,
                ]);
            });
        });

        static::deleting(function ($paymentMethod) {
            Transaction::where('asset_id', $paymentMethod->id)->orWhere('method', $paymentMethod->name)->delete();
        });
    }

    public function userAccounts(): BelongsToMany
    {
        return $this->belongsToMany(UserAccount::class)->withPivot('id', 'address')->withTimestamps();
    }
}
