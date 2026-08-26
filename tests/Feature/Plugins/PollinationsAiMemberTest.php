<?php

namespace Tests\Feature\Plugins;

use App\Helpers\Hooks;
use App\Models\Message;
use App\Models\Option;
use App\Models\User;
use App\Models\Status;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;
use MyAds\Plugins\PollinationsAiMember\PollinationsService;
use MyAds\Plugins\PollinationsAiMember\PollinationsAiMemberService;

class PollinationsAiMemberTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        Hooks::reset();
        $this->seedSiteSettings();

        Option::updateOrCreate(
            ['name' => 'pollinations-ai-member', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        if (file_exists(base_path('plugins/pollinations-ai-member/boot.php'))) {
            require_once base_path('plugins/pollinations-ai-member/boot.php');
        }

        pollinations_ai_member_service()->syncBotUser([
            'bot_username' => 'test_pollinations_bot',
            'bot_name' => 'Test Pollinations Bot',
        ]);
    }

    protected function tearDown(): void
    {
        Hooks::reset();
        parent::tearDown();
    }

    private function createAdminUser(): User
    {
        $admin = User::factory()->create();
        \App\Models\SiteAdmin::create([
            'user_id' => $admin->id,
            'has_full_access' => 1,
            'is_active' => 1,
            'permissions' => \App\Services\AdminAccessService::MODULES,
        ]);
        return $admin;
    }

    public function test_admin_can_access_pollinations_ai_member_settings_page(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/pollinations-ai-member');

        $response->assertStatus(200);
        $response->assertSee('Pollinations.ai');
    }

    public function test_admin_can_save_settings_and_sync_bot_user(): void
    {
        $admin = $this->createAdminUser();

        Http::fake([
            'https://image.pollinations.ai/*' => Http::response('fake_image_bytes', 200, ['Content-Type' => 'image/jpeg']),
            'https://text.pollinations.ai/*' => Http::response('Simulated text generation response', 200, ['Content-Type' => 'text/plain']),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post('/admin/pollinations-ai-member/save', [
                'is_enabled' => 1,
                'api_key' => 'test_key_123',
                'text_model' => 'openai',
                'image_model' => 'flux',
                'bot_username' => 'pollinations_bot_ai',
                'bot_name' => 'Pollinations Bot AI',
                'bot_email' => 'pollinations_bot_ai@myads.local',
                'bot_about' => 'Smart AI platform member',
                'bot_location' => 'Cloud',
                'bot_website' => 'https://pollinations.ai',
                'bot_gender' => 'other',
                'enable_posts' => 1,
                'enable_media' => 1,
                'enable_comments' => 1,
                'enable_reactions' => 1,
                'enable_messages' => 1,
                'enable_persona_evolution' => 1,
                'enable_auto_follow' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['username' => 'pollinations_bot_ai']);
    }

    public function test_pollinations_service_handles_text_and_image_generation(): void
    {
        Http::fake([
            'https://text.pollinations.ai/*' => Http::response('Hello world response', 200, ['Content-Type' => 'text/plain']),
            'https://image.pollinations.ai/*' => Http::response('binary_jpeg_data', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $service = new PollinationsService();

        $text = $service->generateText([['role' => 'user', 'content' => 'Hello']]);
        $this->assertEquals('Hello world response', $text);

        $imageData = $service->generateImage('A futuristic robot member');
        $this->assertNotNull($imageData);
        $this->assertEquals('binary_jpeg_data', $imageData['content']);
        $this->assertEquals('jpg', $imageData['extension']);
    }

    public function test_run_tick_replies_to_private_messages_and_evokes_persona_evolution(): void
    {
        Http::fake([
            'https://text.pollinations.ai/*' => Http::response('مرحباً بك! أنا عضو افتراضي يسعدني التواجد والتواصل معك.', 200, ['Content-Type' => 'text/plain']),
            'https://image.pollinations.ai/*' => Http::response('image_bytes', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        $botUser = User::where('username', 'test_pollinations_bot')->first();
        $this->assertNotNull($botUser);

        $memberUser = User::factory()->create(['username' => 'Ahmed']);

        // Enable plugin
        pollinations_ai_member_service()->saveConfig([
            'is_enabled' => 1,
            'enable_messages' => 1,
            'enable_persona_evolution' => 1,
            'bot_username' => 'test_pollinations_bot',
        ]);

        // Send message from Ahmed to bot
        $msg = new Message();
        if (\Illuminate\Support\Facades\Schema::hasColumn('messages', 'us_env')) {
            $msg->us_env = $memberUser->id;
            $msg->us_rec = $botUser->id;
            $msg->msg = 'مرحباً، كيف حالك؟';
            $msg->time = time();
            $msg->state = 0;
        } else {
            $msg->sender_id = $memberUser->id;
            $msg->receiver_id = $botUser->id;
            $msg->message = 'مرحباً، كيف حالك؟';
            $msg->is_read = 0;
        }
        $msg->save();

        $actions = pollinations_ai_member_service()->runTick();

        $this->assertArrayHasKey('messages', $actions);
        $this->assertEquals(1, $actions['messages']['replied_senders']);

        // Check reply was created
        if (\Illuminate\Support\Facades\Schema::hasColumn('messages', 'us_env')) {
            $this->assertDatabaseHas('messages', [
                'us_env' => $botUser->id,
                'us_rec' => $memberUser->id,
            ]);
        } else {
            $this->assertDatabaseHas('messages', [
                'sender_id' => $botUser->id,
                'receiver_id' => $memberUser->id,
            ]);
        }
    }

    public function test_admin_can_execute_pending_task_via_route(): void
    {
        $admin = $this->createAdminUser();

        Http::fake([
            'https://text.pollinations.ai/*' => Http::response('Updated persona reflection string', 200, ['Content-Type' => 'text/plain']),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post('/admin/pollinations-ai-member/tasks/execute', [
                'task_id' => 'persona_evolve',
            ]);

        $response->assertRedirect();
    }
}
