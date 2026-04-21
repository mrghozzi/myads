<?php

namespace App\Http\Controllers;

use App\Models\OrderOffer;
use App\Models\OrderRequest;
use App\Models\Status;
use App\Support\OrderCategoryOptions;
use App\Services\OrderWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderRequestController extends Controller
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

        $query = $this->baseListingQuery()
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

        $orders = $query->paginate(12)->withQueryString();

        $this->seo([
            'scope_key' => 'orders_index',
            'resource_title' => __('messages.order_requests'),
            'description' => __('messages.order_requests_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.order_requests'), 'url' => route('orders.index')],
            ],
        ]);

        return view('theme::orders.index', [
            'orders' => $orders,
            'categories' => OrderCategoryOptions::all(),
            'filters' => compact('sort', 'search', 'category', 'status'),
            'filterAction' => route('orders.index'),
            'pageTitle' => __('messages.order_requests'),
            'pageSubtitle' => __('messages.browse_latest_orders'),
            'showCreateCta' => true,
        ]);
    }

    public function mine(Request $request)
    {
        $sort = (string) $request->input('sort', 'active');
        $search = trim((string) $request->input('search', ''));
        $category = trim((string) $request->input('category', ''));
        $status = trim((string) $request->input('status', 'all'));

        $query = $this->baseListingQuery()
            ->where('uid', Auth::id())
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
            'popular' => $query->orderByDesc('offers_count')->orderByDesc('last_activity'),
            'budget_high' => $query->orderByDesc('budget_max')->orderByDesc('date'),
            'budget_low' => $query->orderBy('budget_min')->orderByDesc('date'),
            'newest' => $query->orderByDesc('date'),
            default => $query->orderByDesc('last_activity'),
        };

        $orders = $query->paginate(12)->withQueryString();

        return view('theme::orders.index', [
            'orders' => $orders,
            'categories' => OrderCategoryOptions::all(),
            'filters' => compact('sort', 'search', 'category', 'status'),
            'filterAction' => route('orders.mine'),
            'pageTitle' => __('messages.my_orders'),
            'pageSubtitle' => __('messages.order_dashboard_owner_subtitle'),
            'showCreateCta' => true,
        ]);
    }

    public function offers(Request $request)
    {
        $offers = OrderOffer::query()
            ->with(['order.user', 'contract'])
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('theme::orders.offers', [
            'offers' => $offers,
        ]);
    }

    public function create()
    {
        return view('theme::orders.form', [
            'order' => new OrderRequest([
                'pricing_model' => OrderRequest::PRICING_FIXED,
                'budget_currency' => 'USD',
            ]),
            'categories' => OrderCategoryOptions::all(),
            'currencies' => $this->currencies(),
            'isEditing' => false,
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->normalizeOrderPayload($this->validateOrderPayload($request));
        $ledger = app(\App\Services\PointLedgerService::class);

        DB::beginTransaction();

        try {
            $time = time();
            $order = OrderRequest::create([
                'uid' => Auth::id(),
                'title' => $payload['title'],
                'description' => $payload['description'],
                'budget' => $this->legacyBudgetText($payload),
                'category' => $payload['category'],
                'pricing_model' => $payload['pricing_model'],
                'budget_min' => $payload['budget_min'],
                'budget_max' => $payload['budget_max'],
                'budget_currency' => $payload['budget_currency'],
                'delivery_window_days' => $payload['delivery_window_days'],
                'date' => $time,
                'statu' => 1,
                'best_offer_id' => null,
                'last_activity' => $time,
                'avg_rating' => 0,
                'workflow_status' => OrderRequest::WORKFLOW_OPEN,
            ]);

            Status::create([
                'uid' => Auth::id(),
                'tp_id' => $order->id,
                's_type' => 6,
                'date' => $time,
                'txt' => $order->title,
                'statu' => 1,
            ]);

            DB::commit();

            $ledger->award(Auth::user(), 10, 'order_posted', 'points_awarded', 'order', $order->id);
            app(\App\Services\GamificationService::class)->recordEvent(Auth::id(), 'order_request_created');

            return redirect()->route('orders.show', $order)->with('success', __('messages.order_created_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('errMSG', $e->getMessage())->withInput();
        }
    }

    public function show(OrderRequest $order)
    {
        $order->load([
            'user',
            'offers' => fn ($query) => $query->marketplaceVisible()->with('user')->latest('created_at'),
            'awardedOffer.user',
            'contract.provider',
            'statusRecord.user',
        ])->loadCount([
            'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
        ]);

        $viewerOffer = null;
        if (Auth::check()) {
            $viewerOffer = $order->offers
                ->firstWhere('user_id', Auth::id());
        }

        $this->seo([
            'scope_key' => 'order_show',
            'resource_title' => $order->title,
            'description' => $order->description,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.order_requests'), 'url' => route('orders.index')],
                ['name' => $order->title, 'url' => route('orders.show', $order)],
            ],
        ]);

        return view('theme::orders.show', [
            'order' => $order,
            'viewerOffer' => $viewerOffer,
            'currencies' => $this->currencies(),
        ]);
    }

    public function edit(OrderRequest $order)
    {
        $this->authorizeOwner($order);

        if ($order->isManagedWorkflow() || (string) $order->workflow_status === OrderRequest::WORKFLOW_COMPLETED) {
            abort(403);
        }

        return view('theme::orders.form', [
            'order' => $order,
            'categories' => OrderCategoryOptions::all(),
            'currencies' => $this->currencies(),
            'isEditing' => true,
        ]);
    }

    public function update(Request $request, OrderRequest $order)
    {
        $this->authorizeOwner($order);

        if ($order->isManagedWorkflow() || (string) $order->workflow_status === OrderRequest::WORKFLOW_COMPLETED) {
            abort(403);
        }

        $payload = $this->normalizeOrderPayload($this->validateOrderPayload($request));

        $order->fill([
            'title' => $payload['title'],
            'description' => $payload['description'],
            'budget' => $this->legacyBudgetText($payload),
            'category' => $payload['category'],
            'pricing_model' => $payload['pricing_model'],
            'budget_min' => $payload['budget_min'],
            'budget_max' => $payload['budget_max'],
            'budget_currency' => $payload['budget_currency'],
            'delivery_window_days' => $payload['delivery_window_days'],
        ]);
        $order->syncLifecycleState(OrderRequest::WORKFLOW_OPEN);
        $order->save();

        $order->statusRecord()?->update([
            'txt' => $order->title,
            'statu' => 1,
        ]);

        return redirect()->route('orders.show', $order)->with('success', __('messages.order_updated_successfully'));
    }

    public function award(Request $request, OrderRequest $order)
    {
        $request->validate([
            'offer_id' => ['required', 'integer', Rule::exists('order_offers', 'id')],
        ]);

        $offer = OrderOffer::query()->findOrFail((int) $request->input('offer_id'));
        $this->workflow->award($order, $offer, Auth::user());

        return back()->with('success', __('messages.best_offer_selected'));
    }

    public function start(OrderRequest $order)
    {
        $this->workflow->start($order, Auth::user());

        return back()->with('success', __('messages.order_started_successfully'));
    }

    public function deliver(Request $request, OrderRequest $order)
    {
        $request->validate([
            'delivery_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->workflow->deliver($order, Auth::user(), $request->input('delivery_note'));

        return back()->with('success', __('messages.order_delivered_successfully'));
    }

    public function complete(Request $request, OrderRequest $order)
    {
        $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->workflow->complete(
            $order,
            Auth::user(),
            $request->filled('rating') ? (int) $request->input('rating') : null,
            $request->input('review')
        );

        return back()->with('success', __('messages.order_completed_successfully'));
    }

    public function rate(Request $request, OrderRequest $order)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->workflow->rate(
            $order,
            Auth::user(),
            (int) $request->input('rating'),
            $request->input('review')
        );

        return back()->with('success', __('messages.rating_submitted'));
    }

    public function cancel(Request $request, OrderRequest $order)
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->workflow->cancel($order, Auth::user(), $request->input('note'));

        return back()->with('success', __('messages.order_cancelled_successfully'));
    }

    public function close(OrderRequest $order)
    {
        $this->workflow->close($order, Auth::user());

        return back()->with('success', __('messages.order_closed_successfully'));
    }

    public function destroy(OrderRequest $order)
    {
        if ((int) $order->uid !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $orderId = $order->id;

            Status::where('tp_id', $orderId)->where('s_type', 6)->delete();
            OrderOffer::where('order_request_id', $orderId)->delete();
            \App\Models\Like::where('sid', $orderId)->where('type', 6)->delete();
            $order->contract()->delete();
            \App\Models\Option::where('o_parent', $orderId)->whereIn('o_type', ['o_order', 'order_comment'])->delete();
            $order->delete();

            DB::commit();

            return redirect()->route('orders.index')->with('success', __('messages.order_deleted_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('errMSG', $e->getMessage());
        }
    }

    private function baseListingQuery()
    {
        return OrderRequest::query()
            ->with(['user', 'awardedOffer.user'])
            ->withCount([
                'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
            ]);
    }

    private function validateOrderPayload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'category' => ['nullable', 'string', 'max:100'],
            'pricing_model' => ['required', Rule::in([
                OrderRequest::PRICING_FIXED,
                OrderRequest::PRICING_RANGE,
                OrderRequest::PRICING_NEGOTIABLE,
            ])],
            'budget_min' => ['nullable', 'numeric', 'min:0'],
            'budget_max' => ['nullable', 'numeric', 'min:0'],
            'budget_currency' => ['required', Rule::in(array_keys($this->currencies()))],
            'delivery_window_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);
    }

    private function normalizeOrderPayload(array $payload): array
    {
        $pricingModel = $payload['pricing_model'];
        $budgetMin = $payload['budget_min'] !== null ? (float) $payload['budget_min'] : null;
        $budgetMax = $payload['budget_max'] !== null ? (float) $payload['budget_max'] : null;

        if ($pricingModel === OrderRequest::PRICING_NEGOTIABLE) {
            $budgetMin = null;
            $budgetMax = null;
        } elseif ($budgetMin !== null && $budgetMax !== null && $budgetMin > $budgetMax) {
            [$budgetMin, $budgetMax] = [$budgetMax, $budgetMin];
        } elseif ($budgetMin !== null && $budgetMax === null) {
            $budgetMax = $budgetMin;
        } elseif ($budgetMin === null && $budgetMax !== null) {
            $budgetMin = $budgetMax;
        }

        $category = collect(OrderCategoryOptions::all())
            ->pluck('slug')
            ->contains($payload['category'])
            ? $payload['category']
            : 'uncategorized';

        return [
            'title' => $payload['title'],
            'description' => $payload['description'],
            'category' => $category,
            'pricing_model' => $pricingModel,
            'budget_min' => $budgetMin,
            'budget_max' => $budgetMax,
            'budget_currency' => $payload['budget_currency'],
            'delivery_window_days' => $payload['delivery_window_days'] !== null
                ? (int) $payload['delivery_window_days']
                : null,
        ];
    }

    private function legacyBudgetText(array $payload): string
    {
        if ($payload['pricing_model'] === OrderRequest::PRICING_NEGOTIABLE || ($payload['budget_min'] === null && $payload['budget_max'] === null)) {
            return __('messages.order_budget_negotiable');
        }

        $currency = $payload['budget_currency'];
        $min = $payload['budget_min'];
        $max = $payload['budget_max'];

        if ($min !== null && $max !== null && abs($min - $max) < 0.01) {
            return $currency . ' ' . number_format($min, 2);
        }

        return __('messages.order_budget_range_value', [
            'currency' => $currency,
            'min' => number_format((float) $min, 2),
            'max' => number_format((float) $max, 2),
        ]);
    }

    private function currencies(): array
    {
        return [
            'USD' => 'USD',
            'EUR' => 'EUR',
            'GBP' => 'GBP',
            'PTS' => 'PTS',
        ];
    }

    private function authorizeOwner(OrderRequest $order): void
    {
        if ((int) $order->uid !== (int) Auth::id()) {
            abort(403);
        }
    }
}
