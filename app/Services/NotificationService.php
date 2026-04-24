<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Mail\SystemNotificationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send a notification to a user and optionally an email.
     * 
     * @param User|int|null $recipient
     * @param string $message
     * @param string $url
     * @param string $logo
     * @param int|null $excludeUserId
     * @param string|null $type Specific notification type for email settings (e.g. follower, comment, etc.)
     * @return Notification|null
     */
    public function send(
        User|int|null $recipient,
        string $message,
        string $url = '',
        string $logo = 'notification',
        ?int $excludeUserId = null,
        ?string $type = null,
        bool $storeInDb = true
    ): ?Notification {
        $user = $recipient instanceof User ? $recipient : User::find($recipient);
        
        if (!$user) {
            return null;
        }

        $recipientId = (int) $user->id;

        if ($excludeUserId !== null && $recipientId === $excludeUserId) {
            return null;
        }

        $notification = null;

        // 1. Create Database Notification (if enabled)
        if ($storeInDb) {
            $notification = Notification::create([
                'uid' => $recipientId,
                'name' => $message,
                'nurl' => $url,
                'logo' => $logo ?: 'notification',
                'time' => time(),
                'state' => 0,
            ]);
        }

        // 2. Handle Email Notification
        try {
            $this->sendEmailNotification($user, $message, $url, $logo, $type);
        } catch (\Throwable $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
        }

        return $notification;
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
            Mail::to($user->email)->send(new SystemNotificationMail($user, $message, $url));
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
