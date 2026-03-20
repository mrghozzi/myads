<?php

namespace App\Support;

use App\Models\Directory;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DirectoryPresenter
{
    public static function presentFeedItem(Status $activity, array $metrics = []): ?array
    {
        $listing = $activity->related_content;

        if (!$listing instanceof Directory) {
            return null;
        }

        return self::presentListing($listing, $activity, $metrics);
    }

    public static function presentListing(Directory $listing, ?Status $activity = null, array $metrics = []): array
    {
        $owner = $listing->user;
        $category = $listing->category;
        $publishedAt = $activity?->date ?: $listing->date;
        $description = trim((string) $listing->txt);
        $excerpt = trim(preg_replace('/\s+/u', ' ', strip_tags($description)));

        return [
            'listing' => $listing,
            'activity' => $activity,
            'title' => $listing->name,
            'detail_url' => route('directory.show', $listing->id),
            'visit_url' => self::buildShortUrl($listing),
            'display_domain' => self::extractDomain($listing->url),
            'excerpt' => Str::limit($excerpt, 200),
            'description' => $description,
            'description_html' => $description !== '' ? nl2br(e($description)) : '',
            'tags' => self::parseTags($listing->metakeywords),
            'owner' => $owner,
            'owner_name' => $owner?->username ?? __('messages.unknown_user'),
            'owner_url' => $owner ? route('profile.show', $owner->username) : null,
            'owner_avatar' => $owner?->img ? asset($owner->img) : theme_asset('img/avatar.jpg'),
            'category' => $category,
            'category_name' => $category?->name ?? __('messages.category_fallback'),
            'category_url' => $category ? route('directory.category.legacy', $category->id) : null,
            'published_timestamp' => $publishedAt,
            'published_diff' => $publishedAt ? Carbon::createFromTimestamp((int) $publishedAt)->diffForHumans() : '',
            'views' => (int) ($listing->vu ?? 0),
            'reactions_count' => (int) ($metrics['reactions_count'] ?? 0),
            'comments_count' => (int) ($metrics['comments_count'] ?? 0),
            'current_reaction' => $metrics['current_reaction'] ?? null,
            'can_manage' => (bool) ($metrics['can_manage'] ?? false),
        ];
    }

    public static function buildShortUrl(Directory $listing): string
    {
        $hash = hash('crc32', $listing->url . $listing->id);

        return route('directory.redirect.short', 'site-' . $hash);
    }

    public static function extractDomain(?string $url): string
    {
        $host = parse_url((string) $url, PHP_URL_HOST);

        if (!$host && $url) {
            $host = parse_url('https://' . ltrim($url, '/'), PHP_URL_HOST);
        }

        if (!$host) {
            return (string) $url;
        }

        return preg_replace('/^www\./i', '', $host) ?: $host;
    }

    public static function parseTags(?string $keywords): array
    {
        if (!$keywords) {
            return [];
        }

        return collect(preg_split('/[,;\r\n]+/', $keywords) ?: [])
            ->map(static fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique()
            ->values()
            ->take(8)
            ->all();
    }
}
