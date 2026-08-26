<?php

namespace Tests\Feature;

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RequireAdminPasswordConfirmation;
use App\Models\Message;
use App\Models\SiteAdmin;
use App\Models\User;
use App\Services\AdminAccessService;
use App\Support\SecuritySettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class SecuritySuiteFeatureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_security_module_acl_is_enforced_for_scoped_admins(): void
    {
        $this->seedSiteSettings();

        $securityAdmin = User::factory()->create(['username' => 'securityadmin']);
        SiteAdmin::create([
            'user_id' => $securityAdmin->id,
            'permissions' => ['security'],
            'is_active' => true,
            'has_full_access' => false,
        ]);

        $limitedAdmin = User::factory()->create(['username' => 'usersadmin']);
        SiteAdmin::create([
            'user_id' => $limitedAdmin->id,
            'permissions' => ['users'],
            'is_active' => true,
            'has_full_access' => false,
        ]);

        $access = app(AdminAccessService::class);

        $this->assertTrue($access->canAccess($securityAdmin, 'admin.security.index'));
        $this->assertFalse($access->canAccess($limitedAdmin, 'admin.security.index'));
    }

    public function test_admin_routes_require_password_confirmation_when_enabled(): void
    {
        $this->seedSiteSettings();

        $superAdmin = User::factory()->create([
            'id' => 1,
            'username' => 'rootsecurity',
        ]);

        SecuritySettings::save([
            'admin_password_confirmation_enabled' => 1,
            'admin_password_confirmation_ttl_minutes' => 30,
        ]);

        $middleware = app(RequireAdminPasswordConfirmation::class);
        $request = Request::create('/admin/security', 'GET');
        $session = app('session')->driver();
        $session->start();
        $request->setLaravelSession($session);
        $request->setUserResolver(fn () => $superAdmin);

        $response = $middleware->handle($request, fn () => response('ok'));
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame(route('admin.confirm-password.form'), $response->headers->get('Location'));

        RequireAdminPasswordConfirmation::markConfirmed($request);
        $okResponse = $middleware->handle($request, fn () => response('ok'));

        $this->assertSame(200, $okResponse->getStatusCode());
    }

    public function test_private_messages_are_encrypted_when_setting_is_enabled(): void
    {
        $this->seedSiteSettings();

        SecuritySettings::save([
            'private_message_encryption_enabled' => 1,
        ]);

        $sender = User::factory()->create(['username' => 'encsender']);
        $receiver = User::factory()->create(['username' => 'encreceiver']);

        $message = Message::create([
            'name' => $sender->username,
            'us_env' => $sender->id,
            'us_rec' => $receiver->id,
            'time' => time(),
            'state' => 3,
            'msg' => '',
        ]);
        $message->text = 'Secret hello';
        $message->save();

        $this->assertStringStartsWith('enc:', (string) $message->getRawOriginal('msg'));
        $this->assertSame('Secret hello', $message->text);
    }

    public function test_private_message_routes_accept_encrypted_conversation_keys_and_legacy_numeric_ids(): void
    {
        $this->seedSiteSettings();

        $sender = User::factory()->create(['username' => 'routingsender']);
        $receiver = User::factory()->create(['username' => 'routingreceiver']);

        $encryptedConversationKey = Message::encodeConversationRouteKey($sender, $receiver);

        $this->assertNotSame((string) $receiver->id, $encryptedConversationKey);

        $this->actingAs($sender);

        $this->get(route('messages.show', $encryptedConversationKey))
            ->assertOk()
            ->assertSeeText($receiver->username);

        $this->get(route('messages.show', $receiver->id))
            ->assertOk()
            ->assertSeeText($receiver->username);
    }

    public function test_private_message_thread_shows_a_notice_when_encrypted_messages_begin(): void
    {
        $this->seedSiteSettings();

        $sender = User::factory()->create(['username' => 'plainthenenc']);
        $receiver = User::factory()->create(['username' => 'plainthenenc2']);

        Message::create([
            'name' => $sender->username,
            'us_env' => $sender->id,
            'us_rec' => $receiver->id,
            'msg' => 'Legacy plaintext message',
            'time' => time() - 120,
            'state' => 0,
        ]);

        SecuritySettings::save([
            'private_message_encryption_enabled' => 1,
        ]);

        $encryptedMessage = new Message();
        $encryptedMessage->name = $receiver->username;
        $encryptedMessage->us_env = $receiver->id;
        $encryptedMessage->us_rec = $sender->id;
        $encryptedMessage->text = 'Encrypted follow-up';
        $encryptedMessage->time = time() - 60;
        $encryptedMessage->state = 0;
        $encryptedMessage->save();

        $notice = __('messages.private_messages_encryption_notice');

        $response = $this->actingAs($sender)
            ->get(route('messages.show', Message::encodeConversationRouteKey($sender, $receiver)));

        $response->assertOk()
            ->assertSeeText('Legacy plaintext message')
            ->assertSeeText('Encrypted follow-up')
            ->assertSeeText($notice);

        $this->assertSame(1, substr_count($response->getContent(), $notice));
    }

    public function test_profile_short_route_redirects_numeric_ids_to_public_uid_when_enabled(): void
    {
        $this->seedSiteSettings();

        SecuritySettings::save([
            'public_member_ids_enabled' => 1,
        ]);

        $user = User::factory()->create([
            'username' => 'publicmember',
            'public_uid' => 'PUBTEST12345',
        ]);

        // When public member IDs are enabled, numeric ID lookups must return 404
        // to prevent member ID enumeration attacks.
        $numericResponse = $this->get('/u/id/' . $user->id);
        $numericResponse->assertStatus(404);

        // Public UID lookups should still redirect to the profile page.
        $controller = app(ProfileController::class);
        $publicResponse = $controller->showById($user->public_uid);

        $this->assertSame(route('profile.show', $user->username), $publicResponse->getTargetUrl());
    }

    public function test_header_sidebar_avatar_links_use_public_profile_short_routes(): void
    {
        $this->seedSiteSettings();

        SecuritySettings::save([
            'public_member_ids_enabled' => 1,
        ]);

        $user = User::factory()->create([
            'username' => 'headerpublic',
            'public_uid' => 'HEADPUB12345',
        ]);

        $this->actingAs($user);

        $expectedUrl = route('profile.short', $user->publicRouteIdentifier());
        $legacyUrl = url('/u/' . $user->id);

        $navHtml = view('theme::partials.header.nav')->render();
        $mobileSidebarHtml = view('theme::partials.header.mobile_sidebar', [
            'site_menus' => collect(),
        ])->render();
        $desktopSidebarHtml = view('theme::partials.header.desktop_sidebar', [
            'site_settings' => (object) ['titer' => 'MyAds'],
            'site_menus' => collect(),
            'available_languages' => collect(),
        ])->render();

        $this->assertStringContainsString('href="' . $expectedUrl . '"', $navHtml);
        $this->assertStringContainsString('href="' . $expectedUrl . '"', $mobileSidebarHtml);
        $this->assertStringContainsString('href="' . $expectedUrl . '"', $desktopSidebarHtml);
        $this->assertStringNotContainsString($legacyUrl, $navHtml . $mobileSidebarHtml . $desktopSidebarHtml);
    }
}
