<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComposerRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_composer_renders_on_portal_and_owner_profile_only(): void
    {
        $this->createSetting();

        $owner = User::factory()->create([
            'username' => 'composerowner',
            'email' => 'composerowner@example.com',
        ]);

        $viewer = User::factory()->create([
            'username' => 'composerviewer',
            'email' => 'composerviewer@example.com',
        ]);

        $portalResponse = $this->actingAs($owner)->get(route('portal.index'));

        $portalResponse->assertOk()
            ->assertSee('id="social-composer"', false)
            ->assertSee('id="composer-mode-text"', false)
            ->assertSee('id="composer-mode-gallery"', false)
            ->assertSee('id="composer-mode-link"', false)
            ->assertSee('id="composer-gallery-clear"', false)
            ->assertSee('composer-refresh__tool-label', false);

        $ownerProfileResponse = $this->actingAs($owner)->get(route('profile.show', $owner->username));

        $ownerProfileResponse->assertOk()
            ->assertSee('id="social-composer"', false)
            ->assertSee('id="composer-submit"', false)
            ->assertSee('composer-refresh__identity-name', false);

        $viewerProfileResponse = $this->actingAs($viewer)->get(route('profile.show', $owner->username));

        $viewerProfileResponse->assertOk()
            ->assertDontSee('id="social-composer"', false);
    }

    private function createSetting(): Setting
    {
        return Setting::create([
            'titer' => 'MyAds',
            'url' => 'http://localhost',
            'styles' => 'default',
            'lang' => 'en',
            'timezone' => 'UTC',
        ]);
    }
}
