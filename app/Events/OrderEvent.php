<?php

namespace App\Events;

use App\Models\Order;
use App\Services\BinanceService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class OrderEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $binance;

    public $balances;

    /**
     * Create a new event instance.
     */
    public function __construct(public Order $order, public string $state = '')
    {
        $this->binance = new BinanceService();

        $this->balances = $order->account->balances()->whereHas('asset', function (Builder $query) use ($order) {
            $query->where('name', 'like', '%' . $order->asset . '%')->orWhere('symbol', 'like', '%' . $order->asset . '%');
        })->orderBy('amount', 'desc')->get();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.Order.' . $this->order->user_account_id),
            new PrivateChannel('orders'),
        ];
    }


    public function broadcastAs(): string
    {
        return 'OrderEvent';
    }


    public function broadcastWith(): array
    {
        return [
            'state' => $this->state,
            'order' => $this->order,
        ];
    }
}
