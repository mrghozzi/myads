<?php

namespace Tests\Feature\Themes;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ThemeAjaxActivationTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['id' => 1],
            [
                'username' => 'superadmin',
                'email' => 'admin@example.com',
                'pass' => bcrypt('password123'),
                'ucheck' => 1,
            ]
        );

        Setting::firstOrCreate(
            ['id' => 1],
            [
                'styles' => 'default',
                'sitename' => 'MyAds Test Site',
            ]
        );
    }

    public function test_admin_themes_index_page_renders_with_filters_and_ajax_markup(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/themes');

        $response->assertStatus(200);

        // Verify filter toolbar buttons
        $response->assertSee('theme-filter-btn', false);
        $response->assertSee('data-filter="all"', false);
        $response->assertSee('data-filter="active"', false);
        $response->assertSee('data-filter="inactive"', false);
        $response->assertSee('data-filter="updates"', false);

        // Verify search input
        $response->assertSee('id="theme-search-input"', false);

        // Verify theme card attributes and slots
        $response->assertSee('theme-card-col', false);
        $response->assertSee('data-status=', false);
        $response->assertSee('data-search-text=', false);
        $response->assertSee('theme-action-toggle-slot', false);

        // Verify contextual empty state
        $response->assertSee('id="theme-filter-empty"', false);
        $response->assertSee('id="theme-reset-filter-btn"', false);
    }

    public function test_theme_activation_via_http_redirect_fallback(): void
    {
        // Set initial theme to default
        Setting::where('id', 1)->update(['styles' => 'default']);

        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post('/admin/themes/activate', [
                'slug' => 'bootstrap-sample',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('bootstrap-sample', Setting::first()->styles);
    }

    public function test_ajax_activate_theme_returns_json_response(): void
    {
        // Set initial theme to default
        Setting::where('id', 1)->update(['styles' => 'default']);

        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->postJson('/admin/themes/activate', [
                'slug' => 'bootstrap-sample',
            ], [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'slug' => 'bootstrap-sample',
            'is_active' => true,
            'active_slug' => 'bootstrap-sample',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['message']);
        $this->assertNotEmpty($data['theme_name']);
        $this->assertNotEmpty($data['customizer_url']);

        $this->assertEquals('bootstrap-sample', Setting::first()->styles);
    }

    public function test_ajax_activate_non_existent_theme_returns_error_json(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->postJson('/admin/themes/activate', [
                'slug' => 'non-existent-theme-slug-xyz',
            ], [
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }
}
