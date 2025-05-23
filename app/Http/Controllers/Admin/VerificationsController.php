<?php

namespace App\Http\Controllers\Admin;

use App\Models\KYC;
use App\Models\User;
use Inertia\Inertia;
use App\Enums\RolesEnum;
use Illuminate\Http\Request;
use App\Enums\PermissionsEnum;
use Illuminate\Support\Facades\Auth;
use App\Concerns\ValidationRulesTrait;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Request as FacadesRequest;

class VerificationsController extends BaseController
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
    public function index()
    {
        return Inertia::render('Verifications', $this->get());
    }

    public function get()
    {
        $user = Auth::user();
        $emailsQuery = User::role(RolesEnum::USER->value);
        $kycsQuery = KYC::query();

        return [
            'filters' => FacadesRequest::all('search', 'sort'),
            'can' => [
                'create_verification' => $user->isSuperAdmin(),
                'edit_verification' => $user->isSuperAdmin(),
                'delete_verification' => $user->isSuperAdmin(),
            ],
            'canEditVerification' => $user->can(PermissionsEnum::EDITVERIFICATIONS->value),
            'totalCount' => [
                'emails' => $emailsQuery->count(),
                'kycs' => $kycsQuery->count(),
            ],
            'pendingCount' => [
                'emails' => $emailsQuery->where('email_verified_at', null)->count(),
                'kycs' => $kycsQuery->where('status', 0)->count(),
            ],
            'verifications' => [
                'emails' => $emailsQuery->latest()
                    ->paginate($this->itemsPerPage(20))
                    ->withQueryString()
                    ->through(function ($user) {
                        return [
                            'id' => $user->id,
                            'status' => $this->getStatuses($user->isVerified()['email'])['label'],
                            'detail' => [
                                'user' => $user->info->fullName(),
                                'email' => $user->email,
                                'email_verified_at' => $user->email_verified_at !== null ? $user->email_verified_at->format('Y-m-d H:i:s') : null,
                            ],
                        ];
                    }),
                'kycs' => $kycsQuery->latest()
                    ->paginate($this->itemsPerPage(20))
                    ->withQueryString()
                    ->through(function ($kyc) {
                        return [
                            'id' => $kyc->id,
                            'status' => $this->getStatuses($kyc->status)['label'],
                            'detail' => [
                                'user' => $kyc->user ? $kyc->user->info->fullName() : 'unknown',
                                'id_type' => $kyc->id_type,
                                'id_is_issued_by' => $kyc->issued_by,
                                'file_url' => $kyc->file_url,
                                'created_at' => $kyc->created_at->format('Y-m-d H:i:s'),
                                'updated_at' => $kyc->updated_at->format('Y-m-d H:i:s'),
                            ],
                        ];
                    }),
            ],
        ];
    }

    public function update(int $id, Request $request)
    {
        $statuses = collect(config('vars.statuses'))->pluck('label')->toArray();

        $request->validate($this->verificationRules('update', array_values($statuses)));

        if ($request->type == 'email') {
            $user = User::findOrFail($id);
            $user->email_verified_at = now();
            $user->save();
        } else if ($request->type == 'kycs') {
            $kyc = KYC::findOrFail($id);

            $kyc->update([
                'status' => $this->getStatuses($request->status)['value'],
            ]);
        }

        return back(303)->with('status', $request->type . ' verification status updated');
    }
}
