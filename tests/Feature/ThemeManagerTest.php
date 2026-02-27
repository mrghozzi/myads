<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Support\Facades\File;
use App\Services\ThemeManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ThemeManagerTest extends TestCase
{
    use RefreshDatabase;

    protected $themeManager;
    protected $testThemePath;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create setting table if not exists (in case migration is missing)
        if (!Schema::hasTable('setting')) {
            Schema::create('setting', function (Blueprint $table) {
                $table->id();
                $table->string('styles')->default('default');
                $table->string('titer')->nullable();
                $table->string('url')->nullable();
                $table->text('description')->nullable();
                $table->string('lang')->nullable();
                $table->string('timezone')->nullable();
                $table->integer('close')->default(0);
                $table->string('close_text')->nullable();
                $table->string('a_mail')->nullable();
                $table->integer('a_not')->default(0);
                $table->integer('e_links')->default(0);
                $table->string('facebook')->nullable();
                $table->string('twitter')->nullable();
                $table->string('linkedin')->nullable();
            });
        }

        $this->themeManager = new ThemeManager();
        $this->testThemePath = base_path('themes/test-theme');
        
        // Ensure Setting exists
        Setting::create(['styles' => 'default']);
    }

    protected function tearDown(): void
    {
        if (File::exists($this->testThemePath)) {
            File::deleteDirectory($this->testThemePath);
        }
        parent::tearDown();
    }

    public function test_can_scan_themes()
    {
        // Create a dummy theme
        File::makeDirectory($this->testThemePath, 0755, true);
        File::put($this->testThemePath . '/theme.json', json_encode([
            'name' => 'Test Theme',
            'slug' => 'test-theme',
            'version' => '1.0',
            'author' => 'Tester',
            'description' => 'A test theme'
        ]));

        $themes = $this->themeManager->getAllThemes();

        $this->assertNotEmpty($themes);
        $found = false;
        foreach ($themes as $theme) {
            if ($theme['slug'] === 'test-theme') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function test_can_activate_theme()
    {
        // Create dummy theme
        File::makeDirectory($this->testThemePath, 0755, true);
        File::put($this->testThemePath . '/theme.json', json_encode([
            'name' => 'Test Theme',
            'slug' => 'test-theme',
            'version' => '1.0'
        ]));

        $result = $this->themeManager->activate('test-theme');
        $this->assertTrue($result);

        $this->assertDatabaseHas('setting', [
            'styles' => 'test-theme'
        ]);
    }
    
    public function test_active_theme_is_marked_correctly()
    {
        // Create dummy theme
        File::makeDirectory($this->testThemePath, 0755, true);
        File::put($this->testThemePath . '/theme.json', json_encode([
            'name' => 'Test Theme',
            'slug' => 'test-theme',
            'version' => '1.0'
        ]));
        
        // Activate it
        $this->themeManager->activate('test-theme');
        
        $themes = $this->themeManager->getAllThemes();
        
        foreach ($themes as $theme) {
            if ($theme['slug'] === 'test-theme') {
                $this->assertTrue($theme['is_active']);
            } else {
                $this->assertFalse($theme['is_active']);
            }
        }
    }
}
