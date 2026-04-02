<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\User;
use App\Models\UserPrivacySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;
use ZipArchive;

class MarketplaceExtensionFeedTest extends TestCase
{
    use RefreshDatabase;

    private string $tempPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempPath = base_path('upload/testing-marketplace-feed');
        File::deleteDirectory($this->tempPath);
        File::makeDirectory($this->tempPath, 0755, true);

        Cache::forget('marketplace_extension_catalog_plugins');
        Cache::forget('marketplace_extension_catalog_themes');
    }

    protected function tearDown(): void
    {
        Cache::forget('marketplace_extension_catalog_plugins');
        Cache::forget('marketplace_extension_catalog_themes');

        File::deleteDirectory($this->tempPath);

        parent::tearDown();
    }

    public function test_plugins_feed_only_returns_public_products_with_valid_plugin_manifest(): void
    {
        $owner = $this->createPublicOwner();

        $validProduct = $this->createStoreProduct($owner, 'valid-plugin-product', 'plugins');
        $this->attachZip($validProduct, 'valid-plugin-product.zip', 'plugin.json', [
            'name' => 'Valid Plugin',
            'slug' => 'valid-plugin',
            'version' => '1.4.0',
            'author' => 'Marketplace Tests',
            'description' => 'Valid plugin description.',
            'min_myads' => '4.2.3',
        ]);

        $invalidProduct = $this->createStoreProduct($owner, 'invalid-plugin-product', 'plugins');
        $this->attachZip($invalidProduct, 'invalid-plugin-product.zip', 'README.md', [
            'note' => 'Missing plugin manifest',
        ]);

        $suspendedProduct = $this->createStoreProduct($owner, 'suspended-plugin-product', 'plugins');
        $this->attachZip($suspendedProduct, 'suspended-plugin-product.zip', 'plugin.json', [
            'name' => 'Suspended Plugin',
            'slug' => 'suspended-plugin',
            'version' => '1.0.0',
            'author' => 'Marketplace Tests',
            'description' => 'Should not be listed.',
            'min_myads' => '4.2.3',
        ]);
        Option::create([
            'name' => 'suspended',
            'o_valuer' => '1',
            'o_type' => 'store_status',
            'o_parent' => $suspendedProduct->id,
            'o_order' => 0,
            'o_mode' => (string) time(),
        ]);

        $response = $this->getJson(route('api.marketplace.extensions.plugins'));

        $response->assertOk()
            ->assertJsonPath('type', 'plugins');

        $items = $response->json('items');

        $this->assertCount(1, $items);
        $this->assertSame('valid-plugin', $items[0]['slug']);
        $this->assertSame(route('store.show', 'valid-plugin-product'), $items[0]['product_url']);
    }

    public function test_themes_feed_merges_themes_and_legacy_templates_and_prefers_themes_category(): void
    {
        $owner = $this->createPublicOwner();

        $legacyTheme = $this->createStoreProduct($owner, 'legacy-theme-product', 'templates');
        $this->attachZip($legacyTheme, 'legacy-theme-product.zip', 'theme.json', [
            'name' => 'Legacy Theme',
            'slug' => 'shared-theme',
            'version' => '1.0.0',
            'author' => 'Marketplace Tests',
            'description' => 'Legacy template-backed theme.',
            'min_myads' => '4.2.3',
        ], true);

        $officialTheme = $this->createStoreProduct($owner, 'official-theme-product', 'themes');
        $this->attachZip($officialTheme, 'official-theme-product.zip', 'theme.json', [
            'name' => 'Official Theme',
            'slug' => 'shared-theme',
            'version' => '2.0.0',
            'author' => 'Marketplace Tests',
            'description' => 'Official themes category item.',
            'min_myads' => '4.2.3',
        ]);

        $additionalLegacyTheme = $this->createStoreProduct($owner, 'legacy-only-theme-product', 'templates');
        $this->attachZip($additionalLegacyTheme, 'legacy-only-theme-product.zip', 'theme.json', [
            'name' => 'Legacy Only Theme',
            'slug' => 'legacy-only-theme',
            'version' => '1.1.0',
            'author' => 'Marketplace Tests',
            'description' => 'Still listed through compatibility.',
            'min_myads' => '4.2.3',
        ]);

        $response = $this->getJson(route('api.marketplace.extensions.themes'));

        $response->assertOk()
            ->assertJsonPath('type', 'themes');

        $items = collect($response->json('items'))->keyBy('slug');

        $this->assertCount(2, $items);
        $this->assertSame(route('store.show', 'official-theme-product'), $items['shared-theme']['product_url']);
        $this->assertSame('themes', $items['shared-theme']['category']);
        $this->assertSame('templates', $items['legacy-only-theme']['category']);
    }

    private function createPublicOwner(): User
    {
        $owner = User::factory()->create();

        UserPrivacySetting::create([
            'user_id' => $owner->id,
            'profile_visibility' => 'public',
        ]);

        return $owner;
    }

    private function createStoreProduct(User $owner, string $name, string $category): Product
    {
        $product = Product::create([
            'name' => $name,
            'o_valuer' => 'Marketplace feed product',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 0,
            'o_mode' => 'upload/product-cover.png',
        ]);

        Option::create([
            'name' => $category,
            'o_valuer' => '',
            'o_type' => 'store_type',
            'o_parent' => $product->id,
            'o_order' => 0,
            'o_mode' => 'others',
        ]);

        return $product;
    }

    /**
     * @param array<string, string> $manifest
     */
    private function attachZip(Product $product, string $archiveName, string $manifestFile, array $manifest, bool $nested = false): void
    {
        $archivePath = $this->tempPath . DIRECTORY_SEPARATOR . $archiveName;
        $zip = new ZipArchive();

        $this->assertTrue($zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true);

        $payload = (string) json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $entryName = $nested ? 'release-root/' . $manifestFile : $manifestFile;
        $zip->addFromString($entryName, $payload);
        $zip->close();

        ProductFile::create([
            'name' => 'v1.0.0',
            'o_valuer' => 'Marketplace package',
            'o_type' => 'store_file',
            'o_parent' => $product->id,
            'o_order' => 0,
            'o_mode' => 'upload/testing-marketplace-feed/' . $archiveName,
        ]);
    }
}
