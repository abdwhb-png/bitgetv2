<?php

namespace App\Models;

use App\Events\UserAccountEvent;
use App\Events\UserEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserAccount extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'wallet_addresses' => 'array',
        'order_profit_range' => 'array',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];


    static function booted()
    {
        static::created(function ($account) {
            event(new UserAccountEvent($account, 'created'));
        });

        static::updated(function ($account) {
            event(new UserAccountEvent($account, 'updated'));
        });
    }


    protected function balance(): Attribute
    {
        return Attribute::get(function (): float {
            $balance = 0;

            $this->balances->each(function ($b) use (&$balance) {
                $balance += $b->amount;
            });

            return round($balance, 2);
        });
    }


    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class);
    }


    public function getBalance($wallet = 'total'): float
    {
        if ($wallet === 'total') {
            return $this->balance;
        }

        $b = $this->balances()->whereHas('asset', function (Builder $query) use ($wallet) {
            $query->where('name', $wallet)->orWhere('symbol', $wallet);
        })->first();

        return $b ? $b->amount : 0.00;
    }


    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }


    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class)->withPivot('id', 'address')->withTimestamps();
    }


    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch(Builder $query, $search)
    {
        $query->Where('account_no', 'like', '%' . $search . '%');
    }
}
