<?php

namespace App\Models;

use App\Events\UserAccountEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    protected $guarded = [
        'id',
    ];

    static function booted()
    {
        static::updated(function ($balance) {
            event(new UserAccountEvent($balance->account, 'updated', 'balance'));
        });
    }


    public function account(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }


    public function asset(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'asset_id');
    }


    public function inReview()
    {
        return $this->account->transactions()
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->where('asset_id', $this->asset_id)
                    ->orWhere('method', $this->asset->name);
            })
            ->where(function ($query) {
                $query->where('type', 'deposit')
                    ->orWhere('type', 'withdrawal');
            })
            ->sum('converted_amount');
    }
}