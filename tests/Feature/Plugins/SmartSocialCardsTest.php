<?php

namespace Tests\Feature\Plugins;

use App\Models\Option;
use App\Models\User;
use App\Services\PluginManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MyAds\Plugins\SmartSocialCards\Services\SocialCardGeneratorService;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class SmartSocialCardsTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSiteSettings();

        // Require plugin boot file
        $bootFile = base_path('plugins/smart-social-cards/boot.php');
        if (file_exists($bootFile)) {
            require_once $bootFile;
        }
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'id' => 1,
            'username' => 'rootsystem',
        ]);
    }

    public function test_plugin_is_discovered_by_plugin_manager(): void
    {
        $manager = new PluginManager();
        $plugins = $manager->getAllPlugins();

        $plugin = collect($plugins)->firstWhere('slug', 'smart-social-cards');

        $this->assertNotNull($plugin, 'Smart Social OG Cards plugin should be discovered by PluginManager');
        $this->assertEquals('1.0.0', $plugin['version']);
        $this->assertEquals('Smart Social OG Cards', $plugin['name']);
        $this->assertEquals('4.5.6', $plugin['min_myads']);
    }

    public function test_og_image_preview_endpoint_returns_png(): void
    {
        $response = $this->get(route('og_image.preview', [
            'title' => 'Test Community Announcement',
            'category' => 'FORUM TOPIC',
            'author' => 'john_doe',
            'theme' => 'midnight',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');

        $content = $response->getContent();
        $this->assertNotEmpty($content);

        $imageInfo = getimagesizefromstring($content);
        $this->assertNotFalse($imageInfo);
        $this->assertEquals(1200, $imageInfo[0]);
        $this->assertEquals(630, $imageInfo[1]);
    }

    public function test_og_image_entity_endpoint_and_304_etag_cache(): void
    {
        $response = $this->get('/og-image/topic/999.png');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('max-age=2592000', $cacheControl);
        $this->assertStringContainsString('public', $cacheControl);

        $etag = $response->headers->get('ETag');
        $this->assertNotEmpty($etag);

        // Conditional GET with If-None-Match
        $cachedResponse = $this->withHeaders(['If-None-Match' => $etag])->get('/og-image/topic/999.png');
        $cachedResponse->assertStatus(304);
    }

    public function test_admin_dashboard_access(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.smart_social_cards.index'));

        $response->assertStatus(200);
        $response->assertSee('Smart Social OG Cards');
    }

    public function test_admin_save_settings(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.smart_social_cards.settings'), [
            'theme' => 'cyberpunk',
            'watermark' => 'custom-myads.io',
            'enabled_topics' => 1,
            'enabled_products' => 1,
            'enabled_videos' => 0,
            'enabled_news' => 1,
            'enabled_directory' => 0,
            'enabled_pages' => 1,
            'cache_enabled' => 1,
        ]);

        $response->assertRedirect(route('admin.smart_social_cards.index'));
        $response->assertSessionHas('success');

        $service = new SocialCardGeneratorService();
        $config = $service->getConfig();

        $this->assertEquals('cyberpunk', $config['theme']);
        $this->assertEquals('custom-myads.io', $config['watermark']);
        $this->assertTrue($config['enabled_topics']);
        $this->assertFalse($config['enabled_videos']);
    }

    public function test_admin_clear_cache(): void
    {
        $admin = $this->createAdmin();

        // Render one card to populate cache
        $this->get('/og-image/topic/101.png');

        $response = $this->actingAs($admin)->post(route('admin.smart_social_cards.clear_cache'));

        $response->assertRedirect(route('admin.smart_social_cards.index'));
        $response->assertSessionHas('success');
    }

    public function test_resolve_og_image_url_helper(): void
    {
        $service = new SocialCardGeneratorService();
        
        $request = \Illuminate\Http\Request::create('/topic/42', 'GET');
        $request->setRouteResolver(function () {
            $route = new \Illuminate\Routing\Route('GET', '/topic/{id}', []);
            $route->name('forum.topic');
            $route->bind(request());
            $route->setParameter('id', '42');
            return $route;
        });

        $url = $service->resolveOgImageUrl($request, []);
        $this->assertNotNull($url);
        $this->assertStringContainsString('/og-image/topic/42.png', $url);
    }
}
