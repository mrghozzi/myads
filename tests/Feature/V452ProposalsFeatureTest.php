<?php

namespace Tests\Feature;

use App\Helpers\Hooks;
use App\Models\Option;
use App\Models\User;
use App\Services\CustomAds\CustomAdServingService;
use App\View\Components\WidgetColumn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class V452ProposalsFeatureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        Hooks::reset();
        $this->seedSiteSettings();

        Option::updateOrCreate(
            ['o_type' => 'mobile_api', 'name' => 'api_key'],
            ['o_valuer' => 'test-api-key-452', 'o_order' => 0, 'o_mode' => '', 'o_parent' => 0]
        );
    }

    protected function tearDown(): void
    {
        Hooks::reset();
        parent::tearDown();
    }

    public function test_hooks_helper_get_actions_and_filters(): void
    {
        Hooks::add_action('test_v452_action', function () {}, 10, 2);
        Hooks::add_filter('test_v452_filter', function ($val) { return $val; }, 5, 1);

        $actions = Hooks::getActions();
        $filters = Hooks::getFilters();

        $this->assertArrayHasKey('test_v452_action', $actions);
        $this->assertArrayHasKey('test_v452_filter', $filters);
    }

    public function test_admin_plugins_inspector_page_renders_for_authorized_admin(): void
    {
        $admin = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/plugins/inspector');

        $response->assertStatus(200);
        $response->assertSee(__('messages.plugins_inspector'));
    }

    public function test_widget_column_applies_registered_plugin_widgets_filter_hook(): void
    {
        Hooks::add_filter('registered_plugin_widgets', function ($widgets, $side) {
            $widgets->push((object) [
                'id' => 9999,
                'name' => 'Plugin Test Widget',
                'o_type' => 'box_widget',
                'o_parent' => $side,
            ]);
            return $widgets;
        }, 10, 2);

        $widgetComponent = new WidgetColumn('portal_left');
        $this->assertTrue($widgetComponent->widgets->contains('id', 9999));
    }

    public function test_custom_ads_overall_ad_quality_stats(): void
    {
        $serving = new CustomAdServingService();
        $stats = $serving->getOverallAdQualityStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('quality_score', $stats);
        $this->assertArrayHasKey('total_clicks', $stats);
        $this->assertArrayHasKey('legitimate_clicks', $stats);
        $this->assertArrayHasKey('flagged_clicks', $stats);
        $this->assertEquals(100.0, $stats['quality_score']);
    }

    public function test_sanctum_token_revocation_api(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Mobile App Device');

        $this->assertCount(1, $user->tokens);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(
                "/api/settings/tokens/{$token->accessToken->id}/revoke",
                [],
                ['X-API-KEY' => 'test-api-key-452']
            );

        $response->assertStatus(200);
        $this->assertCount(0, $user->fresh()->tokens);
    }

    public function test_v452_translation_keys_exist_in_arabic_and_english(): void
    {
        $keys = [
            'plugins_inspector',
            'plugins_inspector_desc',
            'registered_actions',
            'registered_filters',
            'continuous_audio_player',
            'ad_quality_index',
            'device_revoked_successfully',
        ];

        foreach ($keys as $key) {
            $this->assertNotEquals("messages.{$key}", __('messages.' . $key, [], 'ar'));
            $this->assertNotEquals("messages.{$key}", __('messages.' . $key, [], 'en'));
        }
    }
}
