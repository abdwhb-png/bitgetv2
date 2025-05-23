<?php

namespace App\Events;

use App\Models\UserAccount;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserAccountEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public UserAccount $account,
        public string $state = 'created',
        public string $section = 'account'
    ) {
        if ($state == 'updated') {
            event(new UserEvent($account->user, 'account-updated'));
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
            new PrivateChannel('App.Models.User.Account.' . $this->account->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'UserAccountEvent';
    }

    public function broadcastWith(): array
    {
        return [
            'state' => $this->state,
            'section' => $this->section,
            'account' => $this->account,
        ];
    }
}
