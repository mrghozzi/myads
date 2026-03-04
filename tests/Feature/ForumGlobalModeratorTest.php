<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumModerator;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumGlobalModeratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_moderator_can_pin_and_lock_topics_in_all_categories(): void
    {
        $admin = User::factory()->create();
        $globalModeratorUser = User::factory()->create();
        $owner = User::factory()->create();

        $categoryA = ForumCategory::create([
            'name' => 'Category A',
            'icons' => 'fa-comments',
            'txt' => 'A',
            'ordercat' => 1,
        ]);
        $categoryB = ForumCategory::create([
            'name' => 'Category B',
            'icons' => 'fa-comments',
            'txt' => 'B',
            'ordercat' => 2,
        ]);

        ForumModerator::create([
            'user_id' => $globalModeratorUser->id,
            'is_global' => 1,
            'permissions' => ['pin_topics', 'lock_topics'],
            'is_active' => 1,
            'created_by' => $admin->id,
        ]);

        $topicA = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'Topic A',
            'txt' => 'Content A',
            'cat' => $categoryA->id,
            'statu' => 1,
            'date' => time(),
            'reply' => 0,
            'vu' => 0,
        ]);

        $topicB = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'Topic B',
            'txt' => 'Content B',
            'cat' => $categoryB->id,
            'statu' => 1,
            'date' => time(),
            'reply' => 0,
            'vu' => 0,
        ]);

        $this->actingAs($globalModeratorUser)
            ->postJson('/forum/' . $topicA->id . '/pin')
            ->assertOk()
            ->assertJsonPath('is_pinned', true);

        $this->actingAs($globalModeratorUser)
            ->postJson('/forum/' . $topicB->id . '/pin')
            ->assertOk()
            ->assertJsonPath('is_pinned', true);

        $this->actingAs($globalModeratorUser)
            ->postJson('/forum/' . $topicA->id . '/lock')
            ->assertOk()
            ->assertJsonPath('is_locked', true);

        $this->actingAs($globalModeratorUser)
            ->postJson('/forum/' . $topicB->id . '/lock')
            ->assertOk()
            ->assertJsonPath('is_locked', true);

        $this->assertDatabaseHas('forum', ['id' => $topicA->id, 'is_pinned' => 1, 'is_locked' => 1]);
        $this->assertDatabaseHas('forum', ['id' => $topicB->id, 'is_pinned' => 1, 'is_locked' => 1]);
    }
}
