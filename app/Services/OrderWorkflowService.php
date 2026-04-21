<?php

namespace App\Services;

use App\Models\OrderContract;
use App\Models\OrderOffer;
use App\Models\OrderRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly PointLedgerService $points,
        private readonly GamificationService $gamification,
        private readonly OrderContractService $contracts
    ) {
    }

    public function award(OrderRequest $order, OrderOffer $offer, User $actor): OrderRequest
    {
        if ((int) $order->uid !== (int) $actor->id) {
            throw new AuthorizationException();
        }

        if (!$order->isOpenForOffers()) {
            throw ValidationException::withMessages([
                'offer' => __('messages.order_cannot_be_awarded'),
            ]);
        }

        if ((int) $offer->order_request_id !== (int) $order->id || $offer->status !== OrderOffer::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'offer' => __('messages.order_offer_invalid'),
            ]);
        }

        return DB::transaction(function () use ($order, $offer) {
            OrderOffer::query()
                ->where('order_request_id', $order->id)
                ->where('id', '!=', $offer->id)
                ->where('status', OrderOffer::STATUS_ACTIVE)
                ->update(['status' => OrderOffer::STATUS_ARCHIVED, 'updated_at' => now()]);

            $offer->status = OrderOffer::STATUS_AWARDED;
            $offer->awarded_at = now();
            $offer->save();

            $order->best_offer_id = $offer->id;
            $order->syncLifecycleState(OrderRequest::WORKFLOW_AWARDED);
            $order->save();

            $this->contracts->createOrRefreshFromOffer($order, $offer);
            $this->points->award($offer->user_id, 50, 'best_offer_winner', 'points_awarded', 'order_offer', $offer->id);
            $this->gamification->recordEvent($offer->user_id, 'best_offer_selected');

            $this->notifications->send(
                $offer->user_id,
                __('messages.best_offer_selected'),
                route('orders.show', $order->id),
                'notification',
                $order->uid
            );

            return $order->fresh(['awardedOffer.user', 'contract']);
        });
    }

    public function start(OrderRequest $order, User $actor): OrderRequest
    {
        $contract = $order->contract()->firstOrFail();
        $this->assertProvider($contract, $actor);

        if ((string) $order->workflow_status !== OrderRequest::WORKFLOW_AWARDED) {
            throw ValidationException::withMessages([
                'workflow' => __('messages.order_cannot_be_started'),
            ]);
        }

        return DB::transaction(function () use ($order, $contract) {
            $order->syncLifecycleState(OrderRequest::WORKFLOW_IN_PROGRESS);
            $order->save();
            $this->contracts->transition($contract, OrderContract::STATUS_IN_PROGRESS);

            return $order->fresh(['contract', 'awardedOffer.user']);
        });
    }

    public function deliver(OrderRequest $order, User $actor, ?string $note = null): OrderRequest
    {
        $contract = $order->contract()->firstOrFail();
        $this->assertProvider($contract, $actor);

        if ((string) $order->workflow_status !== OrderRequest::WORKFLOW_IN_PROGRESS) {
            throw ValidationException::withMessages([
                'workflow' => __('messages.order_cannot_be_delivered'),
            ]);
        }

        return DB::transaction(function () use ($order, $contract, $note) {
            $order->syncLifecycleState(OrderRequest::WORKFLOW_DELIVERED);
            $order->save();
            $this->contracts->transition($contract, OrderContract::STATUS_DELIVERED, [
                'delivery_note' => $note,
            ]);

            $this->notifications->send(
                $order->uid,
                __('messages.order_delivered_notification', ['title' => $order->title]),
                route('orders.show', $order->id),
                'notification',
                $contract->provider_user_id
            );

            return $order->fresh(['contract', 'awardedOffer.user']);
        });
    }

    public function complete(OrderRequest $order, User $actor, ?int $rating = null, ?string $review = null): OrderRequest
    {
        $contract = $order->contract()->firstOrFail();

        if ((int) $order->uid !== (int) $actor->id) {
            throw new AuthorizationException();
        }

        if (!in_array((string) $order->workflow_status, [
            OrderRequest::WORKFLOW_DELIVERED,
            OrderRequest::WORKFLOW_IN_PROGRESS,
        ], true)) {
            throw ValidationException::withMessages([
                'workflow' => __('messages.order_cannot_be_completed'),
            ]);
        }

        return DB::transaction(function () use ($order, $contract, $rating, $review) {
            $order->syncLifecycleState(OrderRequest::WORKFLOW_COMPLETED);
            $order->save();
            $this->contracts->transition($contract, OrderContract::STATUS_COMPLETED, [
                'completion_note' => $review,
            ]);

            if ($order->awardedOffer) {
                $this->applyRating($order, $rating, $review);
            }

            $this->notifications->send(
                $contract->provider_user_id,
                __('messages.order_completed_notification', ['title' => $order->title]),
                route('orders.show', $order->id),
                'notification',
                $order->uid
            );

            return $order->fresh(['contract', 'awardedOffer.user']);
        });
    }

    public function rate(OrderRequest $order, User $actor, int $rating, ?string $review = null): OrderRequest
    {
        if ((int) $order->uid !== (int) $actor->id) {
            throw new AuthorizationException();
        }

        if ((string) $order->workflow_status !== OrderRequest::WORKFLOW_COMPLETED) {
            throw ValidationException::withMessages([
                'workflow' => __('messages.order_rating_after_completion_only'),
            ]);
        }

        return DB::transaction(function () use ($order, $rating, $review) {
            $this->applyRating($order, $rating, $review);

            return $order->fresh(['contract', 'awardedOffer.user']);
        });
    }

    public function cancel(OrderRequest $order, User $actor, ?string $note = null): OrderRequest
    {
        $canCancel = (int) $order->uid === (int) $actor->id || $actor->isAdmin();
        if (!$canCancel) {
            throw new AuthorizationException();
        }

        if ($order->isTerminal()) {
            throw ValidationException::withMessages([
                'workflow' => __('messages.order_already_closed'),
            ]);
        }

        return DB::transaction(function () use ($order, $actor, $note) {
            $order->syncLifecycleState(OrderRequest::WORKFLOW_CANCELLED);
            $order->save();

            OrderOffer::query()
                ->where('order_request_id', $order->id)
                ->whereIn('status', [OrderOffer::STATUS_ACTIVE, OrderOffer::STATUS_AWARDED])
                ->update(['status' => OrderOffer::STATUS_ARCHIVED, 'updated_at' => now()]);

            if ($order->contract) {
                $this->contracts->transition($order->contract, OrderContract::STATUS_CANCELLED, [
                    'completion_note' => $note,
                ]);
            }

            if ($actor->isAdmin() && (int) $order->uid !== (int) $actor->id) {
                $this->notifications->send(
                    $order->uid,
                    __('messages.order_cancelled_by_admin', ['title' => $order->title]),
                    route('orders.show', $order->id),
                    'info',
                    $actor->id
                );
            }

            return $order->fresh(['contract', 'awardedOffer.user']);
        });
    }

    public function close(OrderRequest $order, User $actor): OrderRequest
    {
        $canClose = (int) $order->uid === (int) $actor->id || $actor->isAdmin();
        if (!$canClose) {
            throw new AuthorizationException();
        }

        if ((string) $order->workflow_status !== OrderRequest::WORKFLOW_OPEN) {
            throw ValidationException::withMessages([
                'workflow' => __('messages.order_close_only_open'),
            ]);
        }

        return DB::transaction(function () use ($order, $actor) {
            $order->syncLifecycleState(OrderRequest::WORKFLOW_CLOSED);
            $order->save();

            OrderOffer::query()
                ->where('order_request_id', $order->id)
                ->where('status', OrderOffer::STATUS_ACTIVE)
                ->update(['status' => OrderOffer::STATUS_ARCHIVED, 'updated_at' => now()]);

            if ($actor->isAdmin() && (int) $order->uid !== (int) $actor->id) {
                $this->notifications->send(
                    $order->uid,
                    __('messages.order_closed_by_admin', ['title' => $order->title]),
                    route('orders.show', $order->id),
                    'info',
                    $actor->id
                );
            }

            return $order->refresh();
        });
    }

    private function applyRating(OrderRequest $order, ?int $rating, ?string $review = null): void
    {
        if (!$order->awardedOffer || $rating === null) {
            return;
        }

        $order->awardedOffer->client_rating = max(1, min(5, $rating));
        $order->awardedOffer->client_review = $review;
        $order->awardedOffer->rated_at = now();
        $order->awardedOffer->save();

        $order->avg_rating = (float) $order->awardedOffer->client_rating;
        $order->save();

        if ((int) $order->awardedOffer->client_rating === 5) {
            $this->gamification->recordEvent($order->awardedOffer->user_id, 'five_star_rating_received');
        } else {
            $this->gamification->refreshBadges($order->awardedOffer->user_id);
        }
    }

    private function assertProvider(OrderContract $contract, User $actor): void
    {
        if ((int) $contract->provider_user_id !== (int) $actor->id) {
            throw new AuthorizationException();
        }
    }
}
