<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Option;
use App\Models\OrderOffer;
use App\Models\OrderRequest;
use App\Models\Status;
use App\Services\OrderWorkflowService;
use App\Support\OrderCategoryOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $sort = trim((string) $request->input('sort', 'newest'));

        $kpis = [
            'total' => OrderRequest::count(),
            'open' => OrderRequest::where('workflow_status', OrderRequest::WORKFLOW_OPEN)->count(),
            'in_progress' => OrderRequest::whereIn('workflow_status', [
                OrderRequest::WORKFLOW_AWARDED,
                OrderRequest::WORKFLOW_IN_PROGRESS,
                OrderRequest::WORKFLOW_DELIVERED,
            ])->count(),
            'completed' => OrderRequest::where('workflow_status', OrderRequest::WORKFLOW_COMPLETED)->count(),
            'cancelled' => OrderRequest::whereIn('workflow_status', [
                OrderRequest::WORKFLOW_CANCELLED,
                OrderRequest::WORKFLOW_CLOSED,
            ])->count(),
            'offers' => OrderOffer::count(),
        ];

        $query = OrderRequest::query()
            ->with(['user', 'awardedOffer.user'])
            ->withCount([
                'offers as offers_count' => fn ($subQuery) => $subQuery->marketplaceVisible(),
            ])
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($nested) use ($search) {
                    if (is_numeric($search)) {
                        $nested->where('id', (int) $search);
                    }
                    $nested->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', '%' . $search . '%'));
                });
            })
            ->when($status !== '' && $status !== 'all', fn ($builder) => $builder->where('workflow_status', $status))
            ->when($category !== '', fn ($builder) => $builder->where('category', $category));

        match ($sort) {
            'active' => $query->orderByDesc('last_activity'),
            'popular' => $query->orderByDesc('offers_count')->orderByDesc('last_activity'),
            'budget_high' => $query->orderByDesc('budget_max')->orderByDesc('date'),
            'budget_low' => $query->orderBy('budget_min')->orderByDesc('date'),
            default => $query->orderByDesc('id'),
        };

        $orders = $query->paginate(20)->withQueryString();

        return view('admin::admin.orders.index', [
            'orders' => $orders,
            'kpis' => $kpis,
            'categories' => OrderCategoryOptions::all(),
            'filters' => compact('search', 'status', 'category', 'sort'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $search = trim((string) $request->input('search', ''));
        $status = trim((string) $request->input('status', 'all'));
        $category = trim((string) $request->input('category', ''));

        $orders = OrderRequest::query()
            ->with(['user', 'awardedOffer.user'])
            ->withCount([
                'offers as offers_count' => fn ($subQuery) => $subQuery->marketplaceVisible(),
            ])
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($nested) use ($search) {
                    if (is_numeric($search)) {
                        $nested->where('id', (int) $search);
                    }
                    $nested->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', '%' . $search . '%'));
                });
            })
            ->when($status !== '' && $status !== 'all', fn ($builder) => $builder->where('workflow_status', $status))
            ->when($category !== '', fn ($builder) => $builder->where('category', $category))
            ->orderByDesc('id')
            ->get();

        $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $output = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel Arabic character compatibility
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, [
                'ID',
                'Title',
                'Client Username',
                'Client Email',
                'Category',
                'Status',
                'Pricing Model',
                'Budget Min',
                'Budget Max',
                'Currency',
                'Offers Count',
                'Created At',
                'Last Activity',
            ]);

            foreach ($orders as $order) {
                fputcsv($output, [
                    $order->id,
                    $order->title,
                    $order->user?->username ?? 'N/A',
                    $order->user?->email ?? 'N/A',
                    $order->category,
                    $order->workflow_status,
                    $order->pricing_model,
                    $order->budget_min,
                    $order->budget_max,
                    $order->budget_currency,
                    $order->offers_count,
                    date('Y-m-d H:i', (int) $order->date),
                    $order->last_activity ? date('Y-m-d H:i', (int) $order->last_activity) : 'N/A',
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(OrderRequest $order)
    {
        $order->load([
            'user',
            'offers' => fn ($query) => $query->with('user')->latest('created_at'),
            'awardedOffer.user',
            'contract.provider',
        ])->loadCount([
            'offers as offers_count' => fn ($query) => $query->marketplaceVisible(),
        ]);

        return view('admin::admin.orders.show', [
            'order' => $order,
            'categories' => OrderCategoryOptions::all(),
        ]);
    }

    public function update(OrderRequest $order, Request $request)
    {
        $validated = $request->validate([
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
            'budget_currency' => ['required', Rule::in(['USD', 'EUR', 'GBP', 'PTS'])],
        ]);

        $this->workflow->updateByAdmin($order, $validated);

        return back()->with('success', __('messages.order_updated_successfully'));
    }

    public function updateAdminNotes(OrderRequest $order, Request $request)
    {
        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $this->workflow->updateAdminNotes($order, $request->input('admin_notes'));

        return back()->with('success', __('messages.order_admin_notes_saved'));
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

    public function destroy(OrderRequest $order)
    {
        DB::beginTransaction();

        try {
            $orderId = $order->id;

            Status::where('tp_id', $orderId)->where('s_type', 6)->delete();
            OrderOffer::where('order_request_id', $orderId)->delete();
            Like::where('sid', $orderId)->where('type', 6)->delete();
            $order->contract()->delete();
            Option::where('o_parent', $orderId)->whereIn('o_type', ['o_order', 'order_comment'])->delete();

            if ($order->attachment_path) {
                $filePath = storage_path('app/' . ltrim($order->attachment_path, '/\\'));
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }

            $order->delete();

            DB::commit();

            return redirect()->route('admin.orders.index')->with('success', __('messages.order_deleted_successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);
            return back()->with('errMSG', __('messages.error_occurred'));
        }
    }

    public function destroyOffer(OrderOffer $offer)
    {
        $order = $offer->order;

        if ($offer->status === OrderOffer::STATUS_AWARDED) {
            return back()->with('errMSG', __('messages.order_offer_awarded_cannot_delete'));
        }

        $offer->delete();

        if ($order) {
            $order->last_activity = time();
            $order->save();
        }

        return back()->with('success', __('messages.order_offer_deleted_successfully'));
    }
}
