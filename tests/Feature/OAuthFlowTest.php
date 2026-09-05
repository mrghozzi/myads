<?php

namespace Tests\Feature;

use App\Models\DeveloperApp;
use App\Models\DeveloperAuthorization;
use App\Models\DeveloperAuthorizationCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OAuthFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $appOwner;
    private User $endUser;
    private DeveloperApp $developerApp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);
        
        $this->appOwner = User::factory()->create();
        $this->endUser = User::factory()->create();
        
        $this->developerApp = DeveloperApp::create([
            'user_id' => $this->appOwner->id,
            'name' => 'OAuth Test App',
            'domain' => 'https://example.com',
            'description' => 'Test',
            'client_id' => Str::random(40),
            'client_secret' => Str::random(60),
            'redirect_uris' => [
                'https://example.com/callback',
                'https://wordpresstest.is-best.net/wp-admin/admin.php?page=adstn-auto-poster&action=adstn_oauth_callback'
            ],
            'requested_scopes' => ['user.identity.read', 'user.profile.read', 'user.content.write'],
            'status' => 'active'
        ]);
    }

    public function test_authorize_endpoint_validates_request_and_shows_consent_screen()
    {
        $response = $this->actingAs($this->endUser)->get('/oauth/authorize?' . http_build_query([
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => 'https://example.com/callback',
            'response_type' => 'code',
            'scope' => 'user.profile.read',
            'state' => 'xyz123'
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->developerApp->name);
        $response->assertSee($this->developerApp->domain);
        $response->assertSee(__('messages.authorize_app'));
        $response->assertSee(__('messages.requested_permissions'));
        $response->assertSee(__('messages.dev_scope_profile_read'));
        $response->assertSee(__('messages.dev_scope_profile_read_desc'));
        $response->assertSee(__('messages.authorize_disclaimer'));
        $response->assertSee('name="client_id"', false);
        $response->assertSee('value="' . $this->developerApp->client_id . '"', false);
        $response->assertSee('name="scope"', false);
        $response->assertSee('value="user.profile.read"', false);
    }

    public function test_user_can_accept_authorization_and_get_code()
    {
        $response = $this->actingAs($this->endUser)->post('/oauth/authorize', [
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => 'https://example.com/callback',
            'response_type' => 'code',
            'scope' => 'user.profile.read',
            'state' => 'xyz123',
            'action' => 'accept'
        ]);

        $response->assertRedirectContains('https://example.com/callback?code=');
        $response->assertRedirectContains('&state=xyz123');

        $authorizationCode = DeveloperAuthorizationCode::query()
            ->where('developer_app_id', $this->developerApp->id)
            ->where('user_id', $this->endUser->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($authorizationCode);
        $this->assertFalse((bool) $authorizationCode->used);
    }

    public function test_authorized_apps_settings_page_renders_for_member(): void
    {
        DeveloperAuthorization::create([
            'user_id' => $this->endUser->id,
            'developer_app_id' => $this->developerApp->id,
            'scopes' => ['user.profile.read'],
        ]);

        $response = $this->actingAs($this->endUser)->get('/settings/apps');

        $response->assertOk();
        $response->assertSee(__('messages.authorized_apps'));
        $response->assertSee(__('messages.account_settings')); // From settings_nav
        $response->assertSee(route('profile.apps')); // Check the nav link exists
        $response->assertSee('section-banner'); // New shell element
        $response->assertSee('widget-box'); // New shell element
        $response->assertSee($this->developerApp->name);
        $response->assertSee($this->developerApp->domain);
        $response->assertSee(__('messages.revoke_access'));
    }

    public function test_user_can_revoke_app_authorization(): void
    {
        $auth = DeveloperAuthorization::create([
            'user_id' => $this->endUser->id,
            'developer_app_id' => $this->developerApp->id,
            'scopes' => ['user.profile.read'],
        ]);

        $response = $this->actingAs($this->endUser)
            ->from('/settings/apps')
            ->post("/settings/apps/{$auth->id}/revoke");

        $response->assertRedirect('/settings/apps');
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('developer_authorizations', [
            'id' => $auth->id
        ]);
    }

    public function test_authorized_apps_shows_empty_state_when_none_authorized(): void
    {
        $response = $this->actingAs($this->endUser)->get('/settings/apps');

        $response->assertOk();
        $response->assertSee(__('messages.no_authorized_apps'));
    }

    public function test_token_endpoint_exchanges_code_for_tokens()
    {
        // 1. Create a valid authorization code manually
        $codeStr = Str::random(40);
        DeveloperAuthorizationCode::create([
            'developer_app_id' => $this->developerApp->id,
            'user_id' => $this->endUser->id,
            'code' => hash('sha256', $codeStr),
            'redirect_uri' => 'https://example.com/callback',
            'scopes' => ['user.profile.read'],
            'expires_at' => now()->addMinutes(10)
        ]);

        // 2. Call token endpoint
        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->developerApp->client_id,
            'client_secret' => $this->developerApp->client_secret, // Raw secret
            'redirect_uri' => 'https://example.com/callback',
            'code' => $codeStr
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
            'refresh_token'
        ]);
        
        $authorizationCode = DeveloperAuthorizationCode::query()
            ->where('developer_app_id', $this->developerApp->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($authorizationCode);
        $this->assertTrue((bool) $authorizationCode->used);

        // Ensure tokens were created
        $this->assertDatabaseHas('developer_access_tokens', [
            'developer_app_id' => $this->developerApp->id,
            'user_id' => $this->endUser->id
        ]);
    }

    public function test_user_can_accept_authorization_with_query_params_in_redirect_uri()
    {
        $wpRedirectUri = 'https://wordpresstest.is-best.net/wp-admin/admin.php?page=adstn-auto-poster&action=adstn_oauth_callback';

        $response = $this->actingAs($this->endUser)->post('/oauth/authorize', [
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => $wpRedirectUri,
            'response_type' => 'code',
            'scope' => 'user.identity.read user.profile.read user.content.write',
            'state' => '2e98bb54bd',
            'action' => 'accept'
        ]);

        // RFC 6749 Section 4.1.2: query params MUST be appended with &, not ?
        $expectedRedirectPrefix = $wpRedirectUri . '&code=';
        $response->assertRedirectContains($expectedRedirectPrefix);
        $response->assertRedirectContains('&state=2e98bb54bd');

        // Confirm there is NO double question mark in the redirect target
        $targetUrl = $response->headers->get('Location');
        $this->assertEquals(1, substr_count($targetUrl, '?'), "Redirect URL should contain exactly one '?' character");
    }

    public function test_user_can_reject_authorization_with_query_params_in_redirect_uri()
    {
        $wpRedirectUri = 'https://wordpresstest.is-best.net/wp-admin/admin.php?page=adstn-auto-poster&action=adstn_oauth_callback';

        $response = $this->actingAs($this->endUser)->post('/oauth/authorize', [
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => $wpRedirectUri,
            'response_type' => 'code',
            'scope' => 'user.identity.read',
            'state' => '2e98bb54bd',
            'action' => 'reject'
        ]);

        $expectedRedirect = $wpRedirectUri . '&error=access_denied&state=2e98bb54bd';
        $response->assertRedirect($expectedRedirect);
    }

    public function test_scopes_can_be_delimited_by_commas_or_spaces()
    {
        $wpRedirectUri = 'https://wordpresstest.is-best.net/wp-admin/admin.php?page=adstn-auto-poster&action=adstn_oauth_callback';

        $response = $this->actingAs($this->endUser)->post('/oauth/authorize', [
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => $wpRedirectUri,
            'response_type' => 'code',
            'scope' => 'user.identity.read, user.profile.read, user.content.write',
            'state' => 'state_comma_test',
            'action' => 'accept'
        ]);

        $response->assertRedirectContains($wpRedirectUri . '&code=');

        $authCode = DeveloperAuthorizationCode::query()
            ->where('developer_app_id', $this->developerApp->id)
            ->where('user_id', $this->endUser->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($authCode);
        $this->assertContains('user.identity.read', $authCode->scopes);
        $this->assertContains('user.profile.read', $authCode->scopes);
        $this->assertContains('user.content.write', $authCode->scopes);
    }

    public function test_token_endpoint_exchanges_code_for_tokens_with_query_params_in_redirect_uri()
    {
        $wpRedirectUri = 'https://wordpresstest.is-best.net/wp-admin/admin.php?page=adstn-auto-poster&action=adstn_oauth_callback';

        $codeStr = Str::random(40);
        DeveloperAuthorizationCode::create([
            'developer_app_id' => $this->developerApp->id,
            'user_id' => $this->endUser->id,
            'code' => hash('sha256', $codeStr),
            'redirect_uri' => $wpRedirectUri,
            'scopes' => ['user.identity.read', 'user.profile.read'],
            'expires_at' => now()->addMinutes(10)
        ]);

        $response = $this->postJson('/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->developerApp->client_id,
            'client_secret' => $this->developerApp->client_secret,
            'redirect_uri' => $wpRedirectUri,
            'code' => $codeStr
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'refresh_token']);
    }

    public function test_token_endpoint_supports_http_basic_authentication()
    {
        $wpRedirectUri = 'https://wordpresstest.is-best.net/wp-admin/admin.php?page=adstn-auto-poster&action=adstn_oauth_callback';

        $codeStr = Str::random(40);
        DeveloperAuthorizationCode::create([
            'developer_app_id' => $this->developerApp->id,
            'user_id' => $this->endUser->id,
            'code' => hash('sha256', $codeStr),
            'redirect_uri' => $wpRedirectUri,
            'scopes' => ['user.profile.read'],
            'expires_at' => now()->addMinutes(10)
        ]);

        // RFC 6749 Section 2.3.1 HTTP Basic authentication
        $response = $this->withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->developerApp->client_id . ':' . $this->developerApp->client_secret),
        ])->postJson('/oauth/token', [
            'grant_type' => 'authorization_code',
            'redirect_uri' => $wpRedirectUri,
            'code' => $codeStr
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token', 'refresh_token']);
    }

    public function test_guest_is_redirected_to_login_with_next_and_intended_session()
    {
        $targetUrl = '/oauth/authorize?' . http_build_query([
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => 'https://example.com/callback',
            'response_type' => 'code',
            'scope' => 'user.profile.read',
            'state' => 'test_guest_state'
        ]);

        $response = $this->get($targetUrl);

        $response->assertRedirect();
        $response->assertSessionHas('url.intended');
    }

    public function test_authorize_with_null_requested_scopes_does_not_fail()
    {
        $this->developerApp->requested_scopes = null;
        $this->developerApp->save();

        $response = $this->actingAs($this->endUser)->get('/oauth/authorize?' . http_build_query([
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => 'https://example.com/callback',
            'response_type' => 'code',
            'scope' => 'user.identity.read user.content.write',
            'state' => 'state_null_scopes'
        ]));

        $response->assertStatus(200);
    }

    public function test_app_owner_can_authorize_draft_app_in_development_mode()
    {
        $this->developerApp->status = 'draft';
        $this->developerApp->save();

        // Developer who owns the app can authorize it
        $response = $this->actingAs($this->appOwner)->get('/oauth/authorize?' . http_build_query([
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => 'https://example.com/callback',
            'response_type' => 'code',
            'scope' => 'user.identity.read',
            'state' => 'state_draft_owner'
        ]));

        $response->assertStatus(200);
    }

    public function test_other_user_cannot_authorize_draft_app()
    {
        $this->developerApp->status = 'draft';
        $this->developerApp->save();

        // End user who does NOT own the app cannot authorize draft app
        $response = $this->actingAs($this->endUser)->get('/oauth/authorize?' . http_build_query([
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => 'https://example.com/callback',
            'response_type' => 'code',
            'scope' => 'user.identity.read',
            'state' => 'state_draft_nonowner'
        ]));

        $response->assertStatus(400);
    }

    public function test_oauth_grants_valid_catalog_scopes_and_syncs_to_app_even_if_initially_missing()
    {
        // App initially only has identity scope
        $this->developerApp->requested_scopes = ['user.identity.read'];
        $this->developerApp->save();

        // Client requests both identity and content.write (e.g. from WordPress plugin)
        $response = $this->actingAs($this->endUser)->post('/oauth/authorize', [
            'client_id' => $this->developerApp->client_id,
            'redirect_uri' => 'https://example.com/callback',
            'response_type' => 'code',
            'scope' => 'user.identity.read user.content.write',
            'state' => 'xyz456',
            'action' => 'accept',
        ]);

        $response->assertRedirectContains('https://example.com/callback?code=');

        $authCode = DeveloperAuthorizationCode::query()
            ->where('developer_app_id', $this->developerApp->id)
            ->where('user_id', $this->endUser->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($authCode);
        $scopes = is_array($authCode->scopes) ? $authCode->scopes : json_decode($authCode->scopes, true);
        $this->assertContains('user.identity.read', $scopes);
        $this->assertContains('user.content.write', $scopes);

        // Verify scopes were synced to the app record
        $this->developerApp->refresh();
        $this->assertContains('user.content.write', $this->developerApp->requested_scopes);
    }
}
