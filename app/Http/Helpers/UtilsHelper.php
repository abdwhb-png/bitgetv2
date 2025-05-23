<?php

namespace App\Http\Helpers;

use App\Enums\RolesEnum;
use App\Enums\WalletsEnum;
use App\NotifData;
use App\Models\User;
use App\Models\UserAccount;
use App\Notifications\DefaultNotif;
use Illuminate\Support\Facades\Notification;

class UtilsHelper
{

    static public function getPMethods(?string $type = ''): array
    {
        $pMethods = collect(WalletsEnum::cases())->map(function ($item) {
            return [
                'name' => $item->name(),
                'symbol' => $item->symbol()
            ];
        });

        return match ($type) {
            'symbols' => $pMethods->pluck('symbol')->toArray(),
            'names' => $pMethods->pluck('name')->toArray(),
            default => $pMethods->toArray(),
        };
    }


    static public function notifyAdmins(NotifData $notifData)
    {
        $users = User::role(RolesEnum::ADMIN->value)->get();

        Notification::send($users, new DefaultNotif($notifData));
    }


    static public function notifySuperAdmins(NotifData $notifData)
    {
        $superAdmins = User::role(RolesEnum::SUPERADMIN->value)->get();
        $roots = User::role(RolesEnum::ROOT->value)->get();

        Notification::send($superAdmins, new DefaultNotif($notifData));
        Notification::send($roots, new DefaultNotif($notifData));
    }


    static public function notifyRoots(NotifData $notifData)
    {
        $users = User::role(RolesEnum::ROOT->value)->get();

        Notification::send($users, new DefaultNotif($notifData));
    }


    public static function generateAccountNo()
    {
        do {
            // Generate a random 9-digit number
            $number = random_int(100000000, 999999999); // Ensures the number is 9 digits
        } while (UserAccount::where('account_no', $number)->exists()); // Check for uniqueness

        return $number; // Return the unique number
    }


    public static function formatAmount($amount): string
    {
        return number_format($amount, 6);
    }
}