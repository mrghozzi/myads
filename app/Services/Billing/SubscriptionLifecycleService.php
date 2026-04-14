<?php

namespace App\Services\Billing;

use App\Models\BillingOrder;
use App\Models\BillingTransaction;
use App\Models\MemberSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\V420SchemaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionLifecycleService
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly BillingCurrencyService $currencies,
        private readonly BillingGatewayRegistry $gateways,
        private readonly SubscriptionPlanService $plans,
        private readonly SubscriptionEntitlementService $entitlements
    ) {
    }

    public function createOrder(User $user, SubscriptionPlan $plan, string $gatewayKey, string $currencyCode): BillingOrder
    {
        $gateway = $this->gateways->get($gatewayKey);
        $conversion = $this->currencies->convertFromBase((float) $plan->base_price, $currencyCode);
        $currency = $conversion['currency'];
        $planSnapshot = $this->plans->planSnapshot($plan);

        return DB::transaction(function () use ($user, $plan, $gateway, $conversion, $currency, $planSnapshot): BillingOrder {
            $order = BillingOrder::query()->create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => (int) $user->id,
                'subscription_plan_id' => (int) $plan->id,
                'gateway' => $gateway->key(),
                'status' => $gateway->key() === 'bank_transfer'
                    ? BillingOrder::STATUS_PENDING_RECEIPT
                    : BillingOrder::STATUS_PENDING_CHECKOUT,
                'base_currency_code' => $this->currencies->baseCurrency()->code,
                'currency_code' => (string) $currency->code,
                'base_amount' => (float) $plan->base_price,
                'display_amount' => (float) $conversion['display_amount'],
                'exchange_rate_snapshot' => (float) $conversion['exchange_rate'],
                'expires_at' => now()->addHours(24),
                'plan_snapshot' => $planSnapshot,
                'meta' => [
                    'currency_symbol' => $currency->symbol,
                    'currency_name' => $currency->name,
                    'currency_decimal_places' => (int) $currency->decimal_places,
                    'gateway_label' => $gateway->label(),
                ],
            ]);

            $this->logTransaction($order, [
                'gateway' => $order->gateway,
                'transaction_type' => 'order_created',
                'status' => $order->status,
                'amount' => $order->display_amount,
                'currency_code' => $order->currency_code,
                'exchange_rate_snapshot' => $order->exchange_rate_snapshot,
                'processed_at' => now(),
                'meta' => [
                    'order_number' => $order->order_number,
                ],
            ]);

            return $order;
        });
    }

    public function attachGatewayCheckout(BillingOrder $order, array $checkoutPayload): BillingOrder
    {
        $order->forceFill([
            'status' => (string) ($checkoutPayload['status'] ?? $order->status),
            'gateway_checkout_reference' => $checkoutPayload['gateway_checkout_reference'] ?? $order->gateway_checkout_reference,
            'meta' => array_merge((array) $order->meta, (array) ($checkoutPayload['meta'] ?? [])),
        ])->save();

        $this->logTransaction($order, [
            'gateway' => $order->gateway,
            'transaction_type' => 'checkout_created',
            'status' => $order->status,
            'amount' => $order->display_amount,
            'currency_code' => $order->currency_code,
            'exchange_rate_snapshot' => $order->exchange_rate_snapshot,
            'processed_at' => now(),
            'meta' => (array) ($checkoutPayload['meta'] ?? []),
        ]);

        return $order->refresh();
    }

    public function recordGatewayState(BillingOrder|int $order, array $payload, string $transactionType = 'gateway_event'): BillingOrder
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return $order instanceof BillingOrder ? $order : new BillingOrder();
        }

        return DB::transaction(function () use ($order, $payload, $transactionType): BillingOrder {
            /** @var BillingOrder $model */
            $model = $order instanceof BillingOrder
                ? BillingOrder::query()->lockForUpdate()->findOrFail($order->id)
                : BillingOrder::query()->lockForUpdate()->findOrFail((int) $order);

            if ($model->status !== BillingOrder::STATUS_PAID) {
                $nextStatus = (string) ($payload['status'] ?? $model->status);
                if (in_array($nextStatus, [
                    BillingOrder::STATUS_PENDING_CHECKOUT,
                    BillingOrder::STATUS_PENDING_RECEIPT,
                    BillingOrder::STATUS_PENDING_REVIEW,
                    BillingOrder::STATUS_FAILED,
                    BillingOrder::STATUS_CANCELLED,
                    BillingOrder::STATUS_REJECTED,
                ], true)) {
                    $model->status = $nextStatus;
                }

                if (!empty($payload['gateway_checkout_reference'])) {
                    $model->gateway_checkout_reference = (string) $payload['gateway_checkout_reference'];
                }

                if (!empty($payload['external_transaction_id'])) {
                    $model->gateway_reference = (string) $payload['external_transaction_id'];
                }

                if ($nextStatus === BillingOrder::STATUS_REJECTED && $model->rejected_at === null) {
                    $model->rejected_at = now();
                }

                $model->meta = array_merge((array) $model->meta, (array) ($payload['meta'] ?? []));
                $model->save();
            }

            $this->logTransaction($model, [
                'gateway' => $model->gateway,
                'transaction_type' => $transactionType,
                'status' => $payload['status'] ?? $model->status,
                'external_transaction_id' => $payload['external_transaction_id'] ?? null,
                'amount' => $payload['amount'] ?? $model->display_amount,
                'currency_code' => $payload['currency_code'] ?? $model->currency_code,
                'exchange_rate_snapshot' => $model->exchange_rate_snapshot,
                'processed_at' => now(),
                'meta' => (array) ($payload['meta'] ?? []),
            ]);

            return $model->refresh();
        });
    }

    public function completePaidOrder(BillingOrder|int $order, array $payload, string $transactionType = 'gateway_capture'): BillingOrder
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return $order instanceof BillingOrder ? $order : new BillingOrder();
        }

        return DB::transaction(function () use ($order, $payload, $transactionType): BillingOrder {
            /** @var BillingOrder $model */
            $model = $order instanceof BillingOrder
                ? BillingOrder::query()->lockForUpdate()->findOrFail($order->id)
                : BillingOrder::query()->lockForUpdate()->findOrFail((int) $order);

            $alreadyPaid = $model->status === BillingOrder::STATUS_PAID;

            $model->status = BillingOrder::STATUS_PAID;
            $model->paid_at = $model->paid_at ?: now();
            $model->approved_at = $model->approved_at ?: now();
            $model->gateway_reference = (string) ($payload['external_transaction_id'] ?? $model->gateway_reference ?? '');
            $model->gateway_checkout_reference = (string) ($payload['gateway_checkout_reference'] ?? $model->gateway_checkout_reference ?? '');
            $model->meta = array_merge((array) $model->meta, (array) ($payload['meta'] ?? []));
            $model->save();

            $this->logTransaction($model, [
                'gateway' => $model->gateway,
                'transaction_type' => $transactionType,
                'status' => BillingOrder::STATUS_PAID,
                'external_transaction_id' => $payload['external_transaction_id'] ?? null,
                'amount' => $payload['amount'] ?? $model->display_amount,
                'currency_code' => $payload['currency_code'] ?? $model->currency_code,
                'exchange_rate_snapshot' => $model->exchange_rate_snapshot,
                'processed_at' => now(),
                'meta' => (array) ($payload['meta'] ?? []),
            ]);

            if ($alreadyPaid) {
                return $model->refresh();
            }

            $this->syncUserSubscriptions((int) $model->user_id);

            $active = MemberSubscription::query()
                ->where('user_id', $model->user_id)
                ->where('status', MemberSubscription::STATUS_ACTIVE)
                ->lockForUpdate()
                ->first();

            $planSnapshot = (array) $model->plan_snapshot;
            $planName = (string) ($planSnapshot['name'] ?? __('messages.billing_subscription_plan'));

            if ($active && (int) $active->subscription_plan_id === (int) $model->subscription_plan_id) {
                $active->ends_at = $this->extendEndsAt($active, $planSnapshot);
                $active->save();

                $model->member_subscription_id = $active->id;
                $model->save();

                $user = User::query()->findOrFail($model->user_id);
                $this->entitlements->grantPurchaseBenefits(
                    $user,
                    (array) ($planSnapshot['entitlements'] ?? []),
                    (int) $model->id,
                    $planName
                );

                return $model->refresh();
            }

            $startsAt = now();
            $status = MemberSubscription::STATUS_ACTIVE;

            if ($active) {
                $status = MemberSubscription::STATUS_QUEUED;
                $startsAt = $active->ends_at ? $active->ends_at->copy() : now();
            }

            $subscription = MemberSubscription::query()->create([
                'user_id' => (int) $model->user_id,
                'subscription_plan_id' => $model->subscription_plan_id ? (int) $model->subscription_plan_id : null,
                'billing_order_id' => (int) $model->id,
                'queued_from_subscription_id' => $active?->id,
                'status' => $status,
                'plan_name' => $planName,
                'plan_snapshot' => $planSnapshot,
                'entitlements_snapshot' => (array) ($planSnapshot['entitlements'] ?? []),
                'starts_at' => $startsAt,
                'ends_at' => $this->calculateEndsAt($planSnapshot, $startsAt),
                'activated_at' => $status === MemberSubscription::STATUS_ACTIVE ? now() : null,
                'meta' => [
                    'source_order_number' => $model->order_number,
                ],
            ]);

            $model->member_subscription_id = (int) $subscription->id;
            $model->save();

            if ($status === MemberSubscription::STATUS_ACTIVE) {
                $this->entitlements->applyActivationBenefits((int) $model->user_id, $subscription);
            }

            return $model->refresh();
        });
    }

    public function uploadReceipt(BillingOrder $order, string $receiptPath, ?string $note = null): BillingOrder
    {
        $order->forceFill([
            'status' => BillingOrder::STATUS_PENDING_REVIEW,
            'receipt_path' => $receiptPath,
            'receipt_note' => $note,
        ])->save();

        $this->logTransaction($order, [
            'gateway' => $order->gateway,
            'transaction_type' => 'receipt_uploaded',
            'status' => BillingOrder::STATUS_PENDING_REVIEW,
            'amount' => $order->display_amount,
            'currency_code' => $order->currency_code,
            'exchange_rate_snapshot' => $order->exchange_rate_snapshot,
            'processed_at' => now(),
            'meta' => [
                'receipt_path' => $receiptPath,
            ],
        ]);

        return $order->refresh();
    }

    public function reviewBankTransfer(BillingOrder $order, string $action, ?string $adminNote = null): BillingOrder
    {
        if ($action === 'approve') {
            $order->forceFill([
                'admin_note' => $adminNote,
            ])->save();

            return $this->completePaidOrder($order, [
                'status' => BillingOrder::STATUS_PAID,
                'external_transaction_id' => $order->gateway_reference,
                'amount' => $order->display_amount,
                'currency_code' => $order->currency_code,
                'meta' => [
                    'review_action' => 'approve',
                    'admin_note' => $adminNote,
                ],
            ], 'admin_approved');
        }

        $order->forceFill([
            'admin_note' => $adminNote,
            'rejected_at' => now(),
        ])->save();

        return $this->recordGatewayState($order, [
            'status' => BillingOrder::STATUS_REJECTED,
            'amount' => $order->display_amount,
            'currency_code' => $order->currency_code,
            'meta' => [
                'review_action' => 'reject',
                'admin_note' => $adminNote,
            ],
        ], 'admin_rejected');
    }

    /**
     * @return array{active:?MemberSubscription,queued:?MemberSubscription}
     */
    public function syncUserSubscriptions(User|int $user): array
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return ['active' => null, 'queued' => null];
        }

        $userId = $user instanceof User ? (int) $user->id : (int) $user;

        try {
            return DB::transaction(function () use ($userId): array {
                $now = now();

                $activeSubscriptions = MemberSubscription::query()
                    ->where('user_id', $userId)
                    ->where('status', MemberSubscription::STATUS_ACTIVE)
                    ->lockForUpdate()
                    ->get();

                foreach ($activeSubscriptions as $activeSubscription) {
                    if ($activeSubscription->ends_at && $activeSubscription->ends_at->lte($now)) {
                        $activeSubscription->forceFill([
                            'status' => MemberSubscription::STATUS_EXPIRED,
                            'completed_at' => $activeSubscription->completed_at ?: $activeSubscription->ends_at,
                        ])->save();
                    }
                }

                $active = MemberSubscription::query()
                    ->where('user_id', $userId)
                    ->where('status', MemberSubscription::STATUS_ACTIVE)
                    ->where(function ($query) use ($now) {
                        $query->whereNull('ends_at')->orWhere('ends_at', '>', $now);
                    })
                    ->orderByDesc('activated_at')
                    ->first();

                if (!$active) {
                    $queued = MemberSubscription::query()
                        ->where('user_id', $userId)
                        ->where('status', MemberSubscription::STATUS_QUEUED)
                        ->where(function ($query) use ($now) {
                            $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                        })
                        ->orderBy('starts_at')
                        ->lockForUpdate()
                        ->first();

                    if ($queued) {
                        $startsAt = $queued->starts_at ?: $now;
                        $queued->forceFill([
                            'status' => MemberSubscription::STATUS_ACTIVE,
                            'starts_at' => $startsAt,
                            'activated_at' => $queued->activated_at ?: $now,
                            'ends_at' => $queued->ends_at ?: $this->calculateEndsAt((array) $queued->plan_snapshot, $startsAt),
                        ])->save();

                        $this->entitlements->applyActivationBenefits($userId, $queued);
                        $active = $queued->refresh();
                    }
                }

                $queued = MemberSubscription::query()
                    ->where('user_id', $userId)
                    ->where('status', MemberSubscription::STATUS_QUEUED)
                    ->orderBy('starts_at')
                    ->first();

                return [
                    'active' => $active,
                    'queued' => $queued,
                ];
            });
        } catch (\Throwable) {
            return ['active' => null, 'queued' => null];
        }
    }

    public function logTransaction(BillingOrder $order, array $payload): BillingTransaction
    {
        return BillingTransaction::query()->create([
            'billing_order_id' => (int) $order->id,
            'user_id' => (int) $order->user_id,
            'gateway' => (string) ($payload['gateway'] ?? $order->gateway),
            'transaction_type' => (string) ($payload['transaction_type'] ?? 'event'),
            'status' => (string) ($payload['status'] ?? $order->status),
            'external_transaction_id' => trim((string) ($payload['external_transaction_id'] ?? '')) ?: null,
            'amount' => round((float) ($payload['amount'] ?? $order->display_amount), 2),
            'currency_code' => (string) ($payload['currency_code'] ?? $order->currency_code),
            'exchange_rate_snapshot' => (float) ($payload['exchange_rate_snapshot'] ?? $order->exchange_rate_snapshot),
            'processed_at' => $payload['processed_at'] ?? now(),
            'meta' => (array) ($payload['meta'] ?? []),
        ]);
    }

    private function calculateEndsAt(array $planSnapshot, Carbon $startsAt): ?Carbon
    {
        if (!empty($planSnapshot['is_lifetime'])) {
            return null;
        }

        $durationDays = max(1, (int) ($planSnapshot['duration_days'] ?? 30));

        return $startsAt->copy()->addDays($durationDays);
    }

    private function extendEndsAt(MemberSubscription $subscription, array $planSnapshot): ?Carbon
    {
        if (!empty($planSnapshot['is_lifetime']) || $subscription->ends_at === null) {
            return null;
        }

        $anchor = $subscription->ends_at->greaterThan(now())
            ? $subscription->ends_at->copy()
            : now();

        return $this->calculateEndsAt($planSnapshot, $anchor);
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'BIL-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        } while (BillingOrder::query()->where('order_number', $number)->exists());

        return $number;
    }
}
