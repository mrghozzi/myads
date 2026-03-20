<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Link;
use App\Models\Setting;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyAdsListDeleteActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_list_pages_render_direct_delete_forms(): void
    {
        $user = $this->seedUserAndTheme();

        $banner = Banner::create([
            'uid' => $user->id,
            'name' => 'Banner Ad',
            'url' => 'https://example.com/banner',
            'img' => 'https://example.com/banner.png',
            'px' => '468x60',
            'statu' => 1,
            'vu' => 0,
            'clik' => 0,
        ]);

        $link = Link::create([
            'uid' => $user->id,
            'name' => 'Text Ad',
            'url' => 'https://example.com/link',
            'txt' => 'Link text',
            'statu' => 1,
            'clik' => 0,
        ]);

        $visit = Visit::create([
            'uid' => $user->id,
            'name' => 'Visit Site',
            'url' => 'https://example.com/visit',
            'tims' => '4',
            'statu' => 1,
            'vu' => 0,
        ]);

        $this->actingAs($user)->get('/b_list')
            ->assertOk()
            ->assertSee(route('ads.banners.destroy', $banner->id), false)
            ->assertSee('name="_method" value="DELETE"', false);

        $this->actingAs($user)->get('/l_list')
            ->assertOk()
            ->assertSee(route('ads.links.destroy', $link->id), false)
            ->assertSee('name="_method" value="DELETE"', false);

        $this->actingAs($user)->get('/v_list')
            ->assertOk()
            ->assertSee(route('visits.destroy', $visit->id), false)
            ->assertSee('name="_method" value="DELETE"', false);
    }

    public function test_delete_routes_remove_items_from_legacy_pages(): void
    {
        $user = $this->seedUserAndTheme();

        $banner = Banner::create([
            'uid' => $user->id,
            'name' => 'Banner Ad',
            'url' => 'https://example.com/banner',
            'img' => 'https://example.com/banner.png',
            'px' => '468x60',
            'statu' => 1,
            'vu' => 0,
            'clik' => 0,
        ]);

        $link = Link::create([
            'uid' => $user->id,
            'name' => 'Text Ad',
            'url' => 'https://example.com/link',
            'txt' => 'Link text',
            'statu' => 1,
            'clik' => 0,
        ]);

        $visit = Visit::create([
            'uid' => $user->id,
            'name' => 'Visit Site',
            'url' => 'https://example.com/visit',
            'tims' => '4',
            'statu' => 1,
            'vu' => 0,
        ]);

        $this->actingAs($user)->delete(route('ads.banners.destroy', $banner->id))
            ->assertRedirect(route('ads.banners.index'));
        $this->assertDatabaseMissing('banner', ['id' => $banner->id]);

        $this->actingAs($user)->delete(route('ads.links.destroy', $link->id))
            ->assertRedirect(route('ads.links.index'));
        $this->assertDatabaseMissing('link', ['id' => $link->id]);

        $this->actingAs($user)->delete(route('visits.destroy', $visit->id))
            ->assertRedirect(route('visits.index'));
        $this->assertDatabaseMissing('visits', ['id' => $visit->id]);
    }

    private function seedUserAndTheme(): User
    {
        Setting::create([
            'titer' => 'MyAds',
            'url' => 'https://example.test',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
            'e_links' => 1,
        ]);

        return User::factory()->create();
    }
}
