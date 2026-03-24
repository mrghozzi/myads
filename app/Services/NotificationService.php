<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public function send(
        User|int|null $recipient,
        string $message,
        string $url = '',
        string $logo = 'notification',
        ?int $excludeUserId = null
    ): ?Notification {
        $recipientId = $recipient instanceof User ? (int) $recipient->id : (int) $recipient;
        if ($recipientId <= 0) {
            return null;
        }

        if ($excludeUserId !== null && $recipientId === $excludeUserId) {
            return null;
        }

        return Notification::create([
            'uid' => $recipientId,
            'name' => $message,
            'nurl' => $url,
            'logo' => $logo ?: 'notification',
            'time' => time(),
            'state' => 0,
        ]);
    }
}
