<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class PersonalInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => ["required", "string", "email", "max:255", Rule::unique('users')->ignore($this->user()->id)],
            "first_name" => ["required", "string", "max:255"],
            "last_name" => ["required", "string", "max:255"],
            'birth_date' => ['required', 'date'],
            'phone_number' => ['required', 'phone:INTERNATIONAL'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'zip_code' => ['nullable', 'string', 'max:255'],
            'nationality' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'phone_number.phone' => 'The phone number must be a valid international phone number.',
        ];
    }


    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function updateVerifiedUser(): void
    {
        $user = $this->user();

        $user->forceFill([
            'email' => $this->get('email'),
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
