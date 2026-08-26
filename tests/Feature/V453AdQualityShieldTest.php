<?php

namespace Tests\Feature;

use App\Models\CustomAdCreative;
use App\Models\CustomAdDeal;
use App\Models\CustomAdEvent;
use App\Models\CustomAdPlacement;
use App\Models\User;
use App\Services\CustomAds\CustomAdServingService;
use App\Services\CustomAds\CustomAdSettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class V453AdQualityShieldTest extends TestCase
{
    use RefreshDatabase;

    private function createPlacementAndDeal(): array
    {
        $publisher = User::factory()->create(['pts' => 0]);
        $advertiser = User::factory()->create(['pts' => 100]);

        $placement = CustomAdPlacement::create([
            'user_id' => $publisher->id,
            'name' => 'Premium Leaderboard',
            'placement_key' => 'leaderboard_key_' . uniqid(),
            'format' => 'banner',
            'size' => '728x90',
            'daily_pts' => 20,
            'status' => CustomAdPlacement::STATUS_ACTIVE,
        ]);

        $deal = CustomAdDeal::create([
            'placement_id' => $placement->id,
            'publisher_id' => $publisher->id,
            'advertiser_id' => $advertiser->id,
            'initiated_by_id' => $advertiser->id,
            'source' => CustomAdDeal::SOURCE_REQUEST,
            'payment_type' => CustomAdDeal::PAYMENT_PTS_DAILY,
            'daily_pts' => 20,
            'total_pts' => 60,
            'reserved_pts' => 60,
            'status' => CustomAdDeal::STATUS_ACTIVE,
        ]);

        $token = 'shield_tok_' . uniqid();
        $creative = CustomAdCreative::create([
            'deal_id' => $deal->id,
            'headline' => 'Scale Your SaaS Fast',
            'target_url' => 'https://advertiser.example.com/scale',
            'token' => $token,
            'status' => CustomAdCreative::STATUS_APPROVED,
        ]);

        return [$publisher, $advertiser, $placement, $deal, $creative];
    }

    public function test_iab_viewability_beacon_records_viewable_impression(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        $telemetry = base64_encode(json_encode([
            'vw' => true,
            'dw' => 1200,
            'vs' => 'visible',
            'dh' => false,
            'wd' => false,
            'mm' => 15,
        ]));

        $response = $this->post(route('ads.custom.view', ['token' => $creative->token]), [
            '_bh' => $telemetry,
        ]);

        $response->assertNoContent();

        $this->assertDatabaseHas('custom_ad_events', [
            'creative_id' => $creative->id,
            'event_type' => CustomAdEvent::TYPE_IMPRESSION,
            'is_flagged' => false,
        ]);

        $creative->refresh();
        $this->assertSame(1, (int) $creative->impressions);
    }

    public function test_valid_human_behavior_telemetry_records_clean_click(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        // 1. Initial serve
        $this->get(route('ads.custom.serve', ['placement' => $placement->placement_key]));

        $this->travel(2)->seconds();

        // 2. Legitimate human telemetry (movement, dwell time > 1.5s, visible tab)
        $telemetry = base64_encode(json_encode([
            'vw' => true,
            'dw' => 2100,
            'vs' => 'visible',
            'dh' => false,
            'wd' => false,
            'mm' => 34,
            'tc' => 0,
        ]));

        $response = $this->get(route('ads.custom.click', [
            'token' => $creative->token,
            '_bh' => $telemetry,
        ]));

        $response->assertRedirect('https://advertiser.example.com/scale');

        $event = CustomAdEvent::where('creative_id', $creative->id)
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->first();

        $this->assertNotNull($event);
        $this->assertFalse($event->is_flagged);
        $this->assertNull($event->flag_reason);

        $creative->refresh();
        $this->assertSame(1, (int) $creative->clicks);
    }

    public function test_touch_telemetry_on_mobile_records_clean_click(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        $this->travel(2)->seconds();

        $telemetry = base64_encode(json_encode([
            'vw' => true,
            'dw' => 1900,
            'vs' => 'visible',
            'dh' => false,
            'wd' => false,
            'mm' => 0,
            'tc' => 1,
            'td' => 120, // 120ms tap duration
        ]));

        $this->get(route('ads.custom.click', [
            'token' => $creative->token,
            '_bh' => $telemetry,
        ]))->assertRedirect('https://advertiser.example.com/scale');

        $event = CustomAdEvent::where('creative_id', $creative->id)
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->latest('occurred_at')
            ->first();

        $this->assertFalse($event->is_flagged);
    }

    public function test_webdriver_and_headless_indicators_flag_as_bot_fingerprint(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        $this->travel(2)->seconds();

        // Bot telemetry with webdriver flag true
        $botTelemetry = base64_encode(json_encode([
            'vw' => true,
            'dw' => 2000,
            'vs' => 'visible',
            'dh' => false,
            'wd' => true, // Headless webdriver detected!
            'mm' => 0,
        ]));

        $this->get(route('ads.custom.click', [
            'token' => $creative->token,
            '_bh' => $botTelemetry,
        ]))->assertRedirect('https://advertiser.example.com/scale');

        $event = CustomAdEvent::where('creative_id', $creative->id)
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->latest('occurred_at')
            ->first();

        $this->assertNotNull($event);
        $this->assertTrue($event->is_flagged);
        $this->assertSame(CustomAdEvent::REASON_BOT_FINGERPRINT, $event->flag_reason);

        // Creative clicks must NOT increment on bot clicks
        $this->assertSame(0, (int) $creative->fresh()->clicks);
    }

    public function test_bot_user_agent_is_flagged_as_bot_fingerprint(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        $this->travel(2)->seconds();

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) HeadlessChrome/120.0.0.0 Safari/537.36',
        ])->get(route('ads.custom.click', [
            'token' => $creative->token,
        ]))->assertRedirect('https://advertiser.example.com/scale');

        $event = CustomAdEvent::where('creative_id', $creative->id)
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->latest('occurred_at')
            ->first();

        $this->assertTrue($event->is_flagged);
        $this->assertSame(CustomAdEvent::REASON_BOT_FINGERPRINT, $event->flag_reason);
        $this->assertSame(0, (int) $creative->fresh()->clicks);
    }

    public function test_click_on_hidden_or_background_tab_is_flagged_with_hidden_tab_reason(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        $this->travel(2)->seconds();

        $hiddenTelemetry = base64_encode(json_encode([
            'vw' => true,
            'dw' => 2500,
            'vs' => 'hidden', // Tab is backgrounded/hidden
            'dh' => true,
            'wd' => false,
            'mm' => 2,
        ]));

        $this->get(route('ads.custom.click', [
            'token' => $creative->token,
            '_bh' => $hiddenTelemetry,
        ]))->assertRedirect('https://advertiser.example.com/scale');

        $event = CustomAdEvent::where('creative_id', $creative->id)
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->latest('occurred_at')
            ->first();

        $this->assertTrue($event->is_flagged);
        $this->assertSame(CustomAdEvent::REASON_HIDDEN_TAB, $event->flag_reason);
        $this->assertSame(0, (int) $creative->fresh()->clicks);
    }

    public function test_click_with_short_dwell_time_is_flagged_with_dwell_too_short_reason(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        // Click occurs within 200ms of loading (unnatural rapid automation)
        $rapidTelemetry = base64_encode(json_encode([
            'vw' => true,
            'dw' => 200, // < 1500ms
            'vs' => 'visible',
            'dh' => false,
            'wd' => false,
            'mm' => 1,
        ]));

        $this->get(route('ads.custom.click', [
            'token' => $creative->token,
            '_bh' => $rapidTelemetry,
        ]))->assertRedirect('https://advertiser.example.com/scale');

        $event = CustomAdEvent::where('creative_id', $creative->id)
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->latest('occurred_at')
            ->first();

        $this->assertTrue($event->is_flagged);
        $this->assertSame(CustomAdEvent::REASON_DWELL_TOO_SHORT, $event->flag_reason);
        $this->assertSame(0, (int) $creative->fresh()->clicks);
    }

    public function test_duplicate_clicks_within_rate_window_are_flagged_with_rapid_click_reason(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        $telemetry = base64_encode(json_encode([
            'vw' => true,
            'dw' => 2000,
            'vs' => 'visible',
            'dh' => false,
            'wd' => false,
            'mm' => 10,
        ]));

        // First click is clean
        $this->get(route('ads.custom.click', [
            'token' => $creative->token,
            '_bh' => $telemetry,
        ]));

        $this->assertSame(1, (int) $creative->fresh()->clicks);

        // Immediate duplicate click from same visitor / IP
        $this->get(route('ads.custom.click', [
            'token' => $creative->token,
            '_bh' => $telemetry,
        ]));

        $secondEvent = CustomAdEvent::where('creative_id', $creative->id)
            ->where('event_type', CustomAdEvent::TYPE_CLICK)
            ->latest('occurred_at')
            ->first();

        $this->assertTrue($secondEvent->is_flagged);
        $this->assertSame(CustomAdEvent::REASON_RAPID_CLICK, $secondEvent->flag_reason);
        $this->assertSame(1, (int) $creative->fresh()->clicks);
    }

    public function test_flagged_impressions_are_excluded_from_daily_payouts(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        // Create only a FLAGGED impression (bot/fraud)
        CustomAdEvent::create([
            'placement_id' => $placement->id,
            'deal_id' => $deal->id,
            'creative_id' => $creative->id,
            'publisher_id' => $publisher->id,
            'advertiser_id' => $advertiser->id,
            'event_type' => CustomAdEvent::TYPE_IMPRESSION,
            'visitor_key' => 'bot-visitor-1',
            'country_code' => 'US',
            'device_type' => 'desktop',
            'is_flagged' => true,
            'flag_reason' => CustomAdEvent::REASON_BOT_FINGERPRINT,
            'occurred_at' => now()->subHours(12),
        ]);

        $settlement = app(CustomAdSettlementService::class);
        $result = $settlement->releaseDailyPayouts(now()->toDateString());

        // Payout should be skipped because valid impressions count is 0
        $this->assertSame(0, $result['paid']);
        $this->assertSame(0.0, (float) $publisher->fresh()->pts);
    }

    public function test_overall_ad_quality_stats_provides_reason_breakdown(): void
    {
        [$publisher, $advertiser, $placement, $deal, $creative] = $this->createPlacementAndDeal();

        // 1. Create 1 clean click
        CustomAdEvent::create([
            'placement_id' => $placement->id,
            'deal_id' => $deal->id,
            'creative_id' => $creative->id,
            'publisher_id' => $publisher->id,
            'advertiser_id' => $advertiser->id,
            'event_type' => CustomAdEvent::TYPE_CLICK,
            'visitor_key' => 'human-1',
            'country_code' => 'US',
            'device_type' => 'desktop',
            'is_flagged' => false,
            'occurred_at' => now(),
        ]);

        // 2. Create 1 rapid click
        CustomAdEvent::create([
            'placement_id' => $placement->id,
            'deal_id' => $deal->id,
            'creative_id' => $creative->id,
            'publisher_id' => $publisher->id,
            'advertiser_id' => $advertiser->id,
            'event_type' => CustomAdEvent::TYPE_CLICK,
            'visitor_key' => 'human-2',
            'country_code' => 'US',
            'device_type' => 'desktop',
            'is_flagged' => true,
            'flag_reason' => CustomAdEvent::REASON_RAPID_CLICK,
            'occurred_at' => now(),
        ]);

        // 3. Create 1 bot click
        CustomAdEvent::create([
            'placement_id' => $placement->id,
            'deal_id' => $deal->id,
            'creative_id' => $creative->id,
            'publisher_id' => $publisher->id,
            'advertiser_id' => $advertiser->id,
            'event_type' => CustomAdEvent::TYPE_CLICK,
            'visitor_key' => 'bot-1',
            'country_code' => 'US',
            'device_type' => 'desktop',
            'is_flagged' => true,
            'flag_reason' => CustomAdEvent::REASON_BOT_FINGERPRINT,
            'occurred_at' => now(),
        ]);

        $service = app(CustomAdServingService::class);
        $stats = $service->getOverallAdQualityStats();

        $this->assertSame(3, $stats['total_clicks']);
        $this->assertSame(1, $stats['legitimate_clicks']);
        $this->assertSame(2, $stats['flagged_clicks']);
        $this->assertEquals(33.3, $stats['quality_score']);
        $this->assertSame(1, $stats['flagged_by_reason'][CustomAdEvent::REASON_RAPID_CLICK]);
        $this->assertSame(1, $stats['flagged_by_reason'][CustomAdEvent::REASON_BOT_FINGERPRINT]);
        $this->assertSame(0, $stats['flagged_by_reason'][CustomAdEvent::REASON_HIDDEN_TAB]);
    }
}
