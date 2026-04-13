<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Like;
use App\Models\User;
use App\Services\KnowledgebaseCommunityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreProductKnowledgebaseUiTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_show_renders_single_actions_menu_and_report_links_for_regular_viewer(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'viewer-product');

        $response = $this->actingAs($viewer)->get(route('store.show', $product->name));
        $html = $response->getContent();

        $response->assertOk()
            ->assertSee(__('messages.report_product'))
            ->assertSee(__('messages.report_publisher'))
            ->assertDontSee(__('messages.edit_product'));

        $this->assertSame(1, substr_count($html, 'data-store-actions-menu'));
    }

    public function test_store_show_renders_management_actions_for_owner_and_delete_redirects_to_store_index(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'owner-product');

        $this->actingAs($owner)
            ->get(route('store.show', $product->name))
            ->assertOk()
            ->assertSee(__('messages.edit_product'))
            ->assertDontSee(__('messages.report_product'));

        $response = $this->actingAs($owner)->post(route('store.delete'), ['id' => $product->id]);

        $response->assertRedirect(route('store.index'));
        $response->assertSessionHas('success', __('messages.product_deleted'));
        $this->assertDatabaseMissing('options', [
            'o_type' => 'store',
            'name' => $product->name,
        ]);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee(__('messages.product_deleted'));
    }

    public function test_store_delete_keeps_json_response_for_existing_javascript_clients(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'json-delete-product');

        $this->actingAs($owner)
            ->postJson(route('store.delete'), ['id' => $product->id])
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_knowledgebase_show_displays_topic_and_publisher_reports_when_author_exists(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $author = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-report-product');
        $article = $this->createKnowledgebaseArticle($product, 'install-guide', $author->id);

        $this->actingAs($viewer)
            ->get(route('kb.show', ['name' => $product->name, 'article' => $article->name]))
            ->assertOk()
            ->assertSee(__('messages.report_topic'))
            ->assertSee(__('messages.report_publisher'))
            ->assertSee($author->username);
    }

    public function test_knowledgebase_show_hides_report_publisher_when_current_article_is_guest_authored(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-guest-product');
        $article = $this->createKnowledgebaseArticle($product, 'guest-guide', 0);

        $this->actingAs($viewer)
            ->get(route('kb.show', ['name' => $product->name, 'article' => $article->name]))
            ->assertOk()
            ->assertSee(__('messages.report_topic'))
            ->assertDontSee(__('messages.report_publisher'));
    }

    public function test_knowledgebase_store_creates_community_status_when_requested_for_a_new_topic(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-share-product');

        $response = $this->withSession(['kb_captcha' => '9'])
            ->actingAs($owner)
            ->post(route('kb.store'), [
                'store' => $product->name,
                'name' => 'install-guide',
                'txt' => 'This article explains how to install the package correctly on a fresh MYADS site.',
                'capt' => '9',
                'share_to_community' => '1',
            ]);

        $response->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => 'install-guide']));
        $response->assertSessionHas('success', __('messages.knowledgebase_published_to_community'));

        $article = Option::query()
            ->where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', 'install-guide')
            ->where('o_order', 0)
            ->first();

        $this->assertNotNull($article);
        $this->assertDatabaseHas('options', [
            'o_type' => 'knowledgebase',
            'o_mode' => $product->name,
            'name' => 'install-guide',
            'o_order' => 0,
            'o_parent' => $owner->id,
        ]);
        $this->assertDatabaseHas('status', [
            'uid' => $owner->id,
            'tp_id' => $article->id,
            's_type' => KnowledgebaseCommunityService::STATUS_TYPE,
        ]);
    }

    public function test_knowledgebase_store_keeps_show_redirect_when_share_toggle_is_off(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-standard-product');

        $this->withSession(['kb_captcha' => '12'])
            ->actingAs($owner)
            ->post(route('kb.store'), [
                'store' => $product->name,
                'name' => 'release-notes',
                'txt' => 'Release notes body for the knowledgebase topic.',
                'capt' => '12',
            ])
            ->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => 'release-notes']));

        $this->assertDatabaseMissing('status', [
            'uid' => $owner->id,
            's_type' => KnowledgebaseCommunityService::STATUS_TYPE,
        ]);
    }

    public function test_knowledgebase_store_does_not_create_community_post_for_guests_even_if_toggle_is_sent(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-guest-share-product');

        $response = $this->withSession(['kb_captcha' => '4'])
            ->post(route('kb.store'), [
                'store' => $product->name,
                'name' => 'guest-start',
                'txt' => 'Guest-authored article body for the knowledgebase flow.',
                'capt' => '4',
                'share_to_community' => '1',
            ]);

        $response->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => 'guest-start']));
        $this->assertDatabaseMissing('status', [
            's_type' => KnowledgebaseCommunityService::STATUS_TYPE,
        ]);
    }

    public function test_knowledgebase_store_does_not_create_community_post_for_pending_suggestions(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-suggestion-product');
        $this->createKnowledgebaseArticle($product, 'update-guide', $owner->id);

        $response = $this->withSession(['kb_captcha' => '6'])
            ->actingAs($viewer)
            ->post(route('kb.store'), [
                'store' => $product->name,
                'name' => 'update-guide',
                'txt' => 'Suggested edits that should remain pending and never open the composer.',
                'capt' => '6',
                'share_to_community' => '1',
            ]);

        $response->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => 'update-guide']));
        $this->assertDatabaseHas('options', [
            'o_type' => 'knowledgebase',
            'o_mode' => $product->name,
            'name' => 'update-guide',
            'o_order' => 1,
            'o_parent' => $viewer->id,
        ]);
        $this->assertDatabaseMissing('status', [
            'uid' => $viewer->id,
            's_type' => KnowledgebaseCommunityService::STATUS_TYPE,
        ]);
    }

    public function test_knowledgebase_show_exposes_direct_community_publish_and_external_share_actions_for_authenticated_users(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-actions-product');
        $article = $this->createKnowledgebaseArticle($product, 'launch-guide', $owner->id);

        $this->actingAs($viewer)
            ->get(route('kb.show', ['name' => $product->name, 'article' => $article->name]))
            ->assertOk()
            ->assertSee(__('messages.share_to_community'))
            ->assertSee(__('messages.share_externally'))
            ->assertSee(route('kb.community.publish'), false)
            ->assertDontSee(route('portal.share'), false)
            ->assertSee(__('messages.facebook'))
            ->assertSee(__('messages.twitter'))
            ->assertSee(__('messages.linkedin'))
            ->assertSee(__('messages.telegram'))
            ->assertSee("sharePost('facebook'", false)
            ->assertSee("sharePost('telegram'", false);
    }

    public function test_knowledgebase_show_hides_community_publish_action_for_guests_but_keeps_external_share_actions(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-public-actions-product');
        $article = $this->createKnowledgebaseArticle($product, 'public-guide', $owner->id);

        $this->get(route('kb.show', ['name' => $product->name, 'article' => $article->name]))
            ->assertOk()
            ->assertDontSee(__('messages.share_to_community'))
            ->assertSee(__('messages.share_externally'))
            ->assertDontSee(route('kb.community.publish'), false)
            ->assertSee("sharePost('facebook'", false);
    }

    public function test_manual_knowledgebase_publish_creates_a_new_community_status_each_time(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-republish-product');
        $article = $this->createKnowledgebaseArticle($product, 'launch-guide', $owner->id);

        $this->actingAs($viewer)
            ->post(route('kb.community.publish'), [
                'store' => $product->name,
                'article' => $article->name,
            ])
            ->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => $article->name]))
            ->assertSessionHas('success', __('messages.knowledgebase_published_to_community'));

        $this->actingAs($viewer)
            ->post(route('kb.community.publish'), [
                'store' => $product->name,
                'article' => $article->name,
            ])
            ->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => $article->name]));

        $this->assertSame(2, Status::query()
            ->where('uid', $viewer->id)
            ->where('tp_id', $article->id)
            ->where('s_type', KnowledgebaseCommunityService::STATUS_TYPE)
            ->count());
    }

    public function test_knowledgebase_community_post_supports_reactions_comments_and_comment_deletion(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-interaction-product');
        $article = $this->createKnowledgebaseArticle($product, 'guide', $owner->id);
        $status = $this->createKnowledgebaseCommunityStatus($article, $viewer);

        $this->actingAs($viewer)
            ->postJson(route('reaction.toggle'), [
                'id' => $status->id,
                'type' => 'knowledgebase',
                'reaction' => 'like',
            ])
            ->assertOk();

        $this->assertDatabaseHas('like', [
            'uid' => $viewer->id,
            'sid' => $status->id,
            'type' => KnowledgebaseCommunityService::REACTION_TYPE,
        ]);

        $this->actingAs($viewer)
            ->post(route('comment.store'), [
                'id' => $status->id,
                'type' => 'knowledgebase',
                'comment' => 'Helpful guide for launch day.',
            ])
            ->assertOk()
            ->assertSee('Helpful guide for launch day.');

        $comment = Option::query()
            ->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)
            ->where('o_parent', $status->id)
            ->first();

        $this->assertNotNull($comment);

        $this->actingAs($viewer)
            ->postJson(route('comment.delete'), [
                'trashid' => $comment->id,
                'type' => 'knowledgebase',
            ])
            ->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('options', [
            'id' => $comment->id,
            'o_type' => KnowledgebaseCommunityService::COMMENT_OPTION_TYPE,
        ]);
    }

    public function test_deleting_knowledgebase_community_post_removes_only_the_status_and_its_interactions(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $reactor = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-delete-status-product');
        $article = $this->createKnowledgebaseArticle($product, 'delete-guide', $owner->id);
        $status = $this->createKnowledgebaseCommunityStatus($article, $viewer);
        $secondStatus = $this->createKnowledgebaseCommunityStatus($article, $owner);

        $postLike = Like::create([
            'uid' => $reactor->id,
            'sid' => $status->id,
            'type' => KnowledgebaseCommunityService::REACTION_TYPE,
            'time_t' => time(),
        ]);
        Option::create([
            'name' => 'like',
            'o_type' => 'data_reaction',
            'o_order' => $reactor->id,
            'o_parent' => $postLike->id,
            'o_valuer' => 'like',
            'o_mode' => time(),
        ]);

        $comment = Option::create([
            'name' => 'coment_kb',
            'o_type' => KnowledgebaseCommunityService::COMMENT_OPTION_TYPE,
            'o_order' => $reactor->id,
            'o_parent' => $status->id,
            'o_valuer' => 'Please add more deployment notes.',
            'o_mode' => time(),
        ]);

        $commentLike = Like::create([
            'uid' => $owner->id,
            'sid' => $comment->id,
            'type' => KnowledgebaseCommunityService::COMMENT_REACTION_TYPE,
            'time_t' => time(),
        ]);
        Option::create([
            'name' => 'love',
            'o_type' => 'data_reaction',
            'o_order' => $owner->id,
            'o_parent' => $commentLike->id,
            'o_valuer' => 'love',
            'o_mode' => time(),
        ]);

        $this->actingAs($viewer)
            ->postJson(route('kb.community.delete'), ['id' => $status->id])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('status', ['id' => $status->id]);
        $this->assertDatabaseMissing('options', ['id' => $comment->id]);
        $this->assertDatabaseMissing('like', ['id' => $postLike->id]);
        $this->assertDatabaseMissing('like', ['id' => $commentLike->id]);
        $this->assertDatabaseHas('options', ['id' => $article->id, 'o_type' => 'knowledgebase']);
        $this->assertDatabaseHas('status', ['id' => $secondStatus->id]);
    }

    public function test_admin_reports_page_understands_knowledgebase_report_type(): void
    {
        $this->seedThemeSetting();
        $admin = $this->createAdmin();
        $owner = User::factory()->create();
        $reporter = User::factory()->create();
        $author = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-admin-product');
        $article = $this->createKnowledgebaseArticle($product, 'release-notes', $author->id);

        Report::create([
            'uid' => $reporter->id,
            'txt' => 'Needs moderator review',
            's_type' => 205,
            'tp_id' => $article->id,
            'statu' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertSee(route('kb.show', ['name' => $product->name, 'article' => $article->name]), false)
            ->assertSee(__('messages.knowledgebase') . ': ' . $article->name, false)
            ->assertSee($author->username);
    }

    private function seedThemeSetting(): void
    {
        Setting::create([
            'titer' => 'MyAds',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'id' => 1,
            'username' => 'admin',
            'email' => 'admin@example.test',
        ]);
    }

    private function createStoreProduct(User $owner, string $name): Product
    {
        return Product::create([
            'name' => $name,
            'o_valuer' => 'Store product description for the redesigned page.',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 15,
            'o_mode' => 'upload/product-cover.png',
        ]);
    }

    private function createKnowledgebaseArticle(Product $product, string $articleName, int $authorId): Option
    {
        return Option::create([
            'name' => $articleName,
            'o_valuer' => '<p>Knowledgebase article body</p>',
            'o_type' => 'knowledgebase',
            'o_parent' => $authorId,
            'o_order' => 0,
            'o_mode' => $product->name,
        ]);
    }

    private function createKnowledgebaseCommunityStatus(Option $article, User $publisher): Status
    {
        return Status::create([
            'uid' => $publisher->id,
            'tp_id' => $article->id,
            's_type' => KnowledgebaseCommunityService::STATUS_TYPE,
            'date' => time(),
            'statu' => 1,
        ]);
    }
}
