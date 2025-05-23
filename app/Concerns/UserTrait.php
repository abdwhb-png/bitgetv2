<?php

namespace App\Concerns;

use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait UserTrait
{
    public function scopeisVerified(): array
    {
        return [
            'email' => $this->email_verified_at !== null,
            'kyc' => !$this->kyc ? -100 : $this->kyc->status,
        ];
    }

    public function isInvitedBy(bool $fullname = false): User|string|null
    {
        $user = User::find($this->invited_by);

        if (!$user) {
            return null;
        }

        return $fullname ? $user->info->fullName() : $user;
    }

    public function isAdmin(): bool
    {
        $roles = collect(RolesEnum::cases())->filter(function ($role) {
            return $role->value !== RolesEnum::USER->value;
        });

        return $this->hasRole($roles->pluck('value')->toArray());
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole([RolesEnum::SUPERADMIN->value, RolesEnum::ROOT->value]);
    }

    public function getBalances(): Collection
    {
        return $this->account->balances()
            ->get()
            ->map(function ($balance) {
                return [
                    'id' => $balance->id,
                    'wallet' => $balance->wallet,
                    'asset_id' => $balance->asset_id,
                    'asset' => $balance->asset,
                    'amount' => round($balance->amount, 6),
                    'in_review' => $balance->inReview()
                ];
            });
    }

    public function getWalletAddresses(): Collection
    {
        return $this->account->paymentMethods()->get()->map(function ($method) {
            return
                [
                    'id' => $method->pivot->id,
                    'address' => $method->pivot->address,
                    'method' => $method->withoutRelations(),
                ];
        });
    }

    public function resourceforAdmin(): array
    {
        $start = Arr::only($this->toArray(request()), ['info', 'full_name', 'full_address', 'isInfoCompleted', 'two_factor_enabled']);

        $end = [
            'account' => $this->account->withoutRelations(),
            'balances' => $this->account->balances->map(function ($balance) {
                return [
                    'id' => $balance->id,
                    'name' => $balance->asset->name,
                    'symbol' => $balance->asset->symbol,
                    'amount' => $balance->amount,
                    'in_review' => $balance->inReview(),
                ];
            }),
            'wallet_addresses' => $this->getWalletAddresses()->map(function ($wallet) {
                return [
                    'id' => $wallet['id'],
                    'name' => $wallet['method']['name'],
                    'symbol' => $wallet['method']['symbol'],
                    'address' => $wallet['address'],
                ];
            }),
            'email' => $this->email,
        ];

        return $start + $end;
    }
}
