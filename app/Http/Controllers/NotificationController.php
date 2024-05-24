<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
   
    public function showNotifications()
    {
        // $notifications = Auth::user()->notifications;
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->get();

        return view('particles.header', compact('notifications'));
    }
    public function customNotifications()
    {
        return $this->hasMany(CustomNotification::class);
    }
}
