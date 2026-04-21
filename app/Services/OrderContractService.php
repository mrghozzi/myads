<?php

namespace App\Services;

use App\Models\OrderContract;
use App\Models\OrderOffer;
use App\Models\OrderRequest;

class OrderContractService
{
    public function createOrRefreshFromOffer(OrderRequest $order, OrderOffer $offer): OrderContract
    {
        $snapshot = [
            'order' => [
                'id' => $order->id,
                'title' => $order->title,
                'description' => $order->description,
                'category' => $order->category,
                'pricing_model' => $order->pricing_model,
                'budget' => [
                    'min' => $order->budget_min,
                    'max' => $order->budget_max,
                    'currency' => $order->budget_currency,
                ],
                'delivery_window_days' => $order->delivery_window_days,
            ],
            'offer' => [
                'id' => $offer->id,
                'provider_user_id' => $offer->user_id,
                'pricing_model' => $offer->pricing_model,
                'quoted_amount' => $offer->quoted_amount,
                'currency_code' => $offer->currency_code,
                'delivery_days' => $offer->delivery_days,
                'message' => $offer->message,
            ],
        ];

        return OrderContract::updateOrCreate(
            ['order_request_id' => $order->id],
            [
                'order_offer_id' => $offer->id,
                'client_user_id' => $order->uid,
                'provider_user_id' => $offer->user_id,
                'status' => OrderContract::STATUS_AWARDED,
                'pricing_model' => $offer->pricing_model,
                'quoted_amount' => $offer->quoted_amount,
                'currency_code' => $offer->currency_code,
                'delivery_days' => $offer->delivery_days,
                'snapshot_payload' => $snapshot,
                'awarded_at' => now(),
            ]
        );
    }

    public function transition(OrderContract $contract, string $status, array $attributes = []): OrderContract
    {
        $payload = ['status' => $status];

        if ($status === OrderContract::STATUS_IN_PROGRESS) {
            $payload['started_at'] = $contract->started_at ?: now();
        }

        if ($status === OrderContract::STATUS_DELIVERED) {
            $payload['delivered_at'] = now();
            $payload['delivery_note'] = $attributes['delivery_note'] ?? $contract->delivery_note;
        }

        if ($status === OrderContract::STATUS_COMPLETED) {
            $payload['completed_at'] = now();
            $payload['completion_note'] = $attributes['completion_note'] ?? $contract->completion_note;
        }

        if ($status === OrderContract::STATUS_CANCELLED) {
            $payload['cancelled_at'] = now();
            $payload['completion_note'] = $attributes['completion_note'] ?? $contract->completion_note;
        }

        $contract->fill($payload);
        $contract->save();

        return $contract->refresh();
    }
}
