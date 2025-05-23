<?php

namespace App\Events;

use App\Models\Balance;
use App\Models\Transaction;
use App\Models\UserAccount;
use App\Models\SystemSetting;
use App\Services\BinanceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;

class TransactionEvent implements ShouldDispatchAfterCommit, ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $binance;

    public $handlingFee;

    public $user;
    public $account;
    public $balance;


    /**
     * Create a new event instance.
     */
    public function __construct(public Transaction $transaction, public string $state = '')
    {
        $this->binance = new BinanceService();

        $this->handlingFee == SystemSetting::where('status', 1)->first()->handling_fee / 100;

        $this->account = UserAccount::find($transaction->user_account_id);
        $this->user = $this->account->user;
        $this->balance = Balance::where('user_account_id', $this->account->id)
            ->where(function ($query) use ($transaction) {
                $query->where('asset_id', $transaction->asset_id)
                    ->orWhere('wallet', $transaction->method);
            })->first();


        if ($state == 'created' && $transaction->type != 'swap') {

            $transaction->balance_on_create = $this->balance ? $this->balance->amount : null;

            $symbol = $transaction->method . 'USDT';

            try {
                $result = $this->binance->getTickerPrice(['symbol' => $symbol]);
                $price = $result ? $result['price'] : 1;
            } catch (\Throwable $th) {
                Log::channel('binance')->debug("Error while converting amount for transaction -> " . $transaction->id . " : " . $th);
                $price = 1;
            } finally {
                if ($transaction->converted_amount === null) {

                    $convAmount = $transaction->amount;

                    if (!str_starts_with($transaction->symbol, "USDT")) {
                        $convAmount =  $transaction->amount / $price;
                    }

                    $transaction->converted_amount = $convAmount;

                    if ($transaction->type == 'withdrawal' && $transaction->converted_amount > $this->account->getBalance($transaction->method)) {
                        $transaction->deleted_reason = 'insufficient funds';
                        $transaction->status = 'rejected';
                        // $transaction->delete();
                    }
                }
            }

            $transaction->save();
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.Transaction.' . $this->transaction->user_account_id),
            new PrivateChannel('transactions'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'TransactionEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'state' => $this->state,
            'account' => $this->transaction->account,
            'transaction' => $this->transaction,
        ];
    }
}
