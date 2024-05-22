<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomNotification;
class NotificationController extends Controller
{
   

public function markAsRead(Request $request, $notificationId) {
    $notification = CustomNotification::findOrFail($notificationId);
    $notification->markAsRead();
    // Optionally, you can perform other actions or return a response
}

}
