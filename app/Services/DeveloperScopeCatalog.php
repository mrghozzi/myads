<?php

namespace App\Services;

class DeveloperScopeCatalog
{
    /**
     * Get all available scopes and their metadata.
     */
    public static function getAllScopes(): array
    {
        return [
            // User Identity & Profile
            'user.identity.read' => [
                'id' => 'user.identity.read',
                'name' => 'messages.dev_scope_identity_read',
                'description' => 'messages.dev_scope_identity_read_desc',
                'category' => 'identity',
                'is_sensitive' => false,
            ],
            'user.profile.read' => [
                'id' => 'user.profile.read',
                'name' => 'messages.dev_scope_profile_read',
                'description' => 'messages.dev_scope_profile_read_desc',
                'category' => 'identity',
                'is_sensitive' => false,
            ],
            'user.email.read' => [
                'id' => 'user.email.read',
                'name' => 'messages.dev_scope_email_read',
                'description' => 'messages.dev_scope_email_read_desc',
                'category' => 'identity',
                'is_sensitive' => true,
            ],
            'user.social_links.read' => [
                'id' => 'user.social_links.read',
                'name' => 'messages.dev_scope_social_links_read',
                'description' => 'messages.dev_scope_social_links_read_desc',
                'category' => 'identity',
                'is_sensitive' => false,
            ],
            'user.follows.read' => [
                'id' => 'user.follows.read',
                'name' => 'messages.dev_scope_follows_read',
                'description' => 'messages.dev_scope_follows_read_desc',
                'category' => 'identity',
                'is_sensitive' => false,
            ],
            'user.follows.write' => [
                'id' => 'user.follows.write',
                'name' => 'messages.dev_scope_follows_write',
                'description' => 'messages.dev_scope_follows_write_desc',
                'category' => 'identity',
                'is_sensitive' => true,
            ],

            // Content & Interaction
            'user.content.read' => [
                'id' => 'user.content.read',
                'name' => 'messages.dev_scope_content_read',
                'description' => 'messages.dev_scope_content_read_desc',
                'category' => 'content',
                'is_sensitive' => false,
            ],
            'user.content.write' => [
                'id' => 'user.content.write',
                'name' => 'messages.dev_scope_content_write',
                'description' => 'messages.dev_scope_content_write_desc',
                'category' => 'content',
                'is_sensitive' => true,
            ],
            'user.reactions.write' => [
                'id' => 'user.reactions.write',
                'name' => 'messages.dev_scope_reactions_write',
                'description' => 'messages.dev_scope_reactions_write_desc',
                'category' => 'content',
                'is_sensitive' => false,
            ],
            'user.comments.write' => [
                'id' => 'user.comments.write',
                'name' => 'messages.dev_scope_comments_write',
                'description' => 'messages.dev_scope_comments_write_desc',
                'category' => 'content',
                'is_sensitive' => true,
            ],

            // Messages & Notifications
            'user.messages.read' => [
                'id' => 'user.messages.read',
                'name' => 'messages.dev_scope_messages_read',
                'description' => 'messages.dev_scope_messages_read_desc',
                'category' => 'messaging',
                'is_sensitive' => true,
            ],
            'user.messages.write' => [
                'id' => 'user.messages.write',
                'name' => 'messages.dev_scope_messages_write',
                'description' => 'messages.dev_scope_messages_write_desc',
                'category' => 'messaging',
                'is_sensitive' => true,
            ],
            'user.notifications.read' => [
                'id' => 'user.notifications.read',
                'name' => 'messages.dev_scope_notifications_read',
                'description' => 'messages.dev_scope_notifications_read_desc',
                'category' => 'messaging',
                'is_sensitive' => false,
            ],

            // Economy & Gamification
            'user.wallet.read' => [
                'id' => 'user.wallet.read',
                'name' => 'messages.dev_scope_wallet_read',
                'description' => 'messages.dev_scope_wallet_read_desc',
                'category' => 'economy',
                'is_sensitive' => true,
            ],
            'user.badges.read' => [
                'id' => 'user.badges.read',
                'name' => 'messages.dev_scope_badges_read',
                'description' => 'messages.dev_scope_badges_read_desc',
                'category' => 'economy',
                'is_sensitive' => false,
            ],

            // Media & Communities
            'user.clips.read' => [
                'id' => 'user.clips.read',
                'name' => 'messages.dev_scope_clips_read',
                'description' => 'messages.dev_scope_clips_read_desc',
                'category' => 'community',
                'is_sensitive' => false,
            ],
            'user.clips.write' => [
                'id' => 'user.clips.write',
                'name' => 'messages.dev_scope_clips_write',
                'description' => 'messages.dev_scope_clips_write_desc',
                'category' => 'community',
                'is_sensitive' => false,
            ],
            'user.forums.read' => [
                'id' => 'user.forums.read',
                'name' => 'messages.dev_scope_forums_read',
                'description' => 'messages.dev_scope_forums_read_desc',
                'category' => 'community',
                'is_sensitive' => false,
            ],
            'user.forums.write' => [
                'id' => 'user.forums.write',
                'name' => 'messages.dev_scope_forums_write',
                'description' => 'messages.dev_scope_forums_write_desc',
                'category' => 'community',
                'is_sensitive' => true,
            ],

            // Store & Ads
            'user.store.read' => [
                'id' => 'user.store.read',
                'name' => 'messages.dev_scope_store_read',
                'description' => 'messages.dev_scope_store_read_desc',
                'category' => 'commerce',
                'is_sensitive' => false,
            ],
            'user.orders.read' => [
                'id' => 'user.orders.read',
                'name' => 'messages.dev_scope_orders_read',
                'description' => 'messages.dev_scope_orders_read_desc',
                'category' => 'commerce',
                'is_sensitive' => true,
            ],
            'user.ads.read' => [
                'id' => 'user.ads.read',
                'name' => 'messages.dev_scope_ads_read',
                'description' => 'messages.dev_scope_ads_read_desc',
                'category' => 'commerce',
                'is_sensitive' => false,
            ],

            // App Owner Scopes
            'owner.profile.read' => [
                'id' => 'owner.profile.read',
                'name' => 'messages.dev_scope_owner_profile_read',
                'description' => 'messages.dev_scope_owner_profile_read_desc',
                'category' => 'owner',
                'is_sensitive' => false,
            ],
            'owner.content.read' => [
                'id' => 'owner.content.read',
                'name' => 'messages.dev_scope_owner_content_read',
                'description' => 'messages.dev_scope_owner_content_read_desc',
                'category' => 'owner',
                'is_sensitive' => false,
            ],
            'owner.follow.write' => [
                'id' => 'owner.follow.write',
                'name' => 'messages.dev_scope_owner_follow_write',
                'description' => 'messages.dev_scope_owner_follow_write_desc',
                'category' => 'owner',
                'is_sensitive' => true,
            ],
            'owner.messages.read' => [
                'id' => 'owner.messages.read',
                'name' => 'messages.dev_scope_owner_messages_read',
                'description' => 'messages.dev_scope_owner_messages_read_desc',
                'category' => 'owner',
                'is_sensitive' => true,
            ],
            'owner.messages.write' => [
                'id' => 'owner.messages.write',
                'name' => 'messages.dev_scope_owner_messages_write',
                'description' => 'messages.dev_scope_owner_messages_write_desc',
                'category' => 'owner',
                'is_sensitive' => true,
            ],
        ];
    }

