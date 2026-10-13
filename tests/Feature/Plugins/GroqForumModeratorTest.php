<?php

namespace Tests\Feature\Plugins;

use App\Models\User;
use App\Models\Option;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\ForumComment;
use App\Models\Status;
use MyAds\Plugins\GroqForumModerator\GroqForumModeratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class GroqForumModeratorTest extends TestCase
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

    private function enablePlugin(): void
    {
        Option::updateOrCreate(
            ['name' => 'groq-forum-moderator', 'o_type' => 'plugins'],
            ['o_valuer' => '1']
        );
    }

    public function test_admin_settings_page_renders_correctly(): void
    {
        $admin = $this->createAdmin();
        $this->enablePlugin();

        $response = $this->actingAs($admin)->get('/admin/groq-forum-moderator');

        $response->assertOk();
        $response->assertSee('Groq Smart Forum Moderator');
        $response->assertSee('Engine Configuration');
        $response->assertSee('Llama 3.3 70B Versatile');
        $response->assertSee('btnTestGroqConnection', false);
    }

    public function test_admin_can_save_config_and_sync_bot_user(): void
    {
        $admin = $this->createAdmin();
        $this->enablePlugin();

        $response = $this->actingAs($admin)->post('/admin/groq-forum-moderator/save', [
            'is_enabled' => 1,
            'api_key' => 'gsk_test_api_key_123',
            'model' => 'llama-3.3-70b-versatile',
            'bot_name' => 'Super AI Moderator',
            'bot_avatar' => 'https://example.com/bot.png',
            'persona_prompt' => 'Custom bot persona',
            'tick_probability' => 25,
            'cooldown_minutes' => 5,
            'topic_freq_hours' => 12,
            'enable_category_check' => 1,
            'enable_moderation' => 1,
            'enable_comments' => 1,
            'enable_tldr' => 1,
            'enable_fact_check' => 1,
            'enable_auto_tags' => 1,
            'enable_mentions' => 1,
            'warning_period_hours' => 48,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $service = groq_forum_moderator_service();
        $config = $service->getConfig();
        $this->assertEquals(1, $config['is_enabled']);
        $this->assertEquals('gsk_test_api_key_123', $config['api_key']);
        $this->assertEquals('llama-3.3-70b-versatile', $config['model']);
        $this->assertEquals('Super AI Moderator', $config['bot_name']);

        $bot = $service->getBotUser();
        $this->assertNotNull($bot);
        $this->assertEquals('Super AI Moderator', $bot->username);
        $this->assertTrue($bot->canModerateForum());
    }

    public function test_api_connection_test_returns_json(): void
    {
        $admin = $this->createAdmin();
        $this->enablePlugin();

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'OK',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($admin)->postJson('/admin/groq-forum-moderator/test-connection', [
            'api_key' => 'gsk_fake_key',
            'model' => 'llama-3.3-70b-versatile',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'model' => 'llama-3.3-70b-versatile',
        ]);
        $this->assertArrayHasKey('latency_ms', $response->json());
    }

    public function test_bot_generates_topic_with_v463_architecture_fields(): void
    {
        $admin = $this->createAdmin();
        $this->enablePlugin();

        $category = ForumCategory::create([
            'name' => 'Technology Discussions',
            'slug' => 'technology-discussions',
            'color' => '#615dfa',
            'ordercat' => 1,
            'visibility' => 0,
        ]);

        $service = groq_forum_moderator_service();
        $service->syncBotUser(['bot_name' => 'TestBot']);
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'gsk_test_key',
            'model' => 'llama-3.3-70b-versatile',
        ]);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'title' => 'The Future of AI Systems in 2026',
                                'content' => "## Overview\n\nAI systems have evolved dramatically.",
                                'tags' => ['#AI', '#Tech'],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $topic = $service->generateSpecificTopic($category->id);

        $this->assertNotNull($topic);
        $this->assertEquals('The Future of AI Systems in 2026', $topic->name);
        $this->assertEquals($category->id, $topic->cat);
        $this->assertNotEmpty($topic->slug);
        $this->assertEquals(0, $topic->reply);
        $this->assertEquals(0, $topic->comments_count);
        $this->assertNotNull($topic->last_posted_at);
        $this->assertEquals($topic->uid, $topic->last_poster_id);

        // Strict isolation to s_type = 2
        $status = Status::where('tp_id', $topic->id)->where('s_type', 2)->first();
        $this->assertNotNull($status);
        $this->assertEquals($topic->uid, $status->uid);

        // ForumOnly scope includes it
        $this->assertTrue(ForumTopic::forumOnly()->where('id', $topic->id)->exists());
    }

    public function test_create_bot_comment_updates_topic_metrics_and_pins_tldr(): void
    {
        $this->createAdmin();
        $this->enablePlugin();

        $category = ForumCategory::create([
            'name' => 'General',
            'slug' => 'general',
            'color' => '#615dfa',
            'ordercat' => 1,
            'visibility' => 0,
        ]);

        $service = groq_forum_moderator_service();
        $bot = $service->syncBotUser(['bot_name' => 'ModBot']);

        $topic = ForumTopic::create([
            'cat' => $category->id,
            'uid' => 1,
            'name' => 'Original Topic',
            'slug' => 'original-topic',
            'txt' => 'Some long content',
            'date' => time() - 3600,
            'last_posted_at' => time() - 3600,
            'last_poster_id' => 1,
            'comments_count' => 0,
            'reply' => 0,
            'statu' => 1,
        ]);
        Status::create(['uid' => 1, 'date' => time() - 3600, 's_type' => 2, 'tp_id' => $topic->id]);

        // Post a pinned TL;DR summary
        $comment = $service->createBotComment($topic, '📌 Pinned Summary Content', null, true);

        $this->assertNotNull($comment);
        $this->assertTrue($comment->isPinned());
        $this->assertEquals($bot->id, $comment->uid);

        $topic->refresh();
        $this->assertEquals(1, $topic->comments_count);
        $this->assertEquals(1, $topic->reply);
        $this->assertEquals($bot->id, $topic->last_poster_id);
        $this->assertEquals($comment->id, $topic->pinned_comment_id);
        $this->assertGreaterThanOrEqual($comment->date, $topic->last_posted_at);
    }

    public function test_heal_bot_topics_repairs_missing_statuses_and_fields(): void
    {
        $this->createAdmin();
        $this->enablePlugin();

        $category = ForumCategory::create([
            'name' => 'General',
            'slug' => 'general',
            'color' => '#615dfa',
            'ordercat' => 1,
            'visibility' => 0,
        ]);

        $service = groq_forum_moderator_service();
        $bot = $service->syncBotUser(['bot_name' => 'HealBot']);

        // Create broken topic (missing status and title is 'text')
        $topic = ForumTopic::create([
            'cat' => $category->id,
            'uid' => $bot->id,
            'name' => 'text',
            'txt' => "First line of content\nMore lines...",
            'date' => time(),
            'statu' => 1,
        ]);

        $fixedCount = $service->healBotTopics();
        $this->assertGreaterThanOrEqual(1, $fixedCount);

        $topic->refresh();
        $this->assertNotEquals('text', $topic->name);
        $this->assertNotEmpty($topic->slug);

        // Status record with s_type = 2 created
        $hasStatus = Status::where('tp_id', $topic->id)->where('s_type', 2)->exists();
        $this->assertTrue($hasStatus);
    }

    public function test_artisan_command_runs_successfully(): void
    {
        $this->createAdmin();
        $this->enablePlugin();

        $service = groq_forum_moderator_service();
        $service->syncBotUser(['bot_name' => 'ArtisanBot']);
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'gsk_test',
        ]);

        $exitCode = Artisan::call('groq-moderator:run', ['--action' => 'heal']);
        $this->assertEquals(0, $exitCode);
    }
}
