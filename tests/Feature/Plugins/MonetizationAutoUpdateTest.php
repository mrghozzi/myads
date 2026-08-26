<?php

namespace Tests\Feature\Plugins;

use App\Models\User;
use App\Models\Option;
use App\Models\Short;
use App\Services\PluginManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class MonetizationAutoUpdateTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    private string $testPluginSlug = 'temp-auto-update-test';
    private string $testPluginPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();

        // Setup test plugin directory
        $this->testPluginPath = base_path('plugins/' . $this->testPluginSlug);
        $this->cleanupPlugin();
        File::makeDirectory($this->testPluginPath, 0755, true);

        // Write a mock plugin.json with ADStn_url
        File::put($this->testPluginPath . '/plugin.json', json_encode([
            'name' => 'Temp Auto Update Test Plugin',
            'slug' => $this->testPluginSlug,
            'description' => 'A dummy plugin for update tests.',
            'version' => '1.0.0',
            'author' => 'TestRunner',
            'ADStn_url' => 'temp-auto-update-test-slug',
        ]));
    }

    protected function tearDown(): void
    {
        $this->cleanupPlugin();
        parent::tearDown();
    }

    private function cleanupPlugin(): void
    {
        if (File::exists($this->testPluginPath)) {
            File::deleteDirectory($this->testPluginPath);
        }
    }

    /**
     * Test client-side PluginManager checkForUpdates detects and calls ADStn update URL.
     */
    public function test_client_update_checking_adstn_plugin_success(): void
    {
        // 1. Add license key to options
        Option::create([
            'o_type' => 'plugin_license',
            'name' => $this->testPluginSlug . '_key',
            'o_valuer' => 'ADSTN-ACTIVATE-TEST-1234',
        ]);

        // 2. Fake updates server response
        Http::fake([
            'https://www.adstn.ovh/api/marketplace/extensions/plugins' => Http::response([
                'success' => true,
                'version' => '2.0.0',
                'download_url' => 'https://www.adstn.ovh/api/marketplace/extensions/download?slug=temp-auto-update-test-slug',
                'changelog' => 'New awesome features!',
            ], 200)
        ]);

        // 3. Trigger check
        $updates = app(PluginManager::class)->checkForUpdates();

        // 4. Assert update is found
        $this->assertArrayHasKey($this->testPluginSlug, $updates);
        $this->assertEquals('2.0.0', $updates[$this->testPluginSlug]['new_version']);
        $this->assertEquals('New awesome features!', $updates[$this->testPluginSlug]['changelog']);

        // Assert post parameters were sent correctly
        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.adstn.ovh/api/marketplace/extensions/plugins' &&
                   $request['slug'] === 'temp-auto-update-test-slug' &&
                   $request['version'] === '1.0.0' &&
                   $request['license_key'] === 'ADSTN-ACTIVATE-TEST-1234';
        });
    }

    /**
     * Test store-side API /api/marketplace/extensions/plugins for free plugin.
     */
    public function test_store_api_plugin_update_checker_free_plugin(): void
    {
        // Create free product in store
        $product = Option::create([
            'o_type' => 'store',
            'name' => 'free-plugin',
            'o_valuer' => 'Free Plugin',
            'o_order' => 0, // Price: 0 PTS
            'o_parent' => 1,
        ]);

        // Create version file in store
        $file = Option::create([
            'o_type' => 'store_file',
            'name' => '2.1.0', // version
            'o_valuer' => 'Changelog description',
            'o_parent' => $product->id,
            'o_mode' => 'upload/store/free-plugin.zip',
        ]);

        // Call update API (GET request)
        $response = $this->json('GET', '/api/marketplace/extensions/plugins', [
            'slug' => 'free-plugin',
            'version' => '1.0.0',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'version' => '2.1.0',
            'changelog' => 'Changelog description',
        ]);
        $this->assertStringContainsString('/api/marketplace/extensions/download', $response->json('download_url'));
    }

    /**
     * Test store-side API /api/marketplace/extensions/plugins for paid plugin (Success, Mismatch, Activation).
     */
    public function test_store_api_plugin_update_checker_paid_plugin(): void
    {
        // Create paid product in store
        $product = Option::create([
            'o_type' => 'store',
            'name' => 'paid-plugin',
            'o_valuer' => 'Paid Plugin',
            'o_order' => 500, // Price: 500 PTS
            'o_parent' => 1,
        ]);

        $file = Option::create([
            'o_type' => 'store_file',
            'name' => '3.0.0',
            'o_parent' => $product->id,
            'o_mode' => 'upload/store/paid-plugin.zip',
        ]);

        // Insert license key
        DB::table('product_licenses')->insert([
            'product_id' => $product->id,
            'user_id' => 2,
            'license_key' => 'ADSTN-PAID-KEY',
            'domain' => null, // not activated yet
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 1. Missing license key on paid product should return 400
        $response = $this->json('POST', '/api/marketplace/extensions/plugins', [
            'slug' => 'paid-plugin',
            'version' => '1.0.0',
        ]);
        $response->assertStatus(400);

        // 2. Correct license key should activate domain and return details
        $response = $this->json('POST', '/api/marketplace/extensions/plugins', [
            'slug' => 'paid-plugin',
            'version' => '1.0.0',
            'license_key' => 'ADSTN-PAID-KEY',
            'domain' => 'my-client-site.com',
        ]);
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'version' => '3.0.0',
        ]);

        // Verify database domain activation
        $license = DB::table('product_licenses')->where('license_key', 'ADSTN-PAID-KEY')->first();
        $this->assertEquals('my-client-site.com', $license->domain);

        // 3. Different domain check should return 400
        $response = $this->json('POST', '/api/marketplace/extensions/plugins', [
            'slug' => 'paid-plugin',
            'version' => '1.0.0',
            'license_key' => 'ADSTN-PAID-KEY',
            'domain' => 'another-site.com',
        ]);
        $response->assertStatus(400);
        $response->assertJsonPath('success', false);
    }

    /**
     * Test store-side API /api/marketplace/extensions/download.
     */
    public function test_store_api_download_paid_plugin(): void
    {
        // Setup paid product & license
        $product = Option::create([
            'o_type' => 'store',
            'name' => 'download-plugin',
            'o_valuer' => 'Download Plugin',
            'o_order' => 500,
            'o_parent' => 1,
        ]);

        $file = Option::create([
            'o_type' => 'store_file',
            'name' => '1.5.0',
            'o_parent' => $product->id,
            'o_mode' => 'upload/store/download-plugin.zip',
        ]);

        Short::create([
            'uid' => 1,
            'url' => 'upload/store/download-plugin.zip',
            'sho' => 'hash123',
            'clik' => 0,
            'sh_type' => 7867,
            'tp_id' => $file->id,
        ]);

        DB::table('product_licenses')->insert([
            'product_id' => $product->id,
            'user_id' => 2,
            'license_key' => 'ADSTN-DOWNLOAD-KEY',
            'domain' => 'client-domain.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Write a mock dummy zip file to prevent 404 file missing
        $zipDir = base_path('upload/store');
        if (!File::exists($zipDir)) {
            File::makeDirectory($zipDir, 0755, true);
        }
        File::put($zipDir . '/download-plugin.zip', 'dummy zip contents');

        // 1. Download with invalid license key should return 400
        $response = $this->get('/api/marketplace/extensions/download?slug=download-plugin&license_key=BAD-KEY&domain=client-domain.com');
        $response->assertStatus(400);

        // 2. Download with valid key & domain should deliver the file
        $response = $this->get('/api/marketplace/extensions/download?slug=download-plugin&license_key=ADSTN-DOWNLOAD-KEY&domain=client-domain.com');
        $response->assertOk();
        $response->assertDownload('download-plugin.zip');

        // Cleanup dummy file
        File::delete($zipDir . '/download-plugin.zip');
    }
}
