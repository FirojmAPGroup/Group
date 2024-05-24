<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Notifications\LoginNotification;

class SendLoginNotification
{
    // public function handle(Login $event)
    // {
    //     $event->user->notify(new LoginNotification());
    // }
    public function handle(Login $event)
    {
        $user = $event->user;

        // Create and send the notification
        $user->notify(new LoginNotification([
            'type' => 'login',
            'message' => 'You have just logged in.',
            'user_id' => $user->id,
            'user_name' => $user->first_name . ' ' . $user->last_name, // Ensure this concatenation is correct and both names exist
        ]));
        
    }
}
