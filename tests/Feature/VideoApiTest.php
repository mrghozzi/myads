<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Status;
use App\Models\StatusLinkPreview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Concerns\SeedsSiteSettings;

class VideoApiTest extends TestCase
{
    use RefreshDatabase, SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();

        Option::updateOrCreate(
            ['o_type' => 'mobile_api', 'name' => 'api_key'],
            ['o_valuer' => 'test-api-key']
        );
    }

    public function test_video_api_feed_returns_successful_structure()
    {
        $user = User::factory()->create();

        // Create a video post (s_type = 10)
        Status::create([
            'uid' => $user->id,
            's_type' => 10,
            'txt' => 'Sample API Video Post',
            'date' => time(),
        ]);

        // Create a clip post (s_type = 14)
        Status::create([
            'uid' => $user->id,
            's_type' => 14,
            'txt' => 'Sample API Clip Post',
            'date' => time(),
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders(['X-API-KEY' => 'test-api-key'])
            ->getJson('/api/video/feed');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'filter',
                'search_query',
                'clips',
                'videos',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    public function test_video_api_feed_excludes_directory_listings_and_store_products()
    {
        $user = User::factory()->create();

        // Create directory listing (s_type = 1) with a YouTube link preview
        $directoryPost = Status::create([
            'uid' => $user->id,
            's_type' => 1,
            'txt' => 'Directory Site with YouTube Video',
            'date' => time(),
        ]);

        StatusLinkPreview::create([
            'status_id' => $directoryPost->id,
            'url' => 'https://www.youtube.com/watch?v=directoryTestId',
            'title' => 'Directory Site YouTube Video',
        ]);

        // Create store product (s_type = 7867) with a YouTube link preview
        $storePost = Status::create([
            'uid' => $user->id,
            's_type' => 7867,
            'txt' => 'Store Product with YouTube Video',
            'date' => time(),
        ]);

        StatusLinkPreview::create([
            'status_id' => $storePost->id,
            'url' => 'https://www.youtube.com/watch?v=storeTestId',
            'title' => 'Store Product YouTube Video',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders(['X-API-KEY' => 'test-api-key'])
            ->getJson('/api/video/feed');

        $response->assertStatus(200);

        $videoIds = collect($response->json('videos.data'))->pluck('id')->toArray();

        $this->assertNotContains($directoryPost->id, $videoIds, 'Directory post must be excluded from Video API feed.');
        $this->assertNotContains($storePost->id, $videoIds, 'Store product must be excluded from Video API feed.');
    }
}
