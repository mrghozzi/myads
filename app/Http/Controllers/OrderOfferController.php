<?php

namespace App\Http\Controllers;

use App\Models\OrderOffer;
use App\Models\OrderRequest;
use App\Services\OrderOfferService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderOfferController extends Controller
{
    public function __construct(
        private readonly OrderOfferService $offers
    ) {
    }

    public function store(Request $request, OrderRequest $order)
    {
        $offer = $this->offers->create($order, $request->user(), $this->validatePayload($request));

        return redirect()->route('orders.show', $order)
            ->with('success', __('messages.order_offer_created_successfully'))
            ->with('highlight_offer', $offer->id);
    }

    public function update(Request $request, OrderOffer $offer)
    {
        $this->offers->update($offer, $request->user(), $this->validatePayload($request));

        return redirect()->route('orders.show', $offer->order_request_id)
            ->with('success', __('messages.order_offer_updated_successfully'))
            ->with('highlight_offer', $offer->id);
    }

    public function destroy(Request $request, OrderOffer $offer)
    {
        $orderId = $offer->order_request_id;
        $offerId = $offer->id;

        $this->offers->withdraw($offer, $request->user());

        return redirect()->route('orders.show', $orderId)
            ->with('success', __('messages.order_offer_withdrawn_successfully'))
            ->with('highlight_offer', $offerId);
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'pricing_model' => ['required', Rule::in(['fixed', 'hourly', 'negotiable'])],
            'quoted_amount' => ['nullable', 'numeric', 'min:0'],
            'currency_code' => ['required', Rule::in(['USD', 'EUR', 'GBP', 'PTS'])],
            'delivery_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'message' => ['required', 'string', 'max:8000'],
        ]);
    }
}
