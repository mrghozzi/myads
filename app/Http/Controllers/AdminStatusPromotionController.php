<?php

namespace App\Http\Controllers;

use App\Services\StatusPromotionService;
use App\Services\V420SchemaService;
use App\Support\StatusPromotionSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminStatusPromotionController extends Controller
{
    public function __construct(
        private readonly StatusPromotionService $promotionService,
        private readonly V420SchemaService $schema
    ) {
    }

    public function index(Request $request)
    {
        $featureAvailable = $this->schema->supports('post_promotions');
        $upgradeNotice = $this->schema->notice('post_promotions', __('messages.status_promotions_title'));
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $objective = trim((string) $request->query('objective', ''));
        $promotions = $featureAvailable
            ? $this->promotionService->adminPromotions($search, $status, $objective)
            : new LengthAwarePaginator([], 0, 20);

        return view('admin::admin.status_promotions.index', compact(
            'promotions',
            'featureAvailable',
            'upgradeNotice',
            'search',
            'status',
            'objective'
        ));
    }

    public function settings()
    {
        $featureAvailable = $this->schema->supports('post_promotions');
        $upgradeNotice = $this->schema->notice('post_promotions', __('messages.status_promotions_title'));
        $settings = StatusPromotionSettings::all();

        return view('admin::admin.status_promotions.settings', compact(
            'featureAvailable',
            'upgradeNotice',
            'settings'
        ));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        if (!$this->schema->supports('post_promotions')) {
            return redirect()->route('admin.ads.posts.settings')
                ->with('error', $this->schema->blockedActionMessage('post_promotions', __('messages.status_promotions_title')));
        }

        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'price_per_100_views_pts' => 'required|numeric|min:0|max:999999',
            'price_per_reaction_goal_pts' => 'required|numeric|min:0|max:999999',
            'price_per_comment_goal_pts' => 'required|numeric|min:0|max:999999',
            'price_per_day_pts' => 'required|numeric|min:0|max:999999',
            'estimated_views_per_reaction' => 'required|integer|min:1|max:999999',
            'estimated_views_per_comment' => 'required|integer|min:1|max:999999',
            'estimated_views_per_day' => 'required|integer|min:1|max:999999',
            'min_views_target' => 'required|integer|min:1|max:999999',
            'max_views_target' => 'required|integer|min:1|max:999999',
            'min_reactions_target' => 'required|integer|min:1|max:999999',
            'max_reactions_target' => 'required|integer|min:1|max:999999',
            'min_comments_target' => 'required|integer|min:1|max:999999',
            'max_comments_target' => 'required|integer|min:1|max:999999',
            'min_days_target' => 'required|integer|min:1|max:365',
            'max_days_target' => 'required|integer|min:1|max:365',
            'per_page_limit' => 'required|integer|min:1|max:10',
            'min_gap_between_promotions' => 'required|integer|min:1|max:30',
            'viewer_repeat_cooldown_hours' => 'required|integer|min:0|max:720',
        ]);

        StatusPromotionSettings::save($validated);

        return redirect()->route('admin.ads.posts.settings')
            ->with('success', __('messages.status_promotion_settings_saved'));
    }

    public function updateStatus(Request $request, int $promotion): RedirectResponse
    {
        if (!$this->schema->supports('post_promotions')) {
            return redirect()->route('admin.ads.posts.index')
                ->with('error', $this->schema->blockedActionMessage('post_promotions', __('messages.status_promotions_title')));
        }

        $validated = $request->validate([
            'action' => 'required|string|in:pause,resume,complete',
        ]);

        $updated = $this->promotionService->applyAdminAction($promotion, (string) $validated['action']);
        if (!$updated) {
            return redirect()->route('admin.ads.posts.index')
                ->with('error', __('messages.status_promotion_not_found'));
        }

        return redirect()->route('admin.ads.posts.index')
            ->with('success', __('messages.status_promotion_admin_status_updated'));
    }
}
