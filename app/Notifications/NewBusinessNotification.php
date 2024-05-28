<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Business;


class NewBusinessNotification extends Notification
{
    use Queueable;

    protected $business;
    protected $isNewBusiness;

    public function __construct(Business $business, bool $isNewBusiness)
    {
        $this->business = $business;
        $this->isNewBusiness = $isNewBusiness;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_name' => auth()->user()->first_name .' '. auth()->user()->last_name,
            'message' => $this->isNewBusiness ? 'created a new business name ('.$this->business->name.')' : 'updated an existing business name ('.$this->business->name.')',
            'business_id' => $this->business->id,
            'business_name' => $this->business->name,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'user_name' => auth()->user()-> $this->details['user_name'],
            'message' => $this->isNewBusiness ? 'created a new business' : 'updated an existing business',
            'business_id' => $this->business->id,
            'business_name' => $this->business->name,
        ];
    }
}
