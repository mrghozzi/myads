<?php

namespace App\Support;

use App\Models\OrderRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderMarketplaceBackfill
{
    public static function run(): void
    {
        OrderCategoryOptions::ensureDefaults();

        if (!Schema::hasTable('order_requests') || !Schema::hasTable('order_offers')) {
            return;
        }

        self::hydrateOrderRequests();
        self::migrateLegacyOffers();
        self::buildContracts();
    }

    private static function hydrateOrderRequests(): void
    {
        $orders = DB::table('order_requests')->get();

        foreach ($orders as $order) {
            $budget = self::parseBudget((string) ($order->budget ?? ''));
            $workflowStatus = self::resolveWorkflowStatus($order);
            $lastActivity = (int) ($order->last_activity ?: $order->date ?: time());

            DB::table('order_requests')
                ->where('id', $order->id)
                ->update([
                    'pricing_model' => $budget['pricing_model'],
                    'budget_min' => $budget['budget_min'],
                    'budget_max' => $budget['budget_max'],
                    'budget_currency' => $budget['budget_currency'],
                    'delivery_window_days' => $order->delivery_window_days ?: null,
                    'workflow_status' => $workflowStatus,
                    'last_activity' => $lastActivity,
                    'category' => $order->category ?: 'uncategorized',
                    'statu' => in_array($workflowStatus, [
                        OrderRequest::WORKFLOW_CLOSED,
                        OrderRequest::WORKFLOW_CANCELLED,
                        OrderRequest::WORKFLOW_COMPLETED,
                    ], true) ? 0 : 1,
                ]);
        }
    }

    private static function migrateLegacyOffers(): void
    {
        $orders = DB::table('order_requests')->get()->keyBy('id');
        $legacyOffers = DB::table('options')
            ->whereIn('o_type', ['o_order', 'order_comment'])
            ->orderBy('id')
            ->get();

        $mappedOfferIds = [];

        foreach ($legacyOffers as $legacyOffer) {
            $order = $orders->get($legacyOffer->o_parent);
            if (!$order) {
                continue;
            }

            $existing = DB::table('order_offers')
                ->where('legacy_option_id', $legacyOffer->id)
                ->where('legacy_option_type', $legacyOffer->o_type)
                ->first();

            if ($existing) {
                $mappedOfferIds[$legacyOffer->o_type . ':' . $legacyOffer->id] = $existing->id;
                continue;
            }

            $createdAt = self::legacyOfferCreatedAt($legacyOffer, $order);
            $status = $order->best_offer_id && (int) $order->best_offer_id === (int) $legacyOffer->id
                ? 'awarded'
                : ((string) $order->workflow_status === OrderRequest::WORKFLOW_OPEN ? 'active' : 'archived');

            $offerId = DB::table('order_offers')->insertGetId([
                'order_request_id' => (int) $legacyOffer->o_parent,
                'user_id' => max(1, (int) $legacyOffer->o_order),
                'pricing_model' => (string) ($order->pricing_model ?: 'fixed'),
                'quoted_amount' => null,
                'currency_code' => (string) ($order->budget_currency ?: 'USD'),
                'delivery_days' => $order->delivery_window_days ? (int) $order->delivery_window_days : null,
                'message' => (string) ($legacyOffer->o_valuer ?: __('messages.order_offer_legacy_message')),
                'status' => $status,
                'client_rating' => $legacyOffer->o_type === 'o_order' && (int) $legacyOffer->o_mode >= 1 && (int) $legacyOffer->o_mode <= 5
                    ? (int) $legacyOffer->o_mode
                    : null,
                'client_review' => null,
                'legacy_option_id' => (int) $legacyOffer->id,
                'legacy_option_type' => (string) $legacyOffer->o_type,
                'awarded_at' => $status === 'awarded' ? $createdAt : null,
                'withdrawn_at' => null,
                'rated_at' => $legacyOffer->o_type === 'o_order' && (int) $legacyOffer->o_mode >= 1 && (int) $legacyOffer->o_mode <= 5
                    ? $createdAt
                    : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $mappedOfferIds[$legacyOffer->o_type . ':' . $legacyOffer->id] = $offerId;
        }

        foreach ($orders as $order) {
            if (!$order->best_offer_id) {
                continue;
            }

            $mappedId = $mappedOfferIds['o_order:' . $order->best_offer_id]
                ?? $mappedOfferIds['order_comment:' . $order->best_offer_id]
                ?? null;

            if (!$mappedId) {
                continue;
            }

            $workflowStatus = self::resolveAwardedWorkflowStatus($order);
            DB::table('order_requests')
                ->where('id', $order->id)
                ->update([
                    'best_offer_id' => $mappedId,
                    'workflow_status' => $workflowStatus,
                    'statu' => in_array($workflowStatus, [
                        OrderRequest::WORKFLOW_CLOSED,
                        OrderRequest::WORKFLOW_CANCELLED,
                        OrderRequest::WORKFLOW_COMPLETED,
                    ], true) ? 0 : 1,
                ]);

            if ((float) $order->avg_rating > 0) {
                DB::table('order_offers')
                    ->where('id', $mappedId)
                    ->update([
                        'client_rating' => max(1, min(5, (int) round((float) $order->avg_rating))),
                        'rated_at' => Carbon::now()->toDateTimeString(),
                    ]);
            }
        }
    }

    private static function buildContracts(): void
    {
        if (!Schema::hasTable('order_contracts')) {
            return;
        }

        $orders = DB::table('order_requests')
            ->whereNotNull('best_offer_id')
            ->get();

        foreach ($orders as $order) {
            $offer = DB::table('order_offers')->where('id', $order->best_offer_id)->first();
            if (!$offer) {
                continue;
            }

            $exists = DB::table('order_contracts')
                ->where('order_request_id', $order->id)
                ->exists();

            if ($exists) {
                continue;
            }

            $status = in_array((string) $order->workflow_status, [
                OrderRequest::WORKFLOW_COMPLETED,
                OrderRequest::WORKFLOW_CANCELLED,
                OrderRequest::WORKFLOW_DELIVERED,
                OrderRequest::WORKFLOW_IN_PROGRESS,
            ], true)
                ? (string) $order->workflow_status
                : OrderRequest::WORKFLOW_AWARDED;

            $awardedAt = $offer->awarded_at ?: self::legacyTimestampToDateTime((int) ($order->last_activity ?: $order->date ?: time()));

            DB::table('order_contracts')->insert([
                'order_request_id' => (int) $order->id,
                'order_offer_id' => (int) $offer->id,
                'client_user_id' => (int) $order->uid,
                'provider_user_id' => (int) $offer->user_id,
                'status' => $status,
                'pricing_model' => (string) ($offer->pricing_model ?: $order->pricing_model ?: 'fixed'),
                'quoted_amount' => $offer->quoted_amount,
                'currency_code' => (string) ($offer->currency_code ?: $order->budget_currency ?: 'USD'),
                'delivery_days' => $offer->delivery_days,
                'snapshot_payload' => json_encode([
                    'order' => [
                        'title' => $order->title,
                        'description' => $order->description,
                        'category' => $order->category,
                        'pricing_model' => $order->pricing_model,
                        'budget_min' => $order->budget_min,
                        'budget_max' => $order->budget_max,
                        'budget_currency' => $order->budget_currency,
                        'delivery_window_days' => $order->delivery_window_days,
                    ],
                    'offer' => [
                        'message' => $offer->message,
                        'pricing_model' => $offer->pricing_model,
                        'quoted_amount' => $offer->quoted_amount,
                        'currency_code' => $offer->currency_code,
                        'delivery_days' => $offer->delivery_days,
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'delivery_note' => null,
                'completion_note' => null,
                'awarded_at' => $awardedAt,
                'started_at' => $status === OrderRequest::WORKFLOW_IN_PROGRESS ? $awardedAt : null,
                'delivered_at' => in_array($status, [OrderRequest::WORKFLOW_DELIVERED, OrderRequest::WORKFLOW_COMPLETED], true) ? $awardedAt : null,
                'completed_at' => $status === OrderRequest::WORKFLOW_COMPLETED ? $awardedAt : null,
                'cancelled_at' => $status === OrderRequest::WORKFLOW_CANCELLED ? $awardedAt : null,
                'created_at' => $awardedAt,
                'updated_at' => $awardedAt,
            ]);
        }
    }

    /**
     * @return array{pricing_model: string, budget_min: float|null, budget_max: float|null, budget_currency: string}
     */
    private static function parseBudget(string $budget): array
    {
        $clean = trim($budget);
        $currency = match (true) {
            str_contains(strtoupper($clean), 'PTS') => 'PTS',
            str_contains($clean, '€') => 'EUR',
            str_contains($clean, '£') => 'GBP',
            default => 'USD',
        };

        preg_match_all('/(\d+(?:[.,]\d+)?)/', $clean, $matches);
        $numbers = collect($matches[1] ?? [])
            ->map(static fn ($number) => (float) str_replace(',', '.', $number))
            ->values();

        if ($numbers->isEmpty()) {
            return [
                'pricing_model' => 'negotiable',
                'budget_min' => null,
                'budget_max' => null,
                'budget_currency' => $currency,
            ];
        }

        if ($numbers->count() === 1) {
            return [
                'pricing_model' => 'fixed',
                'budget_min' => $numbers[0],
                'budget_max' => $numbers[0],
                'budget_currency' => $currency,
            ];
        }

        return [
            'pricing_model' => 'range',
            'budget_min' => min($numbers[0], $numbers[1]),
            'budget_max' => max($numbers[0], $numbers[1]),
            'budget_currency' => $currency,
        ];
    }

    private static function resolveWorkflowStatus(object $order): string
    {
        if ($order->best_offer_id) {
            return self::resolveAwardedWorkflowStatus($order);
        }

        return (int) $order->statu === 0
            ? OrderRequest::WORKFLOW_CLOSED
            : OrderRequest::WORKFLOW_OPEN;
    }

    private static function resolveAwardedWorkflowStatus(object $order): string
    {
        if ((float) ($order->avg_rating ?? 0) > 0) {
            return OrderRequest::WORKFLOW_COMPLETED;
        }

        return OrderRequest::WORKFLOW_AWARDED;
    }

    private static function legacyOfferCreatedAt(object $legacyOffer, object $order): string
    {
        $timestamp = match (true) {
            $legacyOffer->o_type === 'order_comment' && (int) $legacyOffer->o_mode > 1000000000 => (int) $legacyOffer->o_mode,
            (int) $order->last_activity > 0 => (int) $order->last_activity,
            default => (int) ($order->date ?: time()),
        };

        return self::legacyTimestampToDateTime($timestamp);
    }

    private static function legacyTimestampToDateTime(int $timestamp): string
    {
        return Carbon::createFromTimestamp(max(1, $timestamp))->toDateTimeString();
    }
}
