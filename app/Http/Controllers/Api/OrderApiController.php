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

        $orders->getCollection()->transform(function ($order) {
            $data = $order->toArray();
            $data['buyer'] = [
                'id' => $order->user?->id,
                'name' => $order->user?->name ?? $order->user?->username ?? 'Unknown',
                'username' => $order->user?->username ?? '',
                'avatar' => $order->user?->avatarUrl() ?? asset('upload/avatar.png'),
            ];
            $data['max_delivery_days'] = $order->delivery_window_days;
            $data['has_attachment'] = $order->hasAttachment();
            $data['is_revision_requested'] = $order->isRevisionRequested();
            return $data;
        });

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

        $buyerData = [
            'id' => $order->user?->id,
            'name' => $order->user?->name ?? $order->user?->username ?? 'Unknown',
            'username' => $order->user?->username ?? '',
            'avatar' => $order->user?->avatarUrl() ?? asset('upload/avatar.png'),
        ];

        $offersData = $order->offers->map(function ($o) {
            $offerArr = $o->toArray();
            $offerArr['txt'] = $o->message;
            $offerArr['price'] = (float) $o->quoted_amount;
            $offerArr['delivery_days'] = (int) $o->delivery_days;
            $offerArr['provider'] = [
                'id' => $o->user?->id,
                'name' => $o->user?->name ?? $o->user?->username ?? 'Unknown',
                'username' => $o->user?->username ?? '',
                'avatar' => $o->user?->avatarUrl() ?? asset('upload/avatar.png'),
            ];
            return $offerArr;
        });

        $contractData = null;
        if ($order->contract) {
            $contractData = array_merge($order->contract->toArray(), [
                'has_delivery_attachment' => $order->contract->hasDeliveryAttachment(),
                'delivery_attachment_name' => $order->contract->delivery_attachment_name,
                'revision_count' => (int) $order->contract->revision_count,
                'revision_note' => $order->contract->revision_note,
                'is_overdue' => $order->contract->isOverdue(),
                'deadline' => $order->contract->deadline?->toIso8601String(),
            ]);
        }

        $orderPayload = array_merge($order->toArray(), [
            'buyer' => $buyerData,
            'max_delivery_days' => $order->delivery_window_days,
            'has_attachment' => $order->hasAttachment(),
            'is_revision_requested' => $order->isRevisionRequested(),
            'attachment_download_url' => $order->hasAttachment() ? route('orders.attachment.download', $order) : null,
            'offers' => $offersData,
            'contract' => $contractData,
        ]);

        return response()->json([
            'success' => true,
            'data' => array_merge($orderPayload, [
                'order' => $orderPayload,
                'viewer_offer' => $viewerOffer,
            ]),
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

        if (!$request->has('content') && $request->has('txt')) {
            $request->merge(['content' => $request->input('txt')]);
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

    public function award(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);
        $request->validate([
            'offer_id' => ['required', 'integer'],
        ]);

        $offer = $order->offers()->findOrFail((int) $request->input('offer_id'));

        try {
            $contract = $this->workflow->award($order, $offer, Auth::user());
            return response()->json([
                'success' => true,
                'message' => __('messages.best_offer_selected'),
                'contract' => $contract,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function start($id)
    {
        $order = OrderRequest::findOrFail($id);

        try {
            $this->workflow->start($order, Auth::user());
            return response()->json([
                'success' => true,
                'message' => __('messages.order_started_successfully'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function deliver(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);
        $request->validate([
            'delivery_note' => ['nullable', 'string', 'max:5000'],
            'delivery_attachment' => ['nullable', 'file', 'max:25600'],
        ]);

        $attachmentData = null;
        if ($request->hasFile('delivery_attachment')) {
            $attachmentData = $this->handleUploadedFile($request->file('delivery_attachment'), 'order_deliverables');
        }

        try {
            $this->workflow->deliver($order, Auth::user(), $request->input('delivery_note'), $attachmentData);
            return response()->json([
                'success' => true,
                'message' => __('messages.order_delivered_successfully'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function revision(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);
        $request->validate([
            'revision_note' => ['required', 'string', 'max:3000'],
        ]);

        try {
            $this->workflow->requestRevision($order, Auth::user(), $request->input('revision_note'));
            return response()->json([
                'success' => true,
                'message' => __('messages.order_revision_requested_successfully'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function complete(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);
        $request->validate([
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->workflow->complete(
                $order,
                Auth::user(),
                $request->filled('rating') ? (int) $request->input('rating') : null,
                $request->input('review')
            );
            return response()->json([
                'success' => true,
                'message' => __('messages.order_completed_successfully'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function cancel(Request $request, $id)
    {
        $order = OrderRequest::findOrFail($id);
        $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->workflow->cancel($order, Auth::user(), $request->input('note'));
            return response()->json([
                'success' => true,
                'message' => __('messages.order_cancelled_successfully'),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    private function handleUploadedFile($file, string $subfolder): array
    {
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');

        $blockedExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'phps', 'phar', 'exe', 'bat', 'cmd', 'sh', 'cgi', 'pl', 'py', 'jsp', 'asp', 'aspx', 'vbs'];
        if (in_array($extension, $blockedExtensions, true)) {
            throw new \RuntimeException(__('messages.file_type_not_allowed') ?? 'File type not allowed.');
        }

        $filename = 'order_' . time() . '_' . \Illuminate\Support\Str::random(12) . ($extension ? '.' . $extension : '');
        $destinationPath = storage_path('app/' . $subfolder);

        if (!is_dir($destinationPath) && !mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
            throw new \RuntimeException('Unable to create attachment directory.');
        }

        $file->move($destinationPath, $filename);

        return [
            'path' => $subfolder . '/' . $filename,
            'name' => $originalName,
        ];
    }
}
