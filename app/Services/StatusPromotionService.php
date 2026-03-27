<?php

namespace App\Services;

use App\Models\Status;
use App\Models\StatusPromotion;
use App\Models\User;
use App\Support\StatusPromotionSettings;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StatusPromotionService
{
    public const PROMOTABLE_STATUS_TYPES = [1, 2, 4, 100, 7867, 6];

    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly StatusPromotionPricingService $pricingService,
        private readonly PointLedgerService $pointLedgerService,
        private readonly NotificationService $notificationService,
        private readonly StatusActivityService $statusActivityService,
    ) {
    }

    public function featureAvailable(): bool
    {
        return $this->schema->supports('post_promotions');
    }

    public function settingsEnabled(): bool
    {
        return (bool) StatusPromotionSettings::get('enabled', 1);
    }

    public function ensureFeatureAvailable(): void
    {
        if (!$this->featureAvailable()) {
            throw ValidationException::withMessages([
                'promotion' => $this->schema->blockedActionMessage('post_promotions', __('messages.status_promotions_title')),
            ]);
        }
    }

    public function isPromotableStatus(Status $status): bool
    {
        return in_array((int) $status->s_type, self::PROMOTABLE_STATUS_TYPES, true);
    }

    public function ensurePromotableStatus(Status $status, User $user): Status
    {
        $this->ensureFeatureAvailable();

        if (!$this->settingsEnabled()) {
            throw ValidationException::withMessages([
                'promotion' => __('messages.status_promotion_disabled'),
            ]);
        }

        if ((int) $status->uid !== (int) $user->id) {
            abort(403);
        }

        if (!$this->isPromotableStatus($status)) {
            throw ValidationException::withMessages([
                'promotion' => __('messages.status_promotion_not_supported'),
            ]);
        }

        $this->statusActivityService->decorate($status);

        if (!$this->isStatusFeedEligible($status)) {
            throw ValidationException::withMessages([
                'promotion' => __('messages.status_promotion_not_visible'),
            ]);
        }

        $existing = $this->ongoingPromotionForStatus($status);
        if ($existing) {
            throw ValidationException::withMessages([
                'promotion' => __('messages.status_promotion_existing_active'),
            ]);
        }

        return $status;
    }

    public function quoteForStatus(Status $status, User $user, string $objective, int $targetQuantity): array
    {
        $this->ensurePromotableStatus($status, $user);

        return $this->pricingService->quote($status, $objective, $targetQuantity);
    }

    public function createPromotion(Status $status, User $user, string $objective, int $targetQuantity): StatusPromotion
    {
        $status = $this->ensurePromotableStatus($status, $user);
        $quote = $this->pricingService->quote($status, $objective, $targetQuantity);
        $mirrorLegacy = !$this->schema->supports('point_history');

        return DB::transaction(function () use ($status, $user, $quote, $mirrorLegacy): StatusPromotion {
            $account = User::query()->lockForUpdate()->findOrFail($user->id);

            if ((float) $account->pts < (float) $quote['charged_pts']) {
                throw ValidationException::withMessages([
                    'target_quantity' => __('messages.status_promotion_insufficient_pts'),
                ]);
            }

            $promotion = StatusPromotion::create([
                'status_id' => $status->id,
                'user_id' => $account->id,
                'objective' => $quote['objective'],
                'target_quantity' => $quote['target_quantity'],
                'charged_pts' => $quote['charged_pts'],
                'smart_factor' => $quote['smart_factor'],
                'delivery_cap_impressions' => $quote['delivery_cap_impressions'],
                'delivered_impressions' => 0,
                'baseline_comments' => (int) $status->comments_count,
                'baseline_reactions' => (int) $status->reactions_count,
                'status' => StatusPromotion::STATUS_ACTIVE,
                'starts_at' => $quote['starts_at'],
                'ends_at' => $quote['ends_at'],
                'meta' => [
                    'estimated_duration_days' => $quote['estimated_duration_days'],
                    'no_refund_on_budget_capped' => true,
                ],
            ]);

            $this->pointLedgerService->award(
                $account,
                -1 * (float) $quote['charged_pts'],
                'status_promotion_purchase',
                'post_promotion_purchase',
                'status_promotion',
                $promotion->id,
                [
                    'status_id' => $status->id,
                    'objective' => $quote['objective'],
                    'target_quantity' => $quote['target_quantity'],
                ],
                $mirrorLegacy
            );

            return $promotion->load(['promotedStatus.user', 'user']);
        });
    }

    public function ongoingPromotionForStatus(Status $status): ?StatusPromotion
    {
        if (!$this->featureAvailable()) {
            return null;
        }

        try {
            return StatusPromotion::query()
                ->where('status_id', $status->id)
                ->ongoing()
                ->latest('id')
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }

    public function memberPromotions(User $user, int $perPage = 20): LengthAwarePaginator
    {
        $paginator = $this->memberPromotionsQuery($user)->paginate($perPage);
        $this->hydratePromotions($paginator->items());

        return $paginator;
    }

    public function memberPromotionsQuery(User $user): Builder
    {
        return StatusPromotion::query()
            ->with(['promotedStatus.user', 'user'])
            ->where('user_id', $user->id)
            ->orderByDesc('id');
    }

    public function adminPromotions(?string $search, ?string $status, ?string $objective, int $perPage = 20): LengthAwarePaginator
    {
        $query = StatusPromotion::query()
            ->with(['promotedStatus.user', 'user'])
            ->orderByDesc('id');

        if ($status && in_array($status, [
            StatusPromotion::STATUS_ACTIVE,
            StatusPromotion::STATUS_COMPLETED,
            StatusPromotion::STATUS_EXPIRED,
            StatusPromotion::STATUS_PAUSED,
            StatusPromotion::STATUS_BUDGET_CAPPED,
        ], true)) {
            $query->where('status', $status);
        }

        if ($objective && in_array($objective, [
            StatusPromotion::OBJECTIVE_VIEWS,
            StatusPromotion::OBJECTIVE_COMMENTS,
            StatusPromotion::OBJECTIVE_REACTIONS,
            StatusPromotion::OBJECTIVE_DAYS,
        ], true)) {
            $query->where('objective', $objective);
        }

        if ($search) {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->whereHas('user', function (Builder $userQuery) use ($search): void {
                    $userQuery->where('username', 'like', '%' . $search . '%');
                })->orWhereHas('promotedStatus', function (Builder $statusQuery) use ($search): void {
                    $statusQuery->where('txt', 'like', '%' . $search . '%');
                });

                if (is_numeric($search)) {
                    $builder->orWhere('id', (int) $search)
                        ->orWhere('status_id', (int) $search)
                        ->orWhere('user_id', (int) $search);
                }
            });
        }

        $paginator = $query->paginate($perPage)->appends([
            'search' => $search,
            'status' => $status,
            'objective' => $objective,
        ]);

        $this->hydratePromotions($paginator->items());

        return $paginator;
    }

    public function hydratePromotions(iterable $promotions): void
    {
        foreach ($promotions as $promotion) {
            if (!$promotion instanceof StatusPromotion) {
                continue;
            }

            $this->syncPromotion($promotion);

            if ($promotion->promotedStatus) {
                $this->statusActivityService->decorate($promotion->promotedStatus);
            }
        }
    }

    public function syncPromotion(StatusPromotion $promotion): StatusPromotion
    {
        if (!$this->featureAvailable()) {
            return $promotion;
        }

        if ($promotion->isFinal() || $promotion->status === StatusPromotion::STATUS_PAUSED) {
            return $promotion;
        }

        $status = $promotion->relationLoaded('promotedStatus')
            ? $promotion->getRelation('promotedStatus')
            : $promotion->promotedStatus()->first();

        if (!$status) {
            return $this->transitionPromotion($promotion, StatusPromotion::STATUS_EXPIRED);
        }

        $now = Carbon::now();
        $progress = $promotion->currentProgressValue($status);

        if ($progress >= (int) $promotion->target_quantity) {
            return $this->transitionPromotion($promotion, StatusPromotion::STATUS_COMPLETED);
        }

        if (
            in_array($promotion->objective, [StatusPromotion::OBJECTIVE_COMMENTS, StatusPromotion::OBJECTIVE_REACTIONS], true)
            && (int) $promotion->delivered_impressions >= (int) $promotion->delivery_cap_impressions
        ) {
            return $this->transitionPromotion($promotion, StatusPromotion::STATUS_BUDGET_CAPPED);
        }

        if ($promotion->objective === StatusPromotion::OBJECTIVE_DAYS && $promotion->ends_at && $now->gte($promotion->ends_at)) {
            return $this->transitionPromotion($promotion, StatusPromotion::STATUS_COMPLETED);
        }

        if ($promotion->ends_at && $now->gt($promotion->ends_at)) {
            return $this->transitionPromotion($promotion, StatusPromotion::STATUS_EXPIRED);
        }

        return $promotion;
    }

    public function applyAdminAction(int $promotionId, string $action): ?StatusPromotion
    {
        if (!$this->featureAvailable()) {
            return null;
        }

        $promotion = StatusPromotion::query()->with(['promotedStatus.user', 'user'])->find($promotionId);
        if (!$promotion) {
            return null;
        }

        return match ($action) {
            'pause' => $this->transitionPromotion($promotion, StatusPromotion::STATUS_PAUSED),
            'resume' => $this->transitionPromotion($promotion, StatusPromotion::STATUS_ACTIVE, false),
            'complete' => $this->transitionPromotion($promotion, StatusPromotion::STATUS_COMPLETED),
            default => $promotion,
        };
    }

    public function injectIntoFeed(Collection $organicItems, ?int $viewerUserId, int $page, int $perPage): Collection
    {
        if (
            !$this->featureAvailable()
            || !$this->settingsEnabled()
            || $page < 1
            || $perPage < 1
            || $organicItems->isEmpty()
        ) {
            return $organicItems;
        }

        $maxPerPage = min(
            max(0, (int) StatusPromotionSettings::get('per_page_limit', 2)),
            intdiv($organicItems->count(), 10)
        );

        if ($maxPerPage < 1) {
            return $organicItems;
        }

        $organicIds = $organicItems->pluck('id')->filter()->map(static fn ($id) => (int) $id)->all();
        $viewerFingerprint = $this->viewerFingerprint($viewerUserId);

        try {
            $candidates = StatusPromotion::query()
                ->with(['promotedStatus.user', 'user'])
                ->where('status', StatusPromotion::STATUS_ACTIVE)
                ->when(!empty($organicIds), fn (Builder $builder) => $builder->whereNotIn('status_id', $organicIds))
                ->get();
        } catch (\Throwable) {
            return $organicItems;
        }

        $activeCandidates = $candidates
            ->map(function (StatusPromotion $promotion) {
                $this->syncPromotion($promotion);

                if ($promotion->status !== StatusPromotion::STATUS_ACTIVE || !$promotion->promotedStatus) {
                    return null;
                }

                $this->statusActivityService->decorate($promotion->promotedStatus);

                return $this->isStatusFeedEligible($promotion->promotedStatus) ? $promotion : null;
            })
            ->filter()
            ->filter(fn (StatusPromotion $promotion) => !$this->viewerCooldownApplies($promotion, $viewerFingerprint))
            ->sort(function (StatusPromotion $left, StatusPromotion $right): int {
                $ratioCompare = $left->deliveryPacingRatio() <=> $right->deliveryPacingRatio();
                if ($ratioCompare !== 0) {
                    return $ratioCompare;
                }

                $leftEnd = optional($left->ends_at)?->timestamp ?? PHP_INT_MAX;
                $rightEnd = optional($right->ends_at)?->timestamp ?? PHP_INT_MAX;
                if ($leftEnd !== $rightEnd) {
                    return $leftEnd <=> $rightEnd;
                }

                return ((int) $right->promotedStatus?->date) <=> ((int) $left->promotedStatus?->date);
            })
            ->take($maxPerPage)
            ->values();

        if ($activeCandidates->isEmpty()) {
            return $organicItems;
        }

        $mixed = collect();
        $adsPlaced = 0;
        $organicSinceLastPromotion = 0;
        $promotionQueue = $activeCandidates->all();
        $gap = max(1, (int) StatusPromotionSettings::get('min_gap_between_promotions', 6));

        foreach ($organicItems as $item) {
            $mixed->push($item);
            $organicSinceLastPromotion++;

            if (empty($promotionQueue)) {
                continue;
            }

            $threshold = $adsPlaced === 0 ? 4 : $gap;
            if ($organicSinceLastPromotion < $threshold) {
                continue;
            }

            /** @var \App\Models\StatusPromotion $promotion */
            $promotion = array_shift($promotionQueue);
            $promotionStatus = $promotion->promotedStatus;

            if (!$promotionStatus) {
                continue;
            }

            $promotionStatus->is_promoted_ad = true;
            $promotionStatus->setRelation('activePromotion', $promotion);
            $mixed->push($promotionStatus);

            $this->markPromotionDelivered($promotion, $viewerFingerprint);

            $adsPlaced++;
            $organicSinceLastPromotion = 0;
        }

        return $mixed;
    }

    public function isStatusFeedEligible(Status $status): bool
    {
        if (!$this->isPromotableStatus($status)) {
            return false;
        }

        if (in_array((int) $status->id, $this->statusActivityService->hiddenDirectoryStatusIds(), true)) {
            return false;
        }

        if ((int) $status->date > time()) {
            return false;
        }

        if ((int) ($status->statu ?? 1) !== 1) {
            return false;
        }

        if (!$status->related_content) {
            return false;
        }

        if ((int) $status->s_type === 7867 && (bool) ($status->related_content->is_suspended ?? false)) {
            return false;
        }

        return true;
    }

    private function transitionPromotion(StatusPromotion $promotion, string $newStatus, bool $notify = true): StatusPromotion
    {
        if ($promotion->status === $newStatus) {
            return $promotion;
        }

        $finalStates = [
            StatusPromotion::STATUS_COMPLETED,
            StatusPromotion::STATUS_EXPIRED,
            StatusPromotion::STATUS_BUDGET_CAPPED,
        ];

        $promotion->status = $newStatus;
        if (in_array($newStatus, $finalStates, true)) {
            $promotion->completed_at = Carbon::now();
        }

        $promotion->save();

        if ($notify && in_array($newStatus, $finalStates, true)) {
            $this->notifyPromotionStatusChange($promotion);
        }

        return $promotion;
    }

    private function notifyPromotionStatusChange(StatusPromotion $promotion): void
    {
        $status = $promotion->relationLoaded('promotedStatus')
            ? $promotion->getRelation('promotedStatus')
            : $promotion->promotedStatus()->first();

        $title = __('messages.status_promotion_notification_title', [
            'id' => $status?->id ?? $promotion->status_id,
        ]);

        $message = match ($promotion->status) {
            StatusPromotion::STATUS_COMPLETED => __('messages.status_promotion_notification_completed', ['title' => $title]),
            StatusPromotion::STATUS_BUDGET_CAPPED => __('messages.status_promotion_notification_budget_capped', ['title' => $title]),
            default => __('messages.status_promotion_notification_expired', ['title' => $title]),
        };

        $this->notificationService->send(
            $promotion->user_id,
            $message,
            route('ads.posts.index'),
            'notification'
        );
    }

    private function markPromotionDelivered(StatusPromotion $promotion, string $viewerFingerprint): void
    {
        $cooldownHours = (int) StatusPromotionSettings::get('viewer_repeat_cooldown_hours', 8);
        $cacheKey = 'status_promotion_view:' . $promotion->id . ':' . sha1($viewerFingerprint);

        if ($cooldownHours > 0 && !Cache::add($cacheKey, 1, Carbon::now()->addHours($cooldownHours))) {
            return;
        }

        $servedAt = Carbon::now();
        StatusPromotion::query()
            ->whereKey($promotion->id)
            ->update([
                'delivered_impressions' => DB::raw('delivered_impressions + 1'),
                'last_served_at' => $servedAt,
            ]);

        $promotion->refresh();

        $this->syncPromotion($promotion);
    }

    private function viewerCooldownApplies(StatusPromotion $promotion, string $viewerFingerprint): bool
    {
        $cooldownHours = (int) StatusPromotionSettings::get('viewer_repeat_cooldown_hours', 8);
        if ($cooldownHours <= 0) {
            return false;
        }

        $cacheKey = 'status_promotion_view:' . $promotion->id . ':' . sha1($viewerFingerprint);

        return Cache::has($cacheKey);
    }

    private function viewerFingerprint(?int $viewerUserId): string
    {
        if ($viewerUserId) {
            return 'user:' . $viewerUserId;
        }

        $request = request();
        $ip = (string) $request->ip();
        $agent = (string) $request->userAgent();

        return 'guest:' . sha1($ip . '|' . $agent);
    }
}
