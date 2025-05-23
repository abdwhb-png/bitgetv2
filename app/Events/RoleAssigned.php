<?php

namespace App\Events;

use App\Enums\RolesEnum;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoleAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $roleName;

    /**
     * Create a new event instance.
     */
    public function __construct(public User $user, $role)
    {
        if ($role instanceof RolesEnum) {
            $this->roleName = $role->value;
        } elseif ($role instanceof Role) {
            $this->roleName = $role->name;
        } elseif (is_string($role)) {
            $this->roleName = $role;
        } else {
            $this->roleName = '';
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
            new PrivateChannel('role-assigned'),
        ];
    }
}