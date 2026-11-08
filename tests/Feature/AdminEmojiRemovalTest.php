<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\SiteAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AdminEmojiRemovalTest extends TestCase
{
    use RefreshDatabase, SeedsSiteSettings;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();

        $this->admin = User::factory()->create(['id' => 1]);
        SiteAdmin::create([
            'user_id' => $this->admin->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);
    }

    private function actingAsAdmin()
    {
        return $this->actingAs($this->admin)->withSession(['admin_password_confirmed_at' => time()]);
    }

    public function test_emojis_database_table_is_dropped(): void
    {
        $this->assertFalse(Schema::hasTable('emojis'), 'The emojis table should not exist in the database.');
    }

    public function test_emoji_model_and_routes_do_not_exist(): void
    {
        $this->assertFileDoesNotExist(app_path('Models/Emoji.php'), 'Emoji model file should be deleted.');
        $this->assertFalse(Route::has('admin.emojis'), 'admin.emojis route should be removed.');
        $this->assertFalse(Route::has('admin.emojis.store'), 'admin.emojis.store route should be removed.');
        $this->assertFalse(Route::has('admin.emojis.delete'), 'admin.emojis.delete route should be removed.');
    }

    public function test_admin_emojis_endpoints_return_404(): void
    {
        $this->actingAsAdmin()->get('/admin/emojis')->assertNotFound();
        $this->actingAsAdmin()->post('/admin/emojis', ['name' => ':test:', 'img' => 'test.png'])->assertNotFound();
        $this->actingAsAdmin()->delete('/admin/emojis/1')->assertNotFound();
    }

    public function test_admin_news_page_renders_cleanly_without_emojis_reference(): void
    {
        News::create([
            'name' => 'System Update Announcement',
            'text' => 'We have updated to v4.7.0.',
            'date' => time(),
            'statu' => 1,
        ]);

        $response = $this->actingAsAdmin()->get(route('admin.news'));
        $response->assertOk();
        $response->assertSee('System Update Announcement');
        $response->assertDontSee('/admin/emojis');
    }

    public function test_post_creation_and_store_creation_pages_render_without_500_error(): void
    {
        \App\Models\ForumCategory::create([
            'name' => 'General Discussion',
            'ordercat' => 1,
            'statu' => 1,
            'sub' => 0,
        ]);

        // /post page
        $postResponse = $this->actingAsAdmin()->get('/post');
        $postResponse->assertOk();

        // /store/create page
        $storeResponse = $this->actingAsAdmin()->get('/store/create');
        $storeResponse->assertOk();

        // /editor/{id} page
        $topic = \App\Models\ForumTopic::create([
            'title' => 'Test Topic',
            'body' => 'Test topic content',
            'cat' => 1,
            'uid' => $this->admin->id,
            'date' => time(),
        ]);
        $editorResponse = $this->actingAsAdmin()->get('/editor/' . $topic->id);
        $editorResponse->assertOk();
    }
}

