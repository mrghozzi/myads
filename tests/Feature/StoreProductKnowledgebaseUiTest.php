<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\Report;
use App\Models\Setting;
use App\Models\User;
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

    public function test_knowledgebase_store_redirects_to_share_composer_when_requested_for_a_new_topic(): void
    {
        $this->seedThemeSetting();
        $this->createAdmin();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'kb-share-product');
        $body = 'This article explains how to install the package correctly on a fresh MYADS site.';

        $response = $this->withSession(['kb_captcha' => '9'])
            ->actingAs($owner)
            ->post(route('kb.store'), [
                'store' => $product->name,
                'name' => 'install-guide',
                'txt' => $body,
                'capt' => '9',
                'share_to_community' => '1',
            ]);

        $location = (string) $response->headers->get('Location');
        $query = $this->parseRedirectQuery($location);

        $response->assertRedirect();
        $this->assertSame(parse_url(route('portal.share'), PHP_URL_PATH), parse_url($location, PHP_URL_PATH));
        $this->assertSame($body, $this->extractShareSummary($query['text'] ?? ''));
        $this->assertStringContainsString('install-guide', $query['text'] ?? '');
        $this->assertStringContainsString($product->name, $query['text'] ?? '');
        $this->assertStringContainsString(route('kb.show', ['name' => $product->name, 'article' => 'install-guide']), $query['text'] ?? '');
        $this->assertDatabaseHas('options', [
            'o_type' => 'knowledgebase',
            'o_mode' => $product->name,
            'name' => 'install-guide',
            'o_order' => 0,
            'o_parent' => $owner->id,
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
    }

    public function test_knowledgebase_store_does_not_redirect_guests_to_share_composer_even_if_toggle_is_sent(): void
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

        $location = (string) $response->headers->get('Location');

        $response->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => 'guest-start']));
        $this->assertSame(parse_url(route('kb.show', ['name' => $product->name, 'article' => 'guest-start']), PHP_URL_PATH), parse_url($location, PHP_URL_PATH));
    }

    public function test_knowledgebase_store_does_not_share_pending_suggestions_to_the_community(): void
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

        $location = (string) $response->headers->get('Location');

        $response->assertRedirect(route('kb.show', ['name' => $product->name, 'article' => 'update-guide']));
        $this->assertSame(parse_url(route('kb.show', ['name' => $product->name, 'article' => 'update-guide']), PHP_URL_PATH), parse_url($location, PHP_URL_PATH));
        $this->assertDatabaseHas('options', [
            'o_type' => 'knowledgebase',
            'o_mode' => $product->name,
            'name' => 'update-guide',
            'o_order' => 1,
            'o_parent' => $viewer->id,
        ]);
    }

    public function test_knowledgebase_show_exposes_community_and_external_share_actions_for_authenticated_users(): void
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
            ->assertSee(route('portal.share'), false)
            ->assertSee(__('messages.facebook'))
            ->assertSee(__('messages.twitter'))
            ->assertSee(__('messages.linkedin'))
            ->assertSee(__('messages.telegram'))
            ->assertSee("sharePost('facebook'", false)
            ->assertSee("sharePost('telegram'", false);
    }

    public function test_knowledgebase_show_hides_community_share_action_for_guests_but_keeps_external_share_actions(): void
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
            ->assertDontSee(route('portal.share'), false)
            ->assertSee("sharePost('facebook'", false);
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

    private function parseRedirectQuery(string $location): array
    {
        $query = [];
        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        return $query;
    }

    private function extractShareSummary(string $shareText): string
    {
        $parts = preg_split("/\r\n|\n|\r/", $shareText) ?: [];

        foreach ($parts as $line) {
            if (str_starts_with($line, 'Summary: ')) {
                return substr($line, strlen('Summary: '));
            }
        }

        return '';
    }
}
