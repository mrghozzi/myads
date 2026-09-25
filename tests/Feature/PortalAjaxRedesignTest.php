<?php

namespace Tests\Feature;

use App\Models\ForumTopic;
use App\Models\SiteAdmin;
use App\Models\Status;
use App\Models\User;
use App\Support\CommunityFeedSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class PortalAjaxRedesignTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
        Cache::flush();
        CommunityFeedSettings::clearCache();
    }

    public function test_portal_page_renders_redesigned_structure_and_controls(): void
    {
        $response = $this->get(route('portal.index'));

        $response->assertOk()
            ->assertSee(__('messages.portal_hero_title'))
            ->assertSee('portal-live-search-input')
            ->assertSee('portal-refresh-feed-btn')
            ->assertSee('portal-tabs-bar');
    }

    public function test_portal_feed_returns_json_on_ajax_request(): void
    {
        $author = User::factory()->create(['username' => 'ajaxposter']);
        $topic = ForumTopic::create([
            'uid' => $author->id,
            'name' => 'Ajax Topic Test',
            'txt' => 'Ajax topic description text.',
            'cat' => 0,
            'statu' => 1,
            'date' => time() - 100,
        ]);
        Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            's_type' => 100,
            'date' => time() - 100,
            'txt' => 'Ajax topic description text.',
        ]);

        $response = $this->getJson(route('portal.index', ['filter' => 'all']));

        $response->assertOk()
            ->assertJsonStructure([
                'type',
                'html',
                'filter',
            ]);

        $this->assertSame('feed', $response->json('type'));
        $this->assertSame('all', $response->json('filter'));
    }

    public function test_portal_search_returns_json_with_search_results_on_ajax(): void
    {
        $user = User::factory()->create(['username' => 'searchablemember']);

        $response = $this->getJson(route('portal.index', ['search' => 'searchablemember']));

        $response->assertOk()
            ->assertJsonStructure([
                'type',
                'html',
                'total_count',
                'search',
            ]);

        $this->assertSame('search', $response->json('type'));
        $this->assertSame('searchablemember', $response->json('search'));
        $this->assertGreaterThanOrEqual(1, $response->json('total_count'));
    }

    public function test_admin_can_update_new_algorithm_settings(): void
    {
        $admin = User::factory()->create(['username' => 'feedadmin']);
        SiteAdmin::create([
            'user_id' => $admin->id,
            'permissions' => ['community'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
        ]);

        $payload = CommunityFeedSettings::all();
        $payload['feed_mode'] = 'simple';
        $payload['media_boost'] = 12.5;
        $payload['verified_author_boost'] = 25.0;
        $payload['feed_page_size'] = 30;
        $payload['trending_highlight_boost'] = 15.0;

        $response = $this->actingAs($admin)
            ->post(route('admin.community.feed.settings.update'), $payload);

        $response->assertRedirect(route('admin.community.feed.settings'));

        $this->assertSame('simple', CommunityFeedSettings::get('feed_mode'));
        $this->assertEquals(12.5, CommunityFeedSettings::get('media_boost'));
        $this->assertEquals(25.0, CommunityFeedSettings::get('verified_author_boost'));
        $this->assertEquals(30, CommunityFeedSettings::get('feed_page_size'));
        $this->assertEquals(15.0, CommunityFeedSettings::get('trending_highlight_boost'));
    }

    public function test_community_feed_settings_presets_exist(): void
    {
        $presets = CommunityFeedSettings::presets();

        $this->assertArrayHasKey('balanced', $presets);
        $this->assertArrayHasKey('high_engagement', $presets);
        $this->assertArrayHasKey('fresh_breaking', $presets);
        $this->assertArrayHasKey('eco_shared', $presets);
    }

    public function test_admin_can_access_feed_settings_view_with_superdesign_components(): void
    {
        $admin = User::factory()->create(['username' => 'feedsettingsadmin']);
        SiteAdmin::create([
            'user_id' => $admin->id,
            'permissions' => ['community'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.community.feed.settings'));

        $response->assertOk()
            ->assertSee('admin-feed-settings')
            ->assertSee('admin-summary-grid')
            ->assertSee('admin-presets-grid')
            ->assertSee('admin-algo-visualizer')
            ->assertSee('admin-save-bar')
            ->assertSee('admin-feed-settings-form');
    }
}
