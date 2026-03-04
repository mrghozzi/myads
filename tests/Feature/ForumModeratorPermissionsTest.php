<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumModerator;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumModeratorPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_moderator_can_delete_topics_only_inside_assigned_categories(): void
    {
        $admin = User::factory()->create();
        $moderatorUser = User::factory()->create();
        $owner = User::factory()->create();

        $allowedCategory = ForumCategory::create([
            'name' => 'Allowed Section',
            'icons' => 'fa-comments',
            'txt' => 'Allowed section',
            'ordercat' => 1,
        ]);

        $blockedCategory = ForumCategory::create([
            'name' => 'Blocked Section',
            'icons' => 'fa-comments',
            'txt' => 'Blocked section',
            'ordercat' => 2,
        ]);

        $moderator = ForumModerator::create([
            'user_id' => $moderatorUser->id,
            'is_global' => 0,
            'permissions' => ['delete_topics'],
            'is_active' => 1,
            'created_by' => $admin->id,
        ]);
        $moderator->categories()->sync([$allowedCategory->id]);

        $topicInAllowedCategory = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'Allowed Topic',
            'txt' => 'Content',
            'cat' => $allowedCategory->id,
            'statu' => 1,
            'date' => time(),
            'reply' => 0,
            'vu' => 0,
        ]);

        $topicInBlockedCategory = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'Blocked Topic',
            'txt' => 'Content',
            'cat' => $blockedCategory->id,
            'statu' => 1,
            'date' => time(),
            'reply' => 0,
            'vu' => 0,
        ]);

        $this->actingAs($moderatorUser)
            ->postJson('/forum/delete', ['id' => $topicInAllowedCategory->id])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('forum', ['id' => $topicInAllowedCategory->id]);

        $this->actingAs($moderatorUser)
            ->postJson('/forum/delete', ['id' => $topicInBlockedCategory->id])
            ->assertStatus(403);

        $this->assertDatabaseHas('forum', ['id' => $topicInBlockedCategory->id]);
    }
}
