<?php

namespace Tests\Feature;

use App\Models\Directory;
use App\Models\ForumTopic;
use App\Models\Product;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class VideoHubIsolationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    public function test_video_hub_displays_video_posts_and_excludes_directory_listings_and_store_products(): void
    {
        $user = User::factory()->create();

        // 1. Genuine video post
        $videoStatus = Status::create([
            'uid' => $user->id,
            's_type' => 10,
            'txt' => 'Genuine Video Title',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'statu' => 1,
            'date' => time(),
        ]);

        // 2. Directory listing (s_type = 1) with YouTube link preview
        $directoryStatus = Status::create([
            'uid' => $user->id,
            'tp_id' => 50,
            's_type' => 1,
            'txt' => 'Directory Site Unique Search Text',
            'statu' => 1,
            'date' => time(),
        ]);

        StatusLinkPreview::create([
            'status_id' => $directoryStatus->id,
            'url' => 'https://www.youtube.com/watch?v=directoryVideoId',
            'title' => 'Directory Site Link Preview',
            'description' => 'Preview description',
            'domain' => 'youtube.com',
        ]);

        // 3. Store Product (s_type = 7867) with YouTube link preview
        $storeStatus = Status::create([
            'uid' => $user->id,
            'tp_id' => 60,
            's_type' => 7867,
            'txt' => 'Store Product Unique Search Text',
            'statu' => 1,
            'date' => time(),
        ]);

        StatusLinkPreview::create([
            'status_id' => $storeStatus->id,
            'url' => 'https://www.youtube.com/watch?v=storeVideoId',
            'title' => 'Store Product Link Preview',
            'description' => 'Store preview description',
            'domain' => 'youtube.com',
        ]);

        // Visit Video Hub
        $response = $this->get(route('video.index'));
        $response->assertOk();
        $response->assertSee('Genuine Video Title');
        $response->assertDontSee('Directory Site Unique Search Text');
        $response->assertDontSee('Store Product Unique Search Text');
    }

    public function test_status_related_content_returns_directory_and_not_forum_topic(): void
    {
        $user = User::factory()->create();

        $topic = ForumTopic::create([
            'uid' => $user->id,
            'cat' => 1,
            'name' => 'Forum Topic With Same ID',
            'txt' => 'Topic body',
            'date' => time(),
        ]);

        $directory = Directory::create([
            'uid' => $user->id,
            'name' => 'Sample Directory Site',
            'url' => 'https://example.com',
            'cat' => 1,
            'statu' => 1,
            'date' => time(),
        ]);

        $directoryStatus = Status::create([
            'uid' => $user->id,
            'tp_id' => $directory->id,
            's_type' => 1,
            'txt' => 'Directory Status sharing same tp_id as topic',
            'statu' => 1,
            'date' => time(),
        ]);

        $this->assertInstanceOf(Directory::class, $directoryStatus->related_content);
        $this->assertNotInstanceOf(ForumTopic::class, $directoryStatus->related_content);
    }
}
