<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Enums\RolesEnum;
use App\Models\UserInfo;
use App\Models\UserAccount;
use App\Enums\PermissionsEnum;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Jetstream;
use App\Http\Helpers\RequestHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Contracts\Permission;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Rules\Role;

/**
 * This class is responsible for validating and creating a new user.
 *
 * The create method is called by the register controller in the
 * laravel/fortify package. It validates the input and creates a new user
 * with the given input. It also assigns the user a role based on the
 * domain that the user is registering from.
 */
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @return User
     */
    public function create(array $input): User
    {
        // Check if the request is coming from the admin domain.
        $role = RequestHelper::isAdminDomain(request()) ? RolesEnum::ADMIN->value : RolesEnum::USER->value;

        // Validate the input from the registration form.
        Validator::make($input, [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:dns', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'invitation_code' => ['required', Rule::exists('user_accounts', 'account_no')],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Get the user account that the invitation code belongs to.
        $referrer = UserAccount::where('account_no', $input['invitation_code'])->firstOrFail();

        // Check if the user that the invitation code belongs to has the
        // permission to invite admins.
        if ($role == RolesEnum::ADMIN->value && !$referrer->user->hasPermissionTo(PermissionsEnum::INVITEADMINS->value)) {
            // If the user does not have the permission, throw a validation
            // exception.
            throw ValidationException::withMessages([
                'invitation_code' => __('You cannot create an admin account using this invitation code.'),
            ]);
        }

        // Check if the user that the invitation code belongs to has the
        // permission to invite users.
        if ($role == RolesEnum::USER->value && !$referrer->user->hasPermissionTo(PermissionsEnum::INVITEUSERS->value)) {
            // If the user does not have the permission, throw a validation
            // exception.
            throw ValidationException::withMessages([
                'invitation_code' => __('You cannot create an account using this invitation code.'),
            ]);
        }

        // Create a new user with the input from the registration form.
        $user = User::create([
            'invited_by' => $referrer->user_id,
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Create a new user info record for the user.
        UserInfo::updateOrCreate([
            'user_id' => $user->id,
        ], [
            'first_name' => $input['first_name'],
            'last_name' => $input['last_name'],
        ]);

        // Assign the user the role that was determined above.
        $user->assignRole($role);

        // Return the newly created user.
        return $user;
    }
}
