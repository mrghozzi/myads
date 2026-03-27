<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Option;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FeedService
{
    private const RECENCY_BASE = 650.0;
    private const RECENCY_DECAY = 1.35;
    private const WEIGHT_VIEW = 0.35;
    private const MAX_VIEWS_SCORE = 40.0;
    private const WEIGHT_REACTION = 2.0;
    private const WEIGHT_COMMENT = 3.0;
    private const WEIGHT_REPOST = 4.0;
    private const BOOST_FOLLOWING = 20.0;
    private const BOOST_AUTHOR_AFFINITY = 10.0;
    private const BOOST_CONTENT_AFFINITY = 8.0;
    private const BOOST_SOCIAL_PROOF = 10.0;
    private const REPEAT_AUTHOR_PENALTY = 12.0;
    private const REPEAT_TYPE_PENALTY = 8.0;
    private const CANDIDATE_POOL = 300;
    private const CACHE_TTL = 300;

    public static function getRankedFeed(?int $userId, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = 'smart_feed:' . ($userId ?: 'guest') . ':' . floor(time() / self::CACHE_TTL);

        $ranked = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return self::buildRankedCollection($userId);
        });

        $total = $ranked->count();
        $organicItems = $ranked->forPage($page, $perPage)->values();
        $items = $organicItems;

        $request = request();
        if (
            (string) $request->query('filter', 'all') === 'all'
            && trim((string) $request->query('search', '')) === ''
        ) {
            $items = app(StatusPromotionService::class)->injectIntoFeed(
                $organicItems,
                $userId,
                $page,
                $perPage
            );
        }

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    private static function buildRankedCollection(?int $userId): Collection
    {
        $schema = app(V420SchemaService::class);

        $hiddenDirectoryStatusIds = $schema->supports('link_previews')
            ? StatusLinkPreview::query()
                ->whereNotNull('directory_status_id')
                ->pluck('directory_status_id')
                ->filter()
                ->values()
            : collect();

        $statuses = Status::visible()
            ->where('date', '<', time())
            ->when($hiddenDirectoryStatusIds->isNotEmpty(), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
            ->orderBy('date', 'desc')
            ->limit(self::CANDIDATE_POOL)
            ->get();

        if ($statuses->isEmpty()) {
            return collect();
        }

        $followingIds = self::followingIds($userId);
        $followingSet = array_flip($followingIds);

        $forumIds = $statuses->whereIn('s_type', [2, 4, 100])->pluck('tp_id')->unique()->values();
        $directoryIds = $statuses->where('s_type', 1)->pluck('tp_id')->unique()->values();
        $storeIds = $statuses->where('s_type', 7867)->pluck('tp_id')->unique()->values();
        $statusIds = $statuses->pluck('id')->unique()->values();

        $forumTopics = $forumIds->isNotEmpty()
            ? ForumTopic::whereIn('id', $forumIds)->get()->keyBy('id')
            : collect();

        $directories = $directoryIds->isNotEmpty()
            ? Directory::whereIn('id', $directoryIds)->get()->keyBy('id')
            : collect();

        $reactionCounts = collect();
        if ($forumIds->isNotEmpty()) {
            $reactionCounts = $reactionCounts->merge(
                Like::where('type', 2)->whereIn('sid', $forumIds)
                    ->selectRaw('sid, COUNT(*) as cnt')
                    ->groupBy('sid')
                    ->pluck('cnt', 'sid')
            );
        }
        if ($directoryIds->isNotEmpty()) {
            $reactionCounts = $reactionCounts->merge(
                Like::where('type', 22)->whereIn('sid', $directoryIds)
                    ->selectRaw('sid, COUNT(*) as cnt')
                    ->groupBy('sid')
                    ->pluck('cnt', 'sid')
            );
        }
        if ($storeIds->isNotEmpty()) {
            $reactionCounts = $reactionCounts->merge(
                Like::where('type', 3)->whereIn('sid', $storeIds)
                    ->selectRaw('sid, COUNT(*) as cnt')
                    ->groupBy('sid')
                    ->pluck('cnt', 'sid')
            );
        }

        $forumCommentCounts = $forumIds->isNotEmpty()
            ? ForumComment::whereIn('tid', $forumIds)
                ->selectRaw('tid, COUNT(*) as cnt')
                ->groupBy('tid')
                ->pluck('cnt', 'tid')
            : collect();

        $directoryCommentCounts = $directoryIds->isNotEmpty()
            ? Option::where('o_type', 'd_coment')
                ->whereIn('o_parent', $directoryIds)
                ->selectRaw('o_parent, COUNT(*) as cnt')
                ->groupBy('o_parent')
                ->pluck('cnt', 'o_parent')
            : collect();

        $storeCommentCounts = $storeIds->isNotEmpty()
            ? Option::where('o_type', 's_coment')
                ->whereIn('o_parent', $storeIds)
                ->selectRaw('o_parent, COUNT(*) as cnt')
                ->groupBy('o_parent')
                ->pluck('cnt', 'o_parent')
            : collect();

        $repostCounts = $schema->supports('reposts') && $statusIds->isNotEmpty()
            ? StatusRepost::whereIn('original_status_id', $statusIds)
                ->selectRaw('original_status_id, COUNT(*) as cnt')
                ->groupBy('original_status_id')
                ->pluck('cnt', 'original_status_id')
            : collect();

        $socialProofForumIds = collect();
        $socialProofDirectoryIds = collect();
        if ($followingIds !== []) {
            if ($forumIds->isNotEmpty()) {
                $socialProofForumIds = ForumComment::whereIn('tid', $forumIds)
                    ->whereIn('uid', $followingIds)
                    ->pluck('tid')
                    ->flip();
            }

            if ($directoryIds->isNotEmpty()) {
                $socialProofDirectoryIds = Option::where('o_type', 'd_coment')
                    ->whereIn('o_parent', $directoryIds)
                    ->whereIn('o_order', $followingIds)
                    ->pluck('o_parent')
                    ->flip();
            }
        }

        $authorAffinitySet = array_flip(self::authorAffinityUserIds($userId));
        $contentAffinityTypes = self::contentAffinityTypes($userId);

        $scored = $statuses->map(function (Status $status) use (
            $followingSet,
            $forumTopics,
            $directories,
            $reactionCounts,
            $forumCommentCounts,
            $directoryCommentCounts,
            $storeCommentCounts,
            $repostCounts,
            $socialProofForumIds,
            $socialProofDirectoryIds,
            $authorAffinitySet,
            $contentAffinityTypes
        ) {
            $score = 0.0;
            $ageHours = max(0, (time() - (int) $status->date) / 3600);
            $score += self::RECENCY_BASE / (1 + pow($ageHours, self::RECENCY_DECAY));

            $views = 0;
            if (in_array((int) $status->s_type, [2, 4, 100], true)) {
                $views = (int) optional($forumTopics->get($status->tp_id))->vu;
            } elseif ((int) $status->s_type === 1) {
                $views = (int) optional($directories->get($status->tp_id))->vu;
            }

            $score += min($views * self::WEIGHT_VIEW, self::MAX_VIEWS_SCORE);
            $score += (int) ($reactionCounts->get($status->tp_id) ?? 0) * self::WEIGHT_REACTION;
            $score += self::commentCountForStatus($status, $forumCommentCounts, $directoryCommentCounts, $storeCommentCounts) * self::WEIGHT_COMMENT;
            $score += (int) ($repostCounts->get($status->id) ?? 0) * self::WEIGHT_REPOST;

            if (isset($followingSet[$status->uid])) {
                $score += self::BOOST_FOLLOWING;
            }

            if (isset($authorAffinitySet[$status->uid])) {
                $score += self::BOOST_AUTHOR_AFFINITY;
            }

            $typeGroup = self::typeGroup((int) $status->s_type);
            if (isset($contentAffinityTypes[$typeGroup])) {
                $score += self::BOOST_CONTENT_AFFINITY;
            }

            if (
                (in_array((int) $status->s_type, [2, 4, 100], true) && $socialProofForumIds->has($status->tp_id))
                || ((int) $status->s_type === 1 && $socialProofDirectoryIds->has($status->tp_id))
            ) {
                $score += self::BOOST_SOCIAL_PROOF;
            }

            $status->smart_score = $score;
            return $status;
        });

        return self::diversify($scored);
    }

    private static function diversify(Collection $statuses): Collection
    {
        $remaining = $statuses->sortByDesc('smart_score')->values();
        $ordered = collect();
        $authorCounts = [];
        $typeCounts = [];

        while ($remaining->isNotEmpty()) {
            $bestIndex = 0;
            $bestAdjustedScore = null;

            foreach ($remaining as $index => $status) {
                $authorPenalty = ($authorCounts[$status->uid] ?? 0) * self::REPEAT_AUTHOR_PENALTY;
                $typePenalty = ($typeCounts[(int) $status->s_type] ?? 0) * self::REPEAT_TYPE_PENALTY;
                $adjusted = (float) $status->smart_score - $authorPenalty - $typePenalty;

                if ($bestAdjustedScore === null || $adjusted > $bestAdjustedScore) {
                    $bestAdjustedScore = $adjusted;
                    $bestIndex = $index;
                }
            }

            /** @var \App\Models\Status $picked */
            $picked = $remaining->get($bestIndex);
            $ordered->push($picked);
            $authorCounts[$picked->uid] = ($authorCounts[$picked->uid] ?? 0) + 1;
            $typeCounts[(int) $picked->s_type] = ($typeCounts[(int) $picked->s_type] ?? 0) + 1;
            $remaining->forget($bestIndex);
            $remaining = $remaining->values();
        }

        return $ordered->values();
    }

    private static function commentCountForStatus(
        Status $status,
        Collection $forumCommentCounts,
        Collection $directoryCommentCounts,
        Collection $storeCommentCounts
    ): int {
        return match ((int) $status->s_type) {
            1 => (int) ($directoryCommentCounts->get($status->tp_id) ?? 0),
            7867 => (int) ($storeCommentCounts->get($status->tp_id) ?? 0),
            default => (int) ($forumCommentCounts->get($status->tp_id) ?? 0),
        };
    }

    private static function followingIds(?int $userId): array
    {
        if (!$userId) {
            return [];
        }

        return Like::where('uid', $userId)
            ->where('type', 1)
            ->pluck('sid')
            ->map(static fn ($id) => (int) $id)
            ->all();
    }

    private static function authorAffinityUserIds(?int $userId): array
    {
        if (!$userId) {
            return [];
        }

        $forumTopicIds = Like::where('uid', $userId)->where('type', 2)->pluck('sid');
        $directoryIds = Like::where('uid', $userId)->where('type', 22)->pluck('sid');
        $commentTopicIds = ForumComment::where('uid', $userId)->pluck('tid');

        $forumAuthors = $forumTopicIds->merge($commentTopicIds)->isNotEmpty()
            ? ForumTopic::whereIn('id', $forumTopicIds->merge($commentTopicIds)->unique())->pluck('uid')
            : collect();

        $directoryAuthors = $directoryIds->isNotEmpty()
            ? Directory::whereIn('id', $directoryIds)->pluck('uid')
            : collect();

        return $forumAuthors
            ->merge($directoryAuthors)
            ->unique()
            ->map(static fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private static function contentAffinityTypes(?int $userId): array
    {
        if (!$userId) {
            return [];
        }

        $types = [];

        if (Like::where('uid', $userId)->where('type', 2)->exists() || ForumComment::where('uid', $userId)->exists()) {
            $types['forum'] = true;
        }

        if (Like::where('uid', $userId)->where('type', 22)->exists() || Option::where('o_order', $userId)->where('o_type', 'd_coment')->exists()) {
            $types['directory'] = true;
        }

        if (Like::where('uid', $userId)->where('type', 3)->exists() || Option::where('o_order', $userId)->where('o_type', 's_coment')->exists()) {
            $types['store'] = true;
        }

        return $types;
    }

    private static function typeGroup(int $statusType): string
    {
        return match ($statusType) {
            1 => 'directory',
            7867 => 'store',
            default => 'forum',
        };
    }
}
