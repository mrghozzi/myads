<?php

namespace Tests\Feature;

use App\Models\BillingCoupon;
use App\Models\BillingOrder;
use App\Models\MemberSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\Billing\BillingCouponService;
use App\Services\Billing\SubscriptionLifecycleService;
use App\Support\SubscriptionGatewaySettings;
use App\Support\SubscriptionSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class BillingDiscountsTrialsAndCouponsTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_plan_sale_price_and_discount_calculation(): void
    {
        $this->seedSiteSettings();

        // 1. Active sale with future expiration
        $plan = SubscriptionPlan::create([
            'name' => 'Pro Launch Deal',
            'base_price' => 100.00,
            'sale_price' => 70.00,
            'sale_ends_at' => now()->addDays(5),
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $this->assertTrue($plan->is_on_sale);
        $this->assertEquals(70.00, $plan->current_price);
        $this->assertEquals(30, $plan->discount_percentage);

        // 2. Expired sale
        $plan->update([
            'sale_ends_at' => now()->subMinute(),
        ]);
        $plan->refresh();

        $this->assertFalse($plan->is_on_sale);
        $this->assertEquals(100.00, $plan->current_price);
        $this->assertEquals(0, $plan->discount_percentage);

        // 3. No sale price set
        $plan->update([
            'sale_price' => null,
            'sale_ends_at' => null,
        ]);
        $plan->refresh();

        $this->assertFalse($plan->is_on_sale);
        $this->assertEquals(100.00, $plan->current_price);
    }

    public function test_coupon_validation_and_calculation(): void
    {
        $this->seedSiteSettings();

        $planA = SubscriptionPlan::create([
            'name' => 'Plan A',
            'base_price' => 100.00,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $planB = SubscriptionPlan::create([
            'name' => 'Plan B',
            'base_price' => 50.00,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        // Percentage coupon for all plans
        $couponPercent = BillingCoupon::create([
            'code' => 'SAVE25',
            'discount_type' => 'percent',
            'discount_value' => 25.00,
            'is_active' => true,
        ]);

        $couponService = app(BillingCouponService::class);

        $res = $couponService->validate('SAVE25', $planA);
        $this->assertTrue($res['valid']);
        $this->assertEquals(25.00, $res['discount_amount']);
        $this->assertEquals(75.00, $res['final_amount']);

        // Fixed coupon restricted to Plan B
        $couponFixed = BillingCoupon::create([
            'code' => 'FIXED10',
            'discount_type' => 'fixed',
            'discount_value' => 10.00,
            'subscription_plan_id' => $planB->id,
            'is_active' => true,
        ]);

        // Fails on Plan A
        $resPlanA = $couponService->validate('FIXED10', $planA);
        $this->assertFalse($resPlanA['valid']);

        // Succeeds on Plan B
        $resPlanB = $couponService->validate('FIXED10', $planB);
        $this->assertTrue($resPlanB['valid']);
        $this->assertEquals(10.00, $resPlanB['discount_amount']);
        $this->assertEquals(40.00, $resPlanB['final_amount']);

        // Expired coupon
        $expiredCoupon = BillingCoupon::create([
            'code' => 'EXPIRED',
            'discount_type' => 'percent',
            'discount_value' => 10.00,
            'expires_at' => now()->subDay(),
            'is_active' => true,
        ]);
        $resExpired = $couponService->validate('EXPIRED', $planA);
        $this->assertFalse($resExpired['valid']);

        // Max redemptions reached
        $exhaustedCoupon = BillingCoupon::create([
            'code' => 'MAXED',
            'discount_type' => 'percent',
            'discount_value' => 10.00,
            'max_uses' => 2,
            'used_count' => 2,
            'is_active' => true,
        ]);
        $resMax = $couponService->validate('MAXED', $planA);
        $this->assertFalse($resMax['valid']);
    }

    public function test_coupon_ajax_validation_endpoint(): void
    {
        $this->seedSiteSettings();
        SubscriptionSettings::save(['enabled' => true, 'base_currency_code' => 'USD']);

        $user = User::factory()->create();
        $plan = SubscriptionPlan::create([
            'name' => 'Gold VIP',
            'base_price' => 80.00,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        BillingCoupon::create([
            'code' => 'VIP50',
            'discount_type' => 'percent',
            'discount_value' => 50.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('billing.coupon.validate'), [
            'plan_id' => $plan->id,
            'coupon_code' => 'vip50', // Case insensitivity test
        ]);

        $response->assertOk();
        $response->assertJson([
            'valid' => true,
            'code' => 'VIP50',
            'discount_amount' => 40.0,
            'final_amount' => 40.0,
        ]);
    }

    public function test_purchase_with_coupon_applies_discount_and_records_usage(): void
    {
        $this->seedSiteSettings();
        SubscriptionSettings::save(['enabled' => 1, 'base_currency_code' => 'USD']);
        SubscriptionGatewaySettings::save('bank_transfer', [
            'enabled' => 1,
            'supported_currencies' => ['USD'],
        ]);

        $user = User::factory()->create();
        $plan = SubscriptionPlan::create([
            'name' => 'Elite Member',
            'base_price' => 100.00,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $coupon = BillingCoupon::create([
            'code' => 'SPECIAL20',
            'discount_type' => 'percent',
            'discount_value' => 20.00,
            'is_active' => true,
        ]);

        // Purchase with coupon
        $response = $this->actingAs($user)->post(route('billing.purchase', $plan->id), [
            'gateway' => 'bank_transfer',
            'currency_code' => 'USD',
            'coupon_code' => 'SPECIAL20',
        ]);

        $response->assertRedirect();

        $order = BillingOrder::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(20.00, $order->discount_amount);
        $this->assertEquals(80.00, $order->final_amount);
        $this->assertEquals('SPECIAL20', $order->coupon_code);
        $this->assertEquals($coupon->id, $order->billing_coupon_id);

        // When order is marked paid, coupon usage increments
        app(SubscriptionLifecycleService::class)->completePaidOrder($order);
        $coupon->refresh();
        $this->assertEquals(1, $coupon->used_count);
    }

    public function test_100_percent_discount_coupon_activates_subscription_immediately(): void
    {
        $this->seedSiteSettings();
        SubscriptionSettings::save(['enabled' => 1, 'base_currency_code' => 'USD']);
        SubscriptionGatewaySettings::save('bank_transfer', [
            'enabled' => 1,
            'supported_currencies' => ['USD'],
        ]);

        $user = User::factory()->create();
        $plan = SubscriptionPlan::create([
            'name' => 'Free Pass Plan',
            'base_price' => 50.00,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        BillingCoupon::create([
            'code' => 'FREE100',
            'discount_type' => 'percent',
            'discount_value' => 100.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('billing.purchase', $plan->id), [
            'gateway' => 'bank_transfer',
            'currency_code' => 'USD',
            'coupon_code' => 'FREE100',
        ]);

        $order = BillingOrder::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals(BillingOrder::STATUS_PAID, $order->status);
        $this->assertEquals(0.00, $order->final_amount);

        // Member subscription should now be active
        $sub = MemberSubscription::where('user_id', $user->id)->first();
        $this->assertNotNull($sub);
        $this->assertEquals('active', $sub->status);
        $this->assertEquals('Free Pass Plan', $sub->plan_name);

        $response->assertRedirect(route('billing.orders.receipt.show', $order->id));
    }

    public function test_free_trial_claim_and_prevention_of_duplicate_claims(): void
    {
        $this->seedSiteSettings();
        SubscriptionSettings::save(['enabled' => true, 'base_currency_code' => 'USD']);

        $user = User::factory()->create();
        $plan = SubscriptionPlan::create([
            'name' => 'Trial Friendly Plan',
            'base_price' => 45.00,
            'trial_days' => 14,
            'duration_days' => 30,
            'is_active' => true,
        ]);

        $lifecycle = app(SubscriptionLifecycleService::class);
        $this->assertTrue($lifecycle->userCanClaimTrial($user->id, $plan));

        // Claim free trial
        $response = $this->actingAs($user)->post(route('billing.trial.claim', $plan->id));
        $response->assertRedirect(route('billing.dashboard'));
        $response->assertSessionHas('success');

        // Check subscription record
        $sub = MemberSubscription::where('user_id', $user->id)->first();
        $this->assertNotNull($sub);
        $this->assertTrue((bool) $sub->is_trial);
        $this->assertEquals('active', $sub->status);
        $this->assertNotNull($sub->trial_ends_at);
        $this->assertTrue($sub->trial_ends_at->isFuture());

        // Attempting to claim again on the same plan should be rejected
        $this->assertFalse($lifecycle->userCanClaimTrial($user->id, $plan));
        $duplicateResponse = $this->actingAs($user)->post(route('billing.trial.claim', $plan->id));
        $duplicateResponse->assertRedirect(route('billing.plans'));
        $duplicateResponse->assertSessionHas('error');
    }

    public function test_landing_page_renders_active_plans_for_visitors_when_billing_enabled(): void
    {
        $this->seedSiteSettings();
        SubscriptionSettings::save(['enabled' => true, 'base_currency_code' => 'USD']);

        SubscriptionPlan::create([
            'name' => 'Showcase Hero Plan',
            'base_price' => 99.00,
            'sale_price' => 69.00,
            'sale_ends_at' => now()->addDays(7),
            'trial_days' => 7,
            'duration_days' => 30,
            'is_active' => true,
            'is_featured' => true,
            'marketing_bullets' => ['Priority Ad Exposure', 'Double PTS Multiplier'],
        ]);

        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('Showcase Hero Plan');
        $response->assertSee('69.00');
        $response->assertSee('7');
    }

    public function test_admin_can_manage_coupons(): void
    {
        $this->seedSiteSettings();

        // User ID 1 is the SuperAdmin in MyAds
        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'superadmin',
        ]);

        // 1. Visit coupons index
        $response = $this->actingAs($admin)->get(route('admin.billing.coupons'));
        $response->assertOk();
        $response->assertSee(__('messages.billing_coupons_title'));

        // 2. Store new coupon
        $storeResponse = $this->actingAs($admin)->post(route('admin.billing.coupons.store'), [
            'code' => 'BLACKFRIDAY',
            'description' => 'Black Friday 50% discount',
            'discount_type' => 'percent',
            'discount_value' => 50,
            'max_uses' => 500,
            'is_active' => 1,
        ]);

        $storeResponse->assertRedirect(route('admin.billing.coupons'));
        $this->assertDatabaseHas('billing_coupons', [
            'code' => 'BLACKFRIDAY',
            'discount_value' => 50,
        ]);

        $coupon = BillingCoupon::where('code', 'BLACKFRIDAY')->first();

        // 3. Update coupon
        $updateResponse = $this->actingAs($admin)->post(route('admin.billing.coupons.update', $coupon->id), [
            'code' => 'BLACKFRIDAY',
            'description' => 'Updated Black Friday discount',
            'discount_type' => 'fixed',
            'discount_value' => 25,
            'is_active' => 1,
        ]);

        $updateResponse->assertRedirect(route('admin.billing.coupons'));
        $coupon->refresh();
        $this->assertEquals('fixed', $coupon->discount_type);
        $this->assertEquals(25.00, $coupon->discount_value);

        // 4. Delete coupon
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.billing.coupons.delete', $coupon->id));
        $deleteResponse->assertRedirect(route('admin.billing.coupons'));
        $this->assertDatabaseMissing('billing_coupons', ['id' => $coupon->id]);
    }
}
