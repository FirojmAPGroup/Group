<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewLeadNotification extends Notification
{
    use Queueable;

    private $details;

    public function __construct(array $details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->details['message'],
            'user_name' => $this->details['user_name'],
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->details['message'],
            'user_name' => $this->details['user_name'],
        ]);
    }
}
