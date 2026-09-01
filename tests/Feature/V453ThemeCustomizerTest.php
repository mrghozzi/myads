<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\User;
use App\Services\ThemeCustomizerService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class V453ThemeCustomizerTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private User $regularUser;
    private ThemeCustomizerService $customizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customizer = app(ThemeCustomizerService::class);

        $this->admin = User::firstOrCreate(
            ['id' => 1],
            [
                'username' => 'superadmin',
                'email' => 'admin@example.com',
                'pass' => bcrypt('password123'),
                'ucheck' => 1,
            ]
        );

        $this->regularUser = User::factory()->create([
            'id' => 999123,
            'username' => 'regular_user',
            'email' => 'regular@example.com',
        ]);
    }

    public function test_theme_customizer_service_returns_defaults_for_theme(): void
    {
        $defaults = $this->customizer->getDefaults('default');

        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('primary_color', $defaults);
        $this->assertArrayHasKey('secondary_color', $defaults);
        $this->assertArrayHasKey('header_bg', $defaults);
        $this->assertArrayHasKey('bg_color', $defaults);
        $this->assertArrayHasKey('card_bg', $defaults);
        $this->assertArrayHasKey('font_family', $defaults);
        $this->assertArrayHasKey('border_radius', $defaults);
        $this->assertSame('#615dfa', $defaults['primary_color']);
        $this->assertSame('Inter', $defaults['font_family']);
    }

    public function test_theme_customizer_service_saves_and_retrieves_variables(): void
    {
        $testInput = [
            'primary_color' => '#059669',
            'secondary_color' => '#10b981',
            'header_bg' => '#059669',
            'bg_color' => '#f0fdf4',
            'card_bg' => '#ffffff',
            'text_color' => '#1e293b',
            'text_muted' => '#64748b',
            'font_family' => 'Cairo',
            'border_radius' => 16,
            'glass_blur' => 20,
            'glass_opacity' => 0.9,
        ];

        $saved = $this->customizer->saveVariables('default', $testInput);

        $this->assertSame('#059669', $saved['primary_color']);
        $this->assertSame('Cairo', $saved['font_family']);
        $this->assertSame(16, $saved['border_radius']);

        $retrieved = $this->customizer->getVariables('default');
        $this->assertSame('#059669', $retrieved['primary_color']);
        $this->assertSame('Cairo', $retrieved['font_family']);
        $this->assertSame(16, $retrieved['border_radius']);
        $this->assertTrue($this->customizer->hasCustomizations('default'));
    }

    public function test_theme_customizer_service_compiles_css_with_variables_and_fonts(): void
    {
        $customVars = [
            'primary_color' => '#7c3aed',
            'secondary_color' => '#ec4899',
            'header_bg' => '#7c3aed',
            'bg_color' => '#faf5ff',
            'card_bg' => '#ffffff',
            'text_color' => '#1e1b4b',
            'text_muted' => '#6b7280',
            'font_family' => 'Outfit',
            'border_radius' => 18,
            'glass_blur' => 22,
            'glass_opacity' => 0.85,
        ];

        $this->customizer->saveVariables('default', $customVars);
        $css = $this->customizer->compileCss('default');

        $this->assertStringContainsString(':root {', $css);
        $this->assertStringContainsString('--theme-primary: #7c3aed;', $css);
        $this->assertStringContainsString('--theme-secondary: #ec4899;', $css);
        $this->assertStringContainsString('--theme-radius: 18px;', $css);
        $this->assertStringContainsString('Outfit', $css);
        $this->assertStringContainsString('fonts.googleapis.com', $css);
    }

    public function test_theme_customizer_service_resets_customizations(): void
    {
        $this->customizer->saveVariables('default', [
            'primary_color' => '#dc2626',
            'font_family' => 'Roboto',
        ]);

        $this->assertTrue($this->customizer->hasCustomizations('default'));

        $this->customizer->reset('default');

        $this->assertFalse($this->customizer->hasCustomizations('default'));
        $variables = $this->customizer->getVariables('default');
        $this->assertSame('#615dfa', $variables['primary_color']);
        $this->assertSame('Inter', $variables['font_family']);
    }

    public function test_admin_can_access_customizer_view(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.themes.customizer'));

        $response->assertOk();
        $response->assertSee('theme-customizer-shell', false);
        $response->assertSee('theme-preview-frame', false);
        $response->assertSee('picker_primary_color', false);
    }

    public function test_admin_can_update_theme_customizations_via_post_and_json(): void
    {
        $response = $this->actingAs($this->admin)->postJson(route('admin.themes.customizer.update'), [
            'theme_slug' => 'default',
            'primary_color' => '#0284c7',
            'secondary_color' => '#38bdf8',
            'header_bg' => '#0284c7',
            'bg_color' => '#f0f9ff',
            'card_bg' => '#ffffff',
            'text_color' => '#0f172a',
            'text_muted' => '#64748b',
            'font_family' => 'Tajawal',
            'border_radius' => 14,
            'glass_blur' => 16,
            'glass_opacity' => 0.85,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'variables' => [
                'primary_color' => '#0284c7',
                'font_family' => 'Tajawal',
            ]
        ]);

        $retrieved = $this->customizer->getVariables('default');
        $this->assertSame('#0284c7', $retrieved['primary_color']);
        $this->assertSame('Tajawal', $retrieved['font_family']);
    }

    public function test_admin_can_reset_theme_customizations_via_post_and_json(): void
    {
        $this->customizer->saveVariables('default', [
            'primary_color' => '#ea580c',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.themes.customizer.reset'), [
            'theme_slug' => 'default',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertFalse($this->customizer->hasCustomizations('default'));
    }

    public function test_non_admin_cannot_access_or_update_theme_customizer(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.themes.customizer'));
        $this->assertTrue($response->isRedirection() || $response->isForbidden());

        $responsePost = $this->actingAs($this->regularUser)->post(route('admin.themes.customizer.update'), [
            'theme_slug' => 'default',
            'primary_color' => '#000000',
        ]);
        $this->assertTrue($responsePost->isRedirection() || $responsePost->isForbidden());
    }

    public function test_master_layout_renders_custom_theme_variables(): void
    {
        $this->customizer->saveVariables('default', [
            'primary_color' => '#9333ea',
            'secondary_color' => '#c084fc',
            'font_family' => 'Cairo',
        ]);

        $response = $this->actingAs($this->admin)->get('/home');
        $response->assertOk();

        // Either loads compiled stylesheet link or renders inline style tag with custom CSS
        $content = $response->getContent();
        $hasCustomLink = str_contains($content, 'id="theme-custom-variables"');
        $hasCustomPrimary = str_contains($content, '#9333ea') || str_contains($content, 'custom_variables.css');

        $this->assertTrue($hasCustomLink || $hasCustomPrimary);
    }

    public function test_welcome_and_auth_pages_render_custom_theme_variables(): void
    {
        $this->customizer->saveVariables('default', [
            'primary_color' => '#dc2626',
            'secondary_color' => '#f43f5e',
            'font_family' => 'Cairo',
        ]);

        $welcomeResponse = $this->get('/');
        $welcomeResponse->assertOk();
        $welcomeContent = $welcomeResponse->getContent();
        $this->assertTrue(str_contains($welcomeContent, 'id="theme-custom-variables"') || str_contains($welcomeContent, 'custom_variables.css'));

        $loginResponse = $this->get('/login');
        $loginResponse->assertOk();
        $loginContent = $loginResponse->getContent();
        $this->assertTrue(str_contains($loginContent, 'id="theme-custom-variables"') || str_contains($loginContent, 'custom_variables.css'));
    }

    public function test_preview_theme_query_loads_target_theme(): void
    {
        $response = $this->get('/?theme_preview=1&preview_theme=bootstrap-sample');
        $response->assertOk();

        // Check customizer view embeds the correct preview theme query and page switcher
        $customizerResponse = $this->actingAs($this->admin)->get(route('admin.themes.customizer', ['theme' => 'bootstrap-sample']));
        $customizerResponse->assertOk();
        $customizerResponse->assertSee('preview_theme=bootstrap-sample', false);
        $customizerResponse->assertSee('switchPreviewPage', false);
    }

    public function test_admin_dashboard_includes_theme_customizer_tip(): void
    {
        Option::updateOrCreate(
            ['name' => 'last_seen_about_version'],
            ['o_valuer' => \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION, 'o_type' => 'version']
        );

        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertOk();
        $response->assertSee(route('admin.themes.customizer'), false);
    }
}
