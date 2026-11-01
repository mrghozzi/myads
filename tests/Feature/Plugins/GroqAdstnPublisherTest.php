<?php

namespace Tests\Feature\Plugins;

use App\Models\ForumTopic;
use App\Models\Option;
use App\Models\Status;
use App\Models\SupportCategory;
use App\Models\SupportReply;
use App\Models\SupportTopic;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use MyAds\Plugins\GroqAdstnPublisher\GroqAdstnPublisherService;
use MyAds\Plugins\GroqAdstnPublisher\PlatformKnowledgeService;
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

        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        $response = $this->actingAs($admin)->get('/admin/groq-adstn-publisher');

        $response->assertOk();
        $response->assertSee('Groq ADStn');
        $response->assertSee('llama-3.3-70b-versatile');
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
            'model' => 'llama-3.3-70b-versatile',
            'temperature' => 0.8,
            'max_tokens' => 800,
            'top_p' => 0.95,
            'bot_user_id' => 254,
            'timeline_enabled' => 1,
            'ar_enabled' => 1,
            'ar_freq_hours' => 12,
            'ar_freq_minutes' => 30,
            'en_enabled' => 1,
            'en_freq_hours' => 6,
            'en_freq_minutes' => 45,
            'support_forum_enabled' => 1,
            'support_forum_auto_reply' => 1,
            'support_forum_delay_minutes' => 10,
            'tickets_enabled' => 1,
            'tickets_admin_copilot' => 1,
            'tickets_auto_reply' => 1,
            'custom_knowledge' => 'Strict Rule: PTS points cannot be transferred between accounts.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $service = new GroqAdstnPublisherService();
        $config = $service->getConfig();

        $this->assertEquals(1, $config['is_enabled']);
        $this->assertEquals('gsk_mock_api_key_123', $config['api_key']);
        $this->assertEquals('llama-3.3-70b-versatile', $config['model']);
        $this->assertEquals(0.8, $config['temperature']);
        $this->assertEquals(800, $config['max_tokens']);
        $this->assertEquals(12, $config['ar_freq_hours']);
        $this->assertEquals(30, $config['ar_freq_minutes']);
        $this->assertEquals(6, $config['en_freq_hours']);
        $this->assertEquals(45, $config['en_freq_minutes']);
        $this->assertEquals(1, $config['support_forum_auto_reply']);
        $this->assertEquals(10, $config['support_forum_delay_minutes']);
        $this->assertEquals(1, $config['tickets_admin_copilot']);
        $this->assertEquals(1, $config['tickets_auto_reply']);
        $this->assertStringContainsString('PTS points cannot be transferred', $config['custom_knowledge']);
    }

    public function test_manual_execute_publishes_post_successfully(): void
    {
        $admin = $this->createAdmin();
        $adstn = $this->createAdstnUser();

        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        $service = new GroqAdstnPublisherService();
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'mock_key',
            'model' => 'llama-3.3-70b-versatile',
        ]);

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

        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        $service = new GroqAdstnPublisherService();
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'mock_key',
            'model' => 'llama-3.3-70b-versatile',
            'timeline_enabled' => 1,
            'ar_enabled' => 1,
            'en_enabled' => 1,
            'ar_freq_hours' => 1,
            'ar_freq_minutes' => 0,
            'en_freq_hours' => 2,
            'en_freq_minutes' => 0,
            'support_forum_auto_reply' => 0,
            'tickets_auto_reply' => 0,
        ]);

        Http::fake([
            'api.groq.com/*' => Http::sequence()
                ->push(['choices' => [['message' => ['content' => 'Arabic Test Post content']]]], 200)
                ->push(['choices' => [['message' => ['content' => 'English Test Post content']]]], 200)
                ->push(['choices' => [['message' => ['content' => 'Arabic Post Round 2']]]], 200)
        ]);

        // First tick
        $actions = $service->runTick();
        $this->assertContains('ar_published', $actions);
        $this->assertContains('en_published', $actions);

        $this->assertTrue(ForumTopic::where('txt', 'Arabic Test Post content')->exists());
        $this->assertTrue(ForumTopic::where('txt', 'English Test Post content')->exists());

        // Second tick immediately
        $actions2 = $service->runTick();
        $this->assertEmpty($actions2);

        // Fast forward 1 hour
        $state = $service->getState();
        $state['last_ar_post'] = time() - 3600;
        $state['last_en_post'] = time() - 3600;
        $service->saveState($state);

        $actions3 = $service->runTick();
        $this->assertContains('ar_published', $actions3);
        $this->assertNotContains('en_published', $actions3);
    }

    public function test_admin_can_test_groq_connection(): void
    {
        $admin = $this->createAdmin();

        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Connection successful! Groq AI is operational on MYADS platform.']]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/groq-adstn-publisher/test-connection', [
            'api_key' => 'mock_test_key',
            'model' => 'llama-3.3-70b-versatile',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertStringContainsString('Connection successful', $response->json('sample'));
    }

    public function test_admin_copilot_generates_draft_reply_for_ticket(): void
    {
        $admin = $this->createAdmin();
        $user = User::factory()->create(['username' => 'test_client']);

        Option::updateOrCreate(
            ['name' => 'groq-adstn-publisher', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        $service = new GroqAdstnPublisherService();
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'mock_key',
            'tickets_enabled' => 1,
            'tickets_admin_copilot' => 1,
        ]);

        $ticket = SupportTicket::create([
            'ticket_number' => 'TCK-12345',
            'user_id' => $user->id,
            'department' => 'technical',
            'priority' => 'high',
            'status' => 'open',
            'subject' => 'How to earn PTS with YouTube Views Exchange?',
            'last_reply_at' => now(),
            'last_reply_by' => $user->id,
        ]);

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'is_admin' => false,
            'message' => 'Hello, I watched a video but my PTS was not credited immediately. Please assist.',
        ]);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => "Hello @test_client, thank you for reaching out to ADStn technical support.\nTo earn PTS points with the YouTube Views Exchange, ensure your timer completes..."]]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/groq-adstn-publisher/tickets/copilot', [
            'ticket_id' => $ticket->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertStringContainsString('Hello @test_client', $response->json('draft'));
    }

    public function test_support_forum_auto_reply_answers_open_topic(): void
    {
        $admin = $this->createAdmin();
        $bot = $this->createAdstnUser();
        $author = User::factory()->create(['username' => 'community_user']);

        $category = SupportCategory::firstOrCreate(
            ['slug' => 'general-inquiries'],
            [
                'name' => 'General Inquiries',
                'description' => 'General questions about platform services',
                'is_active' => true,
                'topics_count' => 0,
                'replies_count' => 0,
            ]
        );

        $topic = SupportTopic::create([
            'user_id' => $author->id,
            'category_id' => $category->id,
            'title' => 'Where can I create banner ad campaigns?',
            'slug' => 'where-can-i-create-banner-ad-campaigns',
            'content' => 'I want to add a 728x90 banner to promote my blog. Where do I go in the menu?',
            'status' => 'open',
            'is_pinned' => false,
            'is_locked' => false,
            'replies_count' => 0,
        ]);
        $topic->created_at = now()->subMinutes(15);
        $topic->saveQuietly();

        $service = new GroqAdstnPublisherService();
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'mock_key',
            'support_forum_enabled' => 1,
            'support_forum_auto_reply' => 1,
            'support_forum_delay_minutes' => 0,
        ]);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => "Hello @community_user!\nYou can launch 728x90 banner campaigns easily from /ads/promote or the Ad Exchange tab on ADStn."]]
                ]
            ], 200)
        ]);

        $processed = $service->getSupportForumService()->autoReplyToPendingTopics($bot, $service->getConfig());

        $this->assertCount(1, $processed);
        $this->assertEquals($topic->id, $processed[0]['topic_id']);

        $reply = SupportReply::where('topic_id', $topic->id)->first();
        $this->assertNotNull($reply);
        $this->assertEquals($bot->id, $reply->user_id);
        $this->assertStringContainsString('ADStn', $reply->content);

        // Verify topic counters updated
        $topic->refresh();
        $this->assertEquals(1, $topic->replies_count);
        $this->assertEquals($bot->id, $topic->last_reply_user_id);
    }

    public function test_support_forum_quick_answer_api(): void
    {
        $user = User::factory()->create();

        $category = SupportCategory::firstOrCreate(
            ['slug' => 'store-marketplace'],
            [
                'name' => 'Store & Marketplace',
                'is_active' => true,
            ]
        );

        $topic = SupportTopic::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'How to download purchased store items?',
            'slug' => 'how-to-download-purchased-store-items',
            'content' => 'I bought a script with PTS. Where can I find the download link?',
            'status' => 'open',
        ]);

        $service = new GroqAdstnPublisherService();
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'mock_key',
            'support_forum_enabled' => 1,
            'support_forum_user_widget' => 1,
        ]);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'You can access your purchased downloads anytime under /store/my-purchases in your profile menu.']]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($user)->postJson('/api/groq-adstn-publisher/support/quick-answer', [
            'topic_id' => $topic->id,
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertStringContainsString('/store/my-purchases', $response->json('answer'));
    }

    public function test_platform_knowledge_service_includes_modules_and_custom_rules(): void
    {
        $service = new PlatformKnowledgeService();
        $context = $service->buildPromptContext('tickets', [
            'knowledge_use_core_docs' => 1,
            'custom_knowledge' => 'SPECIAL RULE: Weekend promotions offer 2x PTS points on visits.',
        ]);

        $this->assertStringContainsString('ADVERTISING EXCHANGE ECOSYSTEM', $context);
        $this->assertStringContainsString('POINTS ECONOMY (PTS)', $context);
        $this->assertStringContainsString('Anti-Fraud Shield v2', $context);
        $this->assertStringContainsString('SPECIAL RULE: Weekend promotions offer 2x PTS points', $context);
    }
}
