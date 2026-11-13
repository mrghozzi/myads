<?php

namespace Tests\Feature;

use App\Models\BillingCoupon;
use App\Models\MemberSubscription;
use App\Models\Option;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\SubscriptionSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class MobileBillingApiTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected User $user;
    protected SubscriptionPlan $proPlan;
    protected SubscriptionPlan $trialPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedSiteSettings(['url' => 'http://localhost']);

        Option::updateOrCreate(
            ['o_type' => 'mobile_api', 'name' => 'api_key'],
            ['o_valuer' => 'test-mobile-api-key', 'o_order' => 0]
        );

        SubscriptionSettings::clearCache();
        SubscriptionSettings::save([
            'enabled' => 1,
            'base_currency_code' => 'USD',
        ]);

        $this->user = User::factory()->create([
            'username' => 'mobile_tester',
            'email' => 'mobile_tester@example.com',
            'pts' => 500,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Pro Yearly Super',
            'description' => 'Best plan for professional webmasters',
            'duration_days' => 365,
            'is_lifetime' => false,
            'base_price' => 100.00,
            'sale_price' => 75.00,
            'sale_ends_at' => now()->addDays(7),
            'trial_days' => 7,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
            'accent_color' => '#10b981',
            'layout_style' => 'glassmorphism',
            'recommended_text' => 'POPULAR CHOICE',
            'marketing_bullets' => ['Priority placement', 'No ad fees'],
            'entitlements' => [
                'profile_badge_label' => 'PRO ELITE',
                'profile_badge_color' => '#10b981',
                'subscription_verified_badge' => true,
                'ad_free' => true,
                'bonus_pts' => 500,
            ],
        ]);

        $this->trialPlan = SubscriptionPlan::create([
            'name' => 'Starter Trial',
            'description' => 'Try our services free for 14 days',
            'duration_days' => 30,
            'is_lifetime' => false,
            'base_price' => 20.00,
            'trial_days' => 14,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 2,
            'accent_color' => '#6366f1',
            'layout_style' => 'minimal',
            'marketing_bullets' => ['14-day free trial'],
            'entitlements' => [
                'profile_badge_label' => 'STARTER',
                'profile_badge_color' => '#6366f1',
            ],
        ]);
    }

    public function test_webview_sanctum_bearer_token_authenticates_web_user_to_billing(): void
    {
        $token = $this->user->createToken('mobile-webview-test')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Mobile-App' => 'true',
        ])->get('/billing');

        $response->assertOk();
        $this->assertTrue(auth()->guard('web')->check());
        $this->assertSame($this->user->id, auth()->guard('web')->id());
    }

    public function test_webview_unauthenticated_request_redirects_to_login(): void
    {
        $response = $this->get('/billing');

        $response->assertRedirect('/login');
        $this->assertFalse(auth()->guard('web')->check());
    }

    public function test_api_billing_plans_returns_active_plans_with_promotions(): void
    {
        $response = $this->withHeaders(['X-API-KEY' => 'test-mobile-api-key'])
            ->actingAs($this->user, 'sanctum')
            ->getJson('/api/billing/plans');

        $response->assertOk()
            ->assertJsonPath('enabled', true)
            ->assertJsonCount(2, 'plans');

        $proPlanData = collect($response->json('plans'))->firstWhere('id', $this->proPlan->id);

        $this->assertNotNull($proPlanData);
        $this->assertSame('Pro Yearly Super', $proPlanData['name']);
        $this->assertTrue($proPlanData['is_on_sale']);
        $this->assertSame(75.0, (float) $proPlanData['current_price']);
        $this->assertSame(25, $proPlanData['discount_percentage']);
        $this->assertTrue($proPlanData['has_trial']);
        $this->assertSame(7, $proPlanData['trial_days']);
        $this->assertTrue($proPlanData['is_featured']);
        $this->assertNotEmpty($proPlanData['benefits']);
    }

    public function test_api_billing_subscription_lifecycle(): void
    {
        // 1. Initially no active subscription
        $initialResponse = $this->withHeaders(['X-API-KEY' => 'test-mobile-api-key'])
            ->actingAs($this->user, 'sanctum')
            ->getJson('/api/billing/subscription');

        $initialResponse->assertOk()
            ->assertJsonPath('enabled', true)
            ->assertJsonPath('has_active_subscription', false)
            ->assertJsonPath('subscription', null);

        // 2. Claim free trial via mobile API
        $trialResponse = $this->withHeaders(['X-API-KEY' => 'test-mobile-api-key'])
            ->actingAs($this->user, 'sanctum')
            ->postJson('/api/billing/trial', [
                'plan_id' => $this->trialPlan->id,
            ]);

        $trialResponse->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('subscription.is_trial', true)
            ->assertJsonPath('subscription.plan_name', 'Starter Trial');

        // 3. User now has active subscription
        $activeResponse = $this->withHeaders(['X-API-KEY' => 'test-mobile-api-key'])
            ->actingAs($this->user, 'sanctum')
            ->getJson('/api/billing/subscription');

        $activeResponse->assertOk()
            ->assertJsonPath('has_active_subscription', true)
            ->assertJsonPath('subscription.plan_name', 'Starter Trial')
            ->assertJsonPath('subscription.is_trial', true);

        $this->assertDatabaseHas('member_subscriptions', [
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->trialPlan->id,
            'status' => MemberSubscription::STATUS_ACTIVE,
            'is_trial' => true,
        ]);
    }

    public function test_api_billing_validate_coupon(): void
    {
        $coupon = BillingCoupon::create([
            'code' => 'MOBILE20',
            'discount_type' => 'percent',
            'discount_value' => 20.00,
            'is_active' => true,
            'subscription_plan_id' => null, // all plans
        ]);

        $response = $this->withHeaders(['X-API-KEY' => 'test-mobile-api-key'])
            ->actingAs($this->user, 'sanctum')
            ->postJson('/api/billing/validate-coupon', [
                'plan_id' => $this->proPlan->id,
                'coupon_code' => 'MOBILE20',
            ]);

        $response->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('code', 'MOBILE20')
            ->assertJsonPath('discount_type', 'percent');

        // Effective base price is $75 (on sale), 20% discount is $15 -> $60 final
        $this->assertEquals(15.00, (float) $response->json('discount_amount'));
        $this->assertEquals(60.00, (float) $response->json('final_amount'));
    }
}
