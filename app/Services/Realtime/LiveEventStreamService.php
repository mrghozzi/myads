<?php

namespace App\Services\Realtime;

use App\Models\Message;
use App\Models\Notification;
use App\Models\Report;
use App\Models\Status;
use App\Models\User;
use App\Services\MessageConversationService;

class LiveEventStreamService
{
    /**
     * Format a message into Server-Sent Events (SSE) standard protocol string.
     */
    public function formatSseMessage(string $event, array|string $data, ?string $id = null, ?int $retry = 3000): string
    {
        $output = '';

        if ($id !== null) {
            $output .= "id: {$id}\n";
        }

        if ($retry !== null) {
            $output .= "retry: {$retry}\n";
        }

        if ($event !== '') {
            $output .= "event: {$event}\n";
        }

        $payload = is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string) $data;
        $output .= "data: {$payload}\n\n";

        return $output;
    }

    /**
     * Get the initial handshake payload for an authenticated user.
     */
    public function getInitialHandshake(User $user): array
    {
        $unreadNotifications = $this->getUnreadNotificationsCount($user);
        $unreadMessages = $this->getUnreadMessagesCount($user);

        return [
            'status' => 'connected',
            'user_id' => $user->id,
            'username' => $user->username,
            'unread_notifications' => $unreadNotifications,
            'unread_messages' => $unreadMessages,
            'timestamp' => time(),
            'server_time' => now()->toIso8601String(),
        ];
    }

    /**
     * Poll and assemble all new event payloads for a user since a given timestamp.
     */
    public function pollUserEvents(User $user, int $lastCheckTimestamp): array
    {
        $events = [];
        $now = time();

        // 1. Notification Event Check
        $unreadNotifCount = $this->getUnreadNotificationsCount($user);
        $latestNotification = Notification::where('uid', $user->id)
            ->where('time', '>=', $lastCheckTimestamp)
            ->whereIn('state', [0, 3])
            ->orderByDesc('time')
            ->first();

        $events[] = [
            'type' => 'notifications',
            'data' => [
                'unread_count' => $unreadNotifCount,
                'has_new' => $latestNotification !== null,
                'latest' => $latestNotification ? [
                    'id' => $latestNotification->id,
                    'name' => $latestNotification->name,
                    'url' => $latestNotification->nurl,
                    'logo' => $latestNotification->logo ?: 'notification',
                    'time' => (int) $latestNotification->time,
                ] : null,
            ],
        ];

        // 2. Message Event Check
        $unreadMsgCount = $this->getUnreadMessagesCount($user);
        $latestMessage = Message::with('sender')
            ->where('us_rec', $user->id)
            ->where('time', '>=', $lastCheckTimestamp)
            ->where('state', '!=', 0)
            ->orderByDesc('time')
            ->first();

        $events[] = [
            'type' => 'messages',
            'data' => [
                'unread_count' => $unreadMsgCount,
                'has_new' => $latestMessage !== null,
                'latest' => $latestMessage ? [
                    'id' => $latestMessage->id_msg,
                    'sender_id' => $latestMessage->us_env,
                    'sender_name' => $latestMessage->sender?->username ?? $latestMessage->name,
                    'sender_avatar' => $latestMessage->sender?->img ? url($latestMessage->sender->img) : null,
                    'text_preview' => mb_substr(strip_tags((string) $latestMessage->text), 0, 80),
                    'time' => (int) $latestMessage->time,
                ] : null,
            ],
        ];

        // 3. Community Feed Updates Check
        $newFeedPostsCount = Status::where('statu', 0)
            ->where('uid', '!=', $user->id)
            ->where('date', '>=', $lastCheckTimestamp)
            ->count();

        if ($newFeedPostsCount > 0) {
            $events[] = [
                'type' => 'feed',
                'data' => [
                    'new_posts_count' => $newFeedPostsCount,
                    'timestamp' => $now,
                ],
            ];
        }

        // 4. Admin Monitoring Alerts (for Admin ID 1)
        if ((int) $user->id === 1) {
            $pendingReportsCount = Report::where('statu', 0)->count();
            $events[] = [
                'type' => 'admin',
                'data' => [
                    'pending_reports' => $pendingReportsCount,
                    'timestamp' => $now,
                ],
            ];
        }

        return $events;
    }

    /**
     * Get unread notifications count for a user.
     */
    public function getUnreadNotificationsCount(User $user): int
    {
        return Notification::where('uid', $user->id)
            ->whereIn('state', [0, 3])
            ->count();
    }

    /**
     * Get unread messages count for a user.
     */
    public function getUnreadMessagesCount(User $user): int
    {
        return app(MessageConversationService::class)->unreadConversationCount($user);
    }
}
