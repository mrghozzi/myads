<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\CustomAdCreative;
use App\Models\CustomAdDeal;
use App\Models\CustomAdPlacement;
use App\Models\Link;
use App\Models\SmartAd;
use App\Models\Status;
use App\Models\User;
use App\Services\CustomAds\CustomAdServingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class V453HighTrafficMicroCachingTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    public function test_compound_indexes_exist_on_high_traffic_tables(): void
    {
        // Check tables and ensure columns exist
        $this->assertTrue(Schema::hasTable('status'));
        $this->assertTrue(Schema::hasColumns('status', ['s_type', 'statu', 'date']));

        $this->assertTrue(Schema::hasTable('smart_ads'));
        $this->assertTrue(Schema::hasColumns('smart_ads', ['statu', 'uid']));

        $this->assertTrue(Schema::hasTable('banner'));
        $this->assertTrue(Schema::hasColumns('banner', ['statu', 'px']));

        $this->assertTrue(Schema::hasTable('link'));
        $this->assertTrue(Schema::hasColumns('link', ['statu', 'id']));

        $this->assertTrue(Schema::hasTable('custom_ad_events'));
        $this->assertTrue(Schema::hasColumns('custom_ad_events', ['deal_id', 'event_type', 'is_flagged', 'occurred_at']));
    }

    public function test_custom_ad_serving_deal_selection_is_micro_cached(): void
    {
        $publisher = User::factory()->create();
        $advertiser = User::factory()->create();

        $placement = CustomAdPlacement::query()->create([
            'user_id' => $publisher->id,
            'name' => 'Sidebar Box',
            'format' => CustomAdPlacement::FORMAT_BANNER,
            'size' => '300x250',
            'status' => CustomAdPlacement::STATUS_ACTIVE,
            'is_public' => true,
        ]);

        $deal = CustomAdDeal::query()->create([
            'placement_id' => $placement->id,
            'publisher_id' => $publisher->id,
            'advertiser_id' => $advertiser->id,
            'initiated_by_id' => $publisher->id,
            'payment_type' => CustomAdDeal::PAYMENT_PTS_DAILY,
            'daily_pts' => 10,
            'total_pts' => 70,
            'reserved_pts' => 70,
            'status' => CustomAdDeal::STATUS_ACTIVE,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(7),
        ]);

        $creative = CustomAdCreative::query()->create([
            'deal_id' => $deal->id,
            'headline' => 'Premium Campaign',
            'target_url' => 'https://example.com/target',
            'body' => 'High conversion campaign',
            'status' => CustomAdCreative::STATUS_APPROVED,
        ]);

        $service = app(CustomAdServingService::class);

        // First call should populate micro-cache
        $selectedDeal1 = $service->selectDeal($placement);
        $this->assertNotNull($selectedDeal1);
        $this->assertSame($deal->id, $selectedDeal1->id);

        $cacheKey = 'custom_ad_active_deal_ids_' . $placement->id;
        $this->assertTrue(Cache::has($cacheKey));
        $this->assertEquals([$deal->id], Cache::get($cacheKey));

        // Subsequent call retrieves deal from cached deal IDs
        $selectedDeal2 = $service->selectDeal($placement);
        $this->assertNotNull($selectedDeal2);
        $this->assertSame($deal->id, $selectedDeal2->id);
    }

    public function test_banner_serving_micro_caches_candidate_pool(): void
    {
        $publisher = User::factory()->create(['nvu' => 10]);
        $advertiser = User::factory()->create(['nvu' => 50]);

        $banner = Banner::create([
            'uid' => $advertiser->id,
            'name' => 'Tech Banner',
            'url' => 'https://example.com/banner-land',
            'img' => 'https://example.com/banner.png',
            'px' => '728x90',
            'statu' => 1,
        ]);

        $response = $this->get('/bn.php?id=' . $publisher->id . '&px=728x90');
        $response->assertStatus(200);

        // Verify that active banner pool cache key exists
        $countryCode = 'ZZ';
        $deviceType = 'desktop';
        $microCacheKey = 'active_banner_pool_' . md5('728x90_' . $countryCode . '_' . $deviceType);

        $this->assertTrue(Cache::has($microCacheKey));
        $cachedPool = Cache::get($microCacheKey);
        $this->assertContains($banner->id, $cachedPool);
    }

    public function test_link_serving_micro_caches_candidate_pool(): void
    {
        $publisher = User::factory()->create(['nlink' => 10]);
        $advertiser = User::factory()->create(['nlink' => 50]);

        $link = Link::create([
            'uid' => $advertiser->id,
            'name' => 'Shopping Link',
            'url' => 'https://example.com/link-land',
            'txt' => 'Best Deals Online',
            'statu' => 1,
        ]);

        $response = $this->get('/link.php?id=' . $publisher->id);
        $response->assertStatus(200);

        $countryCode = 'ZZ';
        $deviceType = 'desktop';
        $microCacheKey = 'active_link_pool_' . md5($countryCode . '_' . $deviceType);

        $this->assertTrue(Cache::has($microCacheKey));
        $cachedPool = Cache::get($microCacheKey);
        $this->assertContains($link->id, $cachedPool);
    }

    public function test_smart_ads_serving_micro_caches_candidate_pool(): void
    {
        $publisher = User::factory()->create(['nsmart' => 10]);
        $advertiser = User::factory()->create(['nsmart' => 50]);

        $smartAd = SmartAd::create([
            'uid' => $advertiser->id,
            'landing_url' => 'https://example.com/smart-land',
            'headline_override' => 'Smart Deal Today',
            'statu' => 1,
        ]);

        $response = $this->get('/smart.php?id=' . $publisher->id);
        $response->assertStatus(200);

        $countryCode = 'ZZ';
        $deviceType = 'desktop';
        $microCacheKey = 'active_smart_ads_pool_' . md5('all_' . $countryCode . '_' . $deviceType);

        $this->assertTrue(Cache::has($microCacheKey));
        $cachedPool = Cache::get($microCacheKey);
        $this->assertContains($smartAd->id, $cachedPool);
    }
}
