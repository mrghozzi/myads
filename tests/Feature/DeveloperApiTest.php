<?php

namespace Tests\Feature;

use App\Models\DeveloperApp;
use App\Models\DeveloperAccessToken;
use App\Models\ForumTopic;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeveloperApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private DeveloperApp $devApp;
    private string $plainToken;
    private DeveloperAccessToken $tokenRecord;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'username' => 'testdeveloper',
            'email' => 'dev@example.com',
            'pts' => 150,
        ]);

        $this->devApp = DeveloperApp::create([
            'user_id' => $this->user->id,
            'name' => 'Test App',
            'domain' => 'https://example.com',
            'description' => 'Test',
            'client_id' => Str::random(40),
            'client_secret' => Str::random(60),
            'redirect_uris' => ['https://example.com/callback'],
            'requested_scopes' => ['user.identity.read', 'user.profile.read', 'user.content.write'],
            'status' => 'active',
        ]);

        $this->plainToken = Str::random(60);
        $this->tokenRecord = DeveloperAccessToken::create([
            'developer_app_id' => $this->devApp->id,
            'user_id' => $this->user->id,
            'access_token' => hash('sha256', $this->plainToken),
            'scopes' => ['user.identity.read', 'user.profile.read', 'user.content.write'],
            'expires_at' => now()->addDays(30),
            'revoked' => false,
        ]);
    }

    public function test_me_endpoint_returns_user_identity_with_bearer_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->plainToken,
            'Accept' => 'application/json',
        ])->getJson('/api/developer/v1/me');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'username' => 'testdeveloper',
                'email' => 'dev@example.com',
            ],
        ]);
    }

    public function test_me_endpoint_works_with_query_parameter()
    {
        $response = $this->getJson('/api/developer/v1/me?access_token=' . $this->plainToken);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'username' => 'testdeveloper',
                'email' => 'dev@example.com',
            ],
        ]);
    }

    public function test_my_profile_returns_user_profile()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->plainToken,
        ])->getJson('/api/developer/v1/me/profile');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'username' => 'testdeveloper',
                'points' => 150,
            ],
        ]);
    }

    public function test_post_content_creates_forum_topic_and_status()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->plainToken,
        ])->postJson('/api/developer/v1/me/content', [
            'content' => "First line title\n\nThis is the post content body.",
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Post created successfully',
        ]);

        $statusId = $response->json('data.id');
        $this->assertNotNull($statusId);

        $status = Status::find($statusId);
        $this->assertNotNull($status);
        $this->assertEquals($this->user->id, $status->uid);
        $this->assertEquals(100, $status->s_type);
        $this->assertEquals(1, $status->statu);
        $this->assertNotNull($status->tp_id);

        $topic = ForumTopic::find($status->tp_id);
        $this->assertNotNull($topic);
        $this->assertEquals('First line title', $topic->name);
        $this->assertEquals($this->user->id, $topic->uid);
    }

    public function test_string_scopes_in_database_are_properly_parsed()
    {
        // Simulate legacy or alternate scope storage as space-separated string
        $this->tokenRecord->scopes = 'user.identity.read user.profile.read';
        $this->tokenRecord->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->plainToken,
        ])->getJson('/api/developer/v1/me');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'username' => 'testdeveloper',
            ],
        ]);
    }

    public function test_post_content_accepts_alias_scope_content_write()
    {
        // Token has alias 'content.write' instead of canonical 'user.content.write'
        $this->tokenRecord->scopes = ['content.write'];
        $this->tokenRecord->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->plainToken,
        ])->postJson('/api/developer/v1/me/content', [
            'content' => 'Alias post content',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Post created successfully',
        ]);
    }

    public function test_insufficient_scope_returns_403()
    {
        // Token only has identity scope
        $this->tokenRecord->scopes = ['user.identity.read'];
        $this->tokenRecord->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->plainToken,
        ])->postJson('/api/developer/v1/me/content', [
            'content' => 'Sample test post',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Insufficient scope',
        ]);
    }

    public function test_missing_token_returns_401()
    {
        $response = $this->getJson('/api/developer/v1/me');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Missing or invalid Authorization header',
        ]);
    }

    public function test_expired_token_returns_401()
    {
        $this->tokenRecord->expires_at = now()->subDay();
        $this->tokenRecord->save();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->plainToken,
        ])->getJson('/api/developer/v1/me');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Invalid or expired token',
        ]);
    }
}
