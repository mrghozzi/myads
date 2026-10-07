<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Models\StoreSale;
use App\Models\StoreDiscountCode;
use App\Models\StoreDiscountRedemption;
use App\Models\ProductFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class StoreDiscountsFeatureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    /**
     * Test creating a temporary sale and displaying the discounted price
     */
    public function test_seller_can_create_temporary_sale_and_ui_shows_prices(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create(['pts' => 500]);

        \App\Models\UserPrivacySetting::create([
            'user_id' => $seller->id,
            'profile_visibility' => 'public',
        ]);

        $product = Product::create([
            'name' => 'PremiumTemplate',
            'o_valuer' => 'A template description',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 200, // 200 PTS price
            'o_mode' => 'template.jpg',
            'statu' => 1,
            'date' => time(),
        ]);

        // Create temporary sale: 200 PTS -> 150 PTS (25% off)
        $sale = StoreSale::create([
            'product_id' => $product->id,
            'sale_price' => 150,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'is_active' => true,
        ]);

        // Assert model attributes
        $this->assertEquals(150, $product->current_price);
        $this->assertTrue($product->isOnSale());

        // Check single product page view
        $response = $this->actingAs($buyer)->get(route('store.show', $product->name));
        $response->assertOk();
        $response->assertSee('200'); // old price
        $response->assertSee('150'); // new price
    }

    /**
     * Test Seller Discount Code CRUD
     */
    public function test_seller_discount_code_crud(): void
    {
        $seller = User::factory()->create();
        $product = Product::create([
            'name' => 'MyCoolPlugin',
            'o_valuer' => 'Description',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 100,
            'o_mode' => 'plugin.jpg',
            'statu' => 1,
            'date' => time(),
        ]);

        // 1. View listing
        $response = $this->actingAs($seller)->get(route('store.discounts.index'));
        $response->assertOk();

        // 2. View create form
        $response = $this->actingAs($seller)->get(route('store.discounts.create'));
        $response->assertOk();

        // 3. Store coupon
        $response = $this->actingAs($seller)->post(route('store.discounts.store'), [
            'name' => 'Plugin Launch Offer',
            'code' => 'LAUNCH50',
            'discount_type' => 'percent',
            'discount_value' => 50,
            'scope' => 'one_of_my_products',
            'product_id' => $product->id,
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('store.discounts.index'));
        $this->assertDatabaseHas('store_discount_codes', [
            'code' => 'LAUNCH50',
            'user_id' => $seller->id,
            'applies_to' => 'product',
            'target_value' => $product->id,
        ]);

        $coupon = StoreDiscountCode::where('code', 'LAUNCH50')->first();

        // 4. View edit form
        $response = $this->actingAs($seller)->get(route('store.discounts.edit', $coupon->id));
        $response->assertOk();

        // 5. Update coupon
        $response = $this->actingAs($seller)->post(route('store.discounts.update', $coupon->id), [
            'name' => 'Plugin Launch Offer Updated',
            'code' => 'LAUNCH60',
            'discount_type' => 'percent',
            'discount_value' => 60,
            'scope' => 'all_my_products',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('store.discounts.index'));
        $this->assertDatabaseHas('store_discount_codes', [
            'id' => $coupon->id,
            'code' => 'LAUNCH60',
            'discount_value' => 60,
            'applies_to' => 'all',
            'target_value' => null,
        ]);

        // 6. Delete coupon (GET request is used for deletion routes)
        $response = $this->actingAs($seller)->get(route('store.discounts.destroy', $coupon->id));
        $response->assertRedirect(route('store.discounts.index'));
        $this->assertDatabaseMissing('store_discount_codes', [
            'id' => $coupon->id,
        ]);
    }

    /**
     * Test Admin Discount Code CRUD
     */
    public function test_admin_discount_code_crud(): void
    {
        // User with id=1 is super admin
        $admin = User::factory()->create(['id' => 1]);
        $seller = User::factory()->create();

        $product = Product::create([
            'name' => 'SpecialScript',
            'o_valuer' => 'Description',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 100,
            'o_mode' => 'script.jpg',
            'statu' => 1,
            'date' => time(),
        ]);

        // 1. View listing
        $response = $this->actingAs($admin)->get(route('admin.store.discounts.index'));
        $response->assertOk();

        // 2. View create form
        $response = $this->actingAs($admin)->get(route('admin.store.discounts.create'));
        $response->assertOk();

        // 3. Store coupon
        $response = $this->actingAs($admin)->post(route('admin.store.discounts.store'), [
            'name' => 'Admin Promo',
            'code' => 'ADMIN20',
            'discount_type' => 'fixed',
            'discount_value' => 20,
            'applies_to' => 'seller',
            'target_value' => $seller->id,
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('admin.store.discounts.index'));
        $this->assertDatabaseHas('store_discount_codes', [
            'code' => 'ADMIN20',
            'user_id' => null, // admin coupon
            'applies_to' => 'seller',
            'target_value' => $seller->id,
        ]);

        $coupon = StoreDiscountCode::where('code', 'ADMIN20')->first();

        // 4. View edit form
        $response = $this->actingAs($admin)->get(route('admin.store.discounts.edit', $coupon->id));
        $response->assertOk();

        // 5. Update coupon
        $response = $this->actingAs($admin)->post(route('admin.store.discounts.update', $coupon->id), [
            'name' => 'Admin Promo Edit',
            'code' => 'ADMIN30',
            'discount_type' => 'fixed',
            'discount_value' => 30,
            'applies_to' => 'all',
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('admin.store.discounts.index'));
        $this->assertDatabaseHas('store_discount_codes', [
            'id' => $coupon->id,
            'code' => 'ADMIN30',
            'discount_value' => 30,
            'applies_to' => 'all',
            'target_value' => null,
        ]);

        // 6. Delete coupon (GET request is used for deletion routes)
        $response = $this->actingAs($admin)->get(route('admin.store.discounts.destroy', $coupon->id));
        $response->assertRedirect(route('admin.store.discounts.index'));
        $this->assertDatabaseMissing('store_discount_codes', [
            'id' => $coupon->id,
        ]);
    }

    /**
     * Test Ajax Coupon Verification
     */
    public function test_ajax_discount_validation(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $product = Product::create([
            'name' => 'SamplePlugin',
            'o_valuer' => 'Description',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 100,
            'o_mode' => 'plugin.jpg',
            'statu' => 1,
            'date' => time(),
        ]);

        $coupon = StoreDiscountCode::create([
            'user_id' => $seller->id,
            'name' => 'Sale',
            'code' => 'SALE10',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'applies_to' => 'product',
            'target_value' => $product->id,
            'is_active' => true,
        ]);

        // Validate via POST AJAX
        $response = $this->actingAs($buyer)->postJson(route('store.discounts.validate'), [
            'code' => 'SALE10',
            'product_id' => $product->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'discount_amount' => 10,
                'final_price' => 90,
            ]);
    }

    /**
     * Test purchasing a product with a valid coupon
     */
    public function test_purchase_product_with_coupon_updates_balances_and_creates_license(): void
    {
        $seller = User::factory()->create(['pts' => 100]);
        $buyer = User::factory()->create(['pts' => 500]);

        $product = Product::create([
            'name' => 'BuyablePlugin',
            'o_valuer' => 'Description',
            'o_type' => 'store',
            'o_parent' => $seller->id,
            'o_order' => 100,
            'o_mode' => 'plugin.jpg',
            'statu' => 1,
            'date' => time(),
        ]);

        // Create product file version so download works
        ProductFile::create([
            'name' => 'v1.0.0',
            'o_valuer' => 'my-file.zip',
            'o_type' => 'store_file',
            'o_parent' => $product->id,
            'o_order' => 10,
            'o_mode' => 'upload/initial.zip',
        ]);

        $coupon = StoreDiscountCode::create([
            'user_id' => $seller->id,
            'name' => 'Half Price',
            'code' => 'HALF',
            'discount_type' => 'percent',
            'discount_value' => 50,
            'applies_to' => 'product',
            'target_value' => $product->id,
            'is_active' => true,
        ]);

        // Purchase with coupon
        $response = $this->actingAs($buyer)->postJson(route('store.purchase', $product->id), [
            'code' => 'HALF',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        // Check points
        $buyer->refresh();
        $seller->refresh();

        // Original price 100 - 50% discount = 50 PTS final price
        $this->assertEquals(450, $buyer->pts);
        $this->assertEquals(150, $seller->pts);

        // Check redemption
        $this->assertDatabaseHas('store_discount_redemptions', [
            'user_id' => $buyer->id,
            'discount_code_id' => $coupon->id,
            'product_id' => $product->id,
            'points_saved' => 50,
        ]);

        // Check product license
        $this->assertDatabaseHas('product_licenses', [
            'user_id' => $buyer->id,
            'product_id' => $product->id,
        ]);
    }
}
