<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PaymentMethod;
use Laravel\Jetstream\Features;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate the user is role.
     */
    public function role($role): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterMaking(function (User $user) {
            // ...
        })->afterCreating(function (User $user) use ($role) {
            $user->assignRole($role);
        });
    }

    public function withInfos(): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterMaking(function (User $user) {
            // ...
        })->afterCreating(function (User $user) {
            $user->account->updateOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'account_type' => fake()->randomElement(['Personal', 'Business']),
                    'leverage' => fake()->randomElement(['1:10', '1:50', '1:100']),
                    'mail' => rand(0, 1),
                ]
            );

            $user->account->balances()->each(function ($balance) {
                $balance->update([
                    'amount' => fake()->randomFloat(1, 0.0001, str_starts_with($balance->asset->name, 'USDT') ? 1000 : 0.01),
                ]);
            });

            PaymentMethod::inRandomOrder()->limit(2)->get()->each(function ($paymentMethod) use ($user) {
                $user->account->paymentMethods()->updateExistingPivot($paymentMethod->id, ['address' => fake()->sha256()]);
            });

            $user->info->updateOrCreate(
                [
                    'user_id' => $user->id
                ],
                [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'birth_date' => fake()->date(),
                    'phone_number' => fake()->phoneNumber(),
                    'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
                    'address' => fake()->address(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'country' => fake()->country(),
                    'zip_code' => fake()->postcode(),
                    'nationality' => fake()->country(),
                ]
            );
        });
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn(array $attributes, User $user) => [
                    'name' => $user->info->fullName() . '\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}