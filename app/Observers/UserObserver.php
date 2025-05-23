<?php

namespace App\Observers;

use App\NotifData;
use App\Models\User;
use App\Enums\RolesEnum;
use App\Events\UserEvent;
use Illuminate\Support\Str;
use App\Enums\PermissionsEnum;
use App\Http\Helpers\UtilsHelper;
use Spatie\Permission\Models\Permission;

class UserObserver
{
    /**
     * Méthode centralisée pour déclencher l'événement.
     */
    private function triggerEvent(User $user, string $eventType): void
    {
        event(new UserEvent($user, $eventType));
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->account()->create([
            'account_no' => strtoupper(substr(Str::uuid(), 0, 8)),
        ]);

        $user->info()->create();

        $user->givePermissionTo(PermissionsEnum::INVITEUSERS);

        // instance of NotifData
        $notifData = new NotifData("New account created");
        $notifData->setBody($user->email . " has created a new account");
        $notifData->setSubject("Account creation message");

        // trigger user Event
        $this->triggerEvent($user, 'user-created');

        // notify super-admins about new user
        UtilsHelper::notifySuperAdmins($notifData);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Vérifier des changements spécifiques si nécessaire
        if ($user->wasChanged('roles')) {
            // Par exemple : envoyer une notification
        }

        if ($user->wasChanged('permissions')) {
            // Par exemple : gérer les permissions
        }

        $this->triggerEvent($user, 'user-updated');
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->triggerEvent($user, 'user-deleted');
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        $this->triggerEvent($user, 'user-restored');
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $this->triggerEvent($user, 'user-force-deleted');
    }
}