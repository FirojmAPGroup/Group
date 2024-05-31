<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function showNotifications()
    {
        $user = Auth::user();
    
        // Get the latest 2 notifications
        $notifications = $user->notifications()
            ->where('created_at', '>=', Carbon::now()->subDays(2)) // Filter by notifications from the last 48 hours
            ->orderBy('created_at', 'desc') // Order by creation time, newest first
            ->take(2) // Limit to 2 notifications
            ->get();
    
        $unreadCount = $user->unreadNotifications()->count();
    
        return view('particles.header', compact('notifications', 'unreadCount'));
    }
    

    public function showAllNotifications()
    {
        $user = Auth::user();

        // Get all notifications, ordered by creation time
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();

        return view('notifications.all', compact('notifications'));
    }



    public function markAsRead($notificationId)
    {
        $user = Auth::user();
        $notification = $user->notifications->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }
}
