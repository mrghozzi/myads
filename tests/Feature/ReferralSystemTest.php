<?php

namespace Tests\Feature;

use App\Models\Badge;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class ReferralSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::create([
            'titer' => 'MYADS Network',
            'desk' => 'Ads & Community Platform',
            'mots' => 'ads, community',
            'face' => 'myads',
            'theme' => 'default',
        ]);

        // Seed referral badges if not existing
        $referralBadges = [
            [
                'slug' => 'referral_rookie',
                'name_key' => 'badge_referral_rookie_name',
                'description_key' => 'badge_referral_rookie_desc',
                'icon' => 'fa-user-plus',
                'color' => 'blue',
                'points_reward' => 50,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 1,
                'sort_order' => 34,
                'is_active' => true,
            ],
            [
                'slug' => 'network_builder',
                'name_key' => 'badge_network_builder_name',
                'description_key' => 'badge_network_builder_desc',
                'icon' => 'fa-share-nodes',
                'color' => 'teal',
                'points_reward' => 150,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 5,
                'sort_order' => 35,
                'is_active' => true,
            ],
            [
                'slug' => 'super_promoter',
                'name_key' => 'badge_super_promoter_name',
                'description_key' => 'badge_super_promoter_desc',
                'icon' => 'fa-bullhorn',
                'color' => 'purple',
                'points_reward' => 500,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 20,
                'sort_order' => 36,
                'is_active' => true,
            ],
            [
                'slug' => 'referral_legend',
                'name_key' => 'badge_referral_legend_name',
                'description_key' => 'badge_referral_legend_desc',
                'icon' => 'fa-crown',
                'color' => 'gold',
                'points_reward' => 1500,
                'criteria_type' => 'referrals_count',
                'criteria_target' => 50,
                'sort_order' => 37,
                'is_active' => true,
            ],
        ];

        foreach ($referralBadges as $b) {
            Badge::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }

    public function test_authenticated_user_can_access_referral_banners_and_codes_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('ads.referrals'));

        $response->assertStatus(200);
        $response->assertSeeText(__('messages.referral_dashboard'));
        $response->assertSeeText(__('messages.your_referral_link'));
        $response->assertSee(url('/') . '?ref=' . $user->id);
        $response->assertSeeText(__('messages.referral_badges_title'));
    }

    public function test_authenticated_user_can_access_referrals_list_page(): void
    {
        $referrer = User::factory()->create();
        $referred = User::factory()->create();

        Referral::create([
            'uid' => $referrer->id,
            'ruid' => $referred->id,
            'date' => date('Y-m-d'),
        ]);

        $response = $this->actingAs($referrer)->get(route('legacy.referral'));

        $response->assertStatus(200);
        $response->assertSeeText(__('messages.my_referrals_list'));
        $response->assertSeeText($referred->username);
        $response->assertSeeText(__('messages.referral_badges_title'));
    }

    public function test_home_dashboard_includes_superdesign_referral_hub_widget(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSeeText(__('messages.referral_hub_title'));
        $response->assertSee(url('/') . '?ref=' . $user->id);
    }

    public function test_visiting_page_with_ref_query_sets_referral_cookie(): void
    {
        $referrer = User::factory()->create(['username' => 'promoter100']);

        $response = $this->get('/?ref=' . $referrer->username);

        $response->assertStatus(200);
        $response->assertCookie('ref', 'promoter100');
    }

    public function test_registration_with_referral_cookie_rewards_both_parties_and_unlocks_badge(): void
    {
        $referrer = User::factory()->create([
            'username' => 'leader1',
            'pts' => 50,
        ]);

        $captchaVal = 'test123';
        session(['captcha_result' => $captchaVal]);

        $response = $this->withCookie('ref', $referrer->username)
            ->withSession(['captcha_result' => $captchaVal])
            ->post('/register', [
                'username' => 'newuser77',
                'email' => 'newuser77@example.com',
                'password' => 'Secret123!',
                'password_confirmation' => 'Secret123!',
                'agree_terms' => '1',
                'capt' => $captchaVal,
            ]);

        $response->assertRedirect(route('dashboard'));

        $newUser = User::where('username', 'newuser77')->first();
        $this->assertNotNull($newUser);

        // 1. Referral table has entry
        $referralRecord = Referral::where('uid', $referrer->id)->where('ruid', $newUser->id)->first();
        $this->assertNotNull($referralRecord);

        // 2. Referrer got points (+10 referral bonus + 50 badge unlock reward = 110 PTS)
        $referrer->refresh();
        $this->assertEquals(110, $referrer->pts);

        // 3. New user got 10 default points + 10 welcome referral bonus = 20 PTS!
        $this->assertEquals(20, $newUser->pts);

        // 4. Referral Rookie badge unlocked for referrer
        $rookieBadge = Badge::where('slug', 'referral_rookie')->first();
        $userBadge = UserBadge::where('user_id', $referrer->id)->where('badge_id', $rookieBadge->id)->first();
        $this->assertNotNull($userBadge);
        $this->assertNotNull($userBadge->unlocked_at);
        $this->assertEquals(1, $userBadge->progress);
    }

    public function test_self_referral_does_not_create_referral_record(): void
    {
        $existing = User::factory()->create([
            'username' => 'myself',
            'pts' => 50,
        ]);

        $captchaVal = 'test456';

        // Attempting to register using own username
        $response = $this->withCookie('ref', 'myself')
            ->withSession(['captcha_result' => $captchaVal])
            ->post('/register', [
                'username' => 'myself2',
                'email' => 'myself2@example.com',
                'password' => 'Secret123!',
                'password_confirmation' => 'Secret123!',
                'agree_terms' => '1',
                'capt' => $captchaVal,
            ]);

        $response->assertRedirect(route('dashboard'));

        $newUser = User::where('username', 'myself2')->first();
        $this->assertNotNull($newUser);

        // Should NOT create referral record for self-referral if same user id (or user referrals)
        $this->assertDatabaseMissing('referral', [
            'ruid' => $newUser->id,
            'uid' => $newUser->id,
        ]);
    }

    public function test_all_badges_page_lists_referral_badges(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('badges.all'));

        $response->assertStatus(200);
        $response->assertSeeText(__('messages.badge_referral_rookie_name'));
        $response->assertSeeText(__('messages.badge_network_builder_name'));
        $response->assertSeeText(__('messages.badge_super_promoter_name'));
        $response->assertSeeText(__('messages.badge_referral_legend_name'));
    }
}
