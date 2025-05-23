<?php

namespace App\Models;

use App\Events\UserInfoEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserInfo extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    static function booted()
    {
        static::created(function ($userInfo) {
            event(new UserInfoEvent($userInfo, 'created'));
        });

        static::updated(function ($userInfo) {
            event(new UserInfoEvent($userInfo, 'updated'));
        });
    }


    public function fullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }


    public function fullAddress(): string
    {
        $addressParts = array_filter([
            $this->address,
            $this->city,
            $this->state ? "{$this->state} ({$this->zip_code})" : null,
            $this->country,
        ]);

        return implode(', ', $addressParts);
    }


    public function isCompleted(): bool
    {
        return $this->first_name && $this->last_name && $this->address && $this->city  && $this->nationality && $this->country;
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch(Builder $query, $search)
    {
        $query->Where('first_name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%' . $search . '%')
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%']);
    }
}
