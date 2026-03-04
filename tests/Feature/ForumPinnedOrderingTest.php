<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Setting;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumPinnedOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_pinned_topic_appears_first_on_category_page(): void
    {
        Setting::create([
            'titer' => 'MyAds',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);

        $owner = User::factory()->create();
        $category = ForumCategory::create([
            'name' => 'Ordering Category',
            'icons' => 'fa-comments',
            'txt' => 'Ordering',
            'ordercat' => 1,
        ]);

        $oldTime = time() - 3600;
        $newTime = time();

        $pinnedTopic = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'Pinned Topic First',
            'txt' => 'Pinned',
            'cat' => $category->id,
            'statu' => 1,
            'date' => $oldTime,
            'reply' => 0,
            'vu' => 0,
            'is_pinned' => 1,
            'pinned_at' => $newTime,
            'pinned_by' => $owner->id,
        ]);

        $recentTopic = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'Recent Topic Second',
            'txt' => 'Recent',
            'cat' => $category->id,
            'statu' => 1,
            'date' => $newTime,
            'reply' => 0,
            'vu' => 0,
            'is_pinned' => 0,
        ]);

        Status::create([
            'uid' => $owner->id,
            'tp_id' => $pinnedTopic->id,
            's_type' => 2,
            'date' => $oldTime,
            'statu' => 1,
        ]);

        Status::create([
            'uid' => $owner->id,
            'tp_id' => $recentTopic->id,
            's_type' => 2,
            'date' => $newTime,
            'statu' => 1,
        ]);

        $response = $this->get('/f' . $category->id);
        $response->assertOk();
        $response->assertSeeInOrder([
            'Pinned Topic First',
            'Recent Topic Second',
        ]);
    }
}
