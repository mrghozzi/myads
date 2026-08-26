<?php

namespace Tests\Feature;

use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    }

    public function test_authenticated_user_can_access_referral_banners_and_codes_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('ads.referrals'));

        $response->assertStatus(200);
        $response->assertSeeText(__('messages.referral_dashboard'));
        $response->assertSeeText(__('messages.your_referral_link'));
        $response->assertSee(url('/') . '?ref=' . $user->id);
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
    }

    public function test_home_dashboard_includes_superdesign_referral_hub_widget(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSeeText(__('messages.referral_hub_title'));
        $response->assertSee(url('/') . '?ref=' . $user->id);
    }
}
