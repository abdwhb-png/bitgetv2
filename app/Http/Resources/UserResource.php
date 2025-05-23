<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $account =  [
            ...$this->account->fresh()->toArray(),
            'balances' => $this->getBalances(),
        ];

        $result = array_merge(parent::toArray($request), [
            'invited_by' => $this->isInvitedBy(fullname: true),
            'account' => $account,
            'info' => $this->info->withoutRelations(),
            'full_name' => $this->info->fullName(),
            'full_address' => $this->info->fullAddress(),
            'isInfoCompleted' => $this->info->isCompleted(),
            'isVerified' => $this->isVerified(),
            'two_factor_enabled' => is_null($this->two_factor_secret),
            'wallet_addresses' => $this->getWalletAddresses(),
            'orders' => $this->account->orders()->get(),
            'transactions' => $this->account->transactions()->get(),
            'isSuperAdmin' => $this->hasRole(['super-admin', 'root']),
            'permissions' => $this->getDirectPermissions(),
            'allPermissions' => $this->getAllPermissions(),
        ]);

        $except = $this->hasRole('user') ? ['roles', 'permissions', 'invited_by', 'isSuperAdmin'] : [];

        return Arr::except($result, $except);
    }
}
