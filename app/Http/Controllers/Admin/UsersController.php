<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Inertia\Inertia;
use App\Enums\RolesEnum;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use App\Enums\PermissionsEnum;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Concerns\ValidationRulesTrait;
use App\Http\Controllers\BaseController;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Request as FacadesRequest;

class UsersController extends BaseController
{
    use ValidationRulesTrait;

    protected function getStatuses($search): array
    {
        $statuses = collect(config('vars.statuses', []));

        $result = $statuses->filter(function ($status) use ($search) {
            return $status['label'] === $search || $status['value'] === $search;
        });

        if (!$result->count()) {
            throw new \Exception('status not found');
        }

        return $result->first();
    }

    protected function getParsedUser($user): array
    {
        return [
            'id' => $user->id,
            'resource' => $user->resourceForAdmin(),
            'profile_photo_url' => $user->profile_photo_url,
            'full_name' => $user->info->fullName(),
            'detail' => [
                'email' => $user->email,
                'first_name' => $user->info->first_name,
                'last_name' => $user->info->last_name,
                'invited_by' => $user->isInvitedBy(true) ?? 'unknown',
                'total_balance' => '$' . $user->account->balance,
            ],
            'balances' => $user->getBalances()->map(function ($balance) {
                return [
                    'name' => $balance['asset']['name'],
                    'amount' => number_format($balance['amount'], 5),
                    'in_review' => $balance['in_review'],
                ];
            }),
            'statuses' => [
                'email' => $this->getStatuses($user->isVerified()['email'])['label'],
                'kyc' => $this->getStatuses($user->isVerified()['kyc'])['label'],
                'basic_infos' => $user->info->isCompleted() ? 'completed' : 'uncompleted',
            ],
            'dates' => [
                'created' => $user->created_at->diffForHumans(),
                'updated' => $user->updated_at->diffForHumans(),
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
            ],
        ];
    }

    public function index()
    {
        return Inertia::render('Users', $this->get());
    }

    public function get()
    {
        $user = Auth::user();
        $query = User::where('id', '!=', Auth::id())->role(RolesEnum::USER->value);

        return [
            'filters' => FacadesRequest::all('search', 'sort'),
            'can' => [
                'create_user' => $user->isSuperAdmin(),
                'edit_user' => $user->isSuperAdmin(),
                'delete_user' => $user->isSuperAdmin(),
                'manage_admins' => $user->isSuperAdmin(),
            ],
            'canEditUser' => $user->can(PermissionsEnum::EDITUSERS->value),
            'canDeleteUser' => $user->can(PermissionsEnum::DELETEUSERS->value),
            'totalCount' => $query->count(),
            'users' => $query->latest()
                ->filter(FacadesRequest::only('search', 'sort'))
                ->paginate($this->itemsPerPage(20))
                ->withQueryString()
                ->through(fn($item) => $this->getParsedUser($item)),
        ];
    }

    public function update(int | string $id, Request $request)
    {
        $validated = $request->validate($this->userRules('update', $request->section));

        if ($request->section == 'account') {
            $account = UserAccount::where('account_no', $id)->firstOrFail();
            $updated = $account->update($validated);
        }

        if ($request->section == 'balance') {
            $updated = $this->updateBalance($id, $validated['amount']);
        }

        if ($request->section == 'wallet') {
            $updated = $this->updateWalletAddress($id, $validated['address']);
        }

        if (isset($updated) && $updated == false) {
            throw new ValidationException([
                'error' => 'Not found or Failed to update ' . $request->section,
            ]);
        }

        return back(303)->with('status', 'The ' . $validated['section'] . ' has been updated');
    }

    public function adminsIndex()
    {
        $data = User::where('id', '!=', request()->user()->id)
            ->when(request()->user()->hasRole(RolesEnum::ROOT->value), function ($query) {
                $query->withoutRole(RolesEnum::USER->value);
            })
            ->when(request()->user()->hasRole(RolesEnum::SUPERADMIN->value), function ($query) {
                $query->withoutRole(RolesEnum::USER->value)
                    ->withoutRole(RolesEnum::ROOT->value);
            })
            ->get()->map(function ($user) {
                return new UserResource($user);
            });

        return $data;
    }

    public function adminsUpdate(User $user, Request $request)
    {
        $validated = $request->validate($this->adminRules('update', $request->section));

        return back(303)->with('status', 'admin ' . $validated['section'] . ' updated');
    }
}
