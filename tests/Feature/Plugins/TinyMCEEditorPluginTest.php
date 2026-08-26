<?php

namespace Tests\Feature\Plugins;

use App\Helpers\Hooks;
use App\Models\Option;
use App\Models\User;
use App\Services\RichTextEditorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class TinyMCEEditorPluginTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        Hooks::reset();
        $this->seedSiteSettings();
    }

    protected function tearDown(): void
    {
        Hooks::reset();
        parent::tearDown();
    }

    public function test_tinymce_plugin_registers_editor_when_active(): void
    {
        // Activate plugin in DB
        Option::updateOrCreate(
            ['name' => 'tinymce-editor', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        // Include plugin boot file
        require base_path('plugins/myads-tinymce-editor/boot.php');

        $editors = RichTextEditorService::getAvailableEditors();
        $this->assertArrayHasKey('tinymce', $editors);
        $this->assertEquals('TinyMCE 7 (Plugin Editor)', $editors['tinymce']);
    }

    public function test_admin_settings_can_switch_to_tinymce_when_plugin_is_active(): void
    {
        Option::updateOrCreate(
            ['name' => 'tinymce-editor', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        require base_path('plugins/myads-tinymce-editor/boot.php');

        $admin = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post('/admin/settings', [
                'titer' => 'MYADS Test',
                'url' => 'http://localhost',
                'APP_ENV' => 'local',
                'APP_DEBUG' => 'true',
                'APP_TIMEZONE' => 'UTC',
                'APP_LOCALE' => 'ar',
                'SESSION_DRIVER' => 'file',
                'SESSION_LIFETIME' => 120,
                'rich_text_editor' => 'tinymce',
            ]);

        $response->assertRedirect();
        $this->assertEquals('tinymce', RichTextEditorService::getActiveEditor());
    }

    public function test_fallback_to_default_editor_when_tinymce_plugin_is_deactivated(): void
    {
        // Set active editor option to tinymce in DB
        Option::updateOrCreate(
            ['name' => 'rich_text_editor', 'o_type' => 'system_setting'],
            ['o_valuer' => 'tinymce']
        );

        // Deactivate plugin
        Option::updateOrCreate(
            ['name' => 'tinymce-editor', 'o_type' => 'plugins'],
            ['o_valuer' => 0]
        );

        // Do not load boot.php for inactive plugin
        // Since plugin is inactive, tinymce is NOT in available editors
        $editors = RichTextEditorService::getAvailableEditors();
        $this->assertArrayNotHasKey('tinymce', $editors);

        // getActiveEditor must safely fallback to 'quill'
        $active = RichTextEditorService::getActiveEditor();
        $this->assertEquals('quill', $active);
    }

    public function test_tinymce_plugin_action_hooks_render_assets_and_js(): void
    {
        require base_path('plugins/myads-tinymce-editor/boot.php');

        ob_start();
        Hooks::do_action('render_custom_editor_assets', 'tinymce');
        $assetsOutput = ob_get_clean();

        $this->assertStringContainsString('tinymce.min.js', $assetsOutput);

        ob_start();
        Hooks::do_action('render_custom_editor_js', 'editor1', 'tinymce');
        $jsOutput = ob_get_clean();

        $this->assertStringContainsString('tinymce.init', $jsOutput);
        $this->assertStringContainsString('editor1', $jsOutput);
    }
}
