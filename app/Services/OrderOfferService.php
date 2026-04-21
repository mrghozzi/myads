<?php

namespace App\Services;

use App\Models\OrderOffer;
use App\Models\OrderRequest;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class OrderOfferService
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly PointLedgerService $points,
        private readonly GamificationService $gamification
    ) {
    }

    public function create(OrderRequest $order, User $user, array $attributes): OrderOffer
    {
        if ((int) $order->uid === (int) $user->id) {
            throw ValidationException::withMessages([
                'offer' => __('messages.order_offer_self_forbidden'),
            ]);
        }

        if (!$order->isOpenForOffers()) {
            throw ValidationException::withMessages([
                'offer' => __('messages.order_offer_closed'),
            ]);
        }

        $existing = OrderOffer::query()
            ->where('order_request_id', $order->id)
            ->where('user_id', $user->id)
            ->whereIn('status', [OrderOffer::STATUS_ACTIVE, OrderOffer::STATUS_AWARDED])
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'offer' => __('messages.order_offer_duplicate'),
            ]);
        }

        $offer = OrderOffer::create([
            'order_request_id' => $order->id,
            'user_id' => $user->id,
            'pricing_model' => $attributes['pricing_model'],
            'quoted_amount' => $attributes['quoted_amount'],
            'currency_code' => $attributes['currency_code'],
            'delivery_days' => $attributes['delivery_days'],
            'message' => $attributes['message'],
            'status' => OrderOffer::STATUS_ACTIVE,
        ]);

        $order->last_activity = time();
        $order->save();

        $this->points->award($user, 5, 'order_offer_created', 'points_awarded', 'order_offer', $offer->id);
        $this->gamification->recordEvent($user->id, 'order_offer_created');

        if ((int) $order->uid !== (int) $user->id) {
            $this->notifications->send(
                $order->uid,
                __('messages.order_offer_received_notification', [
                    'user' => $user->username,
                    'title' => $order->title,
                ]),
                route('orders.show', $order->id),
                'comment',
                $user->id
            );
        }

        return $offer->fresh(['user', 'order']);
    }

    public function update(OrderOffer $offer, User $user, array $attributes): OrderOffer
    {
        if ((int) $offer->user_id !== (int) $user->id) {
            throw new AuthorizationException();
        }

        $offer->loadMissing('order');
        if (!$offer->isEditable()) {
            throw ValidationException::withMessages([
                'offer' => __('messages.order_offer_not_editable'),
            ]);
        }

        $offer->fill([
            'pricing_model' => $attributes['pricing_model'],
            'quoted_amount' => $attributes['quoted_amount'],
            'currency_code' => $attributes['currency_code'],
            'delivery_days' => $attributes['delivery_days'],
            'message' => $attributes['message'],
        ]);
        $offer->save();

        $offer->order->last_activity = time();
        $offer->order->save();

        return $offer->refresh();
    }

    public function withdraw(OrderOffer $offer, User $user): void
    {
        if ((int) $offer->user_id !== (int) $user->id && !$user->isAdmin()) {
            throw new AuthorizationException();
        }

        if ($offer->status !== OrderOffer::STATUS_ACTIVE) {
            throw ValidationException::withMessages([
                'offer' => __('messages.order_offer_not_withdrawable'),
            ]);
        }

        $offer->status = OrderOffer::STATUS_WITHDRAWN;
        $offer->withdrawn_at = now();
        $offer->save();

        if ($offer->relationLoaded('order') || $offer->order) {
            $offer->order->last_activity = time();
            $offer->order->save();
        }
    }
}
