<?php

namespace App\Http\Controllers;

use App\Models\CustomAdCreative;
use App\Models\CustomAdDeal;
use App\Models\CustomAdPlacement;
use App\Models\User;
use App\Services\CustomAds\CustomAdAnalyticsService;
use App\Services\CustomAds\CustomAdSettlementService;
use App\Services\NotificationService;
use App\Services\SecurityPolicyService;
use App\Support\CustomAdsSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomAdsController extends Controller
{
    public function index(CustomAdAnalyticsService $analytics)
    {
        $this->ensureEnabled();

        $user = Auth::user();
        $placements = CustomAdPlacement::query()
            ->withCount(['deals', 'activeDeals'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->get();

        foreach ($placements as $placement) {
            $placement->summary = $analytics->placementSummary($placement);
        }

        $publisherDeals = CustomAdDeal::query()
            ->with(['placement', 'advertiser', 'creative'])
            ->where('publisher_id', $user->id)
            ->latest('id')
            ->limit(12)
            ->get();

        $advertiserDeals = CustomAdDeal::query()
            ->with(['placement.user', 'publisher', 'creative'])
            ->where('advertiser_id', $user->id)
            ->latest('id')
            ->limit(12)
            ->get();

        return view('theme::ads.custom.index', compact('placements', 'publisherDeals', 'advertiserDeals'));
    }

    public function marketplace()
    {
        $this->ensureEnabled();
        abort_unless(CustomAdsSettings::marketplaceEnabled(), 404);

        $user = Auth::user();
        $placements = CustomAdPlacement::query()
            ->with('user')
            ->withCount('activeDeals')
            ->where('is_public', true)
            ->where('status', CustomAdPlacement::STATUS_ACTIVE)
            ->when($user, fn ($query) => $query->where('user_id', '!=', $user->id))
            ->latest('id')
            ->paginate(18)
            ->withQueryString();

        return view('theme::ads.custom.marketplace', compact('placements'));
    }

    public function createPlacement()
    {
        $this->ensureEnabled();

        return view('theme::ads.custom.placement_form', [
            'placement' => new CustomAdPlacement([
                'format' => CustomAdPlacement::FORMAT_BANNER,
                'size' => 'responsive',
                'is_public' => true,
                'status' => CustomAdPlacement::STATUS_ACTIVE,
                'background_color' => '#ffffff',
                'text_color' => '#1f2937',
                'accent_color' => '#615dfa',
            ]),
            'formats' => $this->formats(),
            'sizes' => $this->sizes(),
        ]);
    }

    public function storePlacement(Request $request)
    {
        $this->ensureEnabled();

        $payload = $this->validatedPlacement($request);
        $payload['user_id'] = Auth::id();
        $payload['is_public'] = $request->boolean('is_public');
        $payload['status'] = CustomAdPlacement::STATUS_ACTIVE;

        $placement = CustomAdPlacement::create($payload);

        return redirect()->route('ads.custom.placements.code', $placement)
            ->with('success', __('messages.custom_ads_placement_created'));
    }

    public function editPlacement(CustomAdPlacement $placement)
    {
        $this->authorizePlacementOwner($placement);

        return view('theme::ads.custom.placement_form', [
            'placement' => $placement,
            'formats' => $this->formats(),
            'sizes' => $this->sizes(),
        ]);
    }

    public function updatePlacement(Request $request, CustomAdPlacement $placement)
    {
        $this->authorizePlacementOwner($placement);

        $payload = $this->validatedPlacement($request);
        $payload['is_public'] = $request->boolean('is_public');
        $payload['status'] = $request->input('status', CustomAdPlacement::STATUS_ACTIVE);

        $placement->update($payload);

        return redirect()->route('ads.custom.index')
            ->with('success', __('messages.custom_ads_placement_updated'));
    }

    public function code(CustomAdPlacement $placement, CustomAdAnalyticsService $analytics)
    {
        $this->authorizePlacementOwner($placement);

        $embedCode = '<script src="' . route('ads.custom.embed') . '" data-placement="' . $placement->placement_key . '" async></script>';
        $summary = $analytics->placementSummary($placement);
        $heatmap = $analytics->hourlyHeatmapForPlacement($placement);

        return view('theme::ads.custom.placement_code', compact('placement', 'embedCode', 'summary', 'heatmap'));
    }

    public function requestDeal(CustomAdPlacement $placement)
    {
        $this->ensureEnabled();
        abort_unless($placement->is_public && $placement->isActive(), 404);
        abort_if((int) $placement->user_id === (int) Auth::id(), 403);

        return view('theme::ads.custom.deal_form', [
            'placement' => $placement->load('user'),
            'deal' => new CustomAdDeal([
                'payment_type' => CustomAdDeal::PAYMENT_PTS_DAILY,
                'daily_pts' => max(1, CustomAdsSettings::minDailyPts()),
                'starts_at' => now()->addDay(),
                'ends_at' => now()->addDays(7),
            ]),
            'creative' => new CustomAdCreative([
                'format' => $placement->format,
                'background_color' => $placement->background_color,
                'text_color' => $placement->text_color,
                'accent_color' => $placement->accent_color,
            ]),
            'source' => CustomAdDeal::SOURCE_REQUEST,
            'maxDurationDays' => CustomAdsSettings::maxDurationDays(),
        ]);
    }

    public function storeRequest(Request $request, CustomAdPlacement $placement, SecurityPolicyService $securityPolicy, NotificationService $notificationService)
    {
        $this->ensureEnabled();
        abort_unless($placement->is_public && $placement->isActive(), 404);
        abort_if((int) $placement->user_id === (int) Auth::id(), 403);

        $deal = $this->createDeal($request, $placement, CustomAdDeal::SOURCE_REQUEST, Auth::user(), $securityPolicy);

        $notificationService->send(
            $placement->user_id,
            __('messages.custom_ads_request_notification', ['user' => Auth::user()->username]),
            route('ads.custom.deals.show', $deal),
            'shopping-bag'
        );

        return redirect()->route('ads.custom.deals.show', $deal)
            ->with('success', __('messages.custom_ads_request_sent'));
    }

    public function inviteDeal(CustomAdPlacement $placement)
    {
        $this->authorizePlacementOwner($placement);

        return view('theme::ads.custom.deal_form', [
            'placement' => $placement->load('user'),
            'deal' => new CustomAdDeal([
                'payment_type' => CustomAdDeal::PAYMENT_PTS_DAILY,
                'daily_pts' => max(1, CustomAdsSettings::minDailyPts()),
                'starts_at' => now()->addDay(),
                'ends_at' => now()->addDays(7),
            ]),
            'creative' => new CustomAdCreative([
                'format' => $placement->format,
                'background_color' => $placement->background_color,
                'text_color' => $placement->text_color,
                'accent_color' => $placement->accent_color,
            ]),
            'source' => CustomAdDeal::SOURCE_INVITE,
            'maxDurationDays' => CustomAdsSettings::maxDurationDays(),
        ]);
    }

    public function storeInvite(Request $request, CustomAdPlacement $placement, SecurityPolicyService $securityPolicy, NotificationService $notificationService)
    {
        $this->authorizePlacementOwner($placement);

        $advertiser = $this->resolveAdvertiser((string) $request->input('advertiser'));
        $deal = $this->createDeal($request, $placement, CustomAdDeal::SOURCE_INVITE, $advertiser, $securityPolicy);

        $notificationService->send(
            $advertiser->id,
            __('messages.custom_ads_invite_notification', ['user' => Auth::user()->username]),
            route('ads.custom.deals.show', $deal),
            'shopping-bag'
        );

        return redirect()->route('ads.custom.deals.show', $deal)
            ->with('success', __('messages.custom_ads_invite_sent'));
    }

    public function showDeal(CustomAdDeal $deal, CustomAdAnalyticsService $analytics)
    {
        $this->authorizeDealMember($deal);

        $deal->load(['placement.user', 'publisher', 'advertiser', 'creative', 'payouts' => fn ($query) => $query->latest('id')]);

        return view('theme::ads.custom.deal_show', [
            'deal' => $deal,
            'summary' => $analytics->dealSummary($deal),
            'heatmap' => $analytics->hourlyHeatmapForDeal($deal),
            'dailySeries' => $analytics->dailySeriesForDeal($deal),
            'referrers' => $analytics->topReferrersForDeal($deal),
            'countries' => $analytics->countriesForDeal($deal),
            'devices' => $analytics->devicesForDeal($deal),
        ]);
    }

    public function editDeal(CustomAdDeal $deal)
    {
        $this->ensureEnabled();
        abort_unless((int) $deal->advertiser_id === (int) Auth::id(), 403);
        abort_unless($deal->status === CustomAdDeal::STATUS_INVITED, 403);

        $deal->load(['placement.user', 'creative']);

        return view('theme::ads.custom.deal_form', [
            'placement' => $deal->placement,
            'deal' => $deal,
            'creative' => $deal->creative,
            'source' => $deal->source,
            'maxDurationDays' => CustomAdsSettings::maxDurationDays(),
            'isEdit' => true,
        ]);
    }

    public function updateDeal(
        Request $request,
        CustomAdDeal $deal,
        SecurityPolicyService $securityPolicy,
        NotificationService $notificationService
    ) {
        $this->ensureEnabled();
        abort_unless((int) $deal->advertiser_id === (int) Auth::id(), 403);
        abort_unless($deal->status === CustomAdDeal::STATUS_INVITED, 403);

        $deal->load('advertiser');
        $request->merge(['advertiser' => $deal->advertiser->username]);

        $payload = $this->validatedDeal($request, $deal->placement, CustomAdDeal::SOURCE_INVITE);
        $creativePayload = $this->validatedCreative($request, $deal->placement);

        if ($violation = $securityPolicy->urlViolation((string) $creativePayload['target_url'], 'ads')) {
            throw ValidationException::withMessages(['target_url' => $violation]);
        }

        if (!empty($creativePayload['image_url']) && ($violation = $securityPolicy->urlViolation((string) $creativePayload['image_url'], 'ads', true))) {
            throw ValidationException::withMessages(['image_url' => $violation]);
        }

        $text = implode(' ', array_filter([
            $creativePayload['headline'] ?? '',
            $creativePayload['body'] ?? '',
            $payload['terms'] ?? '',
        ]));

        if ($violation = $securityPolicy->textViolation($text, 'ads')) {
            throw ValidationException::withMessages(['headline' => $violation]);
        }

        $deal->update(array_merge($payload, [
            'status' => CustomAdDeal::STATUS_PENDING,
        ]));

        $deal->creative->update(array_merge($creativePayload, [
            'status' => CustomAdsSettings::requireReview()
                ? CustomAdCreative::STATUS_PENDING
                : CustomAdCreative::STATUS_APPROVED,
        ]));

        $notificationService->send(
            $deal->publisher_id,
            __('messages.custom_ads_deal_modified_notification', ['user' => Auth::user()->username]),
            route('ads.custom.deals.show', $deal),
            'shopping-bag'
        );

        return redirect()->route('ads.custom.deals.show', $deal)
            ->with('success', __('messages.custom_ads_deal_updated'));
    }

    public function accept(CustomAdDeal $deal, CustomAdSettlementService $settlement)
    {
        $settlement->accept($deal, Auth::user());

        return back()->with('success', __('messages.custom_ads_deal_accepted'));
    }

    public function reject(CustomAdDeal $deal, CustomAdSettlementService $settlement)
    {
        $settlement->reject($deal, Auth::user());

        return back()->with('success', __('messages.custom_ads_deal_rejected'));
    }

    public function cancel(CustomAdDeal $deal, CustomAdSettlementService $settlement)
    {
        $this->authorizeDealMember($deal);
        $settlement->cancel($deal, Auth::user());

        return back()->with('success', __('messages.custom_ads_deal_cancelled'));
    }

    public function pause(CustomAdDeal $deal, CustomAdSettlementService $settlement)
    {
        $settlement->pause($deal, Auth::user());

        return back()->with('success', __('messages.custom_ads_deal_paused'));
    }

    public function resume(CustomAdDeal $deal, CustomAdSettlementService $settlement)
    {
        $settlement->resume($deal, Auth::user());

        return back()->with('success', __('messages.custom_ads_deal_resumed'));
    }

    private function createDeal(
        Request $request,
        CustomAdPlacement $placement,
        string $source,
        User $advertiser,
        SecurityPolicyService $securityPolicy
    ): CustomAdDeal {
        if ((int) $advertiser->id === (int) $placement->user_id) {
            throw ValidationException::withMessages([
                'advertiser' => __('messages.custom_ads_self_deal_not_allowed'),
            ]);
        }

        $payload = $this->validatedDeal($request, $placement, $source);
        $creativePayload = $this->validatedCreative($request, $placement);

        if ($violation = $securityPolicy->urlViolation((string) $creativePayload['target_url'], 'ads')) {
            throw ValidationException::withMessages(['target_url' => $violation]);
        }

        if (!empty($creativePayload['image_url']) && ($violation = $securityPolicy->urlViolation((string) $creativePayload['image_url'], 'ads', true))) {
            throw ValidationException::withMessages(['image_url' => $violation]);
        }

        $text = implode(' ', array_filter([
            $creativePayload['headline'] ?? '',
            $creativePayload['body'] ?? '',
            $payload['terms'] ?? '',
        ]));

        if ($violation = $securityPolicy->textViolation($text, 'ads')) {
            throw ValidationException::withMessages(['headline' => $violation]);
        }

        $publisherId = (int) $placement->user_id;
        $status = $source === CustomAdDeal::SOURCE_INVITE
            ? CustomAdDeal::STATUS_INVITED
            : CustomAdDeal::STATUS_PENDING;

        $deal = CustomAdDeal::create(array_merge($payload, [
            'placement_id' => $placement->id,
            'publisher_id' => $publisherId,
            'advertiser_id' => $advertiser->id,
            'initiated_by_id' => Auth::id(),
            'source' => $source,
            'status' => $status,
        ]));

        $deal->creative()->create(array_merge($creativePayload, [
            'format' => $placement->format,
            'status' => CustomAdsSettings::requireReview()
                ? CustomAdCreative::STATUS_PENDING
                : CustomAdCreative::STATUS_APPROVED,
        ]));

        return $deal->fresh(['placement.user', 'advertiser', 'publisher', 'creative']);
    }

    private function validatedPlacement(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'format' => ['required', Rule::in(array_keys($this->formats()))],
            'size' => ['required', Rule::in(array_keys($this->sizes()))],
            'site_url' => ['nullable', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', Rule::in([
                CustomAdPlacement::STATUS_ACTIVE,
                CustomAdPlacement::STATUS_PAUSED,
                CustomAdPlacement::STATUS_DISABLED,
            ])],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'text_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
    }

    private function validatedDeal(Request $request, CustomAdPlacement $placement, string $source): array
    {
        $validated = $request->validate([
            'advertiser' => [$source === CustomAdDeal::SOURCE_INVITE ? 'required' : 'nullable', 'string', 'max:255'],
            'payment_type' => ['required', Rule::in([CustomAdDeal::PAYMENT_PTS_DAILY, CustomAdDeal::PAYMENT_EXTERNAL])],
            'daily_pts' => ['nullable', 'numeric', 'min:' . CustomAdsSettings::minDailyPts()],
            'external_amount' => [Rule::requiredIf($request->input('payment_type') === CustomAdDeal::PAYMENT_EXTERNAL), 'nullable', 'numeric', 'min:0'],
            'external_currency' => [Rule::requiredIf($request->input('payment_type') === CustomAdDeal::PAYMENT_EXTERNAL), 'nullable', 'string', 'max:8'],
            'external_note' => ['nullable', 'string', 'max:1000'],
            'terms' => ['nullable', 'string', 'max:1000'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
        ]);

        $startsAt = Carbon::parse($validated['starts_at'])->startOfDay();
        $endsAt = Carbon::parse($validated['ends_at'])->endOfDay();
        $durationDays = $startsAt->diffInDays($endsAt->copy()->startOfDay()) + 1;

        if ($durationDays > CustomAdsSettings::maxDurationDays()) {
            throw ValidationException::withMessages([
                'ends_at' => __('messages.custom_ads_duration_too_long', ['days' => CustomAdsSettings::maxDurationDays()]),
            ]);
        }

        $paymentType = $validated['payment_type'];
        $dailyPts = $paymentType === CustomAdDeal::PAYMENT_PTS_DAILY ? round((float) ($validated['daily_pts'] ?? 0), 2) : 0.0;
        $totalPts = round($dailyPts * $durationDays, 2);

        if ($paymentType === CustomAdDeal::PAYMENT_PTS_DAILY && $totalPts < CustomAdsSettings::minTotalPts()) {
            throw ValidationException::withMessages([
                'daily_pts' => __('messages.custom_ads_total_pts_too_low', ['pts' => CustomAdsSettings::minTotalPts()]),
            ]);
        }

        return [
            'payment_type' => $paymentType,
            'daily_pts' => $dailyPts,
            'total_pts' => $totalPts,
            'external_amount' => $paymentType === CustomAdDeal::PAYMENT_EXTERNAL ? ($validated['external_amount'] ?? null) : null,
            'external_currency' => $paymentType === CustomAdDeal::PAYMENT_EXTERNAL ? strtoupper((string) ($validated['external_currency'] ?? '')) : null,
            'external_note' => $paymentType === CustomAdDeal::PAYMENT_EXTERNAL ? ($validated['external_note'] ?? null) : null,
            'terms' => $validated['terms'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    private function validatedCreative(Request $request, CustomAdPlacement $placement): array
    {
        return $request->validate([
            'headline' => ['required', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:280'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'target_url' => ['required', 'url', 'max:2048'],
            'button_label' => ['nullable', 'string', 'max:40'],
            'background_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'text_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);
    }

    private function resolveAdvertiser(string $identifier): User
    {
        $identifier = trim($identifier);
        $user = User::query()
            ->where('username', $identifier)
            ->orWhere('email', $identifier)
            ->when(is_numeric($identifier), fn ($query) => $query->orWhere('id', (int) $identifier))
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'advertiser' => __('messages.custom_ads_advertiser_not_found'),
            ]);
        }

        return $user;
    }

    private function authorizePlacementOwner(CustomAdPlacement $placement): void
    {
        $this->ensureEnabled();
        abort_unless((int) $placement->user_id === (int) Auth::id(), 403);
    }

    private function authorizeDealMember(CustomAdDeal $deal): void
    {
        $this->ensureEnabled();
        abort_unless($deal->canBeManagedBy(Auth::user()), 403);
    }

    private function ensureEnabled(): void
    {
        abort_unless(CustomAdsSettings::enabled(), 404);
    }

    private function formats(): array
    {
        return [
            CustomAdPlacement::FORMAT_BANNER => __('messages.custom_ads_format_banner'),
            CustomAdPlacement::FORMAT_TEXT => __('messages.custom_ads_format_text'),
            CustomAdPlacement::FORMAT_NATIVE => __('messages.custom_ads_format_native'),
        ];
    }

    private function sizes(): array
    {
        return [
            'responsive' => __('messages.responsive'),
            '728x90' => '728x90',
            '468x60' => '468x60',
            '300x250' => '300x250',
            '160x600' => '160x600',
            '320x100' => '320x100',
            '320x50' => '320x50',
        ];
    }
}
