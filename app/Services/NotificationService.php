<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Mail\SystemNotificationMail;
use App\Services\FcmPushService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        protected ?FcmPushService $fcmPush = null
    ) {
        $this->fcmPush = $fcmPush ?? app(FcmPushService::class);
    }

    /**
     * Send a notification to a user, push alert to mobile, and optionally an email.
     * 
     * @param User|int|null $recipient
     * @param string $message
     * @param string $url
     * @param string $logo
     * @param int|null $excludeUserId Also acts as sender/actor ID if not explicitly set
     * @param string|null $type Specific notification type for settings (e.g. follower, comment, etc.)
     * @param bool $storeInDb
     * @param int|null $senderId Explicit sender/actor user ID
     * @return Notification|null
     */
    public function send(
        User|int|null $recipient,
        string $message,
        string $url = '',
        string $logo = 'notification',
        ?int $excludeUserId = null,
        ?string $type = null,
        bool $storeInDb = true,
        ?int $senderId = null
    ): ?Notification {
        $user = $recipient instanceof User ? $recipient : User::find($recipient);
        
        if (!$user) {
            return null;
        }

        $recipientId = (int) $user->id;

        if ($excludeUserId !== null && $recipientId === $excludeUserId) {
            return null;
        }

        $actorId = $senderId ?? ($excludeUserId && $excludeUserId !== $recipientId ? $excludeUserId : null);
        $notification = null;

        // 1. Create Database Notification (if enabled)
        if ($storeInDb) {
            $notification = Notification::create([
                'uid' => $recipientId,
                'sender_id' => $actorId,
                'name' => $message,
                'nurl' => $url,
                'logo' => $logo ?: 'notification',
                'time' => time(),
                'state' => 0,
            ]);
        }

        // 2. Queue Email Notification asynchronously
        try {
            $this->sendEmailNotification($user, $message, $url, $logo, $type);
        } catch (\Throwable $e) {
            Log::error('Failed to queue email notification: ' . $e->getMessage());
        }

        // 3. Dispatch Mobile Push Notification (FCM)
        try {
            $this->sendPushNotification($user, $message, $url, $logo, $type, $actorId, $notification?->id);
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch push notification: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Check settings and send mobile push notification via FCM.
     */
    protected function sendPushNotification(
        User $user,
        string $message,
        string $url,
        string $logo,
        ?string $type,
        ?int $actorId,
        ?int $notificationId
    ): void {
        if (!$type) {
            $type = $this->resolveTypeFromLogo($logo);
        }

        $settings = $user->notificationSetting;
        $fieldName = 'push_' . ($type ?: 'reaction');

        // Default to true if user settings row doesn't exist
        $isEnabled = !$settings || !isset($settings->{$fieldName}) || (bool) $settings->{$fieldName};

        if (!$isEnabled) {
            return;
        }

        // Resolve clean in-app mobile route from URL
        $targetRoute = $this->resolveMobileRoute($url);

        $dataPayload = [
            'notification_id' => $notificationId ? (string) $notificationId : '',
            'type' => $type ?: 'general',
            'logo' => $logo,
            'target_url' => $url,
            'target_route' => $targetRoute,
            'actor_id' => $actorId ? (string) $actorId : '',
        ];

        $appTitle = config('app.name', 'MYADS');
        $this->fcmPush->sendToUser($user, $appTitle, $message, $dataPayload);
    }

    /**
     * Map web URL to clean in-app Flutter route.
     */
    public function resolveMobileRoute(string $url): string
    {
        if (empty($url)) {
            return '/notifications';
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? $url;

        // /clips/208 -> /clips/208
        if (preg_match('#/clips/(\d+)#', $path, $m)) {
            return "/clips/{$m[1]}";
        }

        // /post/123 or /t123 -> /post?id=123
        if (preg_match('#/(?:post|t)/?(\d+)#', $path, $m)) {
            return "/post?id={$m[1]}";
        }

        // /messages/{username} -> /messages/{username}
        if (preg_match('#/(?:messages|chat)/([a-zA-Z0-9_\-\.]+)#', $path, $m)) {
            return "/messages/{$m[1]}";
        }

        // /profile/{username} -> /user-profile?username={username}
        if (preg_match('#/(?:profile|u)/([a-zA-Z0-9_\-\.]+)#', $path, $m)) {
            return "/user-profile?username={$m[1]}";
        }

        // /orders/{id} -> /orders/{id}
        if (preg_match('#/orders/(?:show/)?(\d+)#', $path, $m)) {
            return "/orders/{$m[1]}";
        }

        // /history (quests) -> /settings/history
        if (str_contains($path, 'history') || str_contains($path, 'quests')) {
            return '/settings/history';
        }

        return '/notifications';
    }

    /**
     * Check settings and send email if enabled.
     */
    protected function sendEmailNotification(User $user, string $message, string $url, string $logo, ?string $type): void
    {
        // Resolve type from logo if not provided
        if (!$type) {
            $type = $this->resolveTypeFromLogo($logo);
        }

        if (!$type) {
            return;
        }

        $settings = $user->notificationSetting;
        
        // If no settings found, default to true (legacy behavior/opt-out)
        // Or if the specific setting is enabled
        $fieldName = 'email_' . $type;
        $isEnabled = !$settings || (bool) $settings->{$fieldName};

        if ($isEnabled && !empty($user->email)) {
            try {
                Mail::to($user->email)->queue(new SystemNotificationMail($user, $message, $url));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("NotificationService email queue notice: " . $e->getMessage());
                try {
                    \App\Jobs\SendTransactionalEmailJob::forMailable($user->email, new SystemNotificationMail($user, $message, $url), 'default');
                } catch (\Throwable $err) {
                    \Illuminate\Support\Facades\Log::error("NotificationService failed to queue email: " . $err->getMessage());
                }
            }
        }
    }

    /**
     * Map logo/icon to notification setting types.
     */
    protected function resolveTypeFromLogo(string $logo): ?string
    {
        $map = [
            'comment' => 'new_comment',
            'user-plus' => 'new_follower',
            'envelope' => 'new_message',
            'at' => 'mention',
            'retweet' => 'repost',
            'heart' => 'reaction',
            'comments' => 'forum_reply',
            'shopping-bag' => 'marketplace_update',
            
            // Reaction aliases (from themes/default/assets/img/reaction/)
            'like' => 'reaction',
            'love' => 'reaction',
            'haha' => 'reaction',
            'wow' => 'reaction',
            'sad' => 'reaction',
            'angry' => 'reaction',
        ];

        return $map[$logo] ?? null;
    }
}
