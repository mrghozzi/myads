<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Services\StatusPromotionService;
use App\Services\V420SchemaService;
use App\Support\StatusPromotionSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StatusPromotionController extends Controller
{
    public function __construct(
        private readonly StatusPromotionService $promotionService,
        private readonly V420SchemaService $schema
    ) {
    }

    public function index()
    {
        $user = Auth::user();
        $featureAvailable = $this->schema->supports('post_promotions');
        $upgradeNotice = $this->schema->notice('post_promotions', __('messages.status_promotions_title'));
        $promotions = $featureAvailable
            ? $this->promotionService->memberPromotions($user)
            : new LengthAwarePaginator([], 0, 20);

        return view('theme::ads.posts.index', compact(
            'promotions',
            'featureAvailable',
            'upgradeNotice'
        ));
    }

    public function create(Status $status)
    {
        $user = Auth::user();
        $featureAvailable = $this->schema->supports('post_promotions');
        $upgradeNotice = $this->schema->notice('post_promotions', __('messages.status_promotions_title'));
        $settings = StatusPromotionSettings::all();
        $existingPromotion = null;
        $quote = null;

        if ($featureAvailable) {
            try {
                $status = $this->promotionService->ensurePromotableStatus($status, $user);
                $existingPromotion = $this->promotionService->ongoingPromotionForStatus($status);
                $quote = $this->promotionService->quoteForStatus(
                    $status,
                    $user,
                    'views',
                    (int) $settings['min_views_target']
                );
            } catch (ValidationException $exception) {
                return redirect()->route('ads.posts.index')
                    ->withErrors($exception->errors());
            }
        }

        return view('theme::ads.posts.create', compact(
            'status',
            'featureAvailable',
            'upgradeNotice',
            'settings',
            'existingPromotion',
            'quote'
        ));
    }

    public function quote(Request $request, Status $status): JsonResponse
    {
        if (!$this->schema->supports('post_promotions')) {
            return response()->json([
                'message' => $this->schema->blockedActionMessage('post_promotions', __('messages.status_promotions_title')),
            ], 409);
        }

        $validated = $request->validate([
            'objective' => 'required|string|in:views,comments,reactions,days',
            'target_quantity' => 'required|integer|min:1',
        ]);

        $quote = $this->promotionService->quoteForStatus(
            $status,
            Auth::user(),
            (string) $validated['objective'],
            (int) $validated['target_quantity']
        );

        return response()->json([
            'quote' => $quote,
            'balance_pts' => (int) Auth::user()->pts,
            'can_afford' => (int) Auth::user()->pts >= (int) $quote['charged_pts'],
        ]);
    }

    public function store(Request $request, Status $status): RedirectResponse
    {
        if (!$this->schema->supports('post_promotions')) {
            return redirect()->route('ads.posts.index')
                ->with('error', $this->schema->blockedActionMessage('post_promotions', __('messages.status_promotions_title')));
        }

        $validated = $request->validate([
            'objective' => 'required|string|in:views,comments,reactions,days',
            'target_quantity' => 'required|integer|min:1',
        ]);

        $this->promotionService->createPromotion(
            $status,
            Auth::user(),
            (string) $validated['objective'],
            (int) $validated['target_quantity']
        );

        return redirect()->route('ads.posts.index')
            ->with('success', __('messages.status_promotion_created'));
    }
}
