<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserBlock;

class UserBlockService
{
    /**
     * Block a user.
     *
     * @param User $user The user initiating the block
     * @param User $target The user being blocked
     * @param string $blockType 'messages_only' or 'full_platform'
     * @param int|null $durationDays Number of days for the block, null for forever
     * @return UserBlock
     */
    public function blockUser(User $user, User $target, string $blockType = 'full_platform', ?int $durationDays = null)
    {
        $expiresAt = $durationDays ? now()->addDays($durationDays) : null;

        $block = UserBlock::updateOrCreate(
            [
                'user_id' => $user->id,
                'blocked_user_id' => $target->id,
            ],
            [
                'block_type' => $blockType,
                'expires_at' => $expiresAt,
            ]
        );

        if ($block->wasRecentlyCreated || $block->wasChanged('expires_at')) {
            $notificationService = app(\App\Services\NotificationService::class);
            $message = __('messages.you_were_blocked_by', ['user' => $user->username]) ?? "You have been blocked by {$user->username}.";
            $notificationService->send($target, $message, route('profile.show', $user->username), 'user-times', null, null, true);
        }

        return $block;
    }

    /**
     * Unblock a user.
     *
     * @param User $user The user who initiated the block
     * @param User $target The user to be unblocked
     * @return bool
     */
    public function unblockUser(User $user, User $target)
    {
        return (bool) UserBlock::where('user_id', $user->id)
            ->where('blocked_user_id', $target->id)
            ->delete();
    }

    /**
     * Check if a user is blocked by another user or vice versa.
     *
     * @param User $user First user
     * @param User $target Second user
     * @param string $blockType 'messages_only' or 'full_platform', or 'any' for either
     * @param bool $checkBothWays If true, checks if user blocked target OR target blocked user
     * @return bool
     */
    public function isBlocked(User $user, User $target, string $blockType = 'any', bool $checkBothWays = true)
    {
        if ((int)$user->id === (int)$target->id) {
            return false;
        }

        $query = UserBlock::active()->where(function ($q) use ($user, $target, $checkBothWays) {
            $q->where(function ($sq) use ($user, $target) {
                $sq->where('user_id', $user->id)
                   ->where('blocked_user_id', $target->id);
            });

            if ($checkBothWays) {
                $q->orWhere(function ($sq) use ($user, $target) {
                    $sq->where('user_id', $target->id)
                       ->where('blocked_user_id', $user->id);
                });
            }
        });

        if ($blockType !== 'any') {
            $query->where('block_type', $blockType);
        }

        return $query->exists();
    }

    /**
     * Check if there's a full platform block between two users.
     */
    public function hasFullBlock(User $user, User $target, bool $checkBothWays = true): bool
    {
        return $this->isBlocked($user, $target, 'full_platform', $checkBothWays);
    }

    /**
     * Check if there's a messages block (either messages_only or full_platform) between two users.
     */
    public function hasMessagesBlock(User $user, User $target, bool $checkBothWays = true): bool
    {
        if ((int)$user->id === (int)$target->id) {
            return false;
        }

        return UserBlock::active()->where(function ($q) use ($user, $target, $checkBothWays) {
            $q->where(function ($sq) use ($user, $target) {
                $sq->where('user_id', $user->id)
                   ->where('blocked_user_id', $target->id);
            });

            if ($checkBothWays) {
                $q->orWhere(function ($sq) use ($user, $target) {
                    $sq->where('user_id', $target->id)
                       ->where('blocked_user_id', $user->id);
                });
            }
        })->exists(); // any block type prevents messaging
    }

    /**
     * Get list of users blocked by the given user.
     */
    public function getBlockedUsersFor(User $user)
    {
        return UserBlock::with('blockedUser')
            ->where('user_id', $user->id)
            ->active()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    /**
     * Get IDs of users blocked by or blocking the given user for a specific block type.
     */
    public function getBlockedUserIds(User $user, string $blockType = 'any'): array
    {
        $query = UserBlock::active()
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('blocked_user_id', $user->id);
            });

        if ($blockType !== 'any') {
            $query->where('block_type', $blockType);
        }

        $blocks = $query->get(['user_id', 'blocked_user_id']);
        
        $ids = [];
        foreach ($blocks as $block) {
            $ids[] = $block->user_id == $user->id ? $block->blocked_user_id : $block->user_id;
        }

        return array_unique($ids);
    }
}
