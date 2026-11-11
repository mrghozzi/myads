<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get paginated notifications with category filtering and enriched actor metadata.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $filter = $request->query('filter', 'all');

        $query = Notification::where('uid', $userId)->with('sender');

        // Apply Category Filtering
        switch ($filter) {
            case 'mentions':
                $query->where('logo', 'at');
                break;
            case 'reactions':
                $query->whereIn('logo', ['like', 'love', 'happy', 'funny', 'wow', 'angry', 'sad', 'heart']);
                break;
            case 'comments':
                $query->whereIn('logo', ['comment', 'comments', 'reply']);
                break;
            case 'system':
                $query->whereIn('logo', ['notification', 'logo', 'trophy']);
                break;
            case 'all':
            default:
                break;
        }

        $notifications = $query->orderBy('time', 'desc')->paginate(20);

        // Preload any users whose usernames appear in legacy notification texts to avoid N+1 queries
        $fallbackUsernames = [];
        foreach ($notifications->items() as $notif) {
            if (!$notif->sender_id) {
                if (preg_match('/(?:قام|أعجب|علق|تابعك|رد|ذكرك)\s+([a-zA-Z0-9_\-\.]+)/u', $notif->name, $matches)) {
                    $fallbackUsernames[] = $matches[1];
                } elseif (preg_match('/@([a-zA-Z0-9_\-\.]+)/u', $notif->name, $matches)) {
                    $fallbackUsernames[] = $matches[1];
                }
            }
        }

        $fallbackUsers = empty($fallbackUsernames) 
            ? collect() 
            : User::whereIn('username', array_unique($fallbackUsernames))->get()->keyBy('username');

        $notificationService = app(NotificationService::class);

        $formatted = $notifications->getCollection()->map(function ($notif) use ($fallbackUsers, $notificationService) {
            $sender = $notif->sender;

            // Resolve legacy sender if sender_id was null
            if (!$sender) {
                if (preg_match('/(?:قام|أعجب|علق|تابعك|رد|ذكرك)\s+([a-zA-Z0-9_\-\.]+)/u', $notif->name, $matches)) {
                    $sender = $fallbackUsers->get($matches[1]);
                } elseif (preg_match('/@([a-zA-Z0-9_\-\.]+)/u', $notif->name, $matches)) {
                    $sender = $fallbackUsers->get($matches[1]);
                }
            }

            // Determine notification category type
            $logo = strtolower((string) ($notif->logo ?? 'notification'));
            $type = 'system';
            if (in_array($logo, ['like', 'love', 'happy', 'funny', 'wow', 'angry', 'sad', 'heart'])) {
                $type = 'reaction';
            } elseif (in_array($logo, ['comment', 'comments', 'reply'])) {
                $type = 'comment';
            } elseif ($logo === 'at') {
                $type = 'mention';
            } elseif ($logo === 'user-plus') {
                $type = 'follower';
            } elseif (in_array($logo, ['shopping-bag', 'cart', 'order'])) {
                $type = 'order';
            } elseif (str_contains($notif->name, 'مهمة') || $logo === 'trophy') {
                $type = 'quest';
            }

            // Human-readable time
            $timeAgo = $notif->time ? Carbon::createFromTimestamp($notif->time)->diffForHumans() : '';

            // Clean in-app mobile route
            $targetRoute = $notificationService->resolveMobileRoute($notif->nurl ?? '');

            return [
                'id' => $notif->id,
                'type' => $type,
                'text' => $notif->display_name,
                'time' => $notif->time,
                'time_ago' => $timeAgo,
                'state' => $notif->state,
                'is_unread' => in_array((int)$notif->state, [0, 3]),
                'icon' => $notif->logo ?? 'bell',
                'target_url' => $notif->nurl ?? '',
                'target_route' => $targetRoute,
                'sender' => $sender ? [
                    'id' => $sender->id,
                    'username' => $sender->username,
                    'name' => $sender->name ?? $sender->username,
                    'avatar' => $sender->avatarUrl(),
                    'is_verified' => (bool) $sender->hasVerifiedBadge(),
                ] : null,
                'source_user_id' => $sender?->id,
            ];
        });

        $notifications->setCollection($formatted);

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'has_more' => $notifications->hasMorePages(),
            'filter' => $filter,
        ]);
    }

    /**
     * Mark single notification as read.
     */
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

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $userId = Auth::id();
        Notification::where('uid', $userId)
            ->whereIn('state', [0, 3])
            ->update(['state' => 1]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    /**
     * Delete an individual notification.
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        $notification = Notification::where('id', $id)
            ->where('uid', $userId)
            ->first();

        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json(['success' => true, 'message' => 'Notification deleted']);
    }

    /**
     * Get unread notification counts (overall and categorized).
     */
    public function unreadCount()
    {
        $userId = Auth::id();
        
        $baseQuery = Notification::where('uid', $userId)->whereIn('state', [0, 3]);

        $totalUnread = (clone $baseQuery)->count();
        $mentionsUnread = (clone $baseQuery)->where('logo', 'at')->count();
        $reactionsUnread = (clone $baseQuery)->whereIn('logo', ['like', 'love', 'happy', 'funny', 'wow', 'angry', 'sad', 'heart'])->count();
        $commentsUnread = (clone $baseQuery)->whereIn('logo', ['comment', 'comments', 'reply'])->count();

        return response()->json([
            'success' => true,
            'unread_count' => $totalUnread,
            'counts' => [
                'all' => $totalUnread,
                'mentions' => $mentionsUnread,
                'reactions' => $reactionsUnread,
                'comments' => $commentsUnread,
            ],
        ]);
    }
}
