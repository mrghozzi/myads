<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Option;
use App\Models\OrderRequest;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use App\Services\KnowledgebaseCommunityService;
use App\Support\CommunityFeedSettings;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FeedService
{
    private const FORUM_STATUS_TYPES = [2, 4, 100];
    private const CACHE_VERSION = 'community-feed-v2';

    public static function getRankedFeed(?int $userId, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        $settings = CommunityFeedSettings::all();
        $ttl = (int) ($settings['cache_ttl_seconds'] ?? 300);
        $cacheKey = 'community_feed:' . self::CACHE_VERSION . ':' . ($userId ?: 'guest') . ':' . CommunityFeedSettings::signature();

        if ($ttl > 0) {
            $cacheKey .= ':' . floor(time() / max(1, $ttl));
            $ranked = Cache::remember($cacheKey, $ttl, function () use ($userId, $settings) {
                return self::buildRankedCollection($userId, $settings);
            });
        } else {
            $ranked = self::buildRankedCollection($userId, $settings);
        }

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

    private static function buildRankedCollection(?int $userId, array $settings): Collection
    {
        $schema = app(V420SchemaService::class);
        $now = time();
        $hiddenDirectoryStatusIds = self::hiddenDirectoryStatusIds($schema);
        $followingIds = self::followingIds($userId);
        $recentActivity = self::recentActivityMaps($now, $settings, $followingIds, $schema);
        $candidateIds = self::candidateStatusIds($now, $settings, $hiddenDirectoryStatusIds, $recentActivity, $schema);

        if ($candidateIds === []) {
            return collect();
        }

        $statusMap = self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
            ->whereIn('id', $candidateIds)
            ->get()
            ->keyBy('id');

        $statuses = collect($candidateIds)
            ->map(static fn (int $id) => $statusMap->get($id))
            ->filter()
            ->values();

        if ($statuses->isEmpty()) {
            return collect();
        }

        $metadata = self::metadataForStatuses($statuses, $schema, $recentActivity, $followingIds, $userId);

        return self::diversify(
            $statuses->map(function (Status $status) use ($metadata, $settings, $now) {
                $status->smart_score = self::scoreStatus($status, $metadata, $settings, $now);
                return $status;
            }),
            $settings
        );
    }

    private static function hiddenDirectoryStatusIds(V420SchemaService $schema): Collection
    {
        return $schema->supports('link_previews')
            ? StatusLinkPreview::query()
                ->whereNotNull('directory_status_id')
                ->pluck('directory_status_id')
                ->filter()
                ->map(static fn ($id) => (int) $id)
                ->values()
            : collect();
    }

    private static function baseVisibleStatusQuery(Collection $hiddenDirectoryStatusIds, int $now)
    {
        return Status::visible()
            ->where('date', '<=', $now)
            ->when(
                $hiddenDirectoryStatusIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds->all())
            );
    }

    private static function candidateStatusIds(
        int $now,
        array $settings,
        Collection $hiddenDirectoryStatusIds,
        array $recentActivity,
        V420SchemaService $schema
    ): array {
        $freshCutoff = $now - self::hoursToSeconds((int) $settings['fresh_candidate_hours']);
        $freshIds = self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
            ->where('date', '>=', $freshCutoff)
            ->orderBy('date', 'desc')
            ->limit((int) $settings['fresh_candidate_limit'])
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();

        $trendIds = self::trendCandidateStatuses($now, $settings, $hiddenDirectoryStatusIds, $recentActivity, $schema)
            ->take((int) $settings['rescue_candidate_limit'])
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();

        $candidateIds = array_values(array_unique(array_merge($freshIds, $trendIds)));
        $fallbackFloor = min(200, max(60, (int) ceil(((int) $settings['fresh_candidate_limit']) / 2)));

        if (count($candidateIds) >= $fallbackFloor) {
            return $candidateIds;
        }

        $fallbackIds = self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
            ->when(
                $candidateIds !== [],
                fn ($query) => $query->whereNotIn('id', $candidateIds)
            )
            ->orderBy('date', 'desc')
            ->limit(max(0, $fallbackFloor - count($candidateIds)))
            ->pluck('id')
            ->map(static fn ($id) => (int) $id)
            ->all();

        return array_values(array_unique(array_merge($candidateIds, $fallbackIds)));
    }

    private static function trendCandidateStatuses(
        int $now,
        array $settings,
        Collection $hiddenDirectoryStatusIds,
        array $recentActivity,
        V420SchemaService $schema
    ): Collection {
        $rescueCutoff = $now - self::hoursToSeconds((int) $settings['rescue_max_age_hours']);
        $candidates = collect();

        $forumIds = self::mergedMapKeys(
            $recentActivity['recent_comments']['forum'] ?? collect(),
            $recentActivity['recent_reactions']['forum'] ?? collect()
        );
        $directoryIds = self::mergedMapKeys(
            $recentActivity['recent_comments']['directory'] ?? collect(),
            $recentActivity['recent_reactions']['directory'] ?? collect()
        );
        $storeIds = self::mergedMapKeys(
            $recentActivity['recent_comments']['store'] ?? collect(),
            $recentActivity['recent_reactions']['store'] ?? collect()
        );
        $knowledgebaseStatusIds = self::mergedMapKeys(
            $recentActivity['recent_comments']['knowledgebase'] ?? collect(),
            $recentActivity['recent_reactions']['knowledgebase'] ?? collect()
        );
        $orderIds = self::mergedMapKeys(
            $recentActivity['recent_comments']['order'] ?? collect(),
            $recentActivity['recent_reactions']['order'] ?? collect()
        );
        $repostStatusIds = ($recentActivity['recent_reposts'] ?? collect())
            ->keys()
            ->map(static fn ($id) => (int) $id)
            ->values();

        if ($forumIds->isNotEmpty()) {
            $candidates = $candidates->merge(
                self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
                    ->where('date', '>=', $rescueCutoff)
                    ->whereIn('s_type', self::FORUM_STATUS_TYPES)
                    ->whereIn('tp_id', $forumIds->all())
                    ->get()
            );
        }

        if ($directoryIds->isNotEmpty()) {
            $candidates = $candidates->merge(
                self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
                    ->where('date', '>=', $rescueCutoff)
                    ->where('s_type', 1)
                    ->whereIn('tp_id', $directoryIds->all())
                    ->get()
            );
        }

        if ($storeIds->isNotEmpty()) {
            $candidates = $candidates->merge(
                self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
                    ->where('date', '>=', $rescueCutoff)
                    ->where('s_type', 7867)
                    ->whereIn('tp_id', $storeIds->all())
                    ->get()
            );
        }

        if ($knowledgebaseStatusIds->isNotEmpty()) {
            $candidates = $candidates->merge(
                self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
                    ->where('date', '>=', $rescueCutoff)
                    ->where('s_type', KnowledgebaseCommunityService::STATUS_TYPE)
                    ->whereIn('id', $knowledgebaseStatusIds->all())
                    ->get()
            );
        }

        if ($orderIds->isNotEmpty()) {
            $candidates = $candidates->merge(
                self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
                    ->where('date', '>=', $rescueCutoff)
                    ->where('s_type', 6)
                    ->whereIn('tp_id', $orderIds->all())
                    ->get()
            );
        }

        if ($schema->supports('reposts') && $repostStatusIds->isNotEmpty()) {
            $candidates = $candidates->merge(
                self::baseVisibleStatusQuery($hiddenDirectoryStatusIds, $now)
                    ->where('date', '>=', $rescueCutoff)
                    ->whereIn('id', $repostStatusIds->all())
                    ->get()
            );
        }

        return $candidates
            ->unique('id')
            ->map(function (Status $status) use ($recentActivity, $settings, $now) {
                $recentReactions = self::metricCountForStatus($status, $recentActivity['recent_reactions']);
                $recentComments = self::metricCountForStatus($status, $recentActivity['recent_comments']);
                $recentReposts = (int) (($recentActivity['recent_reposts'] ?? collect())->get($status->id) ?? 0);
                $ageHours = max(0, ($now - (int) $status->date) / 3600);

                $status->trend_signal = self::isTrendEligible(
                    $recentReactions,
                    $recentComments,
                    $recentReposts,
                    $settings,
                    $ageHours
                )
                    ? self::trendSignalForStatus($status, $recentActivity, $settings)
                    : 0;

                return $status;
            })
            ->filter(static fn (Status $status) => (float) ($status->trend_signal ?? 0) > 0)
            ->sortByDesc('trend_signal')
            ->values();
    }

    private static function metadataForStatuses(
        Collection $statuses,
        V420SchemaService $schema,
        array $recentActivity,
        array $followingIds,
        ?int $userId
    ): array {
        $forumIds = $statuses->whereIn('s_type', self::FORUM_STATUS_TYPES)->pluck('tp_id')->unique()->values();
        $directoryIds = $statuses->where('s_type', 1)->pluck('tp_id')->unique()->values();
        $storeIds = $statuses->where('s_type', 7867)->pluck('tp_id')->unique()->values();
        $knowledgebaseStatusIds = $statuses->where('s_type', KnowledgebaseCommunityService::STATUS_TYPE)->pluck('id')->unique()->values();
        $orderIds = $statuses->where('s_type', 6)->pluck('tp_id')->unique()->values();
        $statusIds = $statuses->pluck('id')->unique()->values();

        return [
            'following_set' => array_flip($followingIds),
            'author_affinity_scores' => self::authorAffinityScores($userId, $schema),
            'content_affinity_scores' => self::contentAffinityScores($userId, $schema),
            'recent_activity' => $recentActivity,
            'forum_topics' => $forumIds->isNotEmpty()
                ? ForumTopic::whereIn('id', $forumIds)->get()->keyBy('id')
                : collect(),
            'directories' => $directoryIds->isNotEmpty()
                ? Directory::whereIn('id', $directoryIds)->get()->keyBy('id')
                : collect(),
            'orders' => $orderIds->isNotEmpty()
                ? OrderRequest::whereIn('id', $orderIds)->get()->keyBy('id')
                : collect(),
            'total_reactions' => [
                'forum' => self::countMapForIds(Like::query()->where('type', 2), 'sid', $forumIds),
                'directory' => self::countMapForIds(Like::query()->where('type', 22), 'sid', $directoryIds),
                'store' => self::countMapForIds(Like::query()->where('type', 3), 'sid', $storeIds),
                'knowledgebase' => self::countMapForIds(Like::query()->where('type', KnowledgebaseCommunityService::REACTION_TYPE), 'sid', $knowledgebaseStatusIds),
                'order' => self::countMapForIds(Like::query()->where('type', 6), 'sid', $orderIds),
            ],
            'total_comments' => [
                'forum' => self::countMapForIds(ForumComment::query(), 'tid', $forumIds),
                'directory' => self::countMapForIds(Option::query()->where('o_type', 'd_coment'), 'o_parent', $directoryIds),
                'store' => self::countMapForIds(Option::query()->where('o_type', 's_coment'), 'o_parent', $storeIds),
                'knowledgebase' => self::countMapForIds(Option::query()->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE), 'o_parent', $knowledgebaseStatusIds),
                'order' => self::countMapForIds(Option::query()->where('o_type', 'order_comment'), 'o_parent', $orderIds),
            ],
            'total_reposts' => $schema->supports('reposts')
                ? self::countMapForIds(StatusRepost::query(), 'original_status_id', $statusIds)
                : collect(),
        ];
    }

    private static function recentActivityMaps(
        int $now,
        array $settings,
        array $followingIds,
        V420SchemaService $schema
    ): array {
        $rapidCutoff = $now - self::hoursToSeconds((int) $settings['rapid_window_hours']);
        $trendCutoff = $now - self::hoursToSeconds((int) $settings['trend_window_hours']);

        $recentComments = [
            'forum' => self::groupedCount(ForumComment::query()->where('date', '>=', $trendCutoff), 'tid'),
            'directory' => self::groupedCount(self::recentOptionQuery('d_coment', $trendCutoff), 'o_parent'),
            'store' => self::groupedCount(self::recentOptionQuery('s_coment', $trendCutoff), 'o_parent'),
            'knowledgebase' => self::groupedCount(self::recentOptionQuery(KnowledgebaseCommunityService::COMMENT_OPTION_TYPE, $trendCutoff), 'o_parent'),
            'order' => self::groupedCount(self::recentOptionQuery('order_comment', $trendCutoff), 'o_parent'),
        ];
        $rapidComments = [
            'forum' => self::groupedCount(ForumComment::query()->where('date', '>=', $rapidCutoff), 'tid'),
            'directory' => self::groupedCount(self::recentOptionQuery('d_coment', $rapidCutoff), 'o_parent'),
            'store' => self::groupedCount(self::recentOptionQuery('s_coment', $rapidCutoff), 'o_parent'),
            'knowledgebase' => self::groupedCount(self::recentOptionQuery(KnowledgebaseCommunityService::COMMENT_OPTION_TYPE, $rapidCutoff), 'o_parent'),
            'order' => self::groupedCount(self::recentOptionQuery('order_comment', $rapidCutoff), 'o_parent'),
        ];

        $recentReactions = [
            'forum' => self::groupedCount(self::recentReactionQuery(2, $trendCutoff), 'sid'),
            'directory' => self::groupedCount(self::recentReactionQuery(22, $trendCutoff), 'sid'),
            'store' => self::groupedCount(self::recentReactionQuery(3, $trendCutoff), 'sid'),
            'knowledgebase' => self::groupedCount(self::recentReactionQuery(KnowledgebaseCommunityService::REACTION_TYPE, $trendCutoff), 'sid'),
            'order' => self::groupedCount(self::recentReactionQuery(6, $trendCutoff), 'sid'),
        ];
        $rapidReactions = [
            'forum' => self::groupedCount(self::recentReactionQuery(2, $rapidCutoff), 'sid'),
            'directory' => self::groupedCount(self::recentReactionQuery(22, $rapidCutoff), 'sid'),
            'store' => self::groupedCount(self::recentReactionQuery(3, $rapidCutoff), 'sid'),
            'knowledgebase' => self::groupedCount(self::recentReactionQuery(KnowledgebaseCommunityService::REACTION_TYPE, $rapidCutoff), 'sid'),
            'order' => self::groupedCount(self::recentReactionQuery(6, $rapidCutoff), 'sid'),
        ];

        $recentReposts = $schema->supports('reposts')
            ? self::groupedCount(
                StatusRepost::query()->where('created_at', '>=', date('Y-m-d H:i:s', $trendCutoff)),
                'original_status_id'
            )
            : collect();
        $rapidReposts = $schema->supports('reposts')
            ? self::groupedCount(
                StatusRepost::query()->where('created_at', '>=', date('Y-m-d H:i:s', $rapidCutoff)),
                'original_status_id'
            )
            : collect();

        $socialProof = [
            'forum' => collect(),
            'directory' => collect(),
            'store' => collect(),
            'knowledgebase' => collect(),
            'order' => collect(),
            'status' => collect(),
        ];

        if ($followingIds !== []) {
            $socialProof['forum'] = self::idSet(
                ForumComment::query()
                    ->where('date', '>=', $trendCutoff)
                    ->whereIn('uid', $followingIds)
                    ->pluck('tid')
                    ->merge(
                        Like::query()
                            ->where('type', 2)
                            ->where('time_t', '>=', $trendCutoff)
                            ->whereIn('uid', $followingIds)
                            ->pluck('sid')
                    )
            );
            $socialProof['directory'] = self::idSet(
                self::recentOptionQuery('d_coment', $trendCutoff)
                    ->whereIn('o_order', $followingIds)
                    ->pluck('o_parent')
                    ->merge(
                        Like::query()
                            ->where('type', 22)
                            ->where('time_t', '>=', $trendCutoff)
                            ->whereIn('uid', $followingIds)
                            ->pluck('sid')
                    )
            );
            $socialProof['store'] = self::idSet(
                self::recentOptionQuery('s_coment', $trendCutoff)
                    ->whereIn('o_order', $followingIds)
                    ->pluck('o_parent')
                    ->merge(
                        Like::query()
                            ->where('type', 3)
                            ->where('time_t', '>=', $trendCutoff)
                            ->whereIn('uid', $followingIds)
                            ->pluck('sid')
                    )
            );
            $socialProof['knowledgebase'] = self::idSet(
                self::recentOptionQuery(KnowledgebaseCommunityService::COMMENT_OPTION_TYPE, $trendCutoff)
                    ->whereIn('o_order', $followingIds)
                    ->pluck('o_parent')
                    ->merge(
                        Like::query()
                            ->where('type', KnowledgebaseCommunityService::REACTION_TYPE)
                            ->where('time_t', '>=', $trendCutoff)
                            ->whereIn('uid', $followingIds)
                            ->pluck('sid')
                    )
            );
            $socialProof['order'] = self::idSet(
                self::recentOptionQuery('order_comment', $trendCutoff)
                    ->whereIn('o_order', $followingIds)
                    ->pluck('o_parent')
                    ->merge(
                        Like::query()
                            ->where('type', 6)
                            ->where('time_t', '>=', $trendCutoff)
                            ->whereIn('uid', $followingIds)
                            ->pluck('sid')
                    )
            );

            if ($schema->supports('reposts')) {
                $socialProof['status'] = self::idSet(
                    StatusRepost::query()
                        ->where('created_at', '>=', date('Y-m-d H:i:s', $trendCutoff))
                        ->whereIn('user_id', $followingIds)
                        ->pluck('original_status_id')
                );
            }
        }

        return [
            'recent_comments' => $recentComments,
            'rapid_comments' => $rapidComments,
            'recent_reactions' => $recentReactions,
            'rapid_reactions' => $rapidReactions,
            'recent_reposts' => $recentReposts,
            'rapid_reposts' => $rapidReposts,
            'social_proof' => $socialProof,
        ];
    }

    private static function scoreStatus(Status $status, array $metadata, array $settings, int $now): float
    {
        $ageHours = max(0, ($now - (int) $status->date) / 3600);
        $typeGroup = self::typeGroup((int) $status->s_type);
        $freshnessScore = (float) $settings['freshness_base_score']
            / (1 + pow($ageHours, (float) $settings['freshness_decay_exponent']));

        $totalViews = self::viewCountForStatus($status, $metadata['forum_topics'], $metadata['directories']);
        $totalReactions = self::metricCountForStatus($status, $metadata['total_reactions']);
        $totalComments = self::metricCountForStatus($status, $metadata['total_comments']);
        $totalReposts = (int) ($metadata['total_reposts']->get($status->id) ?? 0);

        $recentReactions = self::metricCountForStatus($status, $metadata['recent_activity']['recent_reactions']);
        $recentComments = self::metricCountForStatus($status, $metadata['recent_activity']['recent_comments']);
        $recentReposts = (int) (($metadata['recent_activity']['recent_reposts'] ?? collect())->get($status->id) ?? 0);
        $rapidReactions = self::metricCountForStatus($status, $metadata['recent_activity']['rapid_reactions']);
        $rapidComments = self::metricCountForStatus($status, $metadata['recent_activity']['rapid_comments']);
        $rapidReposts = (int) (($metadata['recent_activity']['rapid_reposts'] ?? collect())->get($status->id) ?? 0);

        $trendEligible = self::isTrendEligible($recentReactions, $recentComments, $recentReposts, $settings, $ageHours);
        $historicalMultiplier = 1.0;
        $personalizationMultiplier = 1.0;

        if ($ageHours > (int) $settings['freshness_suppression_after_hours']) {
            $freshnessScore *= $trendEligible
                ? max((float) $settings['freshness_suppression_multiplier'], 0.35)
                : (float) $settings['freshness_suppression_multiplier'];

            $historicalMultiplier = $trendEligible
                ? max((float) $settings['freshness_suppression_multiplier'], 0.45)
                : (float) $settings['freshness_suppression_multiplier'];
            $personalizationMultiplier = $trendEligible
                ? 1.0
                : max((float) $settings['freshness_suppression_multiplier'], 0.35);
        }

        $score = $freshnessScore;
        $score += min($totalViews * (float) $settings['view_weight'], (float) $settings['max_views_score']) * $historicalMultiplier;
        $score += $totalReactions * (float) $settings['total_reaction_weight'] * $historicalMultiplier;
        $score += $totalComments * (float) $settings['total_comment_weight'] * $historicalMultiplier;
        $score += $totalReposts * (float) $settings['total_repost_weight'] * $historicalMultiplier;
        $score += $recentReactions * (float) $settings['recent_reaction_weight'];
        $score += $recentComments * (float) $settings['recent_comment_weight'];
        $score += $recentReposts * (float) $settings['recent_repost_weight'];
        $score += $rapidReactions * (float) $settings['rapid_reaction_weight'];
        $score += $rapidComments * (float) $settings['rapid_comment_weight'];
        $score += $rapidReposts * (float) $settings['rapid_repost_weight'];

        if (isset($metadata['following_set'][$status->uid])) {
            $score += (float) $settings['following_boost'] * $personalizationMultiplier;
        }

        $authorAffinity = (float) ($metadata['author_affinity_scores'][$status->uid] ?? 0);
        if ($authorAffinity > 0) {
            $score += min(1.75, sqrt($authorAffinity)) * (float) $settings['author_affinity_boost'] * $personalizationMultiplier;
        }

        $contentAffinity = (float) ($metadata['content_affinity_scores'][$typeGroup] ?? 0);
        if ($contentAffinity > 0) {
            $score += min(1.75, sqrt($contentAffinity)) * (float) $settings['content_affinity_boost'] * $personalizationMultiplier;
        }

        if (self::hasSocialProof($status, $metadata['recent_activity']['social_proof'])) {
            $score += (float) $settings['social_proof_boost'] * $personalizationMultiplier;
        }

        if ($trendEligible) {
            $score += self::trendSignalForStatus($status, $metadata['recent_activity'], $settings) * 0.12;
        }

        return round($score, 4);
    }

    private static function isTrendEligible(
        int $recentReactions,
        int $recentComments,
        int $recentReposts,
        array $settings,
        float $ageHours
    ): bool {
        return $ageHours <= (int) $settings['rescue_max_age_hours']
            && (
                $recentReactions >= (int) $settings['rescue_min_recent_reactions']
                || $recentComments >= (int) $settings['rescue_min_recent_comments']
                || $recentReposts >= (int) $settings['rescue_min_recent_reposts']
            );
    }

    private static function trendSignalForStatus(Status $status, array $recentActivity, array $settings): float
    {
        $recentReactions = self::metricCountForStatus($status, $recentActivity['recent_reactions']);
        $recentComments = self::metricCountForStatus($status, $recentActivity['recent_comments']);
        $recentReposts = (int) (($recentActivity['recent_reposts'] ?? collect())->get($status->id) ?? 0);
        $rapidReactions = self::metricCountForStatus($status, $recentActivity['rapid_reactions']);
        $rapidComments = self::metricCountForStatus($status, $recentActivity['rapid_comments']);
        $rapidReposts = (int) (($recentActivity['rapid_reposts'] ?? collect())->get($status->id) ?? 0);

        return ($recentReactions * (float) $settings['recent_reaction_weight'])
            + ($recentComments * (float) $settings['recent_comment_weight'])
            + ($recentReposts * (float) $settings['recent_repost_weight'])
            + ($rapidReactions * (float) $settings['rapid_reaction_weight'])
            + ($rapidComments * (float) $settings['rapid_comment_weight'])
            + ($rapidReposts * (float) $settings['rapid_repost_weight']);
    }

    private static function diversify(Collection $statuses, array $settings): Collection
    {
        $remaining = $statuses->sortByDesc('smart_score')->values();
        $ordered = collect();
        $authorCounts = [];
        $typeCounts = [];

        while ($remaining->isNotEmpty()) {
            $bestIndex = 0;
            $bestAdjustedScore = null;

            foreach ($remaining as $index => $status) {
                $authorPenalty = ($authorCounts[$status->uid] ?? 0) * (float) $settings['repeat_author_penalty'];
                $typePenalty = ($typeCounts[(int) $status->s_type] ?? 0) * (float) $settings['repeat_type_penalty'];
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

    private static function authorAffinityScores(?int $userId, V420SchemaService $schema): array
    {
        if (!$userId) {
            return [];
        }

        $forumTopicIds = Like::where('uid', $userId)->where('type', 2)->pluck('sid')
            ->merge(ForumComment::where('uid', $userId)->pluck('tid'))
            ->unique()
            ->values();
        $directoryIds = Like::where('uid', $userId)->where('type', 22)->pluck('sid')
            ->merge(Option::where('o_order', $userId)->where('o_type', 'd_coment')->pluck('o_parent'))
            ->unique()
            ->values();
        $storeIds = Like::where('uid', $userId)->where('type', 3)->pluck('sid')
            ->merge(Option::where('o_order', $userId)->where('o_type', 's_coment')->pluck('o_parent'))
            ->unique()
            ->values();
        $knowledgebaseStatusIds = Like::where('uid', $userId)->where('type', KnowledgebaseCommunityService::REACTION_TYPE)->pluck('sid')
            ->merge(Option::where('o_order', $userId)->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)->pluck('o_parent'))
            ->unique()
            ->values();
        $orderIds = Like::where('uid', $userId)->where('type', 6)->pluck('sid')
            ->merge(Option::where('o_order', $userId)->where('o_type', 'order_comment')->pluck('o_parent'))
            ->unique()
            ->values();
        $repostedStatusIds = $schema->supports('reposts')
            ? StatusRepost::query()->where('user_id', $userId)->pluck('original_status_id')->unique()->values()
            : collect();

        return ForumTopic::whereIn('id', $forumTopicIds)->pluck('uid')
            ->merge(Directory::whereIn('id', $directoryIds)->pluck('uid'))
            ->merge(Option::where('o_type', 'store')->whereIn('id', $storeIds)->pluck('o_parent'))
            ->merge(Status::whereIn('id', $knowledgebaseStatusIds)->pluck('uid'))
            ->merge(OrderRequest::whereIn('id', $orderIds)->pluck('uid'))
            ->merge(Status::whereIn('id', $repostedStatusIds)->pluck('uid'))
            ->map(static fn ($id) => (int) $id)
            ->filter(static fn ($id) => $id > 0 && $id !== $userId)
            ->countBy()
            ->all();
    }

    private static function contentAffinityScores(?int $userId, V420SchemaService $schema): array
    {
        if (!$userId) {
            return [];
        }

        return collect([
            'forum' => Like::where('uid', $userId)->where('type', 2)->count()
                + ForumComment::where('uid', $userId)->count()
                + ($schema->supports('reposts') ? StatusRepost::query()->where('user_id', $userId)->count() : 0),
            'directory' => Like::where('uid', $userId)->where('type', 22)->count()
                + Option::where('o_order', $userId)->where('o_type', 'd_coment')->count(),
            'store' => Like::where('uid', $userId)->where('type', 3)->count()
                + Option::where('o_order', $userId)->where('o_type', 's_coment')->count(),
            'knowledgebase' => Like::where('uid', $userId)->where('type', KnowledgebaseCommunityService::REACTION_TYPE)->count()
                + Option::where('o_order', $userId)->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)->count(),
            'order' => Like::where('uid', $userId)->where('type', 6)->count()
                + Option::where('o_order', $userId)->where('o_type', 'order_comment')->count(),
        ])->filter(static fn ($count) => (int) $count > 0)->all();
    }

    private static function typeGroup(int $statusType): string
    {
        return match ($statusType) {
            1 => 'directory',
            7867 => 'store',
            6 => 'order',
            5 => 'news',
            KnowledgebaseCommunityService::STATUS_TYPE => 'knowledgebase',
            default => 'forum',
        };
    }

    private static function viewCountForStatus(Status $status, Collection $forumTopics, Collection $directories): int
    {
        return match (self::typeGroup((int) $status->s_type)) {
            'forum' => (int) optional($forumTopics->get($status->tp_id))->vu,
            'directory' => (int) optional($directories->get($status->tp_id))->vu,
            default => 0,
        };
    }

    private static function metricCountForStatus(Status $status, array $metricMap): int
    {
        $group = self::typeGroup((int) $status->s_type);

        return (int) (($metricMap[$group] ?? collect())->get(self::metricSubjectIdForStatus($status)) ?? 0);
    }

    private static function hasSocialProof(Status $status, array $socialProof): bool
    {
        $group = self::typeGroup((int) $status->s_type);

        return (bool) (($socialProof['status'] ?? collect())->has($status->id)
            || ($socialProof[$group] ?? collect())->has(self::metricSubjectIdForStatus($status)));
    }

    private static function metricSubjectIdForStatus(Status $status): int
    {
        if ((int) $status->s_type === KnowledgebaseCommunityService::STATUS_TYPE) {
            return (int) $status->id;
        }

        return (int) $status->tp_id;
    }

    private static function groupedCount($query, string $column): Collection
    {
        return $query
            ->selectRaw($column . ', COUNT(*) as cnt')
            ->groupBy($column)
            ->pluck('cnt', $column)
            ->mapWithKeys(static fn ($count, $id) => [(int) $id => (int) $count]);
    }

    private static function countMapForIds($query, string $column, Collection $ids): Collection
    {
        if ($ids->isEmpty()) {
            return collect();
        }

        return self::groupedCount($query->whereIn($column, $ids->all()), $column);
    }

    private static function recentOptionQuery(string $type, int $cutoff)
    {
        return Option::query()
            ->where('o_type', $type)
            ->where('o_mode', '>', 0)
            ->where('o_mode', '>=', $cutoff);
    }

    private static function recentReactionQuery(int $type, int $cutoff)
    {
        return Like::query()
            ->where('type', $type)
            ->where('time_t', '>=', $cutoff);
    }

    private static function idSet(Collection $ids): Collection
    {
        return $ids
            ->map(static fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->flip();
    }

    private static function mergedMapKeys(Collection $first, Collection $second): Collection
    {
        return collect(array_keys($first->all() + $second->all()))
            ->map(static fn ($id) => (int) $id)
            ->values();
    }

    private static function hoursToSeconds(int $hours): int
    {
        return max(1, $hours) * 3600;
    }
}
