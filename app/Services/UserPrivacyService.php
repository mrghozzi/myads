<?php

namespace App\Services;

use App\Models\Like;
use App\Models\User;
use App\Models\UserPrivacySetting;

class UserPrivacyService
{
    public const VISIBILITY_PUBLIC = 'public';
    public const VISIBILITY_FOLLOWERS = 'followers';
    public const VISIBILITY_PRIVATE = 'private';

    private const DEFAULTS = [
        'profile_visibility' => self::VISIBILITY_PUBLIC,
        'about_visibility' => self::VISIBILITY_PUBLIC,
        'photos_visibility' => self::VISIBILITY_PUBLIC,
        'followers_visibility' => self::VISIBILITY_PUBLIC,
        'following_visibility' => self::VISIBILITY_PUBLIC,
        'points_history_visibility' => self::VISIBILITY_PRIVATE,
        'allow_direct_messages' => true,
        'allow_mentions' => true,
        'allow_reposts' => true,
        'show_online_status' => true,
    ];

    public function __construct(
        private readonly V420SchemaService $schema
    ) {
    }

    public function settingsFor(User $user): UserPrivacySetting
    {
        if (!$this->schema->supports('privacy')) {
            return tap(new UserPrivacySetting($this->defaultAttributesFor($user)), function (UserPrivacySetting $settings): void {
                $settings->exists = false;
            });
        }

        return UserPrivacySetting::firstOrCreate(
            ['user_id' => (int) $user->id],
            self::DEFAULTS
        );
    }

    public function updateSettings(User $user, array $attributes): UserPrivacySetting
    {
        if (!$this->schema->supports('privacy')) {
            throw new \RuntimeException(
                $this->schema->blockedActionMessage('privacy', __('messages.privacy_settings'))
            );
        }

        $settings = $this->settingsFor($user);
        $settings->fill($attributes);
        $settings->save();

        return $settings->fresh();
    }

    public function canViewProfile(User $owner, ?User $viewer): bool
    {
        $settings = $this->settingsFor($owner);
        return $this->canViewVisibility($owner, $viewer, $settings->profile_visibility);
    }

    public function canViewAbout(User $owner, ?User $viewer): bool
    {
        $settings = $this->settingsFor($owner);
        return $this->canViewVisibility($owner, $viewer, $settings->about_visibility);
    }

    public function canViewPhotos(User $owner, ?User $viewer): bool
    {
        $settings = $this->settingsFor($owner);
        return $this->canViewVisibility($owner, $viewer, $settings->photos_visibility);
    }

    public function canViewFollowers(User $owner, ?User $viewer): bool
    {
        $settings = $this->settingsFor($owner);
        return $this->canViewVisibility($owner, $viewer, $settings->followers_visibility);
    }

    public function canViewFollowing(User $owner, ?User $viewer): bool
    {
        $settings = $this->settingsFor($owner);
        return $this->canViewVisibility($owner, $viewer, $settings->following_visibility);
    }

    public function canViewPointsHistory(User $owner, ?User $viewer): bool
    {
        $settings = $this->settingsFor($owner);
        return $this->canViewVisibility($owner, $viewer, $settings->points_history_visibility);
    }

    public function canDirectMessage(User $owner, ?User $viewer): bool
    {
        if ($viewer && ((int) $viewer->id === (int) $owner->id || $viewer->hasAdminAccess())) {
            return true;
        }

        return $this->settingsFor($owner)->allow_direct_messages;
    }

    public function canMention(User $owner, ?User $viewer): bool
    {
        if ($viewer && ((int) $viewer->id === (int) $owner->id || $viewer->hasAdminAccess())) {
            return true;
        }

        return $this->settingsFor($owner)->allow_mentions;
    }

    public function canRepost(User $owner, ?User $viewer): bool
    {
        if ($viewer && ((int) $viewer->id === (int) $owner->id || $viewer->hasAdminAccess())) {
            return true;
        }

        return $this->settingsFor($owner)->allow_reposts;
    }

    public function shouldShowOnlineStatus(User $owner, ?User $viewer): bool
    {
        if ($viewer && ((int) $viewer->id === (int) $owner->id || $viewer->hasAdminAccess())) {
            return true;
        }

        return $this->settingsFor($owner)->show_online_status;
    }

    public function visibilityOptions(): array
    {
        return [
            self::VISIBILITY_PUBLIC,
            self::VISIBILITY_FOLLOWERS,
            self::VISIBILITY_PRIVATE,
        ];
    }

    private function canViewVisibility(User $owner, ?User $viewer, string $visibility): bool
    {
        if ($viewer && ((int) $viewer->id === (int) $owner->id || $viewer->hasAdminAccess())) {
            return true;
        }

        if ($visibility === self::VISIBILITY_PUBLIC) {
            return true;
        }

        if ($visibility === self::VISIBILITY_PRIVATE) {
            return false;
        }

        if (!$viewer) {
            return false;
        }

        return Like::where('uid', (int) $viewer->id)
            ->where('sid', (int) $owner->id)
            ->where('type', 1)
            ->exists();
    }

    private function defaultAttributesFor(User $user): array
    {
        return array_merge(['user_id' => (int) $user->id], self::DEFAULTS);
    }
}
