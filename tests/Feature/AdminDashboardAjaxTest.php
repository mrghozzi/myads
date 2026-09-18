<?php

namespace Tests\Feature;

use App\Models\SiteAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AdminDashboardAjaxTest extends TestCase
{
    use RefreshDatabase, SeedsSiteSettings;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
        $this->admin = User::factory()->create(['id' => 1]); // Super Admin
        SiteAdmin::create([
            'user_id' => $this->admin->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);
        \App\Models\Option::create([
            'name' => 'last_seen_about_version',
            'o_valuer' => \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION,
            'o_type' => 'system',
        ]);
    }

    public function test_admin_dashboard_shell_renders_instantaneously_with_skeletons(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get(route('admin.index'));

        $response->assertOk();
        $response->assertSee('sd-skeleton', false);
        $response->assertSee('loadDashboardSequentially', false);
        $response->assertSee('dashboard-kpis-container', false);
        $response->assertSee('dashboard-reactions-container', false);
        $response->assertSee('dashboard-activity-container', false);
        $response->assertSee('adDistributionChart', false);
        $response->assertSee('postsCommunityChart', false);
    }

    public function test_ajax_dashboard_kpis_endpoint_returns_rendered_partial(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get(route('admin.ajax.kpis'));

        $response->assertOk();
        $response->assertSee('kpis-row', false);
        $response->assertSee('sd-stat-num', false);
    }

    public function test_ajax_dashboard_reactions_endpoint_returns_rendered_partial(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get(route('admin.ajax.reactions'));

        $response->assertOk();
        $response->assertSee('sd-reactions-strip', false);
    }

    public function test_ajax_dashboard_activity_endpoint_returns_rendered_partial(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get(route('admin.ajax.activity'));

        $response->assertOk();
        $response->assertSee('dashboard-activity-card', false);
    }

    public function test_ajax_dashboard_ad_charts_endpoint_returns_valid_json(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->getJson(route('admin.ajax.ad_charts'));

        $response->assertOk();
        $response->assertJsonStructure([
            'distribution' => ['labels', 'data'],
            'engagement' => ['labels', 'data'],
        ]);
    }

    public function test_ajax_dashboard_community_charts_endpoint_returns_valid_json(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->getJson(route('admin.ajax.community_charts'));

        $response->assertOk();
        $response->assertJsonStructure([
            'labels',
            'posts' => ['text', 'link', 'gallery', 'video', 'clips'],
            'comments' => ['forum', 'store', 'directory', 'knowledgebase'],
            'reactions' => ['forum', 'store', 'directory', 'orders', 'follows', 'clips', 'knowledgebase'],
        ]);
    }

    public function test_ajax_dashboard_version_check_endpoint_returns_valid_json(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->getJson(route('admin.ajax.version_check'));

        $response->assertOk();
        $response->assertJsonStructure([
            'has_update',
            'current_version',
            'latest_version',
        ]);
    }

    public function test_guests_cannot_access_dashboard_ajax_endpoints(): void
    {
        $response = $this->get(route('admin.ajax.kpis'));
        $response->assertRedirect('/login');

        $responseJson = $this->getJson(route('admin.ajax.ad_charts'));
        $responseJson->assertUnauthorized();
    }
}
