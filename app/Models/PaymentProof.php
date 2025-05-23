<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentProof extends Model
{
    protected $guarded = [
        'id',
    ];


    public function account(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_account_id');
    }


    protected function fileUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return url('storage/' . $this->file_path);
        });
    }
}
