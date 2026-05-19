<?php

namespace App\Services\CustomAds;

use App\Models\CustomAdDeal;
use App\Models\CustomAdEvent;
use App\Models\CustomAdPayout;
use App\Models\User;
use App\Services\PointLedgerService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CustomAdSettlementService
{
    public function __construct(
        private readonly PointLedgerService $ledger,
        private readonly NotificationService $notificationService
    ) {
    }

    public function accept(CustomAdDeal $deal, User $actor): CustomAdDeal
    {
        if (!$deal->canBeAcceptedBy($actor)) {
            throw ValidationException::withMessages([
                'deal' => __('messages.custom_ads_deal_cannot_accept'),
            ]);
        }

        return DB::transaction(function () use ($deal) {
            $deal = CustomAdDeal::query()->lockForUpdate()->findOrFail($deal->id);

            if ($deal->payment_type === CustomAdDeal::PAYMENT_PTS_DAILY && (float) $deal->reserved_pts <= 0) {
                $advertiser = User::query()->lockForUpdate()->findOrFail($deal->advertiser_id);
                $total = round((float) $deal->total_pts, 2);

                if ($total <= 0) {
                    throw ValidationException::withMessages([
                        'total_pts' => __('messages.custom_ads_pts_required'),
                    ]);
                }

                if ((float) $advertiser->pts < $total) {
                    throw ValidationException::withMessages([
                        'total_pts' => __('messages.custom_ads_insufficient_pts'),
                    ]);
                }

                $this->ledger->award(
                    $advertiser,
                    -$total,
                    'custom_ad_pts_reserved',
                    'custom_ad_pts_reserved',
                    'custom_ad_deal',
                    $deal->id,
                    ['placement_id' => $deal->placement_id]
                );

                $deal->reserved_pts = $total;
            }

            $deal->status = CustomAdDeal::STATUS_ACTIVE;
            $deal->accepted_at = now();
            $deal->save();

            $isPublisher = (int) $actor->id === (int) $deal->publisher_id;
            $recipientId = $isPublisher ? $deal->advertiser_id : $deal->publisher_id;
            $messageKey = $isPublisher
                ? 'messages.custom_ads_request_accepted_notification'
                : 'messages.custom_ads_invite_accepted_notification';

            $this->notificationService->send(
                $recipientId,
                __($messageKey, ['user' => $actor->username]),
                route('ads.custom.deals.show', $deal),
                'shopping-bag'
            );

            return $deal->fresh(['placement.user', 'advertiser', 'publisher', 'creative']);
        });
    }

    public function reject(CustomAdDeal $deal, User $actor): CustomAdDeal
    {
        if (!$deal->canBeAcceptedBy($actor)) {
            throw ValidationException::withMessages([
                'deal' => __('messages.custom_ads_deal_cannot_reject'),
            ]);
        }

        $deal->status = CustomAdDeal::STATUS_REJECTED;
        $deal->cancelled_at = now();
        $deal->save();

        return $deal->fresh(['placement.user', 'advertiser', 'publisher', 'creative']);
    }

    public function pause(CustomAdDeal $deal, User $actor): CustomAdDeal
    {
        $this->assertPublisher($deal, $actor);

        if ($deal->status === CustomAdDeal::STATUS_ACTIVE) {
            $deal->status = CustomAdDeal::STATUS_PAUSED;
            $deal->save();
        }

        return $deal->fresh(['placement.user', 'advertiser', 'publisher', 'creative']);
    }

    public function resume(CustomAdDeal $deal, User $actor): CustomAdDeal
    {
        $this->assertPublisher($deal, $actor);

        if ($deal->status === CustomAdDeal::STATUS_PAUSED) {
            $deal->status = CustomAdDeal::STATUS_ACTIVE;
            $deal->save();
        }

        return $deal->fresh(['placement.user', 'advertiser', 'publisher', 'creative']);
    }

    public function cancel(CustomAdDeal $deal, ?User $actor = null): CustomAdDeal
    {
        if ($actor && !$deal->canBeManagedBy($actor) && !($actor->hasAdminAccess() ?? false)) {
            abort(403);
        }

        return DB::transaction(function () use ($deal) {
            $deal = CustomAdDeal::query()->lockForUpdate()->findOrFail($deal->id);

            if (!in_array($deal->status, [
                CustomAdDeal::STATUS_CANCELLED,
                CustomAdDeal::STATUS_COMPLETED,
                CustomAdDeal::STATUS_REJECTED,
            ], true)) {
                $this->refundRemaining($deal, 'cancelled');
                $deal->status = CustomAdDeal::STATUS_CANCELLED;
                $deal->cancelled_at = now();
                $deal->save();
            }

            return $deal->fresh(['placement.user', 'advertiser', 'publisher', 'creative']);
        });
    }

    public function releaseDailyPayouts(Carbon|string|null $forDate = null): array
    {
        $date = $forDate instanceof Carbon
            ? $forDate->copy()
            : ($forDate ? Carbon::parse($forDate) : now()->subDay());

        $start = $date->copy()->startOfDay();
        $end = $date->copy()->endOfDay();
        $paid = 0;
        $skipped = 0;
        $completed = 0;

        CustomAdDeal::query()
            ->where('payment_type', CustomAdDeal::PAYMENT_PTS_DAILY)
            ->whereIn('status', [CustomAdDeal::STATUS_ACTIVE, CustomAdDeal::STATUS_PAUSED])
            ->where(function ($query) use ($end) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $end);
            })
            ->where(function ($query) use ($start) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $start);
            })
            ->with('creative')
            ->orderBy('id')
            ->chunkById(50, function ($deals) use ($start, $end, &$paid, &$skipped) {
                foreach ($deals as $deal) {
                    $result = $this->releaseDailyPayout($deal, $start, $end);
                    $result ? $paid++ : $skipped++;
                }
            });

        CustomAdDeal::query()
            ->where('payment_type', CustomAdDeal::PAYMENT_PTS_DAILY)
            ->whereIn('status', [CustomAdDeal::STATUS_ACTIVE, CustomAdDeal::STATUS_PAUSED])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now()->startOfDay())
            ->orderBy('id')
            ->chunkById(50, function ($deals) use (&$completed) {
                foreach ($deals as $deal) {
                    $this->complete($deal);
                    $completed++;
                }
            });

        return compact('paid', 'skipped', 'completed');
    }

    public function complete(CustomAdDeal $deal): CustomAdDeal
    {
        return DB::transaction(function () use ($deal) {
            $deal = CustomAdDeal::query()->lockForUpdate()->findOrFail($deal->id);
            $this->refundRemaining($deal, 'completed');
            $deal->status = CustomAdDeal::STATUS_COMPLETED;
            $deal->save();

            return $deal->fresh(['placement.user', 'advertiser', 'publisher', 'creative']);
        });
    }

    private function releaseDailyPayout(CustomAdDeal $deal, Carbon $start, Carbon $end): bool
    {
        if ((string) $deal->status !== CustomAdDeal::STATUS_ACTIVE) {
            return false;
        }

        if (CustomAdPayout::query()
            ->where('deal_id', $deal->id)
            ->where('type', CustomAdPayout::TYPE_DAILY)
            ->whereDate('payout_date', $start->toDateString())
            ->exists()) {
            return false;
        }

        $impressions = CustomAdEvent::query()
            ->where('deal_id', $deal->id)
            ->where('event_type', CustomAdEvent::TYPE_IMPRESSION)
            ->whereBetween('occurred_at', [$start, $end])
            ->count();

        if ($impressions <= 0) {
            return false;
        }

        return DB::transaction(function () use ($deal, $start, $impressions) {
            $deal = CustomAdDeal::query()->lockForUpdate()->findOrFail($deal->id);
            $amount = min((float) $deal->daily_pts, $deal->remainingReservedPts());

            if ($amount <= 0) {
                return false;
            }

            $this->ledger->award(
                $deal->publisher_id,
                $amount,
                'custom_ad_daily_payout',
                'custom_ad_daily_payout',
                'custom_ad_deal',
                $deal->id,
                [
                    'placement_id' => $deal->placement_id,
                    'impressions' => $impressions,
                    'payout_date' => $start->toDateString(),
                ]
            );

            CustomAdPayout::create([
                'deal_id' => $deal->id,
                'publisher_id' => $deal->publisher_id,
                'advertiser_id' => $deal->advertiser_id,
                'type' => CustomAdPayout::TYPE_DAILY,
                'amount' => $amount,
                'payout_date' => $start->toDateString(),
                'meta' => ['impressions' => $impressions],
            ]);

            $deal->paid_pts = (float) $deal->paid_pts + $amount;
            $deal->last_paid_on = $start->toDateString();

            if ($deal->remainingReservedPts() <= 0.0001) {
                $deal->status = CustomAdDeal::STATUS_COMPLETED;
            }

            $deal->save();

            return true;
        });
    }

    private function refundRemaining(CustomAdDeal $deal, string $reason): void
    {
        if ($deal->payment_type !== CustomAdDeal::PAYMENT_PTS_DAILY) {
            return;
        }

        $amount = $deal->remainingReservedPts();
        if ($amount <= 0) {
            return;
        }

        $this->ledger->award(
            $deal->advertiser_id,
            $amount,
            'custom_ad_pts_refund',
            'custom_ad_pts_refund',
            'custom_ad_deal',
            $deal->id,
            ['reason' => $reason, 'placement_id' => $deal->placement_id]
        );

        CustomAdPayout::firstOrCreate(
            [
                'deal_id' => $deal->id,
                'type' => CustomAdPayout::TYPE_REFUND,
                'payout_date' => now()->toDateString(),
            ],
            [
                'publisher_id' => $deal->publisher_id,
                'advertiser_id' => $deal->advertiser_id,
                'amount' => $amount,
                'meta' => ['reason' => $reason],
            ]
        );

        $deal->refunded_pts = (float) $deal->refunded_pts + $amount;
    }

    private function assertPublisher(CustomAdDeal $deal, User $actor): void
    {
        if ((int) $deal->publisher_id !== (int) $actor->id) {
            abort(403);
        }
    }
}
