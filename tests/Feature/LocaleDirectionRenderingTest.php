<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleDirectionRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_renders_rtl_for_arabic_locale(): void
    {
        $response = $this->get('/?lang=ar');

        $response->assertOk();
        $response->assertSee('<html lang="ar" dir="rtl"', false);
    }

    public function test_public_page_renders_ltr_for_english_locale(): void
    {
        $response = $this->get('/?lang=en');

        $response->assertOk();
        $response->assertSee('<html lang="en" dir="ltr"', false);
    }

    public function test_public_page_renders_rtl_for_persian_locale(): void
    {
        $response = $this->get('/?lang=fa');

        $response->assertOk();
        $response->assertSee('<html lang="fa" dir="rtl"', false);
    }

    public function test_lang_query_keeps_session_and_cookie_behavior(): void
    {
        $response = $this->get('/?lang=ar');

        $response->assertOk();
        $response->assertSessionHas('locale', 'ar');
        $response->assertCookie('lang', 'ar');
    }

    public function test_admin_page_renders_rtl_html_attributes_for_arabic_locale(): void
    {
        $this->createSetting();

        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'admin-user',
            'email' => 'admin@example.com',
        ]);

        $response = $this->actingAs($admin)->get('/admin/settings?lang=ar');

        $response->assertOk();
        $response->assertSee('<html lang="ar" dir="rtl"', false);
        $response->assertDontSee('lang="zxx"', false);
    }

    public function test_installer_page_uses_active_locale_direction(): void
    {
        $response = $this->get('/install?lang=ar');

        $response->assertOk();
        $response->assertSee('<html lang="ar" dir="rtl"', false);
    }

    public function test_visits_page_uses_active_locale_direction(): void
    {
        $user = User::factory()->create([
            'username' => 'visitor-user',
            'email' => 'visitor@example.com',
        ]);

        $response = $this->actingAs($user)->get('/visits/surf?lang=ar');

        $response->assertOk();
        $response->assertSee('<html lang="ar" dir="rtl"', false);
    }

    private function createSetting(): Setting
    {
        return Setting::create([
            'titer' => 'MyAds',
            'url' => 'http://localhost',
        ]);
    }
}
