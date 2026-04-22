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
            'user.identity.read' => [
                'id' => 'user.identity.read',
                'name' => 'messages.dev_scope_identity_read',
                'description' => 'messages.dev_scope_identity_read_desc',
                'is_sensitive' => false,
            ],
            'user.profile.read' => [
                'id' => 'user.profile.read',
                'name' => 'messages.dev_scope_profile_read',
                'description' => 'messages.dev_scope_profile_read_desc',
                'is_sensitive' => false,
            ],
            'user.social_links.read' => [
                'id' => 'user.social_links.read',
                'name' => 'messages.dev_scope_social_links_read',
                'description' => 'messages.dev_scope_social_links_read_desc',
                'is_sensitive' => false,
            ],
            'user.follows.read' => [
                'id' => 'user.follows.read',
                'name' => 'messages.dev_scope_follows_read',
                'description' => 'messages.dev_scope_follows_read_desc',
                'is_sensitive' => false,
            ],
            'owner.profile.read' => [
                'id' => 'owner.profile.read',
                'name' => 'messages.dev_scope_owner_profile_read',
                'description' => 'messages.dev_scope_owner_profile_read_desc',
                'is_sensitive' => false,
            ],
            'owner.content.read' => [
                'id' => 'owner.content.read',
                'name' => 'messages.dev_scope_owner_content_read',
                'description' => 'messages.dev_scope_owner_content_read_desc',
                'is_sensitive' => false,
            ],
            'owner.follow.write' => [
                'id' => 'owner.follow.write',
                'name' => 'messages.dev_scope_owner_follow_write',
                'description' => 'messages.dev_scope_owner_follow_write_desc',
                'is_sensitive' => true,
            ],
            'owner.messages.read' => [
                'id' => 'owner.messages.read',
                'name' => 'messages.dev_scope_owner_messages_read',
                'description' => 'messages.dev_scope_owner_messages_read_desc',
                'is_sensitive' => true,
            ],
            'owner.messages.write' => [
                'id' => 'owner.messages.write',
                'name' => 'messages.dev_scope_owner_messages_write',
                'description' => 'messages.dev_scope_owner_messages_write_desc',
                'is_sensitive' => true,
            ],
        ];
    }

    public static function getScope(string $id): ?array
    {
        $scopes = self::getAllScopes();
        return $scopes[$id] ?? null;
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
}
