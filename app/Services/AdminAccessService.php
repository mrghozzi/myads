<?php

namespace App\Services;

use App\Models\SiteAdmin;
use App\Models\User;

class AdminAccessService
{
    public function __construct(
        private readonly V420SchemaService $schema
    ) {
    }

    public const MODULES = [
        'dashboard',
        'users',
        'ads',
        'community',
        'design',
        'pages',
        'settings',
        'seo',
        'languages',
        'updates',
        'plugins',
        'themes',
        'administrators',
        'maintenance',
        'security',
        'billing',
    ];

    public function recordFor(User $user): ?SiteAdmin
    {
        if (!$this->schema->supports('site_admins')) {
            return null;
        }

        return $user->relationLoaded('siteAdminEntry')
            ? $user->getRelation('siteAdminEntry')
            : $user->siteAdminEntry()->first();
    }

    public function canAccess(?User $user, ?string $routeName = null, ?string $module = null): bool
    {
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $record = $this->recordFor($user);
        if (!$record || !$record->is_active) {
            return false;
        }

        if ($record->has_full_access) {
            return true;
        }

        $module = $module ?: $this->moduleForRoute($routeName);
        if ($module === null) {
            return true;
        }

        return in_array($module, (array) $record->permissions, true);
    }

    public function canManageAdministrators(?User $user): bool
    {
        return $user?->isSuperAdmin() ?? false;
    }

    public function moduleForRoute(?string $routeName): ?string
    {
        if (!$routeName || !str_starts_with($routeName, 'admin.')) {
            return null;
        }

        return match (true) {
            in_array($routeName, ['admin.index', 'admin.stats'], true) => 'dashboard',
            str_starts_with($routeName, 'admin.users') => 'users',
            str_starts_with($routeName, 'admin.admins') => 'administrators',
            str_starts_with($routeName, 'admin.pages') => 'pages',
            str_starts_with($routeName, 'admin.seo') => 'seo',
            str_starts_with($routeName, 'admin.languages') => 'languages',
            str_starts_with($routeName, 'admin.updates') => 'updates',
            str_starts_with($routeName, 'admin.maintenance') => 'maintenance',
            str_starts_with($routeName, 'admin.plugins') => 'plugins',
            str_starts_with($routeName, 'admin.themes') => 'themes',
            str_starts_with($routeName, 'admin.security') => 'security',
            str_starts_with($routeName, 'admin.billing') => 'billing',
            str_starts_with($routeName, 'admin.widgets'),
            str_starts_with($routeName, 'admin.menus'),
            str_starts_with($routeName, 'admin.site_ads') => 'design',
            str_starts_with($routeName, 'admin.settings'),
            str_starts_with($routeName, 'admin.cookie_notice') => 'settings',
            str_starts_with($routeName, 'admin.community.feed') => 'community',
            str_starts_with($routeName, 'admin.groups') => 'community',
            str_starts_with($routeName, 'admin.banners'),
            str_starts_with($routeName, 'admin.links'),
            str_starts_with($routeName, 'admin.smart_ads'),
            str_starts_with($routeName, 'admin.visits'),
            str_starts_with($routeName, 'admin.ads') => 'ads',
            str_starts_with($routeName, 'admin.forum'),
            str_starts_with($routeName, 'admin.directory'),
            str_starts_with($routeName, 'admin.knowledgebase'),
            str_starts_with($routeName, 'admin.emojis'),
            str_starts_with($routeName, 'admin.news'),
            str_starts_with($routeName, 'admin.reports'),
            str_starts_with($routeName, 'admin.products') => 'community',
            default => null,
        };
    }
}
