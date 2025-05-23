<?php

namespace App\Http\Controllers;

use App\NotifData;
use App\Models\KYC;
use App\Models\Swap;
use Inertia\Inertia;
use App\Models\Order;
use Inertia\Response;
use App\Models\OrderType;
use App\Enums\WalletsEnum;
use App\Events\OrderEvent;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Http\Helpers\UtilsHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use App\Http\Resources\OrderResource;
use App\Concerns\ValidationRulesTrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AppController extends BaseController
{
    use ValidationRulesTrait;


    public function verification(string $type): Response
    {
        if (!in_array($type, config('vars.verification_types', []))) {
            return abort(404);
        }

        if (request()->user()->isVerified()[$type] > 0) {
            return abort(403);
        }

        $page = ucfirst($type);

        return Inertia::render('My/Verification/' . $page, [
            'identificationIDs' => config('vars.identification_ids'),
            'countries' => Storage::json('countries.json'),
        ]);
    }


    public function verificationStore(Request $request)
    {
        $validated = $request->validate($this->verificationRules());

        $file = $validated['photo'];

        if ($request->verification_type == 'kyc') {
            KYC::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'id_type' => $validated['id_type'],
                ],
                [
                    'file_path' => $file->store('kyc'),
                    'issued_by' => $validated['issued_by'],
                ]
            );
        }

        $notifData = new NotifData($request->user()->info->fullName() . ' submitted ' . $request->verification_type . ' verification.');
        $notifData->setBody('ID Type: ' . $validated['id_type'] . '<br>Issued By: ' . $validated['issued_by']);
        UtilsHelper::notifySuperAdmins($notifData);

        return back(303)->with('status', $request->verification_type . ' verification submitted');
    }


    public function walletAddressUpdate(Request $request)
    {
        $request->validate($this->walletAddressRules());

        $request->user()->account->paymentMethods()->updateExistingPivot($request->id, ['address' => $request->wallet_address]);

        return back(303)->with('status', 'wallet address updated');
    }


    public function paymentProof(Request $request)
    {
        $validated = $request->validate($this->paymentProofRules());

        $file = $validated['photo'];

        PaymentProof::create([
            'user_account_id' => $request->user()->account->id,
            'ref_id' => $request->reference_number,
            'method' => $validated['method'],
            'amount' => $validated['amount'],
            'file_path' => $file->store('payment-proofs'),
        ]);

        $notifData = new NotifData($request->user()->info->fullName() . ' submitted payment proof.');
        $notifData->setBody('Reference Number: ' . $request->reference_number . '<br>Method: ' . $validated['method'] . '<br>Amount: ' . $validated['amount']);
        UtilsHelper::notifySuperAdmins($notifData);

        return back(303)->with('status', 'payment proof submitted');
    }


    public function orderIndex()
    {
        return request()->user()->account->orders()->orderBy('created_at', 'desc')->get()->map(function ($order) {
            return Arr::except(new OrderResource($order), "id");
        });
    }


    public function orderStore(Request $request)
    {
        $request->validate($this->orderRules());

        $type = OrderType::where('label', 'like', '%' . $request->type . '%')->first();
        if (!$type) {
            throw ValidationException::withMessages([
                'type' => 'Order type not found',
            ]);
        }

        $timestamp = $request->time ?? time() * 1000;

        $order = new Order([
            'user_account_id' => $request->user()->account->id,
            'order_type_id' => $type->id,
            'symbol' => $request->symbol,
            'quote' => $request->quote,
            'base' => $request->base,
            'asset' => $request->user()->account->currency ?? 'USDT',
            'quantity' => $request->quantity,
            'open_price' => $request->price,
            'open_time' => $timestamp,
            'expiration' => $request->expiration,
            "opened_at" => $timestamp,
        ]);

        $order->save();

        return back(303)->with('status', 'order created');
    }


    public function orderClose(Order $order, Request $request)
    {
        $validated = $request->validate($this->orderRules('close'));

        if ($request->user()->account->can_trade == 0) {
            throw ValidationException::withMessages([
                'error' => 'You are not allowed to trade. Please contact support.',
            ]);
        }

        DB::transaction(function () use ($order, $validated) {
            $order->update([
                "closed_at" => time() * 1000,
                "close_price" => $validated['price'],
            ]);

            event(new OrderEvent($order, 'closed'));
        });


        return response([
            'status' => 'order closed',
        ], 202);

        return back(303)->with('status', 'order closed');
    }


    public function transactionIndex()
    {
        return request()->user()->account->transactions()->orderBy('created_at', 'desc')->get();
    }


    public function transactionStore(Request $request)
    {
        $validated = $request->validate($this->transactionRules());

        if ($request->type == 'withdrawal' && $request->converted_amount && $request->converted_amount < $request->amount) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient balance',
            ]);
        }

        DB::transaction(function () use ($validated) {
            $payMethod = $validated['pay_method'];
            $asset = new PaymentMethod();

            if ($validated['type'] != 'swap') {
                $asset = PaymentMethod::where('name',  $payMethod)->orWhere('symbol', $payMethod)->firstOrFail();
            }

            $transaction = new Transaction([
                'user_account_id' => request()->user()->account->id,
                'type' => $validated['type'],
                'amount' =>  $validated['amount'],
                'method' => $payMethod,
                'asset_id' => isset($asset) ? $asset->id : null,
                'binded_address' => $validated['binded_address'],
                'status' => 'pending',
            ]);

            $transaction->save();
        });

        return back(303)->with('status', 'transaction created');
    }

    public function swapStore(Request $request)
    {
        $validated = $request->validate($this->swapRules());

        $account = $request->user()->account;

        $from = $account->balances->where('wallet', $validated['from']['wallet'])->firstOrFail();
        $to = $account->balances->where('wallet', $validated['to']['wallet'])->firstOrFail();

        if ($validated['from_amount'] > $from->amount) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient balance',
            ]);
        }

        DB::transaction(function () use ($validated, $account, $to, $from) {
            $transaction = new Transaction([
                'user_account_id' => $account->id,
                'type' => 'swap',
                'method' => $validated['from']['wallet'] . ' to ' . $validated['to']['wallet'],
                'amount' => $validated['from_amount'],
                'converted_amount' => $validated['to_amount'],
                'status' => 'approved',
                'completed' => true,
            ]);

            $swap = new Swap([
                'user_account_id' => $account->id,
                'from' => $from->id,
                'to' => $to->id,
                'from_amount' => $validated['from_amount'],
                'to_amount' => $validated['to_amount'],
            ]);

            $transaction->save();
            $swap->save();
        });

        return $this->swap(
            $this->getAccountAsset($from->asset->symbol),
            $this->getAccountAsset($to->asset->symbol),
        );

        return back(303)->with('status', 'successfully swapped');
    }

    public function swap($from = null, $to = null): Response
    {
        $from = $from ?? $this->getAccountAsset(WalletsEnum::USDTTRC20->symbol());
        $to = $to ?? $this->getAccountAsset(WalletsEnum::BITCOIN->symbol());

        $balances = (new UserResource(request()->user()))->getBalances(request()->user()->account);

        return Inertia::render('Swap', [
            'from' => $from,
            'to' => $to,
            'balances' => $balances,
        ]);
    }
}
