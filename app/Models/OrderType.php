<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderType extends Model
{
    protected $guarded = [
        'id',
    ];


    protected function label(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                return strtoupper($value);
            }
        );
    }


    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}