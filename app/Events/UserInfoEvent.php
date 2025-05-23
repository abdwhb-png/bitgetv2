<?php

namespace App\Events;

use App\NotifData;
use App\Models\UserInfo;
use App\Notifications\DefaultNotif;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class UserInfoEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public UserInfo $info, public string $event)
    {
        if ($event == 'created') {
            $notifData = new NotifData('Welcome to ' . config('app.name'), 'We are happy to have you on board.');
            $info->user->notify(new DefaultNotif($notifData, ['database']));
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
            new PrivateChannel('App.Models.User.Info.' . $this->info->id)
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'UserInfoEvent'; // Use the same event name as in your JavaScript
    }

    /**
     * Get the data to broadcast for the model.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'isCompleted' => $this->info->isCompleted(),
            'info' => $this->info,
        ];
    }
}