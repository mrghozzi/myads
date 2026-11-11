<?php

namespace Tests\Feature\Themes;

use App\Models\ForumTopic;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Setting;
use App\Models\Status;
use App\Models\User;
use App\Providers\ThemeServiceProvider;
use App\Support\GroupSettings;
use App\Support\SubscriptionSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class BootstrapSampleGroupsTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSiteSettings(['url' => 'http://localhost']);
        GroupSettings::clearCache();
        GroupSettings::save(GroupSettings::DEFAULTS);
        SubscriptionSettings::save([
            'enabled' => 0,
            'base_currency_code' => 'USD',
        ]);

        // Activate bootstrap-sample theme
        Setting::query()->update(['styles' => 'bootstrap-sample']);
        ThemeServiceProvider::applyTheme('bootstrap-sample');
    }

    public function test_bootstrap_sample_groups_index_renders_categories_and_sort_pills(): void
    {
        $owner = User::factory()->create();
        Group::create([
            'owner_id' => $owner->id,
            'slug' => 'tech-pioneers',
            'name' => 'Tech Pioneers',
            'category' => 'technology',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'join_fee_pts' => 50,
            'members_count' => 10,
            'posts_count' => 25,
        ]);

        $response = $this->withSession(['locale' => 'en'])
            ->get('/groups?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSeeText('Tech Pioneers')
            ->assertSeeText('All Categories')
            ->assertSeeText('Technology & AI')
            ->assertSeeText('Trending')
            ->assertSeeText('Most Members')
            ->assertSeeText('50 PTS');
    }

    public function test_bootstrap_sample_groups_create_renders_category_select_and_live_preview(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->get('/groups/create?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSee('name="category"', false)
            ->assertSee('name="posting_permission"', false)
            ->assertSee('name="join_fee_pts"', false)
            ->assertSeeText('General & Community')
            ->assertSeeText('Technology & AI')
            ->assertSeeText('Live')
            ->assertSee('id="preview-title"', false);
    }

    public function test_bootstrap_sample_groups_edit_renders_fields_and_delete_button(): void
    {
        $owner = User::factory()->create();
        $group = Group::create([
            'owner_id' => $owner->id,
            'slug' => 'design-masters',
            'name' => 'Design Masters',
            'category' => 'design',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'members_count' => 1,
            'posts_count' => 0,
        ]);

        $response = $this->actingAs($owner)
            ->withSession(['locale' => 'en'])
            ->get(route('groups.edit', $group) . '?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSee('name="category"', false)
            ->assertSee('name="posting_permission"', false)
            ->assertSee('name="join_fee_pts"', false)
            ->assertSeeText('Design & UI/UX')
            ->assertSeeText('Delete Group');
    }

    public function test_bootstrap_sample_groups_show_renders_badges_and_pinned_announcement(): void
    {
        $owner = User::factory()->create();
        $group = Group::create([
            'owner_id' => $owner->id,
            'slug' => 'crypto-insiders',
            'name' => 'Crypto Insiders',
            'category' => 'trading',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'join_fee_pts' => 100,
            'members_count' => 5,
            'posts_count' => 1,
        ]);

        $topic = ForumTopic::create([
            'uid' => $owner->id,
            'name' => 'text',
            'txt' => 'Important pinned post content',
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

        $group->update(['pinned_post_id' => $status->id]);

        $response = $this->actingAs($owner)
            ->withSession(['locale' => 'en'])
            ->get(route('groups.show', $group) . '?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSeeText('Crypto Insiders')
            ->assertSeeText('Trading & Crypto')
            ->assertSeeText('100 PTS')
            ->assertSeeText('Pinned Announcement')
            ->assertSeeText('Important pinned post content');
    }

    public function test_bootstrap_sample_groups_renders_arabic_properly(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get('/groups/create?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertDontSeeText('groups_cat_general')
            ->assertSeeText('عام ومجتمعي')
            ->assertSeeText('التكنولوجيا والذكاء الاصطناعي');
    }
}
