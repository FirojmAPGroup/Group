<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LoginNotification extends Notification
{
    use Queueable;

    private $user;
    private $loginTime;

    public function __construct($user, $loginTime)
    {
        $this->user = $user;
        $this->loginTime = $loginTime;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => ' has logged in successfully!',
            'user_id' => $this->user->id,
            'user_name' => $this->user->first_name . ' ' . $this->user->last_name,
            'login_time' => $this->loginTime,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->user->name . ' has logged in successfully!',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'login_time' => $this->loginTime,
        ]);
    }
}
