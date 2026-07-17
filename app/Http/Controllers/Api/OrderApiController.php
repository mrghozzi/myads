<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderRequest;
use App\Models\OrderOffer;
use App\Services\OrderWorkflowService;
use App\Support\OrderCategoryOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderApiController extends Controller
{
    public function __construct(
        private readonly OrderWorkflowService $workflow
    ) {
    }

    public function index(Request $request)
    {
        $sort = (string) $request->input('sort', 'newest');
        $search = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));
        $status = trim((string) $request->input('status', 'all'));

        $query = OrderRequest::query()
            ->with(['user'])
            ->withCount([
                'offers as offers_count' => fn ($q) => $q->marketplaceVisible(),
            ])
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($category !== '', fn ($builder) => $builder->where('category', $category))
            ->when($status !== '' && $status !== 'all', function ($builder) use ($status) {
                if ($status === 'under_review') {
                    $builder->where('workflow_status', OrderRequest::WORKFLOW_OPEN)
                        ->whereHas('offers', fn ($offerQuery) => $offerQuery->marketplaceVisible());
                    return;
                }
                $builder->where('workflow_status', $status);
            });

        match ($sort) {
            'active' => $query->orderByDesc('last_activity'),
            'popular' => $query->orderByDesc('offers_count')->orderByDesc('last_activity'),
            'budget_high' => $query->orderByDesc('budget_max')->orderByDesc('date'),
            'budget_low' => $query->orderBy('budget_min')->orderByDesc('date'),
            default => $query->orderByDesc('date'),
        };

        $orders = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function show($id)
    {
        $order = OrderRequest::with([
            'user',
            'offers' => fn ($query) => $query->marketplaceVisible()->with('user')->latest('created_at'),
            'awardedOffer.user',
            'contract.provider',
        ])->withCount([
            'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
        ])->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => __('messages.not_found')], 404);
        }

        $viewerOffer = null;
        if (Auth::check()) {
            $viewerOffer = $order->offers->firstWhere('user_id', Auth::id());
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'viewer_offer' => $viewerOffer
            ]
        ]);
    }

    public function submitOffer(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);

        if ((int) $order->uid === (int) Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot offer on your own order'], 403);
        }

        if ((string) $order->workflow_status !== OrderRequest::WORKFLOW_OPEN) {
            return response()->json(['success' => false, 'message' => 'Order is not open for offers'], 400);
        }

        $request->validate([
            'content' => ['required', 'string', 'max:5000'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],
            'delivery_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $service = app(\App\Services\OrderOfferService::class);
        
        try {
            $offer = $service->submit(
                Auth::user(),
                $order,
                $request->input('content'),
                $request->filled('price') ? (float) $request->input('price') : null,
                $request->input('currency'),
                $request->filled('delivery_days') ? (int) $request->input('delivery_days') : null
            );

            return response()->json(['success' => true, 'message' => __('messages.offer_submitted_successfully'), 'data' => $offer]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
