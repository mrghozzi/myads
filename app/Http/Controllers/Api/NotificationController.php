<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $notifications = Notification::where('uid', $userId)
            ->orderBy('time', 'desc')
            ->paginate(20);

        // Map notifications to include icon and target_url if not already present in the model or resource
        $formatted = $notifications->getCollection()->map(function ($notif) {
            return [
                'id' => $notif->id,
                'type' => 'general',
                'text' => $notif->name,
                'time' => $notif->time,
                'state' => $notif->state,
                'is_unread' => in_array((int)$notif->state, [0, 3]),
                'icon' => $notif->logo ?? 'bell', // Fallback icon
                'target_url' => $notif->nurl ?? '', // Ensure URL is provided for navigation
                'source_user_id' => null,
            ];
        });

        $notifications->setCollection($formatted);

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
        ]);
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

        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    public function markAllAsRead()
    {
        $userId = Auth::id();
        Notification::where('uid', $userId)
            ->whereIn('state', [0, 3])
            ->update(['state' => 1]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    public function unreadCount()
    {
        $userId = Auth::id();
        $count = Notification::where('uid', $userId)
            ->whereIn('state', [0, 3])
            ->count();

        return response()->json(['success' => true, 'unread_count' => $count]);
    }
}
