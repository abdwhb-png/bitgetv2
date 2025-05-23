<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Events\TransactionEvent;
use App\Events\UserAccountEvent;
use App\Interfaces\MoneyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model implements MoneyInterface
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];


    protected $hidden = [
        'id',
        'user_account_id',
    ];


    static function booted()
    {
        static::creating(function ($item) {
            $item->ref_id = Str::uuid();
        });

        static::created(function ($transaction) {
            event(new TransactionEvent($transaction, 'created'));
        });

        static::updated(function ($transaction) {
            event(new TransactionEvent($transaction, 'updated'));
            event(new UserAccountEvent($transaction->account, 'updated', 'transaction'));
        });
    }

    // protected function amount(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn(float $value) => $this->type == 'withdrawal' ?  $value * -1 : $value,
    //     );
    // }

    public function account(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }

    public function textAmount(): string
    {
        return '$ ' . $this->amount . ' ' . $this->account->currency;
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('ref_id', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('method', 'like', '%' . $search . '%')
                ->orWhere('binded_address', 'like', '%' . $search . '%')
                ->orWhere('amount', 'like', '%' . $search . '%')
                ->orWhere('converted_amount', 'like', '%' . $search . '%')
                ->orWhereHas('account', function (Builder $query) use ($search) {
                    $query->orWhereHas('user', function (Builder $query) use ($search) {
                        $query->search($search);
                    });
                });
        })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['sort'] ?? null, function ($query, $sort) {
                $query->orderBy($sort['field'], $sort['order']);
            });
    }
}
