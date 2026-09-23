<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Link;
use App\Models\SiteAdmin;
use App\Models\SmartAd;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AdminAdsAjaxTest extends TestCase
{
    use RefreshDatabase, SeedsSiteSettings;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
        $this->admin = User::factory()->create(['id' => 1, 'username' => 'superadmin', 'ucheck' => 1]);
        SiteAdmin::create([
            'user_id' => $this->admin->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);
    }

    public function test_banners_index_ajax_returns_html_and_stats(): void
    {
        Banner::create([
            'uid' => $this->admin->id,
            'name' => 'Test Banner Ad',
            'url' => 'https://example.com',
            'img' => 'https://example.com/banner.png',
            'px' => '728x90',
            'statu' => 1,
            'vu' => 150,
            'clik' => 12,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->getJson(route('admin.banners'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'html',
                'stats' => ['total', 'active', 'paused', 'total_views', 'total_clicks'],
                'total',
            ])
            ->assertJson([
                'success' => true,
                'total' => 1,
            ]);
    }

    public function test_banner_quick_status_toggle(): void
    {
        $banner = Banner::create([
            'uid' => $this->admin->id,
            'name' => 'Banner To Toggle',
            'url' => 'https://example.com',
            'img' => 'https://example.com/banner.png',
            'px' => '728x90',
            'statu' => 1,
            'vu' => 0,
            'clik' => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.banners.update', $banner->id), [
                'toggle_status' => 1,
                'statu' => 2,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 2,
            ]);

        $this->assertEquals(2, $banner->fresh()->statu);
    }

    public function test_links_index_ajax_returns_html_and_stats(): void
    {
        Link::create([
            'uid' => $this->admin->id,
            'name' => 'Test Text Ad',
            'url' => 'https://example.com',
            'txt' => 'Best advertising platform',
            'statu' => 1,
            'clik' => 45,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->getJson(route('admin.links'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'html',
                'stats' => ['total', 'active', 'inactive', 'total_clicks'],
                'total',
            ])
            ->assertJson([
                'success' => true,
                'total' => 1,
            ]);
    }

    public function test_link_quick_status_toggle(): void
    {
        $link = Link::create([
            'uid' => $this->admin->id,
            'name' => 'Link To Toggle',
            'url' => 'https://example.com',
            'txt' => 'Some text ad',
            'statu' => 1,
            'clik' => 0,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.links.update', $link->id), [
                'toggle_status' => 1,
                'statu' => 0,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 0,
            ]);

        $this->assertEquals(0, $link->fresh()->statu);
    }

    public function test_visits_index_ajax_returns_html_and_stats(): void
    {
        Visit::create([
            'uid' => $this->admin->id,
            'name' => 'Traffic Campaign',
            'url' => 'https://example.com',
            'vu' => 88,
            'tims' => 2,
            'statu' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->getJson(route('admin.visits'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'html',
                'stats' => ['total', 'active', 'inactive', 'total_delivered'],
                'total',
            ])
            ->assertJson([
                'success' => true,
                'total' => 1,
            ]);
    }

    public function test_visit_quick_status_toggle(): void
    {
        $visit = Visit::create([
            'uid' => $this->admin->id,
            'name' => 'Visit Campaign Toggle',
            'url' => 'https://example.com',
            'vu' => 10,
            'tims' => 1,
            'statu' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.visits.update', $visit->id), [
                'toggle_status' => 1,
                'statu' => 2,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 2,
            ]);

        $this->assertEquals(2, $visit->fresh()->statu);
    }

    public function test_smart_ads_index_ajax_returns_html_and_stats(): void
    {
        SmartAd::create([
            'uid' => $this->admin->id,
            'landing_url' => 'https://example.com/smart',
            'headline_override' => 'Smart AI Promotion',
            'impressions' => 240,
            'clicks' => 18,
            'statu' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->getJson(route('admin.smart_ads'));

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'html',
                'stats' => ['total', 'active', 'paused', 'blocked', 'total_impressions', 'total_clicks'],
                'total',
            ])
            ->assertJson([
                'success' => true,
                'total' => 1,
            ]);
    }

    public function test_smart_ad_quick_status_toggle(): void
    {
        $smartAd = SmartAd::create([
            'uid' => $this->admin->id,
            'landing_url' => 'https://example.com/smart-toggle',
            'headline_override' => 'Smart Ad Toggle Test',
            'impressions' => 0,
            'clicks' => 0,
            'statu' => 1,
        ]);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.smart_ads.update', $smartAd->id), [
                'toggle_status' => 1,
                'statu' => 0,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 0,
            ]);

        $this->assertEquals(0, $smartAd->fresh()->statu);
    }

    public function test_ads_settings_update_ajax(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.ads.settings.update'), [
                'ads_brand_name' => 'MyAds Premium Network',
                'banner_repeat_window_minutes' => 720,
                'banner_fallback_to_seen' => 1,
                'banner_prevent_concurrent_duplicates' => 1,
                'link_repeat_window_minutes' => 30,
                'visit_daily_limit' => 100,
                'visit_points_reward' => 10,
                'visit_vu_reward' => 1.0,
                'smart_ads_points_divisor' => 5.0,
                'ip_visibility' => 'everyone',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }
}
