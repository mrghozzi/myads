<?php

namespace Tests\Feature;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\MemberSubscription;
use App\Models\Status;
use App\Models\User;
use App\Support\GroupSettings;
use App\Support\SubscriptionSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class GroupsFeatureTest extends TestCase
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
    }

    public function test_guest_can_view_public_group_and_see_public_feed_content(): void
    {
        $owner = User::factory()->create(['username' => 'publicgroupowner']);
        $group = $this->createGroup($owner, [
            'slug' => 'wordpress-builders',
            'name' => 'WordPress Builders',
            'short_description' => 'Focused on plugin architecture',
        ]);

        $this->createGroupedStatus($owner, $group, 'Public group update for members');

        $this->get(route('groups.show', $group))
            ->assertOk()
            ->assertSeeText('WordPress Builders')
            ->assertSeeText('Public group update for members')
            ->assertDontSeeText(__('messages.groups_private_shell_title'));
    }

    public function test_guest_sees_private_group_shell_but_cannot_open_private_group_content(): void
    {
        $owner = User::factory()->create(['username' => 'privategroupowner']);
        $group = $this->createGroup($owner, [
            'slug' => 'seo-insiders',
            'name' => 'SEO Insiders',
            'privacy' => Group::PRIVACY_PRIVATE,
            'description' => 'Private search strategy circle',
        ]);

        [$topic] = $this->createGroupedDiscussion($owner, $group, 'Secret Discussion', 'Private growth notes');

        $this->get(route('groups.show', $group))
            ->assertOk()
            ->assertSeeText('SEO Insiders')
            ->assertSeeText(__('messages.groups_private_shell_title'))
            ->assertDontSeeText('Private growth notes');

        $this->get(route('forum.topic', $topic->id))
            ->assertNotFound();
    }

    public function test_public_and_private_join_flows_support_approve_and_reject(): void
    {
        $owner = User::factory()->create(['username' => 'groupowner']);
        $member = User::factory()->create(['username' => 'groupmember']);
        $secondMember = User::factory()->create(['username' => 'groupmembertwo']);

        $publicGroup = $this->createGroup($owner, [
            'slug' => 'saas-builders',
            'name' => 'SaaS Builders',
        ]);

        $this->actingAs($member)
            ->post(route('groups.join', $publicGroup))
            ->assertRedirect();

        $this->assertDatabaseHas('group_memberships', [
            'group_id' => $publicGroup->id,
            'user_id' => $member->id,
            'status' => GroupMembership::STATUS_ACTIVE,
        ]);

        $privateGroup = $this->createGroup($owner, [
            'slug' => 'private-seo-lab',
            'name' => 'Private SEO Lab',
            'privacy' => Group::PRIVACY_PRIVATE,
        ]);

        $this->actingAs($member)
            ->post(route('groups.join', $privateGroup))
            ->assertRedirect();

        $pendingMembership = GroupMembership::query()
            ->where('group_id', $privateGroup->id)
            ->where('user_id', $member->id)
            ->firstOrFail();

        $this->assertSame(GroupMembership::STATUS_PENDING, $pendingMembership->status);

        $this->actingAs($owner)
            ->post(route('groups.members.approve', [$privateGroup, $pendingMembership]))
            ->assertRedirect();

        $this->assertDatabaseHas('group_memberships', [
            'id' => $pendingMembership->id,
            'status' => GroupMembership::STATUS_ACTIVE,
        ]);

        $this->actingAs($secondMember)
            ->post(route('groups.join', $privateGroup))
            ->assertRedirect();

        $rejectedMembership = GroupMembership::query()
            ->where('group_id', $privateGroup->id)
            ->where('user_id', $secondMember->id)
            ->firstOrFail();

        $this->actingAs($owner)
            ->post(route('groups.members.reject', [$privateGroup, $rejectedMembership]))
            ->assertRedirect();

        $this->assertDatabaseHas('group_memberships', [
            'id' => $rejectedMembership->id,
            'status' => GroupMembership::STATUS_REJECTED,
        ]);
    }

    public function test_group_creation_policy_all_members_and_approval_choose_expected_initial_status(): void
    {
        $creator = User::factory()->create(['username' => 'groupcreator']);

        GroupSettings::save([
            'enabled' => 1,
            'creation_policy' => GroupSettings::POLICY_ALL_MEMBERS,
            'eligible_plan_ids' => [],
        ]);

        $this->actingAs($creator)
            ->post(route('groups.store'), [
                'name' => 'All Members Group',
                'slug' => 'all-members-group',
                'privacy' => Group::PRIVACY_PUBLIC,
                'short_description' => 'Open community',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('groups', [
            'slug' => 'all-members-group',
            'status' => Group::STATUS_ACTIVE,
        ]);

        GroupSettings::save([
            'enabled' => 1,
            'creation_policy' => GroupSettings::POLICY_APPROVAL,
            'eligible_plan_ids' => [],
        ]);

        $this->actingAs($creator)
            ->post(route('groups.store'), [
                'name' => 'Approval Group',
                'slug' => 'approval-group',
                'privacy' => Group::PRIVACY_PUBLIC,
                'short_description' => 'Needs review first',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('groups', [
            'slug' => 'approval-group',
            'status' => Group::STATUS_PENDING_REVIEW,
        ]);
    }

    public function test_paid_plan_group_creation_requires_billing_and_an_eligible_active_plan(): void
    {
        $creator = User::factory()->create(['username' => 'paidplangroupcreator']);

        GroupSettings::save([
            'enabled' => 1,
            'creation_policy' => GroupSettings::POLICY_PAID_PLAN,
            'eligible_plan_ids' => [9],
        ]);

        $this->actingAs($creator)
            ->get(route('groups.create'))
            ->assertRedirect(route('groups.index'));

        $this->assertSame(__('messages.groups_paid_plan_unavailable'), session('error'));

        SubscriptionSettings::save([
            'enabled' => 1,
            'base_currency_code' => 'USD',
        ]);

        $this->actingAs($creator)
            ->get(route('groups.create'))
            ->assertRedirect(route('groups.index'));

        $this->assertSame(__('messages.groups_paid_plan_required'), session('error'));

        MemberSubscription::create([
            'user_id' => $creator->id,
            'subscription_plan_id' => 7,
            'status' => MemberSubscription::STATUS_ACTIVE,
            'plan_name' => 'Builder Plan',
            'starts_at' => Carbon::now()->subDay(),
            'ends_at' => Carbon::now()->addMonth(),
            'activated_at' => Carbon::now()->subDay(),
        ]);

        $this->actingAs($creator)
            ->post(route('groups.store'), [
                'name' => 'Blocked Plan Group',
                'slug' => 'blocked-plan-group',
                'privacy' => Group::PRIVACY_PUBLIC,
                'short_description' => 'Wrong plan id',
            ])
            ->assertSessionHas('error', __('messages.groups_paid_plan_required'));

        GroupSettings::save([
            'enabled' => 1,
            'creation_policy' => GroupSettings::POLICY_PAID_PLAN,
            'eligible_plan_ids' => [7],
        ]);

        $this->actingAs($creator)
            ->post(route('groups.store'), [
                'name' => 'Paid Plan Group',
                'slug' => 'paid-plan-group',
                'privacy' => Group::PRIVACY_PUBLIC,
                'short_description' => 'Eligible active plan',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('groups', [
            'slug' => 'paid-plan-group',
            'status' => Group::STATUS_ACTIVE,
        ]);
    }

    public function test_group_feed_uses_shared_composer_and_persists_group_ids_on_publish(): void
    {
        $owner = User::factory()->create(['username' => 'composerowner']);
        $member = User::factory()->create(['username' => 'composermember']);
        $group = $this->createGroup($owner, [
            'slug' => 'composer-group',
            'name' => 'Composer Group',
        ]);

        $this->addMembership($group, $member);

        $this->actingAs($member)
            ->get(route('groups.show', ['group' => $group, 'tab' => 'feed']))
            ->assertOk()
            ->assertSee('id="social-composer-form"', false)
            ->assertSee('name="group_id" value="' . $group->id . '"', false)
            ->assertDontSee(__('messages.move_to_directory'))
            ->assertDontSee(__('messages.quote_repost'));

        $response = $this->actingAs($member)
            ->post(route('status.create'), [
                'group_id' => $group->id,
                'text' => 'Composer shared post body',
                'post_kind' => 'text',
            ]);

        $response->assertRedirect(route('groups.show', ['group' => $group->slug, 'tab' => 'feed']));

        $topic = ForumTopic::query()
            ->where('txt', 'Composer shared post body')
            ->firstOrFail();

        $this->assertDatabaseHas('forum', [
            'id' => $topic->id,
            'group_id' => $group->id,
            'name' => 'text',
        ]);

        $this->assertDatabaseHas('status', [
            'tp_id' => $topic->id,
            'group_id' => $group->id,
            'uid' => $member->id,
        ]);
    }

    public function test_portal_groups_filter_only_shows_posts_from_active_memberships(): void
    {
        $owner = User::factory()->create(['username' => 'portalgroupowner']);
        $member = User::factory()->create(['username' => 'portalgroupmember']);
        $memberGroup = $this->createGroup($owner, [
            'slug' => 'member-group',
            'name' => 'Member Group',
        ]);
        $otherGroup = $this->createGroup($owner, [
            'slug' => 'other-group',
            'name' => 'Other Group',
        ]);

        $this->addMembership($memberGroup, $member);

        $this->createGroupedStatus($owner, $memberGroup, 'Visible group-only post');
        $this->createGroupedStatus($owner, $otherGroup, 'Hidden non-member post');
        $this->createUngroupedStatus($owner, 'Regular portal post');

        $this->actingAs($member)
            ->get(route('portal.index', ['filter' => 'groups']))
            ->assertOk()
            ->assertSeeText('Visible group-only post')
            ->assertDontSeeText('Hidden non-member post')
            ->assertDontSeeText('Regular portal post');
    }

    public function test_group_discussions_stay_out_of_forum_index_and_direct_access_respects_group_privacy(): void
    {
        $owner = User::factory()->create(['username' => 'forumgroupowner']);
        $publicGroup = $this->createGroup($owner, [
            'slug' => 'public-forum-group',
            'name' => 'Public Forum Group',
        ]);
        $privateGroup = $this->createGroup($owner, [
            'slug' => 'private-forum-group',
            'name' => 'Private Forum Group',
            'privacy' => Group::PRIVACY_PRIVATE,
        ]);

        [$publicTopic] = $this->createGroupedDiscussion($owner, $publicGroup, 'Public Group Discussion', 'Visible discussion body');
        [$privateTopic] = $this->createGroupedDiscussion($owner, $privateGroup, 'Private Group Discussion', 'Hidden discussion body');

        $category = ForumCategory::create([
            'name' => 'Forum Category',
            'icons' => 'fa-comments',
            'txt' => 'Main community category',
            'ordercat' => 1,
            'visibility' => 0,
        ]);

        $this->createRegularForumTopic($owner, $category, 'Category Thread', 'Forum category topic body');

        $this->get(route('forum.index'))
            ->assertOk()
            ->assertSeeText('Category Thread')
            ->assertDontSeeText('Public Group Discussion')
            ->assertDontSeeText('Private Group Discussion');

        $this->get(route('forum.topic', $publicTopic->id))
            ->assertOk()
            ->assertSeeText('Public Group Discussion');

        $this->get(route('forum.topic', $privateTopic->id))
            ->assertNotFound();
    }

    public function test_sitemap_lists_only_public_active_groups(): void
    {
        $owner = User::factory()->create(['username' => 'sitemapgroupowner']);

        $publicGroup = $this->createGroup($owner, [
            'slug' => 'public-group-map',
            'name' => 'Public Group Map',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
        ]);

        $this->createGroup($owner, [
            'slug' => 'private-group-map',
            'name' => 'Private Group Map',
            'privacy' => Group::PRIVACY_PRIVATE,
            'status' => Group::STATUS_ACTIVE,
        ]);

        $this->createGroup($owner, [
            'slug' => 'suspended-group-map',
            'name' => 'Suspended Group Map',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_SUSPENDED,
        ]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/sitemap/groups/1.xml', false);

        $sectionResponse = $this->get('/sitemap/groups/1.xml');
        $sectionResponse->assertOk();

        $sectionXml = $sectionResponse->streamedContent();

        $this->assertStringContainsString($publicGroup->slug, $sectionXml);
        $this->assertStringNotContainsString('private-group-map', $sectionXml);
        $this->assertStringNotContainsString('suspended-group-map', $sectionXml);
    }

    public function test_group_moderator_can_pin_and_unpin_post(): void
    {
        $owner = User::factory()->create();
        $mod = User::factory()->create();
        $group = $this->createGroup($owner);
        $this->addMembership($group, $mod, GroupMembership::STATUS_ACTIVE, GroupMembership::ROLE_MODERATOR);

        [, $status] = $this->createGroupedStatus($owner, $group, 'Important announcement');

        $this->actingAs($mod)
            ->post(route('groups.posts.pin', [$group, $status->id]))
            ->assertRedirect();

        $this->assertSame((int) $status->id, (int) $group->fresh()->pinned_post_id);

        $this->actingAs($mod)
            ->post(route('groups.posts.pin', [$group, $status->id]))
            ->assertRedirect();

        $this->assertNull($group->fresh()->pinned_post_id);
    }

    public function test_group_moderator_can_delete_member_post_in_group(): void
    {
        $owner = User::factory()->create();
        $mod = User::factory()->create();
        $member = User::factory()->create();
        $group = $this->createGroup($owner);
        $this->addMembership($group, $mod, GroupMembership::STATUS_ACTIVE, GroupMembership::ROLE_MODERATOR);
        $this->addMembership($group, $member, GroupMembership::STATUS_ACTIVE, GroupMembership::ROLE_MEMBER);

        [, $status] = $this->createGroupedStatus($member, $group, 'Spam post to delete');

        $this->actingAs($mod)
            ->delete(route('groups.posts.delete', [$group, $status->id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('status', ['id' => $status->id]);
    }

    public function test_group_moderator_can_ban_and_unban_member(): void
    {
        $owner = User::factory()->create();
        $mod = User::factory()->create();
        $member = User::factory()->create();
        $group = $this->createGroup($owner);
        $this->addMembership($group, $mod, GroupMembership::STATUS_ACTIVE, GroupMembership::ROLE_MODERATOR);
        $membership = $this->addMembership($group, $member, GroupMembership::STATUS_ACTIVE, GroupMembership::ROLE_MEMBER);

        $this->actingAs($mod)
            ->post(route('groups.members.ban', [$group, $membership]))
            ->assertRedirect();

        $this->assertSame(GroupMembership::STATUS_BANNED, $membership->fresh()->status);

        // Banned member cannot post or view content
        $this->assertFalse(app(\App\Services\GroupAccessService::class)->canPostToGroup($group, $member));

        // Moderator unbans member
        $this->actingAs($mod)
            ->post(route('groups.members.unban', [$group, $membership]))
            ->assertRedirect();

        $this->assertDatabaseMissing('group_memberships', ['id' => $membership->id]);
    }

    public function test_paid_group_with_join_fee_deducts_pts_from_joining_member(): void
    {
        $owner = User::factory()->create(['pts' => 10]);
        $member = User::factory()->create(['pts' => 100]);
        $group = $this->createGroup($owner, ['join_fee_pts' => 50, 'privacy' => Group::PRIVACY_PUBLIC]);

        $this->actingAs($member)
            ->post(route('groups.join', $group))
            ->assertRedirect();

        $this->assertEquals(50, $member->fresh()->pts);
        $this->assertEquals(60, $owner->fresh()->pts);
        $this->assertDatabaseHas('group_memberships', [
            'group_id' => $group->id,
            'user_id' => $member->id,
            'status' => GroupMembership::STATUS_ACTIVE,
        ]);
    }

    public function test_group_owner_can_delete_group(): void
    {
        $owner = User::factory()->create();
        $group = $this->createGroup($owner);

        $this->actingAs($owner)
            ->delete(route('groups.destroy', $group))
            ->assertRedirect(route('groups.index'));

        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
    }

    private function createGroup(User $owner, array $overrides = []): Group
    {
        $group = Group::create(array_merge([
            'owner_id' => $owner->id,
            'slug' => 'group-' . uniqid(),
            'name' => 'Test Group',
            'short_description' => 'Focused test group',
            'description' => 'Group description',
            'rules_markdown' => 'Be respectful.',
            'privacy' => Group::PRIVACY_PUBLIC,
            'status' => Group::STATUS_ACTIVE,
            'is_featured' => false,
            'members_count' => 1,
            'posts_count' => 0,
            'last_activity_at' => now(),
        ], $overrides));

        GroupMembership::create([
            'group_id' => $group->id,
            'user_id' => $owner->id,
            'role' => GroupMembership::ROLE_OWNER,
            'status' => GroupMembership::STATUS_ACTIVE,
            'requested_at' => now(),
            'approved_at' => now(),
            'approved_by' => $owner->id,
        ]);

        return $group->fresh();
    }

    private function addMembership(
        Group $group,
        User $user,
        string $status = GroupMembership::STATUS_ACTIVE,
        string $role = GroupMembership::ROLE_MEMBER
    ): GroupMembership {
        return GroupMembership::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'role' => $role,
            'status' => $status,
            'requested_at' => now(),
            'approved_at' => $status === GroupMembership::STATUS_ACTIVE ? now() : null,
            'approved_by' => $status === GroupMembership::STATUS_ACTIVE ? $group->owner_id : null,
        ]);
    }

    private function createGroupedStatus(User $author, Group $group, string $body): array
    {
        $time = time();

        $topic = ForumTopic::create([
            'uid' => $author->id,
            'name' => 'text',
            'txt' => $body,
            'cat' => 0,
            'group_id' => $group->id,
            'statu' => 1,
            'date' => $time,
            'reply' => 0,
            'vu' => 0,
        ]);

        $status = Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            'group_id' => $group->id,
            's_type' => 100,
            'date' => $time,
            'statu' => 1,
        ]);

        return [$topic, $status];
    }

    private function createUngroupedStatus(User $author, string $body): array
    {
        $time = time();

        $topic = ForumTopic::create([
            'uid' => $author->id,
            'name' => 'text',
            'txt' => $body,
            'cat' => 0,
            'group_id' => null,
            'statu' => 1,
            'date' => $time,
            'reply' => 0,
            'vu' => 0,
        ]);

        $status = Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            'group_id' => null,
            's_type' => 100,
            'date' => $time,
            'statu' => 1,
        ]);

        return [$topic, $status];
    }

    private function createGroupedDiscussion(User $author, Group $group, string $title, string $body): array
    {
        $time = time();

        $topic = ForumTopic::create([
            'uid' => $author->id,
            'name' => $title,
            'txt' => $body,
            'cat' => 0,
            'group_id' => $group->id,
            'statu' => 1,
            'date' => $time,
            'reply' => 0,
            'vu' => 0,
        ]);

        $status = Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            'group_id' => $group->id,
            's_type' => 2,
            'date' => $time,
            'statu' => 1,
        ]);

        return [$topic, $status];
    }

    private function createRegularForumTopic(User $author, ForumCategory $category, string $title, string $body): array
    {
        $time = time();

        $topic = ForumTopic::create([
            'uid' => $author->id,
            'name' => $title,
            'txt' => $body,
            'cat' => $category->id,
            'group_id' => null,
            'statu' => 1,
            'date' => $time,
            'reply' => 0,
            'vu' => 0,
        ]);

        $status = Status::create([
            'uid' => $author->id,
            'tp_id' => $topic->id,
            'group_id' => null,
            's_type' => 2,
            'date' => $time,
            'statu' => 1,
        ]);

        return [$topic, $status];
    }

    public function test_group_create_page_renders_localized_category_names(): void
    {
        $user = User::factory()->create();

        // English
        $responseEn = $this->actingAs($user)->withSession(['locale' => 'en'])->get(route('groups.create'));
        $responseEn->assertOk()
            ->assertDontSeeText('groups_cat_general')
            ->assertSeeText('General & Community')
            ->assertSeeText('Technology & AI');

        // Arabic
        $responseAr = $this->actingAs($user)->withSession(['locale' => 'ar'])->get(route('groups.create'));
        $responseAr->assertOk()
            ->assertDontSeeText('groups_cat_general')
            ->assertSeeText('عام ومجتمعي')
            ->assertSeeText('التكنولوجيا والذكاء الاصطناعي');
    }
}
