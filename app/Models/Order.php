<?php

namespace App\Models;

use App\Events\OrderEvent;
use Illuminate\Support\Str;
use App\Events\UserAccountEvent;
use App\Interfaces\MoneyInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model implements MoneyInterface
{
    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'quantity' => 'decimal:8',
        'percentage' => 'decimal:8',
        'created_at' => 'datetime',
    ];

    static function booted()
    {
        static::creating(function ($item) {
            $item->ref_id = Str::uuid();
        });

        static::created(function ($order) {
            event(new OrderEvent($order, 'created'));
        });

        static::updated(function ($order) {
            event(new OrderEvent($order, 'updated'));
            event(new UserAccountEvent($order->account, 'updated', 'order'));
        });
    }

    public function getStatus(): string
    {
        return $this->closed_at ? 'closed' : 'opened';
    }

    protected function closedAt(): Attribute
    {
        return Attribute::make(
            get: function (int | null $value) {
                if ($value) {
                    $timestamp_s = $value / 1000;
                    return \Carbon\Carbon::createFromTimestamp($timestamp_s)->toDateTimeString();
                }

                return null;
            }
        );
    }


    protected function openedAt(): Attribute
    {
        return Attribute::make(
            get: function (int | null $value) {
                if ($value) {
                    $timestamp_s = $value / 1000;
                    return \Carbon\Carbon::createFromTimestamp($timestamp_s)->toDateTimeString();
                }

                return null;
            }
        );
    }

    protected function type(): Attribute
    {
        return Attribute::get(function (): string {
            return strtoupper($this->orderType->label);
        });
    }


    public function account(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }


    public function orderType(): BelongsTo
    {
        return $this->belongsTo(OrderType::class);
    }


    public function textAmount(): string
    {
        return '$ ' . $this->amount . ' ' . $this->account->currency;
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('symbol', 'like', '%' . $search . '%')
                ->orWhere('ref_id', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%');
            // ->orWhereHas('account.user.info', function (Builder $query) use ($search) {
            //     $query->search($search);
            // });
        })
            ->when($filters['type'] ?? null, function ($query, $type) {
                $query->whereHas('orderType', function (Builder $query) use ($type) {
                    if (is_int($type) || is_numeric($type)) {
                        $query->where('id', $type);
                    } else {
                        $query->where('label', $type);
                    }
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if ($status === 'closed') {
                    $query->whereNotNull('closed_at');
                } elseif ($status === 'opened') {
                    $query->whereNull('closed_at');
                }
            })
            ->when($filters['sort'] ?? null, function ($query, $sort) {
                $query->orderBy($sort['field'], $sort['order']);
            });
    }
}
