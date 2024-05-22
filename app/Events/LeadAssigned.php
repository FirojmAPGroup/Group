<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeadAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $lead;
    public $user;

    public function __construct($leads, $users)
    {
        $this->lead = $leads;
        $this->user = $users;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('leads');
    }
}
