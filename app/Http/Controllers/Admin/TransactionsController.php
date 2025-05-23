<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Enums\PermissionsEnum;
use App\Http\Controllers\BaseController;
use App\Http\Resources\TransactionResource;
use App\Concerns\ValidationRulesTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;

class TransactionsController extends BaseController
{
    use ValidationRulesTrait;

    protected function getParsedTrnasaction($transaction): array
    {
        return
            [
                'id' => $transaction->id,
                'ref_id' => $transaction->ref_id,
                'status' => $transaction->status,
                'type' => $transaction->type,
                'detail' => [
                    'user' => $transaction->account->user ? $transaction->account->user->info->fullName() : 'unknown',
                    'amount' => $transaction->type == 'withdrawal' ?  -1 * $transaction->amount : $transaction->amount,
                    'converted_amount' => $transaction->type == 'withdrawal' ?  -1 * $transaction->converted_amount : $transaction->converted_amount,
                    'method' => $transaction->method,
                    'asset_balance_on_transac_creation' => $transaction->balance_on_create,
                    'binded_address' => $transaction->binded_address,
                ],
                'dates' => [
                    'created' => $transaction->created_at->diffForHumans(),
                    'updated' => $transaction->updated_at->diffForHumans(),
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $transaction->updated_at->format('Y-m-d H:i:s'),
                ],
            ];
    }
    public function index()
    {
        return Inertia::render('Transactions', $this->get());
    }

    public function get()
    {
        $user = Auth::user();
        $query = Transaction::query();
        $filters = FacadesRequest::all('search', 'sort', 'type', 'status');

        return [
            'filters' => $filters,
            'can' => [
                'create_transaction' => $user->isSuperAdmin(),
                'edit_transaction' => $user->isSuperAdmin(),
                'delete_transaction' => $user->isSuperAdmin(),
            ],
            'canEditTransaction' => $user->can(PermissionsEnum::EDITTRANSACTIONS->value),
            'totalCount' => $query->count(),
            'pendingCount' => $query->where('status', 'pending')->count(),
            'transactions' => Transaction::filter($filters)
                ->latest()
                ->paginate($this->itemsPerPage(20))
                ->withQueryString()
                ->through(fn($item) => $this->getParsedTrnasaction($item)),
        ];
    }

    public function update(Transaction $transaction, Request $request)
    {
        $request->validate($this->transactionRules('update'));

        $transaction->update([
            'status' => $request->status,
        ]);

        return back(303)->with('status', 'transaction updated');
    }
}
