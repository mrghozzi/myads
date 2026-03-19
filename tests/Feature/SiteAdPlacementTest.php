<?php

namespace Tests\Feature;

use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Setting;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteAdPlacementTest extends TestCase
{
    use RefreshDatabase;

    private const HOME_SLOT_CODE = 'SITE-AD-HOME-SLOT';
    private const TOPIC_SLOT_CODE = 'SITE-AD-TOPIC-SLOT';
    private const FOOTER_SLOT_CODE = 'SITE-AD-FOOTER-SLOT';

    public function test_welcome_page_renders_home_page_and_footer_slots_once(): void
    {
        $this->seedSiteAds();

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee(self::HOME_SLOT_CODE, false)
            ->assertSee(self::FOOTER_SLOT_CODE, false)
            ->assertDontSee(self::TOPIC_SLOT_CODE, false);

        $this->assertSame(1, substr_count($response->getContent(), self::HOME_SLOT_CODE));
        $this->assertSame(1, substr_count($response->getContent(), self::FOOTER_SLOT_CODE));
    }

    public function test_forum_topic_page_renders_topic_and_footer_slots(): void
    {
        $this->seedSiteAds();

        $author = User::factory()->create();
        $category = ForumCategory::create([
            'name' => 'UI Category',
            'icons' => 'fa-comments',
            'txt' => 'Forum category for ad placement checks',
            'ordercat' => 1,
        ]);

        $topic = ForumTopic::create([
            'uid' => $author->id,
            'name' => 'Topic Slot Check',
            'txt' => 'Forum topic body',
            'cat' => $category->id,
            'statu' => 1,
            'date' => time(),
            'reply' => 0,
            'vu' => 0,
        ]);

        Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            's_type' => 2,
            'date' => time(),
            'statu' => 1,
        ]);

        $response = $this->actingAs($author)->get('/t' . $topic->id);

        $response->assertOk()
            ->assertSee(self::TOPIC_SLOT_CODE, false)
            ->assertSee(self::FOOTER_SLOT_CODE, false)
            ->assertDontSee(self::HOME_SLOT_CODE, false);
    }

    public function test_directory_and_store_detail_pages_render_topic_and_footer_slots(): void
    {
        $this->seedSiteAds();

        $owner = User::factory()->create(['username' => 'detail-owner']);

        $category = DirectoryCategory::create([
            'name' => 'Showcase',
            'sub' => 0,
            'ordercat' => 1,
            'statu' => 1,
            'txt' => 'Directory category',
            'metakeywords' => null,
        ]);

        $listing = Directory::create([
            'uid' => $owner->id,
            'name' => 'Directory Detail',
            'url' => 'https://example.test/listing',
            'txt' => 'Directory description',
            'metakeywords' => 'listing,directory',
            'cat' => $category->id,
            'vu' => 0,
            'statu' => 1,
            'date' => time(),
        ]);

        Status::create([
            'uid' => $owner->id,
            'tp_id' => $listing->id,
            's_type' => 1,
            'date' => time(),
            'statu' => 1,
        ]);

        $directoryResponse = $this->get('/directory/' . $listing->id);

        $directoryResponse->assertOk()
            ->assertSee(self::TOPIC_SLOT_CODE, false)
            ->assertSee(self::FOOTER_SLOT_CODE, false)
            ->assertDontSee(self::HOME_SLOT_CODE, false);

        DB::table('options')->insert([
            'id' => 101,
            'name' => 'store-slot-check',
            'o_valuer' => 'Store body copy',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 0,
            'o_mode' => '',
        ]);

        Status::create([
            'uid' => $owner->id,
            'tp_id' => 101,
            's_type' => 7867,
            'date' => time(),
            'statu' => 1,
        ]);

        $storeResponse = $this->get('/store/store-slot-check');

        $storeResponse->assertOk()
            ->assertSee(self::TOPIC_SLOT_CODE, false)
            ->assertSee(self::FOOTER_SLOT_CODE, false)
            ->assertDontSee(self::HOME_SLOT_CODE, false);
    }

    public function test_dashboard_renders_footer_without_home_page_slot(): void
    {
        $this->seedSiteAds();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/home');

        $response->assertOk()
            ->assertSee(self::FOOTER_SLOT_CODE, false)
            ->assertDontSee(self::HOME_SLOT_CODE, false)
            ->assertDontSee(self::TOPIC_SLOT_CODE, false);
    }

    public function test_admin_site_ads_page_does_not_render_footer_slot(): void
    {
        $this->seedSiteAds();

        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'admin-user',
            'email' => 'admin@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/site-ads');

        $response->assertOk()
            ->assertDontSee('<div class="ads-container"', false);
    }

    private function seedSiteAds(): void
    {
        Setting::create([
            'titer' => 'MyAds',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);

        DB::table('ads')->insert([
            [
                'id' => 1,
                'name' => 'Home Page',
                'code_ads' => self::HOME_SLOT_CODE,
            ],
            [
                'id' => 5,
                'name' => 'Topic',
                'code_ads' => self::TOPIC_SLOT_CODE,
            ],
            [
                'id' => 6,
                'name' => 'Footer',
                'code_ads' => self::FOOTER_SLOT_CODE,
            ],
        ]);
    }
}
