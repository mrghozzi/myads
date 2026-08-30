<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Notification;
use App\Models\Report;
use App\Models\Status;
use App\Models\User;
use App\Services\Realtime\LiveEventStreamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class LiveEventStreamTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    public function test_guest_cannot_access_live_stream(): void
    {
        $response = $this->get('/live/stream');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_connect_to_sse_stream(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/live/stream?max_duration=1');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/event-stream', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('no-cache', (string) $response->headers->get('Cache-Control'));

        // Capture streamed response content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $this->assertStringContainsString('event: handshake', $content);
        $this->assertStringContainsString('event: ping', $content);
        $this->assertStringContainsString('event: reconnect', $content);
    }

    public function test_service_formats_sse_messages_correctly(): void
    {
        $service = app(LiveEventStreamService::class);

        $formatted = $service->formatSseMessage('custom_event', ['key' => 'value'], 'msg_1', 5000);

        $this->assertStringContainsString("id: msg_1\n", $formatted);
        $this->assertStringContainsString("retry: 5000\n", $formatted);
        $this->assertStringContainsString("event: custom_event\n", $formatted);
        $this->assertStringContainsString('data: {"key":"value"}' . "\n\n", $formatted);
    }

    public function test_service_polls_unread_notifications_and_messages(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $service = app(LiveEventStreamService::class);

        // Initially no unread
        $handshake = $service->getInitialHandshake($recipient);
        $this->assertEquals(0, $handshake['unread_notifications']);
        $this->assertEquals(0, $handshake['unread_messages']);

        // Create a notification
        Notification::create([
            'uid' => $recipient->id,
            'name' => 'Test Notification',
            'nurl' => '/test-url',
            'logo' => 'notification',
            'time' => time(),
            'state' => 0,
        ]);

        // Create an unread message
        Message::create([
            'name' => $sender->username,
            'us_env' => $sender->id,
            'us_rec' => $recipient->id,
            'msg' => 'Hello Real-Time!',
            'time' => time(),
            'state' => 3,
        ]);

        // Poll events
        $events = $service->pollUserEvents($recipient, time() - 10);

        $this->assertNotEmpty($events);

        $notifEvent = collect($events)->firstWhere('type', 'notifications');
        $this->assertNotNull($notifEvent);
        $this->assertEquals(1, $notifEvent['data']['unread_count']);
        $this->assertTrue($notifEvent['data']['has_new']);
        $this->assertEquals('Test Notification', $notifEvent['data']['latest']['name']);

        $msgEvent = collect($events)->firstWhere('type', 'messages');
        $this->assertNotNull($msgEvent);
        $this->assertEquals(1, $msgEvent['data']['unread_count']);
        $this->assertTrue($msgEvent['data']['has_new']);
        $this->assertEquals('Hello Real-Time!', $msgEvent['data']['latest']['text_preview']);
    }

    public function test_read_messages_are_not_counted_as_unread_in_handshake_or_poll(): void
    {
        $sender = User::factory()->create();
        $recipient = User::factory()->create();

        $service = app(LiveEventStreamService::class);

        // Create 150 read messages (state = 0)
        for ($i = 1; $i <= 150; $i++) {
            Message::create([
                'name' => $sender->username,
                'us_env' => $sender->id,
                'us_rec' => $recipient->id,
                'msg' => "Read message {$i}",
                'time' => time() - 3600,
                'state' => 0,
            ]);
        }

        // Handshake should report 0 unread messages (not 150 / 99+)
        $handshake = $service->getInitialHandshake($recipient);
        $this->assertEquals(0, $handshake['unread_messages']);

        // Poll events should report 0 unread messages
        $events = $service->pollUserEvents($recipient, time() - 10);
        $msgEvent = collect($events)->firstWhere('type', 'messages');
        $this->assertNotNull($msgEvent);
        $this->assertEquals(0, $msgEvent['data']['unread_count']);
        $this->assertFalse($msgEvent['data']['has_new']);
    }

    public function test_admin_user_receives_admin_monitoring_alerts(): void
    {
        $admin = User::factory()->create(['id' => 1]);
        $service = app(LiveEventStreamService::class);

        // Create pending report
        Report::create([
            'uid' => 2,
            's_type' => 1,
            'tp_id' => 10,
            'txt' => 'Spam report test',
            'statu' => 0,
        ]);

        $events = $service->pollUserEvents($admin, time() - 10);
        $adminEvent = collect($events)->firstWhere('type', 'admin');

        $this->assertNotNull($adminEvent);
        $this->assertGreaterThanOrEqual(1, $adminEvent['data']['pending_reports']);
    }

    public function test_feed_updates_event_dispatched_when_new_posts_exist(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $service = app(LiveEventStreamService::class);

        // Create public post from user2
        Status::create([
            'uid' => $user2->id,
            'txt' => 'Fresh community post',
            's_type' => 0,
            'date' => time(),
            'statu' => 0,
        ]);

        $events = $service->pollUserEvents($user1, time() - 10);
        $feedEvent = collect($events)->firstWhere('type', 'feed');

        $this->assertNotNull($feedEvent);
        $this->assertGreaterThanOrEqual(1, $feedEvent['data']['new_posts_count']);
    }
}
