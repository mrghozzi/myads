<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Option;
use App\Models\Status;
use App\Models\ForumTopic;
use App\Models\User;
use App\Support\GroupSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class GroupApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSiteSettings(['url' => 'http://localhost']);
        Option::updateOrCreate(
            ['o_type' => 'mobile_api', 'name' => 'api_key'],
            ['o_valuer' => 'test-api-key']
        );
        GroupSettings::clearCache();
        GroupSettings::save(GroupSettings::DEFAULTS);
    }

    protected function apiHeaders(): array
    {
        return ['X-API-KEY' => 'test-api-key'];
    }

    public function test_guest_without_sanctum_token_is_unauthorized(): void
    {
        $this->withHeaders($this->apiHeaders())
            ->getJson('/api/groups')
            ->assertUnauthorized();
    }

    public function test_user_can_list_groups_via_api(): void
    {
        $owner = User::factory()->create();
        $group = Group::create([
            'owner_id' => $owner->id,
            'slug' => 'tech-innovators',
            'name' => 'Tech Innovators',
            'category' => 'technology',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'members_count' => 1,
            'posts_count' => 0,
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->withHeaders($this->apiHeaders())
            ->getJson('/api/groups?category=technology');
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['name' => 'Tech Innovators'])
            ->assertJsonFragment(['category' => 'technology']);
    }

    public function test_user_can_view_group_details_via_api(): void
    {
        $owner = User::factory()->create();
        $group = Group::create([
            'owner_id' => $owner->id,
            'slug' => 'dev-community',
            'name' => 'Dev Community',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'members_count' => 1,
            'posts_count' => 0,
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->withHeaders($this->apiHeaders())
            ->getJson("/api/groups/{$group->slug}")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Dev Community')
            ->assertJsonPath('data.permissions.can_view_content', true);
    }

    public function test_user_can_join_and_leave_group_via_api(): void
    {
        $owner = User::factory()->create();
        $group = Group::create([
            'owner_id' => $owner->id,
            'slug' => 'open-circle',
            'name' => 'Open Circle',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'members_count' => 1,
            'posts_count' => 0,
        ]);

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Join
        $this->withHeaders($this->apiHeaders())
            ->postJson("/api/groups/{$group->id}/join")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', GroupMembership::STATUS_ACTIVE);

        $this->assertDatabaseHas('group_memberships', [
            'group_id' => $group->id,
            'user_id' => $user->id,
            'status' => GroupMembership::STATUS_ACTIVE,
        ]);

        // Leave
        $this->withHeaders($this->apiHeaders())
            ->postJson("/api/groups/{$group->id}/leave")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('group_memberships', [
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_moderator_can_pin_post_via_api(): void
    {
        $owner = User::factory()->create();
        $mod = User::factory()->create();
        $group = Group::create([
            'owner_id' => $owner->id,
            'slug' => 'mod-group',
            'name' => 'Mod Group',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'members_count' => 2,
            'posts_count' => 1,
        ]);

        GroupMembership::create([
            'group_id' => $group->id,
            'user_id' => $mod->id,
            'role' => GroupMembership::ROLE_MODERATOR,
            'status' => GroupMembership::STATUS_ACTIVE,
        ]);

        $topic = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'text',
            'txt' => 'Post to pin',
            'cat' => 0,
            'group_id' => $group->id,
            'statu' => 1,
            'date' => time(),
        ]);

        $status = Status::create([
            'uid' => $owner->id,
            'tp_id' => $topic->id,
            'group_id' => $group->id,
            's_type' => 100,
            'date' => time(),
            'statu' => 1,
        ]);

        Sanctum::actingAs($mod);

        $this->withHeaders($this->apiHeaders())
            ->postJson("/api/groups/{$group->id}/posts/{$status->id}/pin")
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('is_pinned', true);

        $this->assertSame((int) $status->id, (int) $group->fresh()->pinned_post_id);
    }
}
