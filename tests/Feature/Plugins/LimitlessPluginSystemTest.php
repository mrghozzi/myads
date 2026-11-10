<?php

namespace Tests\Feature\Plugins;

use App\Helpers\Hooks;
use App\Providers\PluginServiceProvider;
use App\Services\PluginManager;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class LimitlessPluginSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Hooks::reset();
    }

    protected function tearDown(): void
    {
        Hooks::reset();
        parent::tearDown();
    }

    public function test_global_hook_functions_operate_seamlessly(): void
    {
        $executed = false;
        $capturedArg = null;

        // Test add_action & do_action
        add_action('test_custom_action', function ($arg) use (&$executed, &$capturedArg) {
            $executed = true;
            $capturedArg = $arg;
        }, 10, 1);

        $this->assertTrue(has_action('test_custom_action'));
        $this->assertFalse(has_action('non_existent_action'));

        do_action('test_custom_action', 'hello_world');

        $this->assertTrue($executed);
        $this->assertSame('hello_world', $capturedArg);

        // Test add_filter & apply_filters
        add_filter('test_custom_filter', function ($val, $prefix) {
            return $prefix . '_' . strtoupper($val);
        }, 10, 2);

        $this->assertTrue(has_filter('test_custom_filter'));
        $this->assertFalse(has_filter('non_existent_filter'));

        $filtered = apply_filters('test_custom_filter', 'myads', 'super');
        $this->assertSame('super_MYADS', $filtered);

        // Test remove_action & remove_filter
        $actionCb = function () {};
        add_action('removable_action', $actionCb);
        $this->assertTrue(has_action('removable_action'));
        $this->assertTrue(remove_action('removable_action', $actionCb));

        $filterCb = function ($v) { return $v; };
        add_filter('removable_filter', $filterCb);
        $this->assertTrue(has_filter('removable_filter'));
        $this->assertTrue(remove_filter('removable_filter', $filterCb));
    }

    public function test_blade_directives_render_actions_and_filters(): void
    {
        add_action('blade_test_action', function ($text) {
            echo "ACTION_OUTPUT_{$text}";
        });

        add_filter('blade_test_filter', function ($val) {
            return "[FILTERED:{$val}]";
        });

        $renderedAction = Blade::render("@action('blade_test_action', 'ROCKET')");
        $this->assertStringContainsString('ACTION_OUTPUT_ROCKET', $renderedAction);

        $renderedFilter = Blade::render("@filter('blade_test_filter', 'CONTENT')");
        $this->assertStringContainsString('[FILTERED:CONTENT]', $renderedFilter);
    }

    public function test_plugin_manager_validates_syntax_cleanly(): void
    {
        $manager = app(PluginManager::class);

        // Test valid syntax on an existing plugin
        $validPluginDir = base_path('plugins/monetization');
        if (File::isDirectory($validPluginDir)) {
            $errors = $manager->validatePluginSyntax($validPluginDir);
            $this->assertEmpty($errors);
        }

        // Test temporary plugin with a deliberate syntax error
        $tempPluginDir = storage_path('app/temp_test_plugin_syntax');
        File::makeDirectory($tempPluginDir, 0755, true, true);
        File::put($tempPluginDir . '/boot.php', '<?php this is invalid syntax [[;');

        $errors = $manager->validatePluginSyntax($tempPluginDir);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Line', $errors[0]);

        File::deleteDirectory($tempPluginDir);
    }

    public function test_dynamic_view_overrides_mechanism(): void
    {
        // Create mock plugin override view
        $tempOverrideDir = storage_path('app/temp_test_overrides');
        $themeOverrideDir = $tempOverrideDir . '/theme/custom_test_view';
        File::makeDirectory($themeOverrideDir, 0755, true, true);
        File::put($themeOverrideDir . '/sample.blade.php', '<h1>OVERRIDDEN BY PLUGIN</h1>');

        View::prependNamespace('theme_test', $themeOverrideDir);
        $rendered = View::make('theme_test::sample')->render();

        $this->assertSame('<h1>OVERRIDDEN BY PLUGIN</h1>', trim($rendered));

        File::deleteDirectory($tempOverrideDir);
    }
}

