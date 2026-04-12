<?php

namespace Tests\Feature;

use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Option;
use App\Models\OrderRequest;
use App\Models\SiteAdmin;
use App\Models\Status;
use App\Models\User;
use App\Models\UserPrivacySetting;
use App\Services\FeedService;
use App\Support\CommunityFeedSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class CommunityFeedFeatureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSiteSettings();
        Cache::flush();
        CommunityFeedSettings::clearCache();
    }

    public function test_followed_author_post_gets_priority_in_personalized_all_feed(): void
    {
        $viewer = User::factory()->create(['username' => 'feedviewer']);
        $followed = User::factory()->create(['username' => 'followedauthor']);
        $outsider = User::factory()->create(['username' => 'outsiderauthor']);

        Like::create([
            'uid' => $viewer->id,
            'sid' => $followed->id,
            'type' => 1,
            'time_t' => time(),
        ]);

        $followedStatus = $this->createForumStatus($followed, 'Followed author update', time() - 120);
        $outsiderStatus = $this->createForumStatus($outsider, 'Outsider update', time() - 60);

        $ids = $this->actingAs($viewer)->rankedStatusIds($viewer->id);

        $this->assertSame([$followedStatus->id, $outsiderStatus->id], array_slice($ids, 0, 2));
    }

    public function test_recent_trending_post_beats_older_historical_post(): void
    {
        $historicalAuthor = User::factory()->create(['username' => 'historicalauthor']);
        $trendAuthor = User::factory()->create(['username' => 'trendauthor']);

        $oldStatus = $this->createForumStatus($historicalAuthor, 'Old historical giant', time() - (48 * 3600), 500);
        $trendStatus = $this->createForumStatus($trendAuthor, 'Fresh trending post', time() - (2 * 3600), 25);

        $this->addForumReactions($oldStatus, 120, time() - (72 * 3600));
        $this->addForumComments($oldStatus, 40, time() - (72 * 3600));
        $this->addForumReactions($trendStatus, 6, time() - 1800);
        $this->addForumComments($trendStatus, 4, time() - 1200);

        $ids = $this->rankedStatusIds(null);

        $this->assertSame($trendStatus->id, $ids[0] ?? null);
    }

    public function test_old_post_requires_recent_trend_rescue_to_reenter_feed(): void
    {
        $oldAuthor = User::factory()->create(['username' => 'rescueauthor']);
        $freshAuthor = User::factory()->create(['username' => 'freshauthor']);

        $oldStatus = $this->createForumStatus($oldAuthor, 'Needs rescue to return', time() - (120 * 3600), 150);
        $freshStatus = $this->createForumStatus($freshAuthor, 'Fresh anchor post', time() - 1800, 10);

        $initialIds = $this->rankedStatusIds(null);

        $this->assertNotContains($oldStatus->id, $initialIds);
        $this->assertContains($freshStatus->id, $initialIds);

        $this->addForumReactions($oldStatus, 5, time() - 1200);
        $this->addForumComments($oldStatus, 3, time() - 900);

        Cache::flush();
        CommunityFeedSettings::clearCache();

        $rescuedIds = $this->rankedStatusIds(null);

        $this->assertContains($oldStatus->id, $rescuedIds);
    }

    public function test_all_feed_falls_back_to_latest_archived_posts_when_no_recent_candidates_exist(): void
    {
        $author = User::factory()->create(['username' => 'archiveauthor']);

        $olderStatus = $this->createForumStatus($author, 'Archived post one', time() - (14 * 24 * 3600), 20);
        $newerArchivedStatus = $this->createForumStatus($author, 'Archived post two', time() - (10 * 24 * 3600), 10);

        $ids = $this->rankedStatusIds(null);

        $this->assertSame([$newerArchivedStatus->id, $olderStatus->id], array_slice($ids, 0, 2));
    }

    public function test_guest_feed_ignores_personalization_and_prefers_fresher_post(): void
    {
        $viewer = User::factory()->create(['username' => 'unusedviewer']);
        $followed = User::factory()->create(['username' => 'guestfollowed']);
        $outsider = User::factory()->create(['username' => 'guestoutsider']);

        Like::create([
            'uid' => $viewer->id,
            'sid' => $followed->id,
            'type' => 1,
            'time_t' => time(),
        ]);

        $followedStatus = $this->createForumStatus($followed, 'Older followed post', time() - 120);
        $outsiderStatus = $this->createForumStatus($outsider, 'Newer guest post', time() - 60);

        $ids = $this->rankedStatusIds(null);

        $this->assertSame([$outsiderStatus->id, $followedStatus->id], array_slice($ids, 0, 2));
    }

    public function test_changing_feed_settings_changes_signature_and_ranking_without_cache_flush(): void
    {
        $viewer = User::factory()->create(['username' => 'cacheviewer']);
        $followed = User::factory()->create(['username' => 'cachefollowed']);
        $outsider = User::factory()->create(['username' => 'cacheoutsider']);

        Like::create([
            'uid' => $viewer->id,
            'sid' => $followed->id,
            'type' => 1,
            'time_t' => time(),
        ]);

        $followedStatus = $this->createForumStatus($followed, 'Older followed cache post', time() - (3 * 3600));
        $outsiderStatus = $this->createForumStatus($outsider, 'Fresh outsider cache post', time() - 60);

        CommunityFeedSettings::save([
            'freshness_base_score' => 100,
            'following_boost' => 500,
        ]);

        $firstSignature = CommunityFeedSettings::signature();
        $firstIds = $this->actingAs($viewer)->rankedStatusIds($viewer->id);

        CommunityFeedSettings::save([
            'freshness_base_score' => 700,
            'following_boost' => 0,
        ]);

        $secondSignature = CommunityFeedSettings::signature();
        $secondIds = $this->actingAs($viewer)->rankedStatusIds($viewer->id);

        $this->assertNotSame($firstSignature, $secondSignature);
        $this->assertSame($followedStatus->id, $firstIds[0] ?? null);
        $this->assertSame($outsiderStatus->id, $secondIds[0] ?? null);
    }

    public function test_community_admin_can_access_and_update_feed_settings(): void
    {
        $admin = User::factory()->create(['username' => 'communityadmin']);

        SiteAdmin::create([
            'user_id' => $admin->id,
            'permissions' => ['community'],
            'is_active' => true,
            'has_full_access' => false,
            'is_super' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.community.feed.settings'))
            ->assertOk()
            ->assertSeeText(__('messages.community_feed_settings_title'));

        $payload = CommunityFeedSettings::all();
        $payload['following_boost'] = 77;

        $this->actingAs($admin)
            ->post(route('admin.community.feed.settings.update'), $payload)
            ->assertRedirect(route('admin.community.feed.settings'));

        $this->assertDatabaseHas('options', [
            'o_type' => CommunityFeedSettings::OPTION_TYPE,
            'name' => 'following_boost',
            'o_valuer' => '77',
        ]);
    }

    public function test_non_community_admin_cannot_access_feed_settings(): void
    {
        $admin = User::factory()->create(['username' => 'limitedcommunityadmin']);

        SiteAdmin::create([
            'user_id' => $admin->id,
            'permissions' => ['users'],
            'is_active' => true,
            'has_full_access' => false,
            'is_super' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.community.feed.settings'))
            ->assertRedirect('/');
    }

    public function test_order_comments_store_timestamp_for_recent_trend_tracking(): void
    {
        $owner = User::factory()->create(['username' => 'orderowner']);
        $commenter = User::factory()->create(['username' => 'ordercommenter']);

        $order = OrderRequest::create([
            'uid' => $owner->id,
            'title' => 'Need a portal redesign',
            'description' => 'Fresh request',
            'budget' => 100,
            'category' => 'design',
            'date' => time() - 300,
            'statu' => 1,
            'best_offer_id' => null,
            'last_activity' => time() - 300,
            'avg_rating' => 0,
        ]);

        $this->actingAs($commenter)
            ->post(route('comment.store'), [
                'id' => $order->id,
                'type' => 'order',
                'comment' => 'I can help with this today.',
            ])
            ->assertOk();

        $comment = Option::query()
            ->where('o_type', 'order_comment')
            ->where('o_parent', $order->id)
            ->first();

        $this->assertNotNull($comment);
        $this->assertSame($commenter->id, (int) $comment->o_order);
        $this->assertGreaterThan(0, (int) $comment->o_mode);
    }

    private function rankedStatusIds(?int $userId): array
    {
        $request = Request::create('/portal', 'GET', ['filter' => 'all']);
        $this->app->instance('request', $request);

        return collect(FeedService::getRankedFeed($userId, 1, 20)->items())
            ->map(static fn ($status) => (int) $status->id)
            ->all();
    }

    private function createForumStatus(User $user, string $text, int $date, int $views = 0): Status
    {
        UserPrivacySetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'profile_visibility' => 'public',
                'about_visibility' => 'public',
                'photos_visibility' => 'public',
                'followers_visibility' => 'public',
                'following_visibility' => 'public',
                'points_history_visibility' => 'public',
                'allow_direct_messages' => true,
                'allow_mentions' => true,
                'allow_reposts' => true,
                'show_online_status' => true,
            ]
        );

        $topic = ForumTopic::create([
            'uid' => $user->id,
            'name' => 'Topic ' . substr(md5($text . $date), 0, 10),
            'txt' => $text,
            'cat' => 0,
            'statu' => 1,
            'date' => $date,
            'reply' => 0,
            'vu' => $views,
        ]);

        return Status::create([
            'uid' => $user->id,
            'tp_id' => $topic->id,
            's_type' => 100,
            'date' => $date,
            'txt' => $text,
            'statu' => 1,
        ]);
    }

    private function addForumReactions(Status $status, int $count, int $time): void
    {
        $topic = ForumTopic::query()->findOrFail($status->tp_id);
        $users = User::factory()->count($count)->create();

        foreach ($users as $user) {
            Like::create([
                'uid' => $user->id,
                'sid' => $topic->id,
                'type' => 2,
                'time_t' => $time,
            ]);
        }
    }

    private function addForumComments(Status $status, int $count, int $time): void
    {
        $topic = ForumTopic::query()->findOrFail($status->tp_id);
        $users = User::factory()->count($count)->create();

        foreach ($users as $user) {
            ForumComment::create([
                'uid' => $user->id,
                'tid' => $topic->id,
                'txt' => 'Comment ' . $user->id,
                'date' => $time,
            ]);
        }
    }
}
