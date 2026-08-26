<?php

namespace Tests\Feature\Plugins;

use App\Helpers\Hooks;
use App\Models\Message;
use App\Models\Option;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class MyAdsBotTasksTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        Hooks::reset();
        $this->seedSiteSettings();

        Option::updateOrCreate(
            ['name' => 'myads-bot', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        if (file_exists(base_path('plugins/myads-bot/boot.php'))) {
            require base_path('plugins/myads-bot/boot.php');
        }

        myads_bot_service()->syncBotUser([]);
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

    public function test_admin_can_view_myads_bot_pending_tasks_queue(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/myads-bot');

        $response->assertStatus(200);
        $response->assertSee('Interaction Queue');
        $response->assertSee('Profile Status Post');
    }

    public function test_admin_can_cancel_pending_task_via_ajax(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->postJson('/admin/myads-bot/tasks/cancel', [
                'task_id' => 'scheduled:post',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertArrayHasKey('tasks_html', $response->json());
    }

    public function test_admin_task_execution_returns_error_details_when_bot_disabled_or_api_key_missing(): void
    {
        $admin = $this->createAdminUser();

        // Save disabled config
        $service = myads_bot_service();
        $service->saveConfig([
            'is_enabled' => 0,
            'api_key' => '',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->postJson('/admin/myads-bot/tasks/execute', [
                'task_id' => 'scheduled:post',
            ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('disabled', strtolower($response->json('message')));
    }

    public function test_admin_can_cancel_incoming_pm_task(): void
    {
        $admin = $this->createAdminUser();
        $botUser = myads_bot_service()->getBotUser();
        $sender = User::factory()->create();

        $pm = new Message();
        $pm->name = $sender->username;
        $pm->us_env = $sender->id;
        $pm->us_rec = $botUser->id;
        $pm->text = 'Help banner ads';
        $pm->time = time();
        $pm->state = 1;
        $pm->save();

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/myads-bot');

        $response->assertStatus(200);
        $response->assertSee('Private Message');
        $response->assertSee('Help banner ads');

        // Cancel PM task
        $cancelResponse = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->postJson('/admin/myads-bot/tasks/cancel', [
                'task_id' => "pm:{$pm->id_msg}",
            ]);

        $cancelResponse->assertStatus(200);
        $cancelResponse->assertJson(['success' => true]);

        // Re-fetch page, PM should no longer appear in tasks
        $reFetchResponse = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->get('/admin/myads-bot');
        $reFetchResponse->assertDontSee('Help banner ads');
    }

    public function test_periodic_profile_post_parses_structured_title_and_content(): void
    {
        $botUser = myads_bot_service()->getBotUser();
        $service = myads_bot_service();

        // Enable bot with test key
        $service->saveConfig([
            'is_enabled' => 1,
            'api_key' => 'test_key',
        ]);

        // Mock Http for Gemini API
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => "TITLE: How to Optimize Your Ad Campaign\nCONTENT: Here is a detailed multi-paragraph guide explaining how to setup high performing banner and smart ad campaigns on MyAds. Make sure to choose target demographics carefully and monitor CTR daily for best performance."]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $result = $service->executeTask('scheduled:post');
        $this->assertTrue($result);

        $latestTopic = \App\Models\ForumTopic::where('uid', $botUser->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($latestTopic);
        $this->assertEquals('How to Optimize Your Ad Campaign', $latestTopic->name);
        $this->assertStringContainsString('Here is a detailed multi-paragraph guide', $latestTopic->txt);

        $latestStatus = \App\Models\Status::where('uid', $botUser->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($latestStatus);
        $this->assertEquals($latestTopic->id, $latestStatus->tp_id);
    }
}
