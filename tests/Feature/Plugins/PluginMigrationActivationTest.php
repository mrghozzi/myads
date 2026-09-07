<?php

namespace Tests\Feature\Plugins;

use App\Models\Option;
use App\Models\User;
use App\Services\PluginManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class PluginMigrationActivationTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected string $testPluginSlug = 'temp-test-plugin-migration';
    protected string $testPluginDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();

        $this->testPluginDir = base_path('plugins/' . $this->testPluginSlug);
        $this->cleanupTestPlugin();

        // Setup test plugin structure
        File::makeDirectory($this->testPluginDir . '/database/migrations', 0755, true);

        // plugin.json
        File::put($this->testPluginDir . '/plugin.json', json_encode([
            'name' => 'Temp Test Plugin',
            'slug' => $this->testPluginSlug,
            'version' => '1.0.0',
            'description' => 'Test plugin for automatic migrations on activation',
            'author' => 'MyAds Test',
        ]));

        // migration file
        $migrationContent = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('temp_plugin_test_table')) {
            Schema::create('temp_plugin_test_table', function (Blueprint $table) {
                $table->id();
                $table->string('item_name');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('temp_plugin_test_table');
    }
};
PHP;
        File::put($this->testPluginDir . '/database/migrations/2026_09_08_000000_create_temp_plugin_test_table.php', $migrationContent);
    }

    protected function tearDown(): void
    {
        $this->cleanupTestPlugin();
        parent::tearDown();
    }

    protected function cleanupTestPlugin(): void
    {
        if (Schema::hasTable('temp_plugin_test_table')) {
            Schema::dropIfExists('temp_plugin_test_table');
        }

        if (File::isDirectory($this->testPluginDir)) {
            File::deleteDirectory($this->testPluginDir);
        }

        Option::where('name', $this->testPluginSlug)->where('o_type', 'plugins')->delete();
    }

    public function test_plugin_activation_automatically_runs_migrations(): void
    {
        $this->assertFalse(Schema::hasTable('temp_plugin_test_table'));

        $pluginManager = app(PluginManager::class);
        $result = $pluginManager->activate($this->testPluginSlug);

        $this->assertTrue($result);
        $this->assertTrue(Schema::hasTable('temp_plugin_test_table'), 'Table temp_plugin_test_table should exist after plugin activation.');

        $option = Option::where('name', $this->testPluginSlug)->where('o_type', 'plugins')->first();
        $this->assertNotNull($option);
        $this->assertEquals('1', (string) $option->o_valuer);

        // Reactivating should also succeed (idempotent)
        $reactivateResult = $pluginManager->activate($this->testPluginSlug);
        $this->assertTrue($reactivateResult);
    }

    public function test_admin_http_endpoint_runs_migrations_on_plugin_activation(): void
    {
        $this->assertFalse(Schema::hasTable('temp_plugin_test_table'));

        $admin = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post('/admin/plugins/activate', [
                'slug' => $this->testPluginSlug,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertTrue(Schema::hasTable('temp_plugin_test_table'), 'Table temp_plugin_test_table should exist after admin activates the plugin via HTTP.');
    }

    public function test_plugin_without_migrations_activates_cleanly(): void
    {
        $slugNoMig = 'temp-test-plugin-no-mig';
        $pluginDir = base_path('plugins/' . $slugNoMig);
        File::makeDirectory($pluginDir, 0755, true);
        File::put($pluginDir . '/plugin.json', json_encode([
            'name' => 'No Mig Plugin',
            'slug' => $slugNoMig,
            'version' => '1.0.0',
        ]));

        try {
            $pluginManager = app(PluginManager::class);
            $result = $pluginManager->activate($slugNoMig);

            $this->assertTrue($result);
            $option = Option::where('name', $slugNoMig)->where('o_type', 'plugins')->first();
            $this->assertNotNull($option);
            $this->assertEquals('1', (string) $option->o_valuer);
        } finally {
            if (File::isDirectory($pluginDir)) {
                File::deleteDirectory($pluginDir);
            }
            Option::where('name', $slugNoMig)->where('o_type', 'plugins')->delete();
        }
    }
}
