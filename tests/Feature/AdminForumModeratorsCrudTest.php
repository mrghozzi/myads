<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumModerator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminForumModeratorsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_forum_moderator_with_permissions_and_scope(): void
    {
        $admin = User::factory()->create();
        $moderatorUser = User::factory()->create();

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

        $this->actingAs($admin)
            ->post('/admin/forum/moderators', [
                'user_id' => $moderatorUser->id,
                'is_active' => 1,
                'permissions' => ['pin_topics', 'delete_comments'],
                'category_ids' => [$categoryA->id],
            ])
            ->assertStatus(302);

        $moderator = ForumModerator::where('user_id', $moderatorUser->id)->first();
        $this->assertNotNull($moderator);
        $this->assertDatabaseHas('forum_moderator_categories', [
            'moderator_id' => $moderator->id,
            'category_id' => $categoryA->id,
        ]);

        $this->actingAs($admin)
            ->put('/admin/forum/moderators/' . $moderator->id, [
                'user_id' => $moderatorUser->id,
                'is_global' => 1,
                'is_active' => 1,
                'permissions' => ['lock_topics', 'delete_topics'],
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('forum_moderators', [
            'id' => $moderator->id,
            'user_id' => $moderatorUser->id,
            'is_global' => 1,
            'is_active' => 1,
        ]);
        $this->assertDatabaseMissing('forum_moderator_categories', [
            'moderator_id' => $moderator->id,
            'category_id' => $categoryA->id,
        ]);
        $this->assertDatabaseMissing('forum_moderator_categories', [
            'moderator_id' => $moderator->id,
            'category_id' => $categoryB->id,
        ]);

        $this->actingAs($admin)
            ->delete('/admin/forum/moderators/' . $moderator->id)
            ->assertStatus(302);

        $this->assertDatabaseMissing('forum_moderators', ['id' => $moderator->id]);
    }
}
