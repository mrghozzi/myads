<?php

namespace Tests\Feature\Themes;

use App\Helpers\Hooks;
use App\Models\BillingCoupon;
use App\Models\BillingOrder;
use App\Models\MemberSubscription;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Providers\ThemeServiceProvider;
use App\Support\SubscriptionGatewaySettings;
use App\Support\SubscriptionSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class BootstrapSampleBillingTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected User $user;
    protected SubscriptionPlan $proPlan;
    protected SubscriptionPlan $trialPlan;

    protected function setUp(): void
    {
        parent::setUp();

        Hooks::reset();
        $this->seedSiteSettings(['url' => 'http://localhost']);

        SubscriptionSettings::clearCache();
        SubscriptionSettings::save([
            'enabled' => 1,
            'base_currency_code' => 'USD',
        ]);
        SubscriptionGatewaySettings::save('bank_transfer', [
            'enabled' => 1,
        ]);

        // Activate bootstrap-sample theme
        Setting::query()->update(['styles' => 'bootstrap-sample']);
        ThemeServiceProvider::applyTheme('bootstrap-sample');

        $this->user = User::factory()->create([
            'username' => 'bs_sample_user',
            'email' => 'bs_sample@example.com',
            'pts' => 500,
        ]);

        $this->proPlan = SubscriptionPlan::create([
            'name' => 'Pro Webmaster VIP',
            'description' => 'Unlimited advertising and priority exposure',
            'duration_days' => 365,
            'is_lifetime' => false,
            'base_price' => 120.00,
            'sale_price' => 84.00,
            'sale_ends_at' => now()->addDays(5),
            'trial_days' => 0,
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
            'accent_color' => '#10b981',
            'layout_style' => 'neon_glow',
            'recommended_text' => 'MOST POPULAR',
            'marketing_bullets' => ['0% Store Commission', 'VIP Badge on Profile'],
            'entitlements' => [
                'profile_badge_label' => 'VIP PRO',
                'profile_badge_color' => '#10b981',
                'subscription_verified_badge' => true,
                'ad_free' => true,
                'bonus_pts' => 1000,
            ],
        ]);

        $this->trialPlan = SubscriptionPlan::create([
            'name' => 'Starter Trial Boost',
            'description' => 'Try all features free for 7 days',
            'duration_days' => 30,
            'is_lifetime' => false,
            'base_price' => 25.00,
            'trial_days' => 7,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 2,
            'accent_color' => '#6366f1',
            'layout_style' => 'glassmorphism',
            'marketing_bullets' => ['7-Day Free Trial', 'No credit card needed'],
            'entitlements' => [
                'profile_badge_label' => 'STARTER',
                'profile_badge_color' => '#6366f1',
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Hooks::reset();
        parent::tearDown();
    }

    public function test_bootstrap_sample_plans_view_renders_pricing_cards_and_features(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/plans?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSeeText('Pro Webmaster VIP')
            ->assertSeeText('Starter Trial Boost')
            ->assertSeeText('MOST POPULAR')
            ->assertSee('84.00')
            ->assertSee('120.00')
            ->assertSeeText('7-Day Free Trial');

        // Check for interactive elements
        $response->assertSee('coupon-code-input')
            ->assertSee('feature-comparison-matrix')
            ->assertSee('btn-claim-trial');
    }

    public function test_bootstrap_sample_landing_plans_renders_on_welcome_page(): void
    {
        $response = $this->withSession(['locale' => 'en'])
            ->get('/?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSee('subscription-landing-showcase')
            ->assertSeeText('Pro Webmaster VIP')
            ->assertSee('84.00')
            ->assertSeeText('Starter Trial Boost');
    }

    public function test_bootstrap_sample_dashboard_renders_expiration_warning_and_trial_badge(): void
    {
        // Create an expiring trial subscription (expires in 3 days)
        MemberSubscription::create([
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->trialPlan->id,
            'status' => MemberSubscription::STATUS_ACTIVE,
            'is_trial' => true,
            'plan_name' => $this->trialPlan->name,
            'starts_at' => now()->subDays(4),
            'ends_at' => now()->addDays(3),
            'trial_ends_at' => now()->addDays(3),
            'activated_at' => now()->subDays(4),
            'entitlements_snapshot' => $this->trialPlan->entitlements,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/billing?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSeeText('Starter Trial Boost')
            ->assertSee('badge-sub-trial')
            ->assertSee('billing-expiration-warning-banner');
    }

    public function test_bootstrap_sample_order_view_renders_printable_invoice(): void
    {
        $order = BillingOrder::create([
            'order_number' => 'ORD-TEST-9988',
            'user_id' => $this->user->id,
            'subscription_plan_id' => $this->proPlan->id,
            'gateway' => 'bank_transfer',
            'status' => BillingOrder::STATUS_PAID,
            'base_amount' => 100.00,
            'display_amount' => 100.00,
            'discount_amount' => 20.00,
            'tax_amount' => 0.00,
            'total_amount' => 100.00,
            'currency_code' => 'USD',
            'base_currency_code' => 'USD',
            'paid_at' => now(),
            'notes' => 'Test order invoice',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/billing/orders/' . $order->id . '?preview_theme=bootstrap-sample');

        $response->assertOk()
            ->assertSee('printable-invoice-card')
            ->assertSee('btn-print-invoice')
            ->assertSee('INV-ORD-TEST-9988')
            ->assertSee('window.print()')
            ->assertSee('100.00');
    }

    public function test_bootstrap_sample_developer_hooks_execute_on_plans_and_landing(): void
    {
        add_filter('billing_plans_card_class', function ($classes, $plan) {
            return $classes . ' custom-plugin-card-glow';
        });

        add_action('billing_plans_card_badge', function ($plan) {
            echo '<span class="badge bg-danger custom-hook-badge">EXTRA_PLUGIN_BADGE</span>';
        });

        add_action('billing_landing_plans_card_badge', function ($plan) {
            echo '<span class="badge bg-info custom-landing-hook-badge">LANDING_HOOK_BADGE</span>';
        });

        $plansResponse = $this->actingAs($this->user)
            ->withSession(['locale' => 'en'])
            ->get('/plans?preview_theme=bootstrap-sample');

        $plansResponse->assertOk()
            ->assertSee('custom-plugin-card-glow')
            ->assertSee('EXTRA_PLUGIN_BADGE');

        $welcomeResponse = $this->withSession(['locale' => 'en'])
            ->get('/?preview_theme=bootstrap-sample');

        $welcomeResponse->assertOk()
            ->assertSee('LANDING_HOOK_BADGE');
    }
}
