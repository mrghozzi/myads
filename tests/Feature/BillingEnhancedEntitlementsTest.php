<?php

namespace Tests\Feature;

use App\Models\BillingOrder;
use App\Models\MemberSubscription;
use App\Models\SiteAdmin;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\Visit;
use App\Models\Option;
use App\Services\Billing\SubscriptionEntitlementService;
use App\Support\SubscriptionSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class BillingEnhancedEntitlementsTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_entitlement_service_methods_and_benefit_lines(): void
    {
        $this->seedSiteSettings();

        $user = User::factory()->create();

        $service = app(SubscriptionEntitlementService::class);

        // Initially no active subscription
        $this->assertFalse($service->hasAdFreeForUser($user));
        $this->assertEquals(1.0, $service->activePtsMultiplierForUserId($user->id));
        $this->assertFalse($service->hasExchangePriorityForUserId($user->id));
        $this->assertFalse($service->hasFeaturedDirectoryBadgeForUserId($user->id));
        $this->assertEquals(25, $service->maxUploadSizeMbForUserId($user->id));
        $this->assertEquals(20.0, $service->storeCommissionPctForUserId($user->id));

        // Create an active subscription with new VIP entitlements
        $sub = MemberSubscription::create([
            'user_id' => $user->id,
            'status' => 'active',
            'plan_name' => 'VIP Pro',
            'entitlements_snapshot' => [
                'ad_free' => true,
                'pts_multiplier' => 2.0,
                'exchange_priority' => true,
                'featured_directory_badge' => true,
                'max_upload_size_mb' => 100,
                'store_commission_pct' => 5.0,
                'profile_badge_label' => 'VIP Elite',
                'profile_badge_color' => '#f59e0b',
                'subscription_verified_badge' => true,
            ],
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
            'activated_at' => now()->subDay(),
        ]);

        $this->assertTrue($service->hasAdFreeForUser($user));
        $this->assertEquals(2.0, $service->activePtsMultiplierForUserId($user->id));
        $this->assertTrue($service->hasExchangePriorityForUserId($user->id));
        $this->assertTrue($service->hasFeaturedDirectoryBadgeForUserId($user->id));
        $this->assertEquals(100, $service->maxUploadSizeMbForUserId($user->id));
        $this->assertEquals(5.0, $service->storeCommissionPctForUserId($user->id));

        $benefitLines = $service->memberBenefitLines($sub->entitlements_snapshot);
        $this->assertContains(__('messages.billing_ad_free_benefit'), $benefitLines);
        $this->assertContains(__('messages.billing_exchange_priority_benefit'), $benefitLines);
        $this->assertContains(__('messages.billing_featured_directory_benefit'), $benefitLines);
    }

    public function test_ads_site_helper_suppresses_ads_for_ad_free_subscribers(): void
    {
        $this->seedSiteSettings();

        // Ensure ads table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('ads')) {
            DB::table('ads')->updateOrInsert(['id' => 999], [
                'code_ads' => '<div class="test-banner-ad">Banner Ad</div>',
            ]);
        }

        $normalUser = User::factory()->create();
        $this->actingAs($normalUser);

        // For normal user, ads_site returns the ad (or empty if ads table not populated)
        $outputNormal = ads_site(999);

        // For VIP user with ad_free = true
        $vipUser = User::factory()->create();
        MemberSubscription::create([
            'user_id' => $vipUser->id,
            'status' => 'active',
            'plan_name' => 'Ad-Free VIP',
            'entitlements_snapshot' => [
                'ad_free' => true,
            ],
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->actingAs($vipUser);
        $outputVip = ads_site(999);
        $this->assertEquals('', $outputVip);
    }

    public function test_pts_multiplier_applies_to_visit_exchange_verification(): void
    {
        $this->seedSiteSettings();

        $owner = User::factory()->create(['vu' => 50]);
        $site = Visit::create([
            'uid' => $owner->id,
            'name' => 'Test Site',
            'url' => 'https://example.com',
            'tims' => 1,
            'statu' => 1,
            'vu' => 0,
        ]);

        $vipViewer = User::factory()->create(['pts' => 10, 'vu' => 5]);
        MemberSubscription::create([
            'user_id' => $vipViewer->id,
            'status' => 'active',
            'plan_name' => '2x Booster',
            'entitlements_snapshot' => [
                'pts_multiplier' => 2.0,
            ],
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
        ]);

        $this->actingAs($vipViewer);

        $tokenPayload = [
            'site_id' => $site->id,
            'user_id' => $vipViewer->id,
            'duration' => 10,
            'cost' => 1,
            'timestamp' => now()->timestamp - 15,
            'challenge' => 100,
        ];
        $token = Crypt::encryptString(json_encode($tokenPayload));

        $response = $this->postJson(route('visits.verify'), [
            'token' => $token,
            'challenge_answer' => (100 * 7) + 3,
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        // Base reward with 2.0x multiplier
        $baseReward = \App\Support\VisitExchangeSettings::pointsReward();
        $expectedPts = 10 + round($baseReward * 2.0, 2);
        $vipViewer->refresh();
        $this->assertEquals($expectedPts, (float) $vipViewer->pts);
    }

    public function test_admin_can_store_plan_with_enhanced_entitlements_and_member_sees_matrix(): void
    {
        $this->seedSiteSettings();

        SubscriptionSettings::save([
            'enabled' => 1,
            'base_currency_code' => 'USD',
        ]);

        $adminUser = User::factory()->create();
        SiteAdmin::create([
            'user_id' => $adminUser->id,
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($adminUser);

        $response = $this->post(route('admin.billing.plans.store'), [
            'name' => 'Ultimate VIP Plan',
            'description' => 'Best value plan with all perks',
            'base_price' => 49.99,
            'duration_days' => 60,
            'is_featured' => 1,
            'is_active' => 1,
            'ad_free' => 1,
            'pts_multiplier' => 2.5,
            'store_commission_pct' => 0.0,
            'exchange_priority' => 1,
            'featured_directory_badge' => 1,
            'max_upload_size_mb' => 200,
            'profile_badge_label' => 'VIP Boss',
            'profile_badge_color' => '#ec4899',
            'subscription_verified_badge' => 1,
            'bonus_pts' => 1000,
        ]);

        $response->assertRedirect(route('admin.billing.plans'));

        $storedPlan = SubscriptionPlan::where('name', 'Ultimate VIP Plan')->firstOrFail();
        $this->assertEquals(2.5, (float) data_get($storedPlan->entitlements, 'pts_multiplier'));
        $this->assertEquals(0.0, (float) data_get($storedPlan->entitlements, 'store_commission_pct'));
        $this->assertTrue((bool) data_get($storedPlan->entitlements, 'ad_free'));
        $this->assertTrue((bool) data_get($storedPlan->entitlements, 'exchange_priority'));
        $this->assertEquals(200, (int) data_get($storedPlan->entitlements, 'max_upload_size_mb'));

        // Member visiting /plans should see the plan and comparison matrix
        $member = User::factory()->create();
        $this->actingAs($member);

        $plansPage = $this->get(route('billing.plans'));
        $plansPage->assertOk()
            ->assertSee('Ultimate VIP Plan')
            ->assertSee(__('messages.billing_comparison_title'))
            ->assertSee('100% Ad-Free')
            ->assertSee('2.5x Boost')
            ->assertSee('Priority Surge');
    }

    public function test_admin_overview_calculates_saas_metrics(): void
    {
        $this->seedSiteSettings();

        SubscriptionSettings::save([
            'enabled' => 1,
            'base_currency_code' => 'USD',
        ]);

        $adminUser = User::factory()->create();
        SiteAdmin::create([
            'user_id' => $adminUser->id,
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($adminUser);

        $overviewResponse = $this->get(route('admin.billing.overview'));
        $overviewResponse->assertOk()
            ->assertSee(__('messages.billing_summary_estimated_arr'))
            ->assertSee(__('messages.billing_summary_arpu'));
    }
}
