<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Events\FatalErrorEvent;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PersonalInfoRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Jetstream\Http\Controllers\Inertia\UserProfileController;

class UserController extends UserProfileController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $data = [
            'ids' => ['user' => $user->id, 'account' => $user->account->id, 'info' => $user->info->id],
            'unreadNotificationsCount' => $user->unreadNotifications->count(),
            'resource' => new UserResource($user)
        ];
        return $data;
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $request->user()->updateProfilePhoto($request['photo']);

        return back(303)->with('status', 'profile photo updated');
    }


    public function updatePersonalInfo(PersonalInfoRequest $request)
    {
        $user = $request->user();
        if (
            $request['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $request->updateVerifiedUser();
        } else {
            $user->forceFill([
                'email' => $request['email'],
            ])->save();
        }

        $user->info->update($request->except('email'));

        return back(303)->with('status', 'personal information updated');
    }


    public function updatePassword(User $user, Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ]);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return back(303)->with('status', 'password updated');
    }


    public function updateMailNotif(Request $request)
    {
        $account = $request->user()->account;

        $account->mail = !$account->mail;

        $account->save();

        return back(303)->with('status', 'mail notifications updated');
    }


    public function destroy(User $user)
    {
        $user->delete();

        return back(303)->with('status', 'user deleted');
    }
}
