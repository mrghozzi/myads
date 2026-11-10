<?php

namespace Tests\Feature\Themes;

use App\Models\Setting;
use App\Models\User;
use App\Services\ThemeManager;
use App\Services\Security\FileUploadSecurityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;
use Tests\TestCase;
use ZipArchive;

class AdvancedThemeSystemTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;
    private array $createdThemeDirs = [];

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

    protected function tearDown(): void
    {
        // Revert setting to default
        $setting = Setting::first();
        if ($setting && $setting->styles !== 'default') {
            $setting->update(['styles' => 'default']);
        }

        // Clean up any test theme directories
        foreach ($this->createdThemeDirs as $slug) {
            $themePath = base_path('themes/' . $slug);
            if (File::exists($themePath)) {
                File::deleteDirectory($themePath);
            }
            $publicPath = public_path('themes/' . $slug);
            if (File::exists($publicPath)) {
                File::deleteDirectory($publicPath);
            }
        }

        Artisan::call('view:clear');

        parent::tearDown();
    }

    private function registerThemeCleanup(string $slug): void
    {
        if (!in_array($slug, $this->createdThemeDirs)) {
            $this->createdThemeDirs[] = $slug;
        }
    }

    public function test_artisan_theme_create_command_scaffolds_theme(): void
    {
        $slug = 'test-cli-theme';
        $this->registerThemeCleanup($slug);

        $exitCode = Artisan::call('theme:create', [
            'slug' => $slug,
            '--name' => 'CLI Test Theme',
            '--parent' => 'default',
            '--author' => 'Test Author',
        ]);

        $this->assertEquals(0, $exitCode);

        $themeJsonPath = base_path("themes/{$slug}/theme.json");
        $this->assertTrue(File::exists($themeJsonPath));

        $manifest = json_decode(File::get($themeJsonPath), true);
        $this->assertEquals('CLI Test Theme', $manifest['name']);
        $this->assertEquals($slug, $manifest['slug']);
        $this->assertEquals('default', $manifest['parent']);
        $this->assertEquals('Test Author', $manifest['author']);

        // Check assets and views directories
        $this->assertTrue(File::isDirectory(base_path("themes/{$slug}/views")));
        $this->assertTrue(File::exists(base_path("themes/{$slug}/assets/css/theme.css")));
        $this->assertTrue(File::exists(base_path("themes/{$slug}/assets/js/theme.js")));
    }

    public function test_theme_manager_detects_parent_and_child_inheritance_and_template_count(): void
    {
        $slug = 'test-child-inspect';
        $this->registerThemeCleanup($slug);

        $themeManager = app(ThemeManager::class);
        $result = $themeManager->createStarterTheme([
            'name' => 'Inspect Child',
            'slug' => $slug,
            'parent' => 'default',
        ]);

        $this->assertTrue($result['success']);

        $themes = $themeManager->getAllThemes();
        $childTheme = collect($themes)->firstWhere('slug', $slug);

        $this->assertNotNull($childTheme);
        $this->assertTrue($childTheme['is_child']);
        $this->assertEquals('default', $childTheme['parent']);
        $this->assertGreaterThanOrEqual(1, $childTheme['template_count']);
    }

    public function test_theme_manager_validates_syntax_and_catches_errors(): void
    {
        $slug = 'test-syntax-broken';
        $this->registerThemeCleanup($slug);

        $themeManager = app(ThemeManager::class);
        $themeManager->createStarterTheme([
            'name' => 'Syntax Broken Theme',
            'slug' => $slug,
            'parent' => 'default',
        ]);

        // Inject broken PHP into a theme file
        $brokenFilePath = base_path("themes/{$slug}/broken.php");
        File::put($brokenFilePath, '<?php function broken_syntax( { return 123; ');

        $errors = $themeManager->validateThemeSyntax(base_path("themes/{$slug}"));
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('broken.php', $errors[0]);

        // Activation must throw RuntimeException
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/syntax error/i');
        $themeManager->activate($slug);
    }

    public function test_theme_manager_activate_rejects_missing_parent(): void
    {
        $slug = 'test-missing-parent';
        $this->registerThemeCleanup($slug);

        $themeDir = base_path("themes/{$slug}");
        File::makeDirectory($themeDir . '/views', 0755, true);

        $manifest = [
            'name' => 'Missing Parent Theme',
            'slug' => $slug,
            'version' => '1.0.0',
            'parent' => 'non-existent-parent-999',
            'min_myads' => '4.7.0',
        ];
        File::put($themeDir . '/theme.json', json_encode($manifest));

        $themeManager = app(ThemeManager::class);

        $this->expectException(\RuntimeException::class);
        $themeManager->activate($slug);
    }

    public function test_theme_manager_delete_safeguards(): void
    {
        $themeManager = app(ThemeManager::class);

        // 1. Cannot delete default
        $result = $themeManager->delete('default');
        $this->assertIsString($result);

        // 2. Cannot delete active theme
        $slug = 'test-active-del';
        $this->registerThemeCleanup($slug);
        $themeManager->createStarterTheme(['name' => 'Active Del', 'slug' => $slug]);

        Setting::first()->update(['styles' => $slug]);
        $result = $themeManager->delete($slug);
        $this->assertIsString($result);

        // Revert active setting
        Setting::first()->update(['styles' => 'default']);

        // 3. Cannot delete if another theme depends on it as parent
        $parentSlug = 'test-parent-dep';
        $childSlug = 'test-child-dep';
        $this->registerThemeCleanup($parentSlug);
        $this->registerThemeCleanup($childSlug);

        $themeManager->createStarterTheme(['name' => 'Parent Dep', 'slug' => $parentSlug, 'parent' => 'default']);
        $themeManager->createStarterTheme(['name' => 'Child Dep', 'slug' => $childSlug, 'parent' => $parentSlug]);

        $result = $themeManager->delete($parentSlug);
        $this->assertIsString($result);

        // 4. Can delete child theme cleanly
        $result = $themeManager->delete($childSlug);
        $this->assertTrue($result);
        $this->assertFalse(File::exists(base_path("themes/{$childSlug}")));
    }

    public function test_theme_archive_security_validation(): void
    {
        $security = app(FileUploadSecurityService::class);
        $tempDir = storage_path('app/temp_test_' . uniqid());
        File::makeDirectory($tempDir, 0755, true);

        try {
            // Case 1: ZIP with forbidden executable
            $badZipPath = $tempDir . '/bad_executable.zip';
            $zip = new ZipArchive();
            $zip->open($badZipPath, ZipArchive::CREATE);
            $zip->addFromString('theme.json', json_encode(['slug' => 'bad-theme', 'name' => 'Bad Theme']));
            $zip->addFromString('malicious.exe', 'MZ...');
            $zip->close();

            $res = $security->validateThemeArchive($badZipPath);
            $this->assertFalse($res['valid']);
            $this->assertEquals('messages.theme_zip_forbidden_file', $res['error']);

            // Case 2: ZIP with path traversal
            $traversalZipPath = $tempDir . '/traversal.zip';
            $zip = new ZipArchive();
            $zip->open($traversalZipPath, ZipArchive::CREATE);
            $zip->addFromString('../../../etc/passwd', 'root:x:0:0:');
            $zip->close();

            $res = $security->validateThemeArchive($traversalZipPath);
            $this->assertFalse($res['valid']);
            $this->assertEquals('messages.theme_zip_malicious_path', $res['error']);

            // Case 3: Valid theme ZIP
            $goodZipPath = $tempDir . '/good_theme.zip';
            $zip = new ZipArchive();
            $zip->open($goodZipPath, ZipArchive::CREATE);
            $zip->addFromString('theme.json', json_encode([
                'slug' => 'my-good-theme',
                'name' => 'Good Theme',
                'version' => '1.0.0',
                'parent' => 'default',
            ]));
            $zip->addFromString('views/index.blade.php', '<div>Hello</div>');
            $zip->close();

            $res = $security->validateThemeArchive($goodZipPath);
            $this->assertTrue($res['valid']);
            $this->assertEquals('my-good-theme', $res['manifest']['slug']);
        } finally {
            File::deleteDirectory($tempDir);
        }
    }

    public function test_admin_can_scaffold_theme_via_post_endpoint(): void
    {
        $slug = 'test-web-created';
        $this->registerThemeCleanup($slug);

        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post('/admin/themes/create', [
                'name' => 'Web Created Theme',
                'slug' => $slug,
                'parent' => 'default',
                'description' => 'Created from admin UI',
                'author' => 'AdminTester',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(File::exists(base_path("themes/{$slug}/theme.json")));
    }

    public function test_admin_preview_route_and_query_parameter(): void
    {
        // 1. Preview redirect endpoint
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/themes/preview/bootstrap-sample');

        $response->assertRedirect(url('/?preview_theme=bootstrap-sample'));

        // 2. Landing page with preview_theme does NOT redirect authenticated admin to portal
        $landingResponse = $this->actingAs($this->admin)->get('/?preview_theme=bootstrap-sample');
        $landingResponse->assertStatus(200);
        $landingResponse->assertCookie('preview_theme', 'bootstrap-sample');
        $this->assertEquals('bootstrap-sample', $GLOBALS['MYADS_ACTIVE_THEME']);

        // 3. Portal with preview cookie retains preview theme
        $portalResponse = $this->actingAs($this->admin)
            ->withCookie('preview_theme', 'bootstrap-sample')
            ->get('/portal');
        $portalResponse->assertStatus(200);
        $this->assertEquals('bootstrap-sample', $GLOBALS['MYADS_ACTIVE_THEME']);

        // 4. Clearing preview resets cookie
        $clearResponse = $this->actingAs($this->admin)
            ->withCookie('preview_theme', 'bootstrap-sample')
            ->get('/?preview_theme=clear');
        $clearResponse->assertCookieExpired('preview_theme');

        // 5. Admin themes index page shows preview buttons, page selector and reload controls
        $response = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/themes');

        $response->assertStatus(200);
        $response->assertSee('id="uploadThemeModal"', false);
        $response->assertSee('id="createThemeModal"', false);
        $response->assertSee('id="themePreviewModal"', false);
        $response->assertSee('id="preview-page-select"', false);
        $response->assertSee('id="preview-reload-btn"', false);
        $response->assertSee('btn-theme-live-preview', false);
    }

    public function test_hierarchical_view_resolution_and_asset_fallback(): void
    {
        $slug = 'test-view-child';
        $this->registerThemeCleanup($slug);

        $themeManager = app(ThemeManager::class);
        $themeManager->createStarterTheme([
            'name' => 'View Child Theme',
            'slug' => $slug,
            'parent' => 'default',
        ]);

        // Add a unique child view override
        File::put(base_path("themes/{$slug}/views/child_unique_test.blade.php"), '<div class="child-view-content">Child Overridden</div>');

        // Set up hierarchical view search paths as ThemeServiceProvider does
        $childViews = base_path("themes/{$slug}/views");
        $defaultViews = base_path("themes/default/views");
        View::replaceNamespace('theme', [$childViews, $defaultViews]);

        // 1. Child view is rendered
        $rendered = View::make('theme::child_unique_test')->render();
        $this->assertStringContainsString('child-view-content', $rendered);

        // 2. Missing view in child falls back to default
        $this->assertTrue(View::exists('theme::welcome') || View::exists('theme::home'));

        // 3. Global theme_asset resolves correctly
        $assetUrl = theme_asset('css/styles.css');
        $this->assertNotEmpty($assetUrl);
    }
}
