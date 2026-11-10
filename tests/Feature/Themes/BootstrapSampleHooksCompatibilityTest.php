<?php

namespace Tests\Feature\Themes;

use App\Helpers\Hooks;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class BootstrapSampleHooksCompatibilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Hooks::reset();
        View::addNamespace('bs_theme', base_path('themes/bootstrap-sample/views'));
    }

    protected function tearDown(): void
    {
        Hooks::reset();
        parent::tearDown();
    }

    public function test_bootstrap_sample_activity_card_renders_all_plugin_hooks(): void
    {
        // Mock hooks
        add_action('post_card_header', function ($activity) {
            echo '<div id="hook-card-header-' . $activity->id . '">CARD_HEADER_HOOK</div>';
        });

        add_action('post_card_footer', function ($activity) {
            echo '<div id="hook-card-footer-' . $activity->id . '">CARD_FOOTER_HOOK</div>';
        });

        add_action('post_card_actions', function ($activity) {
            echo '<span id="hook-card-actions-' . $activity->id . '">CARD_ACTIONS_HOOK</span>';
        });

        add_action('post_card_options', function ($activity) {
            echo '<li id="hook-card-options-' . $activity->id . '">CARD_OPTIONS_HOOK</li>';
        });

        $user = new User([
            'id' => 9991,
            'username' => 'sample_user',
            'public_uid' => 'pub_sample_user',
            'pts' => 500,
        ]);

        $status = new Status([
            'id' => 8881,
            'uid' => 9991,
            's_type' => 100,
            'tp_id' => 8881,
            'date' => time(),
        ]);
        $status->setRelation('user', $user);

        $html = View::make('bs_theme::partials.activity.wrapper', ['activity' => $status])->render();

        $this->assertStringContainsString('CARD_HEADER_HOOK', $html);
        $this->assertStringContainsString('CARD_FOOTER_HOOK', $html);
        $this->assertStringContainsString('CARD_ACTIONS_HOOK', $html);

        $sharedFooter = View::make('bs_theme::partials.activity.post_footer_shared', ['activity' => $status])->render();
        $this->assertStringContainsString('CARD_ACTIONS_HOOK', $sharedFooter);
        $this->assertStringContainsString('CARD_OPTIONS_HOOK', $sharedFooter);
    }

    public function test_bootstrap_sample_profile_navigation_and_content_hooks(): void
    {
        add_action('user_profile_navigation_tabs', function ($user, $tab) {
            echo '<li class="nav-item"><a class="nav-link">CUSTOM_PLUGIN_TAB</a></li>';
        });

        $user = new User([
            'id' => 9992,
            'username' => 'profile_user',
            'public_uid' => 'pub_profile_user',
            'pts' => 1500,
        ]);

        $navHtml = View::make('bs_theme::profile.navigation', ['user' => $user])->render();
        $this->assertStringContainsString('CUSTOM_PLUGIN_TAB', $navHtml);
    }

    public function test_bootstrap_sample_sidemenu_navigation_hooks(): void
    {
        add_action('theme_sidebar_menu_start', function () {
            echo '<li class="nav-item"><a class="nav-link">SIDEBAR_START_HOOK</a></li>';
        });

        add_action('theme_sidebar_menu_end', function () {
            echo '<li class="nav-item"><a class="nav-link">SIDEBAR_END_HOOK</a></li>';
        });

        $sideHtml = View::make('bs_theme::partials.header.sidemenu')->render();
        $this->assertStringContainsString('SIDEBAR_START_HOOK', $sideHtml);
        $this->assertStringContainsString('SIDEBAR_END_HOOK', $sideHtml);
    }
}

