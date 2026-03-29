<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\TestingSafetyGuard;
use App\Services\UpdateSafetyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class UpdateSafetyFeatureTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_testing_environment_uses_isolated_sqlite_database(): void
    {
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(database_path('testing.sqlite'), config('database.connections.sqlite.database'));
    }

    public function test_testing_database_guard_rejects_non_isolated_connection(): void
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.driver', 'mysql');
        config()->set('database.connections.mysql.database', 'myads2');

        try {
            app(TestingSafetyGuard::class)->ensureIsolated();
            $this->fail('Expected the testing safety guard to reject a non-isolated database connection.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('myads2', $exception->getMessage());
        } finally {
            config()->set('database.default', 'sqlite');
        }
    }

    public function test_update_safety_service_accepts_safe_pending_migrations(): void
    {
        $path = database_path('migrations/2099_01_01_000100_safe_preflight_probe.php');

        File::put($path, <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preflight_safe_probe', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preflight_safe_probe');
    }
};
PHP);

        try {
            $report = app(UpdateSafetyService::class)->run();

            $this->assertTrue($report->isSafe());
            $this->assertContains('2099_01_01_000100_safe_preflight_probe', $report->pendingMigrations);
            $this->assertSame([], $report->destructiveMigrations);
        } finally {
            File::delete($path);
        }
    }

    public function test_update_safety_service_blocks_destructive_pending_migrations(): void
    {
        $path = database_path('migrations/2099_01_01_000200_destructive_preflight_probe.php');

        File::put($path, <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('users');
    }

    public function down(): void
    {
    }
};
PHP);

        try {
            $report = app(UpdateSafetyService::class)->run();

            $this->assertFalse($report->isSafe());
            $this->assertCount(1, $report->destructiveMigrations);
            $this->assertSame('2099_01_01_000200_destructive_preflight_probe', $report->destructiveMigrations[0]['migration']);
        } finally {
            File::delete($path);
        }
    }

    public function test_update_preflight_command_returns_failure_for_destructive_migration(): void
    {
        $path = database_path('migrations/2099_01_01_000300_command_destructive_probe.php');

        File::put($path, <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DELETE FROM users');
    }

    public function down(): void
    {
    }
};
PHP);

        try {
            $exitCode = Artisan::call('myads:update:preflight');

            $this->assertSame(1, $exitCode);
        } finally {
            File::delete($path);
        }
    }

    public function test_admin_update_requires_backup_acknowledgements(): void
    {
        $this->seedSiteSettings();

        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'rootupdater',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.updates.process'));

        $response->assertSessionHasErrors([
            'backup_ack_database',
            'backup_ack_files',
        ]);
    }

    public function test_admin_update_blocks_destructive_release_before_copying_files(): void
    {
        $this->seedSiteSettings();

        $admin = User::factory()->create([
            'id' => 1,
            'username' => 'rootrelease',
        ]);

        $zipPath = storage_path('app/destructive-release.zip');
        $releaseRoot = storage_path('app/destructive-release-root');
        File::delete($zipPath);
        File::deleteDirectory($releaseRoot);
        File::makeDirectory($releaseRoot . '/database/migrations', 0755, true);

        File::put($releaseRoot . '/database/migrations/2099_01_01_000400_release_destructive_probe.php', <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('users');
    }

    public function down(): void
    {
    }
};
PHP);

        $zip = new \ZipArchive;
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFile(
            $releaseRoot . '/database/migrations/2099_01_01_000400_release_destructive_probe.php',
            'myads-release/database/migrations/2099_01_01_000400_release_destructive_probe.php'
        );
        $zip->close();

        Http::fake([
            'https://api.github.com/repos/mrghozzi/myads/releases/latest' => Http::response([
                'tag_name' => 'v4.2.2',
                'name' => 'v4.2.2',
                'body' => 'Test release',
                'assets' => [
                    [
                        'name' => 'myads-update.zip',
                        'browser_download_url' => 'https://downloads.example.test/myads-update.zip',
                        'size' => filesize($zipPath),
                    ],
                ],
            ], 200),
            'https://downloads.example.test/myads-update.zip' => Http::response(file_get_contents($zipPath), 200, [
                'Content-Type' => 'application/zip',
            ]),
        ]);

        try {
            $response = $this->actingAs($admin)->post(route('admin.updates.process'), [
                'backup_ack_database' => '1',
                'backup_ack_files' => '1',
            ]);

            $response->assertRedirect(route('admin.updates'));
            $response->assertSessionHas('error');
            $this->assertStringContainsString(
                __('messages.update_blocked_preflight', ['details' => '']),
                (string) session('error')
            );
        } finally {
            File::delete($zipPath);
            File::deleteDirectory($releaseRoot);
        }
    }
}
