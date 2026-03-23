<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Option;
use App\Models\Product;
use App\Models\Status;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FeedService
{
    // ─── Scoring weights ───────────────────────────────────────────
    const RECENCY_BASE         = 5000;  // higher = more weight on new posts
    const RECENCY_DECAY        = 1.5;   // exponent: higher = faster decay with age
    const WEIGHT_VIEW          = 0.3;   // per view (capped)
    const WEIGHT_REACTION      = 2.0;   // per reaction/like
    const WEIGHT_COMMENT       = 1.5;   // per comment
    const WEIGHT_RECENT_CMT    = 1.5;   // per comment in last 24 h
    const MAX_VIEWS_SCORE      = 50;    // cap on view score
    const BOOST_FOLLOWING      = 20.0;  // author is followed by viewer
    const BOOST_SOCIAL_PROOF   = 10.0;  // a followed user commented
    const CANDIDATE_POOL       = 300;   // rows fetched before scoring
    const CACHE_TTL            = 300;   // seconds (5 min)

    /**
     * Return a paginated, smart-ranked list of Status records.
     *
     * @param  int|null  $userId   ID of the authenticated user (null = guest)
     * @param  int       $page     Current page number
     * @param  int       $perPage  Items per page
     * @return LengthAwarePaginator
     */
    public static function getRankedFeed(?int $userId, int $page = 1, int $perPage = 20): LengthAwarePaginator
    {
        $cacheKey = "smart_feed:{$userId}:" . floor(time() / self::CACHE_TTL);

        // Fetch & score from cache to avoid re-computing on every page flip
        $ranked = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            return self::buildRankedCollection($userId);
        });

        // Manual pagination over in-memory collection
        $total   = $ranked->count();
        $items   = $ranked->forPage($page, $perPage)->values();

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path'  => request()->url(),
            'query' => request()->query(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────

    private static function buildRankedCollection(?int $userId): Collection
    {
        // 1. Fetch candidate pool (most recent N posts)
        $statuses = Status::where('date', '<', time())
            ->orderBy('date', 'desc')
            ->limit(self::CANDIDATE_POOL)
            ->get();

        if ($statuses->isEmpty()) {
            return collect();
        }

        // 2. Pre-load all data needed for scoring in bulk (avoids N+1 queries)
        $followingIds   = self::getFollowingIds($userId);         // users the viewer follows
        $followingSet   = array_flip($followingIds);              // O(1) lookup

        // Gather tp_ids by type for bulk queries
        $forumIds = $statuses->whereIn('s_type', [2, 4, 100])->pluck('tp_id')->unique()->values();
        $dirIds   = $statuses->where('s_type', 1)->pluck('tp_id')->unique()->values();
        $shopIds  = $statuses->where('s_type', 7867)->pluck('tp_id')->unique()->values();
        $newsIds  = $statuses->where('s_type', 5)->pluck('tp_id')->unique()->values();

        // --- Forum topics (views + replies) ---
        $forumTopics = $forumIds->isNotEmpty()
            ? ForumTopic::whereIn('id', $forumIds)->get()->keyBy('id')
            : collect();

        // --- Directory listings (views) ---
        $directories = $dirIds->isNotEmpty()
            ? Directory::whereIn('id', $dirIds)->get()->keyBy('id')
            : collect();

        // --- Recent forum comments (last 24 h) ---
        $recentCutoff       = time() - 86400;
        $recentForumCmts    = $forumIds->isNotEmpty()
            ? ForumComment::whereIn('tid', $forumIds)
                ->where('date', '>=', $recentCutoff)
                ->select('tid', 'uid', 'date')
                ->get()
                ->groupBy('tid')
            : collect();

        // --- All forum comments count ---
        $allForumCmtCounts = $forumIds->isNotEmpty()
            ? ForumComment::whereIn('tid', $forumIds)
                ->selectRaw('tid, COUNT(*) as cnt')
                ->groupBy('tid')
                ->pluck('cnt', 'tid')
            : collect();

        // --- Directory comment counts ---
        $dirCmtCounts = $dirIds->isNotEmpty()
            ? Option::whereIn('o_parent', $dirIds)
                ->where('o_type', 'd_coment')
                ->selectRaw('o_parent, COUNT(*) as cnt')
                ->groupBy('o_parent')
                ->pluck('cnt', 'o_parent')
            : collect();

        // --- Shop comment counts ---
        $shopCmtCounts = $shopIds->isNotEmpty()
            ? Option::whereIn('o_parent', $shopIds)
                ->where('o_type', 's_coment')
                ->selectRaw('o_parent, COUNT(*) as cnt')
                ->groupBy('o_parent')
                ->pluck('cnt', 'o_parent')
            : collect();

        // --- Reactions (likes) per tp_id by type ---
        // type 22 = dir like, 2 = forum like, 3 = shop like
        $reactionData = collect();
        if ($forumIds->isNotEmpty()) {
            $reactionData = $reactionData->concat(
                Like::whereIn('sid', $forumIds)->where('type', 2)
                    ->selectRaw('sid, COUNT(*) as cnt')->groupBy('sid')->get()
            );
        }
        if ($dirIds->isNotEmpty()) {
            $reactionData = $reactionData->concat(
                Like::whereIn('sid', $dirIds)->where('type', 22)
                    ->selectRaw('sid, COUNT(*) as cnt')->groupBy('sid')->get()
            );
        }
        if ($shopIds->isNotEmpty()) {
            $reactionData = $reactionData->concat(
                Like::whereIn('sid', $shopIds)->where('type', 3)
                    ->selectRaw('sid, COUNT(*) as cnt')->groupBy('sid')->get()
            );
        }
        $reactionCounts = $reactionData->pluck('cnt', 'sid');

        // --- Social proof: commenters that the viewer follows ---
        // Map: forum tp_id => bool (has a followed commenter)
        $socialProofTopics = collect();
        if ($userId && $followingIds && $forumIds->isNotEmpty()) {
            $socialProofTopics = ForumComment::whereIn('tid', $forumIds)
                ->whereIn('uid', $followingIds)
                ->pluck('tid')
                ->flip(); // use as set for fast lookup
        }

        // 3. Score each status
        $scored = $statuses->map(function (Status $s) use (
            $followingSet,
            $forumTopics,
            $directories,
            $recentForumCmts,
            $allForumCmtCounts,
            $dirCmtCounts,
            $shopCmtCounts,
            $reactionCounts,
            $socialProofTopics
        ) {
            $score = self::computeScore(
                $s,
                $followingSet,
                $forumTopics,
                $directories,
                $recentForumCmts,
                $allForumCmtCounts,
                $dirCmtCounts,
                $shopCmtCounts,
                $reactionCounts,
                $socialProofTopics
            );

            $s->smart_score = $score;
            return $s;
        });

        // 4. Sort by score descending
        return $scored->sortByDesc('smart_score')->values();
    }

    /**
     * Compute a numeric score for a single Status record.
     */
    private static function computeScore(
        Status $s,
        array $followingSet,
        Collection $forumTopics,
        Collection $directories,
        Collection $recentForumCmts,
        Collection $allForumCmtCounts,
        Collection $dirCmtCounts,
        Collection $shopCmtCounts,
        Collection $reactionCounts,
        Collection $socialProofTopics
    ): float {
        $score = 0.0;

        // ── Recency: score decays with age ──────────────────────────
        $ageHours = max(0, (time() - $s->date) / 3600);
        $score   += self::RECENCY_BASE / (1 + pow($ageHours, self::RECENCY_DECAY));

        // ── Type-specific signals ──────────────────────────────────
        $isDir   = $s->s_type == 1;
        $isForum = in_array($s->s_type, [2, 4, 100]);
        $isShop  = $s->s_type == 7867;
        $isNews  = $s->s_type == 5;

        // Views
        if ($isForum) {
            $topic  = $forumTopics->get($s->tp_id);
            $views  = $topic ? (int) $topic->vu : 0;
            $score += min($views * self::WEIGHT_VIEW, self::MAX_VIEWS_SCORE);
        } elseif ($isDir) {
            $dir   = $directories->get($s->tp_id);
            $views = $dir ? (int) ($dir->vu ?? 0) : 0;
            $score += min($views * self::WEIGHT_VIEW, self::MAX_VIEWS_SCORE);
        }

        // Reactions
        $reactions = (int) ($reactionCounts->get($s->tp_id) ?? 0);
        $score    += $reactions * self::WEIGHT_REACTION;

        // Comments
        if ($isForum) {
            $cmtCount = (int) ($allForumCmtCounts->get($s->tp_id) ?? 0);
            $score   += $cmtCount * self::WEIGHT_COMMENT;

            // Recent comments boost
            $recentCmts = $recentForumCmts->get($s->tp_id, collect());
            $score     += $recentCmts->count() * self::WEIGHT_RECENT_CMT;

            // Social proof (a followed user commented)
            if ($socialProofTopics->has($s->tp_id)) {
                $score += self::BOOST_SOCIAL_PROOF;
            }
        } elseif ($isDir) {
            $cmtCount = (int) ($dirCmtCounts->get($s->tp_id) ?? 0);
            $score   += $cmtCount * self::WEIGHT_COMMENT;
        } elseif ($isShop) {
            $cmtCount = (int) ($shopCmtCounts->get($s->tp_id) ?? 0);
            $score   += $cmtCount * self::WEIGHT_COMMENT;
        }

        // ── Following boost ────────────────────────────────────────
        if (isset($followingSet[$s->uid])) {
            $score += self::BOOST_FOLLOWING;
        }

        return $score;
    }

    /**
     * Return list of user IDs that $userId follows.
     * Returns [] for guests.
     */
    private static function getFollowingIds(?int $userId): array
    {
        if (!$userId) {
            return [];
        }

        return Like::where('uid', $userId)
            ->where('type', 1) // type 1 = follow user
            ->pluck('sid')
            ->toArray();
    }
}
