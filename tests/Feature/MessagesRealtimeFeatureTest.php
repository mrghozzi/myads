<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use App\Models\UserPrivacySetting;
use App\Services\MessageConversationService;
use App\Support\SecuritySettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class MessagesRealtimeFeatureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_messages_workspace_renders_and_marks_active_conversation_read(): void
    {
        $this->seedSiteSettings();
        $viewer = User::factory()->create(['username' => 'viewer']);
        $sender = User::factory()->create(['username' => 'sender']);

        $message = $this->message($sender, $viewer, 'Hello from the new inbox.', 3);

        $response = $this->actingAs($viewer)->get(route('messages.index'));

        $response->assertOk()
            ->assertSee('data-messages-app', false)
            ->assertSee('messages-app.js', false)
            ->assertSee('messages-emoji-categories', false)
            ->assertSee('Search emoji')
            ->assertSee('Hello from the new inbox.');

        $this->assertSame(0, (int) $message->fresh()->state);
    }

    public function test_ajax_send_creates_message_and_returns_rendered_thread_item(): void
    {
        $this->seedSiteSettings();
        $this->disableMessageCooldown();
        Mail::fake();

        $sender = User::factory()->create(['username' => 'sender']);
        $recipient = User::factory()->create(['username' => 'recipient']);
        $routeKey = Message::encodeConversationRouteKey($sender, $recipient);

        $response = $this->actingAs($sender)
            ->postJson(route('messages.send', $routeKey), [
                'message' => 'Realtime hello',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['html', 'latest_id', 'latest_global_id']);

        $this->assertDatabaseHas('messages', [
            'us_env' => $sender->id,
            'us_rec' => $recipient->id,
            'msg' => 'Realtime hello',
            'state' => 3,
        ]);

        $this->assertStringContainsString('Realtime hello', $response->json('html'));
    }

    public function test_ajax_send_accepts_attachment_without_text(): void
    {
        $this->seedSiteSettings();
        $this->disableMessageCooldown();
        Mail::fake();

        $sender = User::factory()->create(['username' => 'attachsender']);
        $recipient = User::factory()->create(['username' => 'attachrecipient']);
        $routeKey = Message::encodeConversationRouteKey($sender, $recipient);

        $response = $this->actingAs($sender)->post(route('messages.send', $routeKey), [
            'message' => '',
            'attachment' => UploadedFile::fake()->create('brief.txt', 12, 'text/plain'),
        ], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $message = Message::query()->where('us_env', $sender->id)->where('us_rec', $recipient->id)->first();
        $this->assertNotNull($message);
        $this->assertSame('brief.txt', $message->attachment_name);
        $this->assertNotEmpty($message->attachment_path);
    }

    public function test_updates_endpoint_appends_active_messages_and_refreshes_counts(): void
    {
        $this->seedSiteSettings();
        $viewer = User::factory()->create(['username' => 'viewer']);
        $partner = User::factory()->create(['username' => 'partner']);

        $old = $this->message($viewer, $partner, 'Old message', 0, time() - 20);
        $new = $this->message($partner, $viewer, 'Fresh incoming message', 3, time());
        $routeKey = Message::encodeConversationRouteKey($viewer, $partner);

        $response = $this->actingAs($viewer)->getJson(route('messages.updates', [
            'conversation' => $routeKey,
            'after_id' => $old->id_msg,
            'toast_after_id' => $old->id_msg,
        ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('unread_count', 0)
            ->assertJsonPath('active_thread.count', 1)
            ->assertJsonPath('toast.sender', 'partner');

        $this->assertStringContainsString('Fresh incoming message', $response->json('active_thread.html'));
        $this->assertStringContainsString('partner', $response->json('conversations_html'));
        $this->assertSame(0, (int) $new->fresh()->state);
    }

    public function test_updates_endpoint_reports_unread_other_conversation(): void
    {
        $this->seedSiteSettings();
        $viewer = User::factory()->create(['username' => 'viewer']);
        $active = User::factory()->create(['username' => 'activepartner']);
        $other = User::factory()->create(['username' => 'otherpartner']);

        $activeMessage = $this->message($viewer, $active, 'Active old', 0, time() - 30);
        $this->message($other, $viewer, 'Unread elsewhere', 3, time());

        $response = $this->actingAs($viewer)->getJson(route('messages.updates', [
            'conversation' => Message::encodeConversationRouteKey($viewer, $active),
            'after_id' => $activeMessage->id_msg,
            'toast_after_id' => $activeMessage->id_msg,
        ]));

        $response->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('active_thread.count', 0)
            ->assertJsonPath('toast.sender', 'otherpartner');
    }

    public function test_encrypted_conversation_keys_are_required_when_message_encryption_is_enabled(): void
    {
        $this->seedSiteSettings();
        SecuritySettings::save([
            'private_message_encryption_enabled' => 1,
            'cooldown_private_message_seconds' => 0,
        ]);

        $viewer = User::factory()->create(['username' => 'viewer']);
        $partner = User::factory()->create(['username' => 'partner']);
        $message = $this->message($partner, $viewer, 'Encrypted route check', 3);

        $validKey = Message::encodeConversationRouteKey($viewer, $partner);

        $this->actingAs($viewer)
            ->get(route('messages.show', $validKey))
            ->assertOk()
            ->assertSee('Encrypted route check');

        $this->actingAs($viewer)
            ->get(route('messages.show', $message->id_msg))
            ->assertNotFound();
    }

    public function test_store_respects_recipient_direct_message_privacy(): void
    {
        $this->seedSiteSettings();
        User::factory()->create(['id' => 1, 'username' => 'root']);
        $sender = User::factory()->create(['username' => 'sender']);
        $recipient = User::factory()->create(['username' => 'closedrecipient']);
        UserPrivacySetting::query()->create([
            'user_id' => $recipient->id,
            'allow_direct_messages' => false,
        ]);

        $response = $this->actingAs($sender)->post(route('messages.store'), [
            'recipient' => $recipient->username,
            'message' => 'Can I message you?',
        ]);

        $response->assertSessionHasErrors('recipient');
        $this->assertDatabaseMissing('messages', [
            'us_env' => $sender->id,
            'us_rec' => $recipient->id,
        ]);
    }

    public function test_long_conversation_initial_render_only_loads_recent_page(): void
    {
        $this->seedSiteSettings();
        $viewer = User::factory()->create(['username' => 'longviewer']);
        $partner = User::factory()->create(['username' => 'longpartner']);

        $this->createLongConversation($viewer, $partner, 80);

        $response = $this->actingAs($viewer)
            ->get(route('messages.show', Message::encodeConversationRouteKey($viewer, $partner)));

        $response->assertOk()
            ->assertSee('data-has-older="1"', false)
            ->assertSee('body-080')
            ->assertSee('body-056')
            ->assertDontSee('body-055')
            ->assertDontSee('body-001');

        $this->assertSame(
            MessageConversationService::PAGE_SIZE,
            substr_count($response->getContent(), 'data-message-id=')
        );
    }

    public function test_history_endpoint_returns_previous_page_without_duplicates(): void
    {
        $this->seedSiteSettings();
        $viewer = User::factory()->create(['username' => 'historyviewer']);
        $partner = User::factory()->create(['username' => 'historypartner']);

        $messages = $this->createLongConversation($viewer, $partner, 80);
        $beforeId = $messages[56]->id_msg;

        $response = $this->actingAs($viewer)->getJson(route('messages.history', [
            'id' => Message::encodeConversationRouteKey($viewer, $partner),
            'before_id' => $beforeId,
        ]));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('count', MessageConversationService::PAGE_SIZE)
            ->assertJsonPath('has_more', true);

        $html = $response->json('html');
        $this->assertStringContainsString('body-055', $html);
        $this->assertStringContainsString('body-031', $html);
        $this->assertStringNotContainsString('body-056', $html);
        $this->assertStringNotContainsString('body-030', $html);
        $this->assertSame(MessageConversationService::PAGE_SIZE, substr_count($html, 'data-message-id='));
    }

    private function message(User $sender, User $recipient, string $text, int $state = 3, ?int $time = null): Message
    {
        $message = new Message();
        $message->name = $sender->username;
        $message->us_env = $sender->id;
        $message->us_rec = $recipient->id;
        $message->text = $text;
        $message->time = $time ?? time();
        $message->state = $state;
        $message->save();

        return $message;
    }

    private function disableMessageCooldown(): void
    {
        SecuritySettings::save([
            'cooldown_private_message_seconds' => 0,
        ]);
    }

    /**
     * @return array<int, Message>
     */
    private function createLongConversation(User $viewer, User $partner, int $count): array
    {
        $messages = [];
        $baseTime = time() - $count;

        for ($index = 1; $index <= $count; $index++) {
            $sender = $index % 2 === 0 ? $partner : $viewer;
            $recipient = $sender->is($viewer) ? $partner : $viewer;

            $messages[$index] = $this->message(
                $sender,
                $recipient,
                sprintf('body-%03d', $index),
                $recipient->is($viewer) ? 3 : 0,
                $baseTime + $index
            );
        }

        return $messages;
    }
}
