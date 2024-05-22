<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserNotifications extends Notification
{
    use Queueable;

    protected $userName;

    public function __construct($userName)
    {
        $this->userName = $userName;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'New lead assigned by ' . $this->userName,
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'New lead assigned by ' . $this->userName,
        ];
    }
}
