<?php

namespace Tests\Feature\Plugins;

use App\Models\User;
use App\Models\Option;
use App\Models\ForumTopic;
use App\Models\Status;
use MyAds\Plugins\GroqAdstnPublisher\GroqAdstnPublisherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class GroqAdstnPublisherTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'id' => 1,
            'username' => 'admin_user',
            'email' => 'admin@example.com',
        ]);
    }

    private function createAdstnUser(): User
    {
        return User::factory()->create([
            'id' => 254,
            'username' => 'adstn',
            'email' => 'adstn@adstn.ovh',
        ]);
    }

    public function test_admin_settings_page_renders_correctly(): void
    {
        $admin = $this->createAdmin();

        // Enable plugin in options first to ensure boot.php is active during request
        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        $response = $this->actingAs($admin)->get('/admin/groq-adstn-publisher');

        $response->assertOk();
        $response->assertSee('Groq ADStn Publisher');
        $response->assertSee('Target Account');
    }

    public function test_save_config_saves_to_database_correctly(): void
    {
        $admin = $this->createAdmin();

        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        $response = $this->actingAs($admin)->post('/admin/groq-adstn-publisher/save', [
            'is_enabled' => 1,
            'api_key' => 'gsk_mock_api_key_123',
            'model' => 'llama-3.3-70b-specdec',
            'ar_freq_hours' => 12,
            'ar_freq_minutes' => 30,
            'en_freq_hours' => 6,
            'en_freq_minutes' => 45,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $service = new GroqAdstnPublisherService();
        $config = $service->getConfig();

        $this->assertEquals(1, $config['is_enabled']);
        $this->assertEquals('gsk_mock_api_key_123', $config['api_key']);
        $this->assertEquals('llama-3.3-70b-specdec', $config['model']);
        $this->assertEquals(12, $config['ar_freq_hours']);
        $this->assertEquals(30, $config['ar_freq_minutes']);
        $this->assertEquals(6, $config['en_freq_hours']);
        $this->assertEquals(45, $config['en_freq_minutes']);
    }

    public function test_manual_execute_publishes_post_successfully(): void
    {
        $admin = $this->createAdmin();
        $adstn = $this->createAdstnUser();

        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        // Configure options
        $service = new GroqAdstnPublisherService();
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'mock_key',
            'model' => 'llama-3.3-70b-versatile',
        ]);

        // Mock Groq API call
        Http::fake([
             'api.groq.com/*' => Http::response([
                 'choices' => [
                     [
                         'message' => [
                             'content' => 'مرحباً بكم في منصتي adstn.ovh لتبادل الإعلانات والخدمات!'
                         ]
                     ]
                 ]
             ], 200)
         ]);
 
         $response = $this->actingAs($admin)->post('/admin/groq-adstn-publisher/execute', [
             'lang' => 'ar'
         ]);
 
         $response->assertRedirect();
         $response->assertSessionHas('success');
 
         // Verify database records (assert that 'منصتي' got replaced by 'ADStn')
         $this->assertTrue(ForumTopic::where('uid', 254)->where('txt', 'مرحباً بكم في ADStn adstn.ovh لتبادل الإعلانات والخدمات!')->exists());
         $this->assertTrue(Status::where('uid', 254)->exists());
 
         // Verify history contains it
         $history = $service->getHistory();
         $this->assertNotEmpty($history);
         $this->assertEquals('ar', $history[0]['lang']);
         $this->assertStringContainsString('مرحباً بكم في ADStn adstn.ovh', $history[0]['text']);
    }

    public function test_tick_publishes_on_due_intervals(): void
    {
        $adstn = $this->createAdstnUser();

        // Enable plugin
        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        $service = new GroqAdstnPublisherService();
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'mock_key',
            'model' => 'llama-3.3-70b-versatile',
            'ar_freq_hours' => 1,
            'ar_freq_minutes' => 0,
            'en_freq_hours' => 2,
            'en_freq_minutes' => 0,
        ]);

        // Fake Groq calls in a single sequence to prevent overriding issues
        Http::fake([
            'api.groq.com/*' => Http::sequence()
                ->push(['choices' => [['message' => ['content' => 'Arabic Test Post content']]]], 200)
                ->push(['choices' => [['message' => ['content' => 'English Test Post content']]]], 200)
                ->push(['choices' => [['message' => ['content' => 'Arabic Post Round 2']]]], 200)
        ]);

        // Run first tick - both ar and en last post are 0, so both should trigger
        $actions = $service->runTick();
        $this->assertContains('ar_published', $actions);
        $this->assertContains('en_published', $actions);

        $this->assertTrue(ForumTopic::where('txt', 'Arabic Test Post content')->exists());
        $this->assertTrue(ForumTopic::where('txt', 'English Test Post content')->exists());

        // Run second tick immediately - shouldn't trigger either language due to interval
        $actions2 = $service->runTick();
        $this->assertEmpty($actions2);

        // Fast forward state manually (simulate 1 hour passed for ar, 2 hours for en)
        $state = $service->getState();
        $state['last_ar_post'] = time() - 3600;
        $state['last_en_post'] = time() - 3600; // English needs 2 hours (7200), so 1 hour is not enough
        $service->saveState($state);

        $actions3 = $service->runTick();
        $this->assertContains('ar_published', $actions3);
        $this->assertNotContains('en_published', $actions3);
    }
}
