<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Page;
use App\Models\SeoRule;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\User;
use App\Services\MaintenanceModeManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_centralized_seo_tags_and_admin_pages_are_noindexed(): void
    {
        $this->seedThemeSetting();

        $page = Page::create([
            'title' => 'SEO Landing',
            'slug' => 'seo-landing',
            'content' => '<p>Centralized SEO content.</p>',
            'status' => 'published',
            'widget_left' => false,
            'widget_right' => false,
            'meta_description' => 'A focused SEO description for the landing page.',
            'meta_keywords' => 'seo, landing, page',
            'order' => 1,
        ]);

        $this->get('/page/' . $page->slug)
            ->assertOk()
            ->assertSee('name="description" content="A focused SEO description for the landing page."', false)
            ->assertSee('name="keywords" content="seo, landing, page"', false)
            ->assertSee('rel="canonical" href="http://localhost/page/seo-landing"', false)
            ->assertSee('property="og:title"', false)
            ->assertDontSee('<meta name="description" content="A focused SEO description for the landing page."><meta name="description"', false);

        $admin = $this->createSuperAdmin();

        $this->actingAs($admin)
            ->get('/admin/seo')
            ->assertOk()
            ->assertSee(trans('messages.seo_dashboard', [], 'en'))
            ->assertSee('name="robots" content="noindex,nofollow"', false);
    }

    public function test_robots_route_allows_public_indexing_by_default_and_can_be_managed_from_admin(): void
    {
        $this->seedThemeSetting();

        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Allow: /')
            ->assertSee('Sitemap: http://localhost/sitemap.xml')
            ->assertDontSee("Disallow: /\n");

        $admin = $this->createSuperAdmin();

        $this->actingAs($admin)
            ->post('/admin/seo/indexing', [
                'allow_indexing' => '1',
                'robots_allow_paths' => "/\n",
                'robots_disallow_paths' => "/admin\n/login\n",
                'robots_extra' => "Host: localhost\n",
            ])
            ->assertRedirect('/admin/seo/indexing');

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Host: localhost')
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /login')
            ->assertSee('Sitemap: http://localhost/sitemap.xml');

        $this->actingAs($admin)
            ->post('/admin/seo/indexing', [
                'robots_allow_paths' => "/\n",
                'robots_disallow_paths' => "/admin\n/login\n",
                'robots_extra' => "Host: localhost\n",
            ])
            ->assertRedirect('/admin/seo/indexing');

        $this->assertDatabaseHas('seo_settings', [
            'allow_indexing' => 0,
        ]);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /')
            ->assertDontSee('Host: localhost');
    }

    public function test_sitemap_sections_include_lastmod_and_skip_noindex_content(): void
    {
        $this->seedThemeSetting();

        $visible = News::create([
            'name' => 'Visible News',
            'text' => 'A searchable news article.',
            'date' => 1710000000,
            'img' => 'upload/news-visible.png',
            'statu' => 1,
        ]);

        $hidden = News::create([
            'name' => 'Hidden News',
            'text' => 'This item should be removed from the sitemap.',
            'date' => 1710000100,
            'img' => 'upload/news-hidden.png',
            'statu' => 1,
        ]);

        SeoRule::create([
            'scope_key' => 'news_show',
            'content_type' => 'news',
            'content_id' => $hidden->id,
            'indexable' => false,
            'is_active' => true,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/sitemap/news/1.xml', false)
            ->assertSee('<lastmod>', false);

        $sectionResponse = $this->get('/sitemap/news/1.xml');
        $sectionResponse->assertOk();

        $sectionXml = $sectionResponse->streamedContent();

        $this->assertStringContainsString('/news/' . $visible->id, $sectionXml);
        $this->assertStringNotContainsString('/news/' . $hidden->id, $sectionXml);
        $this->assertStringContainsString('<lastmod>', $sectionXml);
    }

    public function test_admin_seo_pages_render_and_persist_settings(): void
    {
        $this->seedThemeSetting();
        $admin = $this->createSuperAdmin();

        $this->actingAs($admin)
            ->get('/admin/seo/settings')
            ->assertOk()
            ->assertSee(trans('messages.seo_settings', [], 'en'));

        $this->actingAs($admin)
            ->post('/admin/seo/settings', [
                'default_title' => 'Example SEO Title',
                'default_description' => 'Example SEO description',
                'default_keywords' => 'seo, example',
                'default_robots' => 'index,follow,max-image-preview:large',
                'canonical_mode' => 'strip_tracking',
                'default_og_image' => 'upload/seo-default.png',
                'default_twitter_card' => 'summary_large_image',
                'ga4_enabled' => '1',
                'ga4_measurement_id' => 'G-AB12CDEF34',
            ])
            ->assertRedirect('/admin/seo/settings');

        $this->assertDatabaseHas('seo_settings', [
            'default_title' => 'Example SEO Title',
            'ga4_enabled' => 1,
            'ga4_measurement_id' => 'G-AB12CDEF34',
        ]);

        $this->actingAs($admin)
            ->get('/admin/seo/head')
            ->assertOk()
            ->assertSee(trans('messages.seo_head_meta', [], 'en'));

        $this->actingAs($admin)
            ->get('/admin/seo/rules')
            ->assertOk()
            ->assertSee(trans('messages.seo_rules', [], 'en'));

        $this->actingAs($admin)
            ->get('/admin/seo/indexing')
            ->assertOk()
            ->assertSeeText(trans('messages.seo_indexing_heading', [], 'en'));
    }

    public function test_admin_seo_pages_use_translations_for_arabic_and_french(): void
    {
        $this->seedThemeSetting();
        $admin = $this->createSuperAdmin();

        $this->actingAs($admin)
            ->get('/admin/seo?lang=ar')
            ->assertOk()
            ->assertSee(trans('messages.seo_dashboard', [], 'ar'))
            ->assertSee(trans('messages.seo_open_sitemap', [], 'ar'))
            ->assertDontSee('messages.seo_dashboard')
            ->assertDontSee('SEO Dashboard');

        $this->actingAs($admin)
            ->get('/admin/seo/settings?lang=fr')
            ->assertOk()
            ->assertSee(trans('messages.seo_settings', [], 'fr'))
            ->assertSee(trans('messages.seo_save_settings', [], 'fr'))
            ->assertDontSee('messages.seo_settings')
            ->assertDontSee('SEO Settings');
    }

    public function test_crawler_endpoints_remain_available_during_maintenance_mode(): void
    {
        $this->seedThemeSetting();

        News::create([
            'name' => 'Maintenance News',
            'text' => 'Still available for sitemap sections.',
            'date' => 1710000200,
            'img' => 'upload/news-maintenance.png',
            'statu' => 1,
        ]);

        app(MaintenanceModeManager::class)->enable(null, 'manual');

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: http://localhost/sitemap.xml');

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/sitemap/news/1.xml', false);

        $sectionResponse = $this->get('/sitemap/news/1.xml');
        $sectionResponse->assertOk();
        $this->assertStringContainsString('/news/', $sectionResponse->streamedContent());

        $this->get('/')
            ->assertStatus(503)
            ->assertHeader('Retry-After', (string) app(MaintenanceModeManager::class)->retryAfter());
    }

    private function seedThemeSetting(): void
    {
        Setting::create([
            'titer' => 'MyAds',
            'description' => 'MyAds platform description',
            'url' => 'http://localhost',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);

        SeoSetting::currentPersisted();
    }

    private function createSuperAdmin(): User
    {
        return User::factory()->create([
            'id' => 1,
            'username' => 'super-admin',
            'email' => 'super-admin@example.com',
        ]);
    }
}
