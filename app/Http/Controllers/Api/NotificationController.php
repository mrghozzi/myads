<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $notifications = Notification::where('uid', $userId)
            ->orderBy('time', 'desc')
            ->paginate(20);

        return NotificationResource::collection($notifications);
    }

    public function markAsRead($id)
    {
        $userId = Auth::id();
        $notification = Notification::where('id', $id)
            ->where('uid', $userId)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->state = 1;
        $notification->save();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();
        Notification::where('uid', $userId)
            ->where('state', 0)
            ->update(['state' => 1]);

        return response()->json(['message' => 'All notifications marked as read']);
    }

    public function unreadCount()
    {
        $userId = Auth::id();
        $count = Notification::where('uid', $userId)
            ->where('state', 0)
            ->count();

        return response()->json(['unread_count' => $count]);
    }
}
