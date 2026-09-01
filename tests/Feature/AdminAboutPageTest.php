<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Product;
use App\Models\SiteAdmin;
use App\Models\Status;
use App\Models\User;
use App\Support\SystemVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AdminAboutPageTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_admin_about_page_renders_successfully_for_super_admin(): void
    {
        $this->seedSiteSettings();
        $admin = $this->createSuperAdmin();

        User::factory()->count(3)->create();
        Status::create([
            'uid' => $admin->id,
            's_type' => 100,
            'time' => time(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.about'));

        $response->assertOk()
            ->assertViewIs('admin::about.index')
            ->assertSee('v' . SystemVersion::CURRENT)
            ->assertSee(__('about.tab_whats_new'))
            ->assertSee(__('about.tab_changelog'))
            ->assertSee(__('about.tab_about'))
            ->assertSee(__('about.feature_1_title'))
            ->assertSee(__('about.feature_2_title'))
            ->assertSee(__('about.feature_3_title'))
            ->assertSee(__('about.feature_4_title'))
            ->assertSee(__('about.feature_5_title'))
            ->assertSee(__('about.feature_6_title'))
            ->assertSee('Superdesign Code Windows & Mermaid Diagram Engine')
            ->assertSee('Reverse Proxy & Cloudflare Protocol Trust')
            ->assertSee('Developer Platform Route & JSON Architecture');

        $this->assertDatabaseHas('options', [
            'name' => 'last_seen_about_version',
            'o_valuer' => SystemVersion::CURRENT,
        ]);
    }

    public function test_admin_about_page_renders_in_arabic_locale(): void
    {
        $this->seedSiteSettings();
        $admin = $this->createSuperAdmin();

        app()->setLocale('ar');

        $response = $this->actingAs($admin)->get(route('admin.about', ['lang' => 'ar']));

        $response->assertOk()
            ->assertSee('نوافذ الأكواد Superdesign ومحرك الرسوم التفاعلية Mermaid')
            ->assertSee('توافق البروكسي العكسي ووثوقية بروتوكول Cloudflare')
            ->assertSee('بنية مسارات المطورين والبيانات المعيارية JSON');
    }

    public function test_non_admin_user_cannot_access_about_page(): void
    {
        $this->seedSiteSettings();
        $this->createSuperAdmin(); // ID 1 is super admin

        $regularUser = User::factory()->create([
            'id' => 999,
            'username' => 'regular_member',
        ]);

        $response = $this->actingAs($regularUser)->get(route('admin.about'));

        $response->assertRedirect('/');
    }

    private function createSuperAdmin(): User
    {
        $user = User::factory()->create([
            'id' => 1,
            'username' => 'rootsystem',
            'email' => 'root@example.test',
        ]);

        SiteAdmin::create([
            'user_id' => $user->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);

        return $user;
    }
}
