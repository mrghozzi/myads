<?php

namespace App\Http\Controllers;

use App\Models\CustomAdCreative;
use App\Models\CustomAdDeal;
use App\Models\CustomAdPlacement;
use App\Services\CustomAds\CustomAdSettlementService;
use App\Support\CustomAdsSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCustomAdsController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $placements = CustomAdPlacement::query()
            ->with('user')
            ->withCount('deals')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('placement_key', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', '%' . $search . '%'));
            })
            ->latest('id')
            ->paginate(12, ['*'], 'placements_page')
            ->withQueryString();

        $deals = CustomAdDeal::query()
            ->with(['placement', 'publisher', 'advertiser', 'creative'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('placement', fn ($placementQuery) => $placementQuery->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('publisher', fn ($userQuery) => $userQuery->where('username', 'like', '%' . $search . '%'))
                    ->orWhereHas('advertiser', fn ($userQuery) => $userQuery->where('username', 'like', '%' . $search . '%'));
            })
            ->latest('id')
            ->paginate(12, ['*'], 'deals_page')
            ->withQueryString();

        $summary = [
            'placements' => CustomAdPlacement::count(),
            'deals' => CustomAdDeal::count(),
            'active_deals' => CustomAdDeal::where('status', CustomAdDeal::STATUS_ACTIVE)->count(),
            'pending_creatives' => CustomAdCreative::where('status', CustomAdCreative::STATUS_PENDING)->count(),
        ];

        return view('admin::admin.custom_ads.index', compact('placements', 'deals', 'summary', 'search'));
    }

    public function settings()
    {
        $settings = CustomAdsSettings::all();

        return view('admin::admin.custom_ads.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            CustomAdsSettings::MIN_TOTAL_PTS => ['required', 'numeric', 'min:0'],
            CustomAdsSettings::MIN_DAILY_PTS => ['required', 'numeric', 'min:0'],
            CustomAdsSettings::MAX_DURATION_DAYS => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        CustomAdsSettings::persist(array_merge($validated, [
            CustomAdsSettings::ENABLED => $request->boolean(CustomAdsSettings::ENABLED) ? '1' : '0',
            CustomAdsSettings::MARKETPLACE_ENABLED => $request->boolean(CustomAdsSettings::MARKETPLACE_ENABLED) ? '1' : '0',
            CustomAdsSettings::REQUIRE_REVIEW => $request->boolean(CustomAdsSettings::REQUIRE_REVIEW) ? '1' : '0',
        ]));

        return redirect()->route('admin.custom_ads.settings')
            ->with('success', __('messages.custom_ads_settings_saved'));
    }

    public function updateDealStatus(Request $request, CustomAdDeal $deal, CustomAdSettlementService $settlement)
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in([
                'pause',
                'resume',
                'cancel',
                'complete',
                'approve_creative',
                'reject_creative',
            ])],
        ]);

        match ($validated['action']) {
            'pause' => $this->setDealStatus($deal, CustomAdDeal::STATUS_PAUSED, [CustomAdDeal::STATUS_ACTIVE]),
            'resume' => $this->setDealStatus($deal, CustomAdDeal::STATUS_ACTIVE, [CustomAdDeal::STATUS_PAUSED]),
            'cancel' => $settlement->cancel($deal, auth()->user()),
            'complete' => $settlement->complete($deal),
            'approve_creative' => $deal->creative?->update(['status' => CustomAdCreative::STATUS_APPROVED]),
            'reject_creative' => $deal->creative?->update(['status' => CustomAdCreative::STATUS_REJECTED]),
        };

        return back()->with('success', __('messages.custom_ads_admin_status_updated'));
    }

    public function updatePlacementStatus(Request $request, CustomAdPlacement $placement)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                CustomAdPlacement::STATUS_ACTIVE,
                CustomAdPlacement::STATUS_PAUSED,
                CustomAdPlacement::STATUS_DISABLED,
            ])],
        ]);

        $placement->update(['status' => $validated['status']]);

        return back()->with('success', __('messages.custom_ads_admin_status_updated'));
    }

    private function setDealStatus(CustomAdDeal $deal, string $status, array $allowedCurrent): void
    {
        if (in_array((string) $deal->status, $allowedCurrent, true)) {
            $deal->update(['status' => $status]);
        }
    }
}
