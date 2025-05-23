<?php

namespace App\Models;

use App\Events\UserEvent;
use App\NotifData;
use App\Notifications\DefaultNotif;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KYC extends Model
{
    protected $guarded = [
        'id',
    ];

    protected function fileUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return url('storage/' . $this->file_path);
        });
    }


    static function booted()
    {

        static::created(function ($kyc) {
            event(new UserEvent($kyc->user, 'kyc-created'));
        });

        static::updated(function ($kyc) {
            if ($kyc->status != 0) {
                $notifData = new NotifData('Your KYC verfication has been processed');
                $notifData->setBody($kyc->status ? 'We are happy to inform you that your KYC has been approved. Enjoy your trading with us.' : 'We are sorry to inform you that your KYC has been rejected. Please try again or contact the customer support.');

                $kyc->user->notify(new DefaultNotif($notifData));
            }

            event(new UserEvent($kyc->user, 'kyc-updated'));
        });
    }



    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}