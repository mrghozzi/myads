<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\DeveloperApp;
use App\Models\Option;

class DiagnoseDeveloperPlatform extends Command
{
    protected $signature = 'developer:diagnose';
    protected $description = 'Diagnose Developer Platform issues (create/update apps)';

    public function handle()
    {
        $this->info('=== Developer Platform Diagnostic ===');
        $this->newLine();

        // 1. Check database connection
        $this->info('1. Database Connection:');
        try {
            DB::connection()->getPdo();
            $this->line('   ✅ Database connected: ' . DB::connection()->getDatabaseName());
        } catch (\Throwable $e) {
            $this->error('   ❌ Database connection FAILED: ' . $e->getMessage());
            return 1;
        }

        // 2. Check developer_apps table
        $this->newLine();
        $this->info('2. Table "developer_apps":');
        if (Schema::hasTable('developer_apps')) {
            $this->line('   ✅ Table exists');
            
            $columns = Schema::getColumnListing('developer_apps');
            $this->line('   Columns: ' . implode(', ', $columns));
            
            $requiredCols = ['id', 'user_id', 'name', 'domain', 'description', 'client_id', 'client_secret', 'redirect_uris', 'requested_scopes', 'status', 'created_at', 'updated_at'];
            $missing = array_diff($requiredCols, $columns);
            if (empty($missing)) {
                $this->line('   ✅ All required columns present');
            } else {
                $this->error('   ❌ Missing columns: ' . implode(', ', $missing));
            }
            
            $count = DB::table('developer_apps')->count();
            $this->line('   Current rows: ' . $count);
        } else {
            $this->error('   ❌ Table does NOT exist!');
            $this->warn('   → Run: php artisan migrate');
            return 1;
        }

        // 3. Check options table for developer platform settings
        $this->newLine();
        $this->info('3. Developer Platform Settings:');
        try {
            $settings = Option::where('o_type', 'developer_platform')->get();
            if ($settings->isEmpty()) {
                $legacy = Option::where('name', 'LIKE', 'dev_platform_%')->get();
                if ($legacy->isEmpty()) {
                    $this->warn('   ⚠️ No developer platform settings found in options table!');
                    $this->warn('   → The platform may be DISABLED (default is false)');
                    $this->warn('   → Enable it from admin panel or run:');
                    $this->warn('     INSERT INTO options (name, o_valuer, o_type) VALUES ("enabled", "1", "developer_platform")');
                } else {
                    $this->line('   Legacy settings found:');
                    foreach ($legacy as $opt) {
                        $this->line('   - ' . $opt->name . ' = ' . ($opt->o_valuer ?? 'NULL'));
                    }
                }
            } else {
                foreach ($settings as $opt) {
                    $this->line('   - ' . $opt->name . ' = ' . ($opt->o_valuer ?? 'NULL'));
                }
                
                $enabled = $settings->where('name', 'enabled')->first();
                if ($enabled && $enabled->o_valuer) {
                    $this->line('   ✅ Platform is ENABLED');
                } else {
                    $this->error('   ❌ Platform is DISABLED!');
                }
            }
        } catch (\Throwable $e) {
            $this->error('   ❌ Error reading options: ' . $e->getMessage());
        }

        // 4. Try to INSERT a test record
        $this->newLine();
        $this->info('4. Test INSERT into developer_apps:');
        $testClientId = 'test_diag_' . bin2hex(random_bytes(8));
        try {
            $testApp = DeveloperApp::create([
                'user_id' => 1,
                'name' => 'Diagnostic Test App',
                'domain' => 'https://test.example.com',
                'description' => 'Temporary test - will be deleted',
                'client_id' => $testClientId,
                'client_secret' => bin2hex(random_bytes(32)),
                'redirect_uris' => ['https://test.example.com/callback'],
                'requested_scopes' => ['read_profile'],
                'status' => 'draft',
            ]);

            if ($testApp && $testApp->id) {
                $this->line('   ✅ INSERT succeeded! App ID: ' . $testApp->id);
                
                // 5. Verify the record exists
                $verify = DB::table('developer_apps')->where('id', $testApp->id)->first();
                if ($verify) {
                    $this->line('   ✅ Record verified in database');
                    $this->line('   - redirect_uris stored as: ' . ($verify->redirect_uris ?? 'NULL'));
                    $this->line('   - requested_scopes stored as: ' . ($verify->requested_scopes ?? 'NULL'));
                } else {
                    $this->error('   ❌ Record NOT found after INSERT!');
                }

                // 6. Cleanup
                $testApp->delete();
                $this->line('   ✅ Test record cleaned up');
            } else {
                $this->error('   ❌ INSERT returned null or no ID');
            }
        } catch (\Throwable $e) {
            $this->error('   ❌ INSERT FAILED!');
            $this->error('   Exception: ' . get_class($e));
            $this->error('   Message: ' . $e->getMessage());
            $this->error('   File: ' . $e->getFile() . ':' . $e->getLine());
            
            // Check if it's a SQL error
            if ($e instanceof \Illuminate\Database\QueryException) {
                $this->error('   SQL: ' . $e->getSql());
                $this->error('   Bindings: ' . json_encode($e->getBindings()));
            }
        }

        // 7. Check session driver
        $this->newLine();
        $this->info('5. Session Configuration:');
        $this->line('   Driver: ' . config('session.driver'));
        $this->line('   Lifetime: ' . config('session.lifetime') . ' minutes');
        if (config('session.driver') === 'file') {
            $sessPath = config('session.files', storage_path('framework/sessions'));
            $this->line('   Path: ' . $sessPath);
            if (is_writable($sessPath)) {
                $this->line('   ✅ Session directory is writable');
            } else {
                $this->error('   ❌ Session directory is NOT writable!');
            }
        }

        // 8. Check view cache
        $this->newLine();
        $this->info('6. View Cache:');
        $viewCachePath = storage_path('framework/views');
        $cachedViews = glob($viewCachePath . '/*.php');
        $this->line('   Cached views: ' . count($cachedViews));
        $this->warn('   → Recommended: php artisan view:clear');

        // 9. Check APP_DEBUG
        $this->newLine();
        $this->info('7. App Configuration:');
        $this->line('   APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false'));
        $this->line('   APP_ENV: ' . config('app.env'));

        // 10. Check log file
        $this->newLine();
        $this->info('8. Log File:');
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $this->line('   ✅ Log file exists');
            $this->line('   Size: ' . number_format(filesize($logFile) / 1024, 1) . ' KB');
            if (is_writable($logFile)) {
                $this->line('   ✅ Log file is writable');
            } else {
                $this->error('   ❌ Log file is NOT writable!');
            }
            
            // Check for recent developer app errors
            $logContent = file_get_contents($logFile);
            $devErrors = substr_count($logContent, 'Developer app');
            if ($devErrors > 0) {
                $this->warn('   ⚠️ Found ' . $devErrors . ' "Developer app" error entries in log');
                $this->warn('   → Check: tail -50 storage/logs/laravel.log | grep "Developer app"');
            } else {
                $this->line('   No "Developer app" errors in log');
            }
        } else {
            $this->warn('   ⚠️ Log file does not exist (will be created on first log)');
        }

        // 9. Check DeveloperApp model fillable
        $this->newLine();
        $this->info('9. DeveloperApp Model:');
        $model = new DeveloperApp();
        $this->line('   Fillable: ' . implode(', ', $model->getFillable()));
        $this->line('   Table: ' . $model->getTable());

        $this->newLine();
        $this->info('=== Diagnostic Complete ===');
        
        return 0;
    }
}