    /**
     * Normalize scope aliases to standard scope IDs (e.g. avoiding WAF rules on '.profile').
     */
    public static function normalizeScopeId(string $id): string
    {
        $id = trim($id);
        return match ($id) {
            'profile.read', 'user_profile.read', 'user_profile_read', 'user-profile-read', 'user-profile.read', 'profile' => 'user.profile.read',
            'identity.read', 'user_identity.read', 'user_identity_read', 'user-identity-read', 'identity' => 'user.identity.read',
            'content.write', 'user_content.write', 'user_content_write', 'user-content-write', 'content.create', 'posts.write', 'user.posts.write', 'publish_posts', 'posts.create', 'user.content.create', 'content' => 'user.content.write',
            'content.read', 'user_content.read', 'user_content_read', 'user-content-read', 'posts.read', 'user.posts.read' => 'user.content.read',
            'reactions.write', 'user_reactions.write', 'user_reactions_write', 'user-reactions-write' => 'user.reactions.write',
            'messages.read', 'user_messages.read', 'user_messages_read' => 'user.messages.read',
            'messages.write', 'user_messages.write', 'user_messages_write' => 'user.messages.write',
            'social_links.read', 'user_social_links.read', 'user-social-links-read' => 'user.social_links.read',
            'follows.read', 'user_follows.read' => 'user.follows.read',
            'follows.write', 'user_follows.write' => 'user.follows.write',
            'wallet.read', 'user_wallet.read' => 'user.wallet.read',
            default => $id,
        };
    }

    public static function getScope(string $id): ?array
    {
        $scopes = self::getAllScopes();
        $normalized = self::normalizeScopeId($id);
        return $scopes[$normalized] ?? $scopes[$id] ?? null;
    }

    public static function getScopes(array $ids): array
    {
        $scopes = [];
        foreach ($ids as $id) {
            $scope = self::getScope($id);
            if ($scope) {
                $scopes[] = $scope;
            }
        }
        return $scopes;
    }

    public static function getCategories(): array
    {
        return [
            'identity' => [
                'title' => 'messages.dev_scope_cat_identity',
                'icon' => 'fa-id-badge',
            ],
            'content' => [
                'title' => 'messages.dev_scope_cat_content',
                'icon' => 'fa-newspaper',
            ],
            'messaging' => [
                'title' => 'messages.dev_scope_cat_messaging',
                'icon' => 'fa-comments',
            ],
            'economy' => [
                'title' => 'messages.dev_scope_cat_gamification',
                'icon' => 'fa-wallet',
            ],
            'community' => [
                'title' => 'messages.dev_scope_cat_community',
                'icon' => 'fa-users',
            ],
            'commerce' => [
                'title' => 'messages.dev_scope_cat_commerce',
                'icon' => 'fa-chart-pie',
            ],
            'owner' => [
                'title' => 'messages.dev_scope_cat_owner',
                'icon' => 'fa-shield-halved',
            ],
        ];
    }
}

