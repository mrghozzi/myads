<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;
use ZipArchive;

class AdminExtensionsUiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    private string $tempPath;

    /**
     * @var array<int, string>
     */
    private array $pluginSlugs = [];

    /**
     * @var array<int, string>
     */
    private array $themeSlugs = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempPath = storage_path('app/testing-admin-extensions');
        File::deleteDirectory($this->tempPath);
        File::makeDirectory($this->tempPath, 0755, true);
        \Illuminate\Support\Facades\Cache::flush();
        Http::preventStrayRequests();
    }

    protected function tearDown(): void
    {
        Cache::forget('plugin_updates');
        Cache::forget('theme_updates');
        Cache::forget('remote_extension_marketplace_plugins');
        Cache::forget('remote_extension_marketplace_themes');

        foreach ($this->pluginSlugs as $slug) {
            $this->cleanupExtension(base_path('plugins'), $slug);
            Option::query()->where('o_type', 'plugins')->where('name', $slug)->delete();
        }

        foreach ($this->themeSlugs as $slug) {
            $this->cleanupExtension(base_path('themes'), $slug);
        }

        File::deleteDirectory($this->tempPath);

        parent::tearDown();
    }

    public function test_admin_plugins_page_renders_redesigned_cards_and_routes(): void
    {
        $this->seedSiteSettings();
        $admin = $this->createAdmin();

        $this->createPlugin('active-admin-plugin', [
            'name' => 'Active Admin Plugin',
            'description' => 'Keeps one plugin active for the management dashboard.',
        ], activeState: 1);

        $this->createPlugin('updatable-admin-plugin', [
            'name' => 'Updatable Admin Plugin',
            'description' => 'Shows the changelog and update route on the redesigned page.',
        ], activeState: 0);

        Cache::put('plugin_updates', [
            'updatable-admin-plugin' => [
                'new_version' => '2.0.0',
                'download_url' => 'https://downloads.test/plugin.zip',
                'changelog' => 'Plugin release notes',
                'github_url' => 'https://github.com/example/plugin',
            ],
        ], 3600);

        Http::fake(['https://www.adstn.ovh/api/marketplace/extensions/*' => Http::response(['items' => []])]);
        $response = $this->actingAs($admin)->get(route('admin.plugins'));

        $response->assertOk()
            ->assertSee(__('messages.total_plugins'))
            ->assertSee(__('messages.active_plugins'))
            ->assertSee(__('messages.available_updates'))
            ->assertSee(route('admin.plugins.upload'), false)
            ->assertSee(route('admin.plugins.activate'), false)
            ->assertSee(route('admin.plugins.deactivate'), false)
            ->assertSee(route('admin.plugins.delete'), false)
            ->assertSee(route('admin.plugins.upgrade'), false)
            ->assertSee(__('messages.marketplace'))
            ->assertSee('id="extensionDeleteModal"', false)
            ->assertSee('id="extensionChangelogModal"', false);
    }

    public function test_admin_themes_page_renders_gallery_and_routes(): void
    {
        $this->seedSiteSettings(['styles' => 'default']);
        $admin = $this->createAdmin();

        $this->createTheme('admin-theme-preview', [
            'name' => 'Admin Theme Preview',
            'description' => 'Secondary theme used to expose the activation action.',
        ]);

        Cache::put('theme_updates', [
            'admin-theme-preview' => [
                'new_version' => '2.5.0',
                'download_url' => 'https://downloads.test/theme.zip',
                'changelog' => 'Theme release notes',
                'github_url' => 'https://github.com/example/theme',
            ],
        ], 3600);

        Http::fake(['https://www.adstn.ovh/api/marketplace/extensions/*' => Http::response(['items' => []])]);
        $response = $this->actingAs($admin)->get(route('admin.themes'));

        $response->assertOk()
            ->assertSee(__('messages.total_themes'))
            ->assertSee(__('messages.current_theme'))
            ->assertSee(__('messages.available_updates'))
            ->assertSee(route('admin.themes.activate'), false)
            ->assertSee(route('admin.themes.upgrade'), false)
            ->assertSee(__('messages.marketplace'))
            ->assertSee('id="extensionChangelogModal"', false);
    }

    public function test_admin_plugins_page_renders_remote_marketplace_cards(): void
    {
        $this->seedSiteSettings();
        $admin = $this->createAdmin();

        Http::fake([
            'https://www.adstn.ovh/api/marketplace/extensions/plugins' => Http::response([
                'type' => 'plugins',
                'items' => [[
                    'name' => 'Marketplace Plugin',
                    'slug' => 'marketplace-plugin',
                    'version' => '1.0.0',
                    'author' => 'Test Author',
                    'description' => 'Test Description',
                    'min_myads' => '4.2.3',
                    'product_url' => 'https://www.adstn.ovh/store/marketplace-plugin',
                    'image_url' => 'https://www.adstn.ovh/upload/marketplace-plugin.png',
                    'download_url' => 'https://downloads.test/marketplace-plugin.zip',
                    'category' => 'plugins',
                ]],
            ]),
            'https://www.adstn.ovh/api/marketplace/extensions/themes' => Http::response([
                'type' => 'themes',
                'items' => [],
            ]),
        ]);

        Cache::forget('remote_extension_marketplace_plugins');

        $response = $this->actingAs($admin)->get(route('admin.plugins'));

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
            return str_contains($request->url(), 'adstn.ovh/api/marketplace/extensions/plugins');
        });

        $response->assertOk()
            ->assertSee(__('messages.marketplace'))
            ->assertSee('Marketplace Plugin')
            ->assertSee(__('messages.details'))
            ->assertSee('data-bs-toggle="modal"', false)
            ->assertSee('data-bs-target="#pluginDetailsModal"', false)
            ->assertSee('data-min-myads="4.2.3"', false)
            ->assertSee(__('messages.install_now'))
            ->assertSee('marketplace-plugin')
            ->assertSee('https://downloads.test/marketplace-plugin.zip', false);
    }

    public function test_admin_themes_page_keeps_working_when_remote_marketplace_feed_fails(): void
    {
        $this->seedSiteSettings(['styles' => 'default']);
        $admin = $this->createAdmin();

        Http::fake([
            'https://www.adstn.ovh/api/marketplace/extensions/plugins' => Http::response([
                'type' => 'plugins',
                'items' => [],
            ]),
            'https://www.adstn.ovh/api/marketplace/extensions/themes' => Http::response([], 503),
        ]);

        Cache::forget('remote_extension_marketplace_themes');

        $response = $this->actingAs($admin)->get(route('admin.themes'));

        $response->assertOk()
            ->assertSee(__('messages.marketplace'))
            ->assertSee(__('messages.marketplace_unavailable'))
            ->assertSee(__('messages.marketplace_unavailable_help'));
    }

    public function test_active_plugin_delete_returns_error_message_instead_of_false_success(): void
    {
        $this->seedSiteSettings();
        $admin = $this->createAdmin();

        $this->createPlugin('locked-plugin', [
            'name' => 'Locked Plugin',
        ], activeState: 1);

        $response = $this->from(route('admin.plugins'))
            ->actingAs($admin)
            ->post(route('admin.plugins.delete'), ['slug' => 'locked-plugin']);

        $response->assertRedirect(route('admin.plugins'));
        $response->assertSessionHas('error', __('messages.plugin_delete_active_forbidden'));
        $response->assertSessionMissing('success');

        $this->assertDirectoryExists(base_path('plugins/locked-plugin'));
    }

    public function test_admin_extension_action_messages_are_translated(): void
    {
        $this->seedSiteSettings(['styles' => 'default']);
        $admin = $this->createAdmin();

        $this->createPlugin('plugin-to-activate', ['name' => 'Plugin To Activate'], activeState: 0);
        $this->from(route('admin.plugins'))
            ->actingAs($admin)
            ->post(route('admin.plugins.activate'), ['slug' => 'plugin-to-activate'])
            ->assertRedirect(route('admin.plugins'))
            ->assertSessionHas('success', __('messages.plugin_activated_successfully'));

        $this->createPlugin('plugin-to-deactivate', ['name' => 'Plugin To Deactivate'], activeState: 1);
        $this->from(route('admin.plugins'))
            ->actingAs($admin)
            ->post(route('admin.plugins.deactivate'), ['slug' => 'plugin-to-deactivate'])
            ->assertRedirect(route('admin.plugins'))
            ->assertSessionHas('success', __('messages.plugin_deactivated_successfully'));

        $this->from(route('admin.plugins'))
            ->actingAs($admin)
            ->post(route('admin.plugins.activate'), ['slug' => 'missing-plugin'])
            ->assertRedirect(route('admin.plugins'))
            ->assertSessionHas('error', __('messages.plugin_activation_failed'));

        $this->from(route('admin.plugins'))
            ->actingAs($admin)
            ->post(route('admin.plugins.deactivate'), ['slug' => 'missing-plugin'])
            ->assertRedirect(route('admin.plugins'))
            ->assertSessionHas('error', __('messages.plugin_deactivation_failed'));

        $uploadZipPath = $this->createPluginUploadZip('uploaded-admin-plugin');
        $this->from(route('admin.plugins'))
            ->actingAs($admin)
            ->post(route('admin.plugins.upload'), [
                'plugin_zip' => new UploadedFile($uploadZipPath, 'uploaded-admin-plugin.zip', 'application/zip', null, true),
            ])
            ->assertRedirect(route('admin.plugins'))
            ->assertSessionHas('success', __('messages.plugin_installed_successfully'));

        $this->assertFileExists(base_path('plugins/uploaded-admin-plugin/plugin.json'));

        $this->createTheme('theme-to-activate', ['name' => 'Theme To Activate']);
        $this->from(route('admin.themes'))
            ->actingAs($admin)
            ->post(route('admin.themes.activate'), ['slug' => 'theme-to-activate'])
            ->assertRedirect(route('admin.themes'))
            ->assertSessionHas('success', __('messages.theme_activated_successfully'));

        $this->assertSame('theme-to-activate', Setting::query()->value('styles'));

        $this->from(route('admin.themes'))
            ->actingAs($admin)
            ->post(route('admin.themes.activate'), ['slug' => 'missing-theme'])
            ->assertRedirect(route('admin.themes'))
            ->assertSessionHas('error', __('messages.theme_activation_failed'));
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'id' => 1,
            'username' => 'rootsystem',
        ]);
    }

    /**
     * @param array<string, mixed> $metadataOverrides
     */
    private function createPlugin(string $slug, array $metadataOverrides = [], int $activeState = 0): void
    {
        $this->pluginSlugs[] = $slug;

        $path = base_path("plugins/{$slug}");
        File::deleteDirectory($path);
        File::makeDirectory($path, 0755, true);

        $metadata = array_merge([
            'name' => 'Test Plugin',
            'slug' => $slug,
            'version' => '1.0.0',
            'author' => 'Tests',
            'description' => 'Test plugin description.',
            'min_myads' => '4.2.0',
        ], $metadataOverrides);

        File::put($path . '/plugin.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        File::put($path . '/boot.php', "<?php\n// plugin boot\n");

        Option::query()->updateOrCreate(
            ['name' => $slug, 'o_type' => 'plugins'],
            ['o_valuer' => $activeState]
        );
    }

    /**
     * @param array<string, mixed> $metadataOverrides
     */
    private function createTheme(string $slug, array $metadataOverrides = []): void
    {
        $this->themeSlugs[] = $slug;

        $path = base_path("themes/{$slug}");
        File::deleteDirectory($path);
        File::makeDirectory($path, 0755, true);
        File::makeDirectory($path . '/views', 0755, true);

        $metadata = array_merge([
            'name' => 'Test Theme',
            'slug' => $slug,
            'version' => '1.0.0',
            'author' => 'Tests',
            'description' => 'Test theme description.',
            'min_myads' => '4.2.0',
        ], $metadataOverrides);

        File::put($path . '/theme.json', json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        File::put($path . '/views/home.blade.php', '<div>theme</div>');
    }

    private function createPluginUploadZip(string $slug): string
    {
        $zipPath = $this->tempPath . DIRECTORY_SEPARATOR . $slug . '.zip';
        $zip = new ZipArchive;

        $this->assertTrue($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);
        $zip->addFromString($slug . '/plugin.json', (string) json_encode([
            'name' => 'Uploaded Admin Plugin',
            'slug' => $slug,
            'version' => '1.0.0',
            'author' => 'Tests',
            'description' => 'Uploaded plugin.',
            'min_myads' => '4.2.0',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $zip->addFromString($slug . '/boot.php', "<?php\n// uploaded plugin\n");
        $zip->close();

        $this->pluginSlugs[] = $slug;

        return $zipPath;
    }

    private function cleanupExtension(string $basePath, string $slug): void
    {
        $path = $basePath . DIRECTORY_SEPARATOR . $slug;
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }
    }
}
