<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumModerator;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumLockedTopicCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_member_cannot_comment_on_locked_topic_but_authorized_moderator_can(): void
    {
        $admin = User::factory()->create();
        $owner = User::factory()->create();
        $regularMember = User::factory()->create();
        $moderatorUser = User::factory()->create();

        $category = ForumCategory::create([
            'name' => 'Locked Category',
            'icons' => 'fa-comments',
            'txt' => 'Locked',
            'ordercat' => 1,
        ]);

        $topic = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'Locked Topic',
            'txt' => 'Locked topic text',
            'cat' => $category->id,
            'statu' => 1,
            'date' => time(),
            'reply' => 0,
            'vu' => 0,
            'is_locked' => 1,
            'locked_at' => time(),
            'locked_by' => $admin->id,
        ]);

        $moderator = ForumModerator::create([
            'user_id' => $moderatorUser->id,
            'is_global' => 0,
            'permissions' => ['lock_topics'],
            'is_active' => 1,
            'created_by' => $admin->id,
        ]);
        $moderator->categories()->sync([$category->id]);

        $this->actingAs($regularMember)
            ->postJson('/comment/store', [
                'id' => $topic->id,
                'type' => 'forum',
                'comment' => 'Blocked comment',
            ])
            ->assertStatus(403);

        $response = $this->actingAs($moderatorUser)
            ->post('/comment/store', [
                'id' => $topic->id,
                'type' => 'forum',
                'comment' => 'Allowed moderator comment',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('f_coment', [
            'tid' => $topic->id,
            'uid' => $moderatorUser->id,
            'txt' => 'Allowed moderator comment',
        ]);
    }
}
