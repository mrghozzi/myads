<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Option;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreEditorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_create_page_keeps_sceditor_and_single_linkzip_field(): void
    {
        $this->seedThemeSetting();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('store.create'));
        $html = $response->getContent();

        $response->assertOk()
            ->assertSee('textarea name="txt" id="editor1" rows="15"', false)
            ->assertSee('sceditor.create(textarea', false)
            ->assertSee('jquery.sceditor.min.js', false)
            ->assertSee('data-store-source-picker', false)
            ->assertSee('value="themes"', false)
            ->assertDontSee('value="templates"', false);

        $this->assertSame(1, substr_count($html, 'name="linkzip"'));
    }

    public function test_store_update_page_uses_collapsible_history_and_single_linkzip_field(): void
    {
        $this->seedThemeSetting();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'update-shell-product');

        ProductFile::create([
            'name' => 'v1.0',
            'o_valuer' => 'Initial build',
            'o_type' => 'store_file',
            'o_parent' => $product->id,
            'o_order' => 0,
            'o_mode' => 'upload/initial.zip',
        ]);

        $response = $this->actingAs($owner)->get(route('store.update', $product->name));
        $html = $response->getContent();

        $response->assertOk()
            ->assertSee('store-editor-history', false)
            ->assertSee('data-store-source-picker', false)
            ->assertSee(__('messages.file_versions'), false);

        $this->assertSame(1, substr_count($html, 'name="linkzip"'));
    }

    public function test_store_update_redirects_to_product_page_and_shows_success_flash(): void
    {
        $this->seedThemeSetting();
        $owner = User::factory()->create();
        $product = $this->createStoreProduct($owner, 'updated-product');

        $response = $this->actingAs($owner)->post(route('store.update.store', $product->name), [
            'vnbr' => 'v2.0',
            'desc' => 'Updated build description for release',
            'linkzip' => 'https://example.test/files/product-v2.zip',
            'pts' => 25,
            'img' => 'upload/updated-cover.png',
        ]);

        $response->assertRedirect(route('store.show', $product->name));
        $response->assertSessionHas('success', __('updated_successfully'));

        $this->assertDatabaseHas('options', [
            'o_type' => 'store_file',
            'o_parent' => $product->id,
            'name' => 'v2.0',
            'o_mode' => 'https://example.test/files/product-v2.zip',
        ]);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee(__('updated_successfully'));
    }

    public function test_store_create_submission_accepts_direct_link_and_preserves_topic_content(): void
    {
        $this->seedThemeSetting();
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->post(route('store.store'), [
            'name' => 'new-direct-link-product',
            'desc' => 'A polished product description',
            'vnbr' => 'v1.0',
            'pts' => 10,
            'cat_s' => 'themes',
            'sc_cat' => 'others',
            'txt' => '<p>Rich topic content from SCEditor.</p>',
            'linkzip' => 'https://example.test/files/direct-product.zip',
            'img' => 'upload/product-cover.png',
            'vname' => '1',
        ]);

        $response->assertRedirect(route('store.show', 'new-direct-link-product'));

        $this->assertDatabaseHas('options', [
            'o_type' => 'store',
            'name' => 'new-direct-link-product',
        ]);

        $this->assertDatabaseHas('options', [
            'o_type' => 'store_file',
            'name' => 'v1.0',
            'o_mode' => 'https://example.test/files/direct-product.zip',
        ]);

        $this->assertDatabaseHas('options', [
            'o_type' => 'store_type',
            'o_parent' => Product::where('name', 'new-direct-link-product')->value('id'),
            'name' => 'themes',
        ]);

        $this->assertDatabaseHas('forum', [
            'name' => 'new-direct-link-product',
            'txt' => '<p>Rich topic content from SCEditor.</p>',
        ]);

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee(__('product_added_successfully'));
    }

    public function test_store_categories_endpoint_returns_script_product_subcategory_select_for_themes(): void
    {
        $this->seedThemeSetting();
        $user = User::factory()->create();
        $scriptProduct = $this->createScriptProduct($user, 'theme-base-script');

        $response = $this->actingAs($user)->post(route('store.categories'), [
            'cat_s' => 'themes',
        ]);

        $response->assertOk()
            ->assertSee('name="sc_cat"', false)
            ->assertSee('value="' . $scriptProduct->id . '"', false)
            ->assertSee('theme-base-script', false)
            ->assertSee('value="others"', false);
    }

    public function test_store_categories_endpoint_returns_script_category_select_for_scripts(): void
    {
        $this->seedThemeSetting();
        $user = User::factory()->create();

        Option::create([
            'name' => 'CMS',
            'o_valuer' => '0',
            'o_type' => 'scriptcat',
            'o_parent' => 0,
            'o_order' => 0,
            'o_mode' => '0',
        ]);

        Option::create([
            'name' => 'Forum',
            'o_valuer' => '0',
            'o_type' => 'scriptcat',
            'o_parent' => 0,
            'o_order' => 0,
            'o_mode' => '0',
        ]);

        $response = $this->actingAs($user)->post(route('store.categories'), [
            'cat_s' => 'script',
        ]);

        $response->assertOk()
            ->assertSee('name="sc_cat"', false)
            ->assertSee('value="CMS"', false)
            ->assertSee('value="Forum"', false);
    }

    public function test_store_create_page_renders_old_subcategory_selection(): void
    {
        $this->seedThemeSetting();
        $user = User::factory()->create();
        $scriptProduct = $this->createScriptProduct($user, 'restored-script-product');

        $response = $this
            ->withSession([
                '_old_input' => [
                    'cat_s' => 'themes',
                    'sc_cat' => (string) $scriptProduct->id,
                ],
            ])
            ->actingAs($user)
            ->get(route('store.create'));

        $response->assertOk()
            ->assertSee('name="sc_cat"', false)
            ->assertSee('value="themes" selected', false)
            ->assertSee('value="' . $scriptProduct->id . '" selected', false);
    }

    public function test_store_categories_endpoint_keeps_others_option_when_no_scripts_exist(): void
    {
        $this->seedThemeSetting();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('store.categories'), [
            'cat_s' => 'plugins',
        ]);

        $response->assertOk()
            ->assertSee('name="sc_cat"', false)
            ->assertSee('value="others"', false);
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

        foreach (['script', 'themes', 'plugins', 'templates'] as $category) {
            Option::updateOrCreate(
                ['o_type' => 'storecat', 'name' => $category],
                [
                    'o_valuer' => '0',
                    'o_parent' => 0,
                    'o_order' => 0,
                    'o_mode' => $category,
                ]
            );
        }
    }

    private function createStoreProduct(User $owner, string $name): Product
    {
        return Product::create([
            'name' => $name,
            'o_valuer' => 'Store description',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 15,
            'o_mode' => 'upload/product-cover.png',
        ]);
    }

    private function createScriptProduct(User $owner, string $name): Product
    {
        $product = $this->createStoreProduct($owner, $name);

        Option::create([
            'name' => 'script',
            'o_valuer' => '',
            'o_type' => 'store_type',
            'o_parent' => $product->id,
            'o_order' => 0,
            'o_mode' => 'others',
        ]);

        return $product;
    }
}
