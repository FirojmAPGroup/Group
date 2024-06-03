<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Notifications extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getNotifications()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                "code" => 401,
                "message" => "Unauthorized",
                "data" => []
            ], 401);
        }

            $notifications = $user->notifications->map(function ($notification) {
            $data = $notification->data;
            $userName = isset($data['user_name']) ? $data['user_name'] : null; // Get the user_name from the notification data
            $message = isset($data['message']) ? $data['message'] : null; // Get the message from the notification data
            $createdAt = $notification->created_at; // Get the created_at timestamp of the notification
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'message' => $message, // Include the message in the response
                'user_name'=>$userName,
                'user_id'=>$notification->user_id,
                'created_at' =>$createdAt,
                'read_at' => $notification->read_at,
            ];
        });

        return response()->json([
            "code" => 200,
            "message" => "Notifications retrieved successfully",
            "data" => $notifications
        ]);
    }
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::guard('api')->user();

        // Find the notification by id
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            // Mark the notification as read
            $notification->markAsRead();
            return response()->json([
                'code' => 200,
                'message' => 'Notification marked as read',
                'data' => $notification
            ]);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Notification not found',
                'data' => []
            ], 404);
        }
    }
}
