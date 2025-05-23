<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Events\UserAccountEvent;
use Illuminate\Database\Eloquent\Model;

class Swap extends Model
{
    protected $guarded = [
        'id',
    ];

    static function booted()
    {
        static::creating(function ($item) {
            $item->ref_id = Str::uuid();
        });

        static::created(function ($swap) {
            $swap->fromBalance()->decrement('amount', $swap->from_amount);
            $swap->toBalance()->increment('amount', $swap->to_amount);
            event(new UserAccountEvent($swap->account, 'updated', 'swap'));
        });
    }

    public function account()
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }


    public function fromBalance()
    {
        return $this->belongsTo(Balance::class, 'from');
    }

    public function ToBalance()
    {
        return $this->belongsTo(Balance::class, 'to');
    }
}
