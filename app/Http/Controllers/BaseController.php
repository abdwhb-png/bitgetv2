<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use Illuminate\Http\Request;
use App\Events\FatalErrorEvent;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class BaseController extends Controller
{
    public function __construct(public Request $request) {}

    protected function itemsPerPage(int $default = 50)
    {
        return $this->request->input('per_page', $default);
    }

    protected function currentPage(int $default = 1)
    {
        return $this->request->input('page', $default);
    }

    protected function getStatuses($search): array
    {
        $statuses = collect(config('vars.statuses', []));

        $result = $statuses->filter(function ($status) use ($search) {
            return $status['label'] === $search || $status['value'] === $search;
        });

        if (!$result->count()) {
            return ['label' => 'unknown', 'value' => 'unknown'];
            // throw new \Exception('status not found');
        }

        return $result->first();
    }

    public function getAccountAsset(string $search): array
    {
        $user = request()->user();
        $balances = (new UserResource($user))->getBalances($user->account);

        $result = $balances->first(function ($item) use ($search) {
            return $item['asset']->symbol === $search || $item['asset']->name === $search;
        });


        return $result ? $result : $balances->first()->toArray();
    }


    public function updateBalance($id, $amount)
    {
        try {
            Balance::findOrFail($id)->update(['amount' => $amount]);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function updateWalletAddress($id, $address)
    {
        try {
            DB::table('payment_method_user_account')->where('id', $id)->update(['address' => $address]);
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function fatalError(Request $request)
    {
        $request->validate([
            'error' => 'required|string',
        ]);

        event(new FatalErrorEvent($request->error));

        return back(303)->with('status', 'fatal error sent');
    }
}
