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

        // Get notifications from the last 48 hours
        $notifications = $user->notifications()
            ->where('created_at', '>=', Carbon::now()->subDays(2)) // Filter by notifications from the last 48 hours
            ->orderBy('created_at', 'desc') // Order by creation time, newest first
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

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json(['message' => 'Notification marked as read']);
    }
}
