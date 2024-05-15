<?php

namespace App\Listeners;

use App\Events\SubAdminCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendMailFired
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SubAdminCreated $event): void
    {
        \Log::alert("subadmin register mail send");
        $user = User::find($event->userId)->toArray();
        $password = $event->password;
        $mail = Mail::send('emails.subadmin-create', ['user' => $user,'password'=>$password], function($message) use ($user) {
            $message->to($user['email']);
            $message->subject('Register Successfully');
        });

        \Log::alert(json_encode($mail,true));
    }
}
