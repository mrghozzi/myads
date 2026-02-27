<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Option;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Services\PluginManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PluginManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $pluginManager;
    protected $testPluginPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pluginManager = new PluginManager();
        $this->testPluginPath = base_path('plugins/TestPlugin');
    }

    protected function tearDown(): void
    {
        if (File::exists($this->testPluginPath)) {
            File::deleteDirectory($this->testPluginPath);
        }
        parent::tearDown();
    }

    public function test_can_scan_plugins()
    {
        // Create a dummy plugin
        File::makeDirectory($this->testPluginPath, 0755, true);
        File::put($this->testPluginPath . '/plugin.json', json_encode([
            'name' => 'Test Plugin',
            'slug' => 'test-plugin',
            'version' => '1.0',
            'description' => 'A test plugin'
        ]));

        $plugins = $this->pluginManager->getAllPlugins();

        $this->assertNotEmpty($plugins);
        $found = false;
        foreach ($plugins as $plugin) {
            if ($plugin['slug'] === 'test-plugin') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function test_can_activate_plugin()
    {
        // Create dummy plugin
        File::makeDirectory($this->testPluginPath, 0755, true);
        File::put($this->testPluginPath . '/plugin.json', json_encode([
            'name' => 'Test Plugin',
            'slug' => 'test-plugin',
            'version' => '1.0'
        ]));

        $result = $this->pluginManager->activate('test-plugin');
        $this->assertTrue($result);

        $this->assertDatabaseHas('option', [
            'name' => 'test-plugin',
            'o_type' => 'plugins',
            'o_valuer' => 1
        ]);
    }

    public function test_can_deactivate_plugin()
    {
        // Setup active plugin
        Option::create([
            'name' => 'test-plugin',
            'o_type' => 'plugins',
            'o_valuer' => 1
        ]);

        $result = $this->pluginManager->deactivate('test-plugin');
        $this->assertTrue($result);

        $this->assertDatabaseHas('option', [
            'name' => 'test-plugin',
            'o_type' => 'plugins',
            'o_valuer' => 0
        ]);
    }
}
