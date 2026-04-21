<?php

namespace App\Http\Controllers;

use App\Models\OrderRequest;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function __construct(
        private readonly OrderWorkflowService $workflow
    ) {
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', 'all'));
        $category = trim((string) $request->input('category', ''));

        $orders = OrderRequest::query()
            ->with(['user', 'awardedOffer.user'])
            ->withCount([
                'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', '%' . $search . '%'));
                });
            })
            ->when($status !== '' && $status !== 'all', fn ($query) => $query->where('workflow_status', $status))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderByDesc('last_activity')
            ->paginate(20)
            ->withQueryString();

        return view('admin::admin.orders.index', [
            'orders' => $orders,
            'filters' => compact('search', 'status', 'category'),
        ]);
    }

    public function show(OrderRequest $order)
    {
        $order->load([
            'user',
            'offers' => fn ($query) => $query->marketplaceVisible()->with('user')->latest('created_at'),
            'awardedOffer.user',
            'contract.provider',
        ])->loadCount([
            'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
        ]);

        return view('admin::admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function close(OrderRequest $order, Request $request)
    {
        $this->workflow->close($order, $request->user());

        return back()->with('success', __('messages.order_closed_successfully'));
    }

    public function cancel(OrderRequest $order, Request $request)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->workflow->cancel($order, $request->user(), $request->input('note'));

        return back()->with('success', __('messages.order_cancelled_successfully'));
    }
}
