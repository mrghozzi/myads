<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Short;
use App\Models\SiteAdmin;
use App\Models\User;
use App\Models\UserPrivacySetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class StoreEnhancementsFeatureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    public function test_store_search_filters_products_correctly(): void
    {
        $owner = User::factory()->create();
        UserPrivacySetting::create(['user_id' => $owner->id, 'profile_visibility' => 'public']);

        $product1 = Product::create([
            'name' => 'LaravelCMS',
            'o_valuer' => 'A powerful CMS written in PHP',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 50,
            'o_mode' => 'cms.jpg',
        ]);

        $product2 = Product::create([
            'name' => 'ReactTheme',
            'o_valuer' => 'Modern frontend dashboard template',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 0,
            'o_mode' => 'react.jpg',
        ]);

        // 1. Search for "CMS"
        $response = $this->get(route('store.index', ['q' => 'CMS']));
        $response->assertStatus(200);
        $response->assertSee('LaravelCMS');
        $response->assertDontSee('ReactTheme');

        // 2. Search for "React"
        $response = $this->get(route('store.index', ['q' => 'React']));
        $response->assertStatus(200);
        $response->assertSee('ReactTheme');
        $response->assertDontSee('LaravelCMS');

        // 3. Filter for Free items
        $response = $this->get(route('store.index', ['sort' => 'free']));
        $response->assertStatus(200);
        $response->assertSee('ReactTheme');
        $response->assertDontSee('LaravelCMS');

        // 4. Filter for Paid items
        $response = $this->get(route('store.index', ['sort' => 'paid']));
        $response->assertStatus(200);
        $response->assertSee('LaravelCMS');
        $response->assertDontSee('ReactTheme');
    }

    public function test_pending_product_is_hidden_from_public_but_visible_to_owner_and_admin(): void
    {
        $owner = User::factory()->create();
        UserPrivacySetting::create(['user_id' => $owner->id, 'profile_visibility' => 'public']);

        $product = Product::create([
            'name' => 'PendingExtension',
            'o_valuer' => 'Awaiting admin review',
            'o_type' => 'store',
            'o_parent' => $owner->id,
            'o_order' => 100,
            'o_mode' => 'pending.jpg',
        ]);

        // Mark product as pending
        Option::create([
            'name' => 'pending',
            'o_valuer' => '1',
            'o_type' => 'store_status',
            'o_parent' => $product->id,
        ]);

        // 1. Guest cannot see pending product in store index
        $response = $this->get(route('store.index'));
        $response->assertStatus(200);
        $response->assertDontSee('PendingExtension');

        // 2. Other member cannot view pending product
        $otherUser = User::factory()->create();
        $response = $this->actingAs($otherUser)->get(route('store.show', $product->name));
        $response->assertStatus(403);

        // 3. Product owner can see pending product with badge
        $response = $this->actingAs($owner)->get(route('store.index'));
        $response->assertStatus(200);
        $response->assertSee('PendingExtension');
        $response->assertSee(__('messages.pending_approval'));

        // 4. Admin can view pending product
        $admin = User::factory()->create();
        SiteAdmin::create([
            'user_id' => $admin->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => $admin->id,
        ]);
        $response = $this->actingAs($admin)->get(route('store.index'));
        $response->assertStatus(200);
        $response->assertSee('PendingExtension');

        $response = $this->actingAs($admin)->get(route('store.show', $product->name));
        $response->assertStatus(200);
    }

    public function test_my_purchases_page_displays_buyer_licenses_and_files(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $product = Product::create([
            'name' => 'ProPlugin',
            'o_valuer' => 'Full featured premium plugin',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 200,
            'o_mode' => 'plugin.jpg',
        ]);

        $file = ProductFile::create([
            'name' => 'v1.5',
            'o_valuer' => 'First release',
            'o_type' => 'store_file',
            'o_parent' => $product->id,
            'o_order' => 0,
            'o_mode' => 'upload/pro_plugin_v15.zip',
        ]);

        Short::create([
            'uid' => $seller->id,
            'url' => 'upload/pro_plugin_v15.zip',
            'sho' => hash('crc32', 'upload/pro_plugin_v15.zip' . $file->id),
            'clik' => 0,
            'sh_type' => 7867,
            'tp_id' => $file->id,
        ]);

        // Create purchased license
        DB::table('product_licenses')->insert([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'license_key' => 'ADSTN-TEST-1234-ABCD',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 1. Guest redirected to login
        $response = $this->get(route('store.my_purchases'));
        $response->assertRedirect(route('login'));

        // 2. Buyer can view My Purchases page
        $response = $this->actingAs($buyer)->get(route('store.my_purchases'));
        $response->assertStatus(200);
        $response->assertSee('ProPlugin');
        $response->assertSee('ADSTN-TEST-1234-ABCD');
        $response->assertSee('v1.5');
    }

    public function test_admin_can_filter_search_approve_and_reject_products(): void
    {
        $admin = User::factory()->create(['id' => 1]);
        $seller = User::factory()->create();

        $product = Product::create([
            'name' => 'ModeratedTheme',
            'o_valuer' => 'Needs inspection',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 75,
            'o_mode' => 'theme.jpg',
        ]);

        Option::create([
            'name' => 'pending',
            'o_valuer' => '1',
            'o_type' => 'store_status',
            'o_parent' => $product->id,
        ]);

        // 1. Admin products list with search and pending filter
        $response = $this->actingAs($admin)->get(route('admin.products', ['status' => 'pending', 'search' => 'ModeratedTheme']));
        $response->assertStatus(200);
        $response->assertSee('ModeratedTheme');
        $response->assertSee(__('messages.pending_approval'));

        // 2. Admin approves product
        $response = $this->actingAs($admin)->post(route('admin.products.approve', $product->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('options', [
            'o_type' => 'store_status',
            'o_parent' => $product->id,
            'name' => 'pending',
        ]);
        $this->assertEquals('active', $product->fresh()->moderation_status);

        // 3. Admin rejects product with reason
        $response = $this->actingAs($admin)->post(route('admin.products.reject', $product->id), [
            'reason' => 'Code does not comply with safety rules',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('options', [
            'o_type' => 'store_status',
            'o_parent' => $product->id,
            'name' => 'suspended',
            'o_valuer' => 'Code does not comply with safety rules',
        ]);
        $this->assertEquals('suspended', $product->fresh()->moderation_status);
    }

    public function test_admin_store_sales_monitoring_page(): void
    {
        $admin = User::factory()->create(['id' => 1]);
        $seller = User::factory()->create(['username' => 'super_seller']);
        $buyer = User::factory()->create(['username' => 'happy_buyer']);

        $product = Product::create([
            'name' => 'MonitoredScript',
            'o_valuer' => 'Script on sale',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 120,
            'o_mode' => 'script.jpg',
        ]);

        DB::table('product_licenses')->insert([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'license_key' => 'ADSTN-SALE-5678-WXYZ',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.store.sales'));
        $response->assertStatus(200);
        $response->assertSee('MonitoredScript');
        $response->assertSee('happy_buyer');
        $response->assertSee('super_seller');
        $response->assertSee('ADSTN-SALE-5678-WXYZ');
    }
}
