<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InstallerController extends Controller
{
    public function welcome()
    {
        return view('installer::welcome');
    }

    public function requirements()
    {
        // Required — installation is blocked if any of these fail
        $requirements = [
            'php'       => ['version' => '8.1.0', 'current' => phpversion(), 'status' => version_compare(phpversion(), '8.1.0', '>=')],
            'pdo'       => ['status' => extension_loaded('pdo')],
            'pdo_mysql' => ['status' => extension_loaded('pdo_mysql')],
            'mbstring'  => ['status' => extension_loaded('mbstring')],
            'tokenizer' => ['status' => extension_loaded('tokenizer')],
            'xml'       => ['status' => extension_loaded('xml')],
            'ctype'     => ['status' => extension_loaded('ctype')],
            'json'      => ['status' => extension_loaded('json')],
            'bcmath'    => ['status' => extension_loaded('bcmath')],
            'openssl'   => ['status' => extension_loaded('openssl')],
            'fileinfo'  => ['status' => extension_loaded('fileinfo')],
        ];

        // Optional — show warning but do NOT block installation
        $optional = [
            'gd'  => ['status' => extension_loaded('gd'),  'note' => 'Required for image processing'],
            'zip' => ['status' => extension_loaded('zip'), 'note' => 'Required for plugin/theme uploads'],
        ];

        $folders = [
            'storage/framework' => is_writable(storage_path('framework')),
            'storage/logs'      => is_writable(storage_path('logs')),
            'bootstrap/cache'   => is_writable(base_path('bootstrap/cache')),
            'public'            => is_writable(public_path()),
        ];

        // Only required extensions and folders block the install
        $allMet = !in_array(false, array_column($requirements, 'status'))
                && !in_array(false, $folders);

        return view('installer::requirements', compact('requirements', 'optional', 'folders', 'allMet'));
    }

    public function database()
    {
        return view('installer::database');
    }

    public function processDatabase(Request $request)
    {
        $request->validate([
            'host' => 'required',
            'port' => 'required',
            'database' => 'required',
            'username' => 'required',
        ]);

        try {
            // Test connection
            $pdo = new \PDO(
                "mysql:host={$request->host};port={$request->port};dbname={$request->database}",
                $request->username,
                $request->password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );

            // Generate APP_KEY if missing
            $envPath = base_path('.env');
            if (!file_exists($envPath)) {
                copy(base_path('.env.example'), $envPath);
            }
            $envContent = file_get_contents($envPath);
            if (empty(env('APP_KEY')) || strpos($envContent, 'APP_KEY=\n') !== false || strpos($envContent, 'APP_KEY=') === false) {
                $key = 'base64:' . base64_encode(random_bytes(32));
                $this->writeEnv(['APP_KEY' => $key]);
            }

            // Write DB config to .env
            $this->writeEnv([
                'DB_HOST' => $request->host,
                'DB_PORT' => $request->port,
                'DB_DATABASE' => $request->database,
                'DB_USERNAME' => $request->username,
                'DB_PASSWORD' => $request->password ?? '',
                'APP_URL' => $request->app_url ?? url('/'),
            ]);

            // Reload config with new values
            config([
                'database.connections.mysql.host' => $request->host,
                'database.connections.mysql.port' => $request->port,
                'database.connections.mysql.database' => $request->database,
                'database.connections.mysql.username' => $request->username,
                'database.connections.mysql.password' => $request->password ?? '',
            ]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            return redirect()->route('installer.migrate');

        } catch (\Exception $e) {
            return back()->with('error', 'Database connection failed: ' . $e->getMessage());
        }
    }

    public function migrate()
    {
        return view('installer::migrate');
    }

    public function processMigrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = Artisan::output();

            // Ensure directory table exists (may be missing if migration was recorded but table not created)
            if (!Schema::hasTable('directory')) {
                Schema::create('directory', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->unsignedBigInteger('uid');
                    $tbl->string('name');
                    $tbl->string('url');
                    $tbl->text('txt')->nullable();
                    $tbl->string('metakeywords')->nullable();
                    $tbl->unsignedBigInteger('cat')->default(0);
                    $tbl->integer('vu')->default(0);
                    $tbl->tinyInteger('statu')->default(1);
                    $tbl->bigInteger('date')->default(0);
                });
            }

            try {
                Artisan::call('db:seed', ['--force' => true]);
            } catch (\Exception $e) {
                // Seeder may fail if data exists, that's OK
            }

            // Try artisan storage:link first, fallback to manual symlink
            $this->createStorageLink();

            return redirect()->route('installer.admin');
        } catch (\Exception $e) {
            $output = isset($migrateOutput) ? $migrateOutput : '';
            return back()->with('error', 'Migration failed: ' . $e->getMessage() . ($output ? "\n\nOutput:\n" . $output : ''));
        }
    }

    public function admin()
    {
        return view('installer::admin');
    }

    public function processAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Check if admin exists or create one (first user = id 1 = admin)
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update([
                    'username' => $request->username,
                    'pass' => Hash::make($request->password),
                    'ucheck' => 1, // Verified badge
                ]);
            } else {
                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'pass' => Hash::make($request->password),
                    'pts' => 100,
                    'vu' => 100,
                    'nvu' => 100,
                    'nlink' => 100,
                    'ucheck' => 1, // Verified badge for admin
                    'online' => time(),
                ]);

                // Create slug option for the admin user (legacy requirement)
                $slug = urlencode(mb_ereg_replace('\s+', '-', $request->username));
                \App\Models\Option::create([
                    'name' => $request->username,
                    'o_valuer' => $slug,
                    'o_type' => 'user',
                    'o_parent' => 0,
                    'o_order' => $user->id,
                    'o_mode' => '0',
                ]);
            }

            return redirect()->route('installer.finish');
        } catch (\Exception $e) {
            return back()->with('error', 'Admin creation failed: ' . $e->getMessage());
        }
    }

    public function finish()
    {
        // Create installed file to prevent re-installation
        File::put(storage_path('installed'), date('Y-m-d H:i:s'));

        // Disable debug mode after installation for security
        $this->writeEnv(['APP_DEBUG' => 'false']);

        // Clear all caches for fresh start
        try {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
        } catch (\Exception $e) {
            // Non-critical, ignore
        }

        return view('installer::finish');
    }

    // Update Logic
    public function update()
    {
        return view('installer::update');
    }

    public function processUpdate(Request $request)
    {
        $log = [];

        try {
            // ============================================================
            // STEP 1: Add timestamps to all legacy tables
            // ============================================================
            $tables = [
                'users', 'options', 'status', 'forum', 'f_coment',
                'like', 'news', 'state', 'short', 'referral',
                'banner', 'link', 'visits', 'directory', 'report',
                'messages', 'notif', 'emojis', 'menu', 'ads',
                'cat_dir', 'f_cat', 'setting',
            ];

            foreach ($tables as $tableName) {
                if (Schema::hasTable($tableName)) {
                    Schema::table($tableName, function (Blueprint $tbl) use ($tableName) {
                        if (!Schema::hasColumn($tableName, 'created_at')) {
                            $tbl->timestamp('created_at')->nullable();
                        }
                        if (!Schema::hasColumn($tableName, 'updated_at')) {
                            $tbl->timestamp('updated_at')->nullable();
                        }
                    });
                    $log[] = "✅ Timestamps added: {$tableName}";
                }
            }

            // ============================================================
            // STEP 2: Add missing columns to users table
            // ============================================================
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $tbl) {
                    if (!Schema::hasColumn('users', 'email_verified_at')) {
                        $tbl->timestamp('email_verified_at')->nullable();
                    }
                    if (!Schema::hasColumn('users', 'remember_token')) {
                        $tbl->rememberToken();
                    }
                    if (!Schema::hasColumn('users', 'sig')) {
                        $tbl->text('sig')->nullable();
                    }
                    if (!Schema::hasColumn('users', 'pts')) {
                        $tbl->integer('pts')->default(0);
                    }
                    if (!Schema::hasColumn('users', 'vu')) {
                        $tbl->integer('vu')->default(0);
                    }
                    if (!Schema::hasColumn('users', 'nvu')) {
                        $tbl->float('nvu')->default(0);
                    }
                    if (!Schema::hasColumn('users', 'nlink')) {
                        $tbl->float('nlink')->default(0);
                    }
                });
                $log[] = '✅ Users table updated with Laravel columns';
            }

            // ============================================================
            // STEP 3: Add missing columns to setting table
            // ============================================================
            if (Schema::hasTable('setting')) {
                Schema::table('setting', function (Blueprint $tbl) {
                    if (!Schema::hasColumn('setting', 'cookie_enabled')) {
                        $tbl->boolean('cookie_enabled')->default(false);
                    }
                    if (!Schema::hasColumn('setting', 'cookie_title')) {
                        $tbl->string('cookie_title')->nullable();
                    }
                    if (!Schema::hasColumn('setting', 'cookie_message')) {
                        $tbl->text('cookie_message')->nullable();
                    }
                    if (!Schema::hasColumn('setting', 'cookie_accept_text')) {
                        $tbl->string('cookie_accept_text')->nullable();
                    }
                    if (!Schema::hasColumn('setting', 'cookie_decline_text')) {
                        $tbl->string('cookie_decline_text')->nullable();
                    }
                    if (!Schema::hasColumn('setting', 'cookie_position')) {
                        $tbl->string('cookie_position')->default('bottom');
                    }
                    if (!Schema::hasColumn('setting', 'cookie_bg_color')) {
                        $tbl->string('cookie_bg_color')->default('#1a1a2e');
                    }
                    if (!Schema::hasColumn('setting', 'cookie_text_color')) {
                        $tbl->string('cookie_text_color')->default('#ffffff');
                    }
                    if (!Schema::hasColumn('setting', 'cookie_btn_bg_color')) {
                        $tbl->string('cookie_btn_bg_color')->default('#4f46e5');
                    }
                    if (!Schema::hasColumn('setting', 'cookie_btn_text_color')) {
                        $tbl->string('cookie_btn_text_color')->default('#ffffff');
                    }
                    if (!Schema::hasColumn('setting', 'timezone')) {
                        $tbl->string('timezone')->default('UTC');
                    }
                });
                $log[] = '✅ Setting table updated with cookie/timezone columns';
            }

            // ============================================================
            // STEP 4: Create missing Laravel tables
            // ============================================================
            if (!Schema::hasTable('password_reset_tokens')) {
                Schema::create('password_reset_tokens', function (Blueprint $tbl) {
                    $tbl->string('email', 191)->primary();
                    $tbl->string('token');
                    $tbl->timestamp('created_at')->nullable();
                });
                $log[] = '✅ Created: password_reset_tokens';
            }

            if (!Schema::hasTable('sessions')) {
                Schema::create('sessions', function (Blueprint $tbl) {
                    $tbl->string('id', 191)->primary();
                    $tbl->foreignId('user_id')->nullable()->index();
                    $tbl->string('ip_address', 45)->nullable();
                    $tbl->text('user_agent')->nullable();
                    $tbl->longText('payload');
                    $tbl->integer('last_activity')->index();
                });
                $log[] = '✅ Created: sessions';
            }

            if (!Schema::hasTable('cache')) {
                Schema::create('cache', function (Blueprint $tbl) {
                    $tbl->string('key', 191)->primary();
                    $tbl->mediumText('value');
                    $tbl->integer('expiration');
                });
                $log[] = '✅ Created: cache';
            }

            if (!Schema::hasTable('cache_locks')) {
                Schema::create('cache_locks', function (Blueprint $tbl) {
                    $tbl->string('key', 191)->primary();
                    $tbl->string('owner');
                    $tbl->integer('expiration');
                });
                $log[] = '✅ Created: cache_locks';
            }

            if (!Schema::hasTable('jobs')) {
                Schema::create('jobs', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->string('queue')->index();
                    $tbl->longText('payload');
                    $tbl->unsignedTinyInteger('attempts');
                    $tbl->unsignedInteger('reserved_at')->nullable();
                    $tbl->unsignedInteger('available_at');
                    $tbl->unsignedInteger('created_at');
                });
                $log[] = '✅ Created: jobs';
            }

            if (!Schema::hasTable('failed_jobs')) {
                Schema::create('failed_jobs', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->string('uuid')->unique();
                    $tbl->text('connection');
                    $tbl->text('queue');
                    $tbl->longText('payload');
                    $tbl->longText('exception');
                    $tbl->timestamp('failed_at')->useCurrent();
                });
                $log[] = '✅ Created: failed_jobs';
            }

            // ============================================================
            // STEP 5: Create options table if missing
            // ============================================================
            if (!Schema::hasTable('options')) {
                Schema::create('options', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->string('name')->nullable();
                    $tbl->longText('o_valuer')->nullable();
                    $tbl->string('o_type')->nullable();
                    $tbl->integer('o_parent')->default(0);
                    $tbl->integer('o_order')->default(0);
                    $tbl->string('o_mode')->nullable();
                });
                $log[] = '✅ Created: options';
            }

            // ============================================================
            // STEP 5.1: Create pages table if missing (v4.1 feature)
            // ============================================================
            if (!Schema::hasTable('pages')) {
                Schema::create('pages', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->string('title');
                    $tbl->string('slug')->unique();
                    $tbl->longText('content')->nullable();
                    $tbl->enum('status', ['published', 'draft'])->default('published');
                    $tbl->boolean('widget_left')->default(true);
                    $tbl->boolean('widget_right')->default(true);
                    $tbl->text('meta_description')->nullable();
                    $tbl->text('meta_keywords')->nullable();
                    $tbl->integer('order')->default(0);
                    $tbl->timestamps();
                });
                $log[] = '✅ Created: pages';
            }

            // ============================================================
            // STEP 5.2: Add forum moderation columns & tables (v4.1)
            // ============================================================
            if (Schema::hasTable('forum')) {
                Schema::table('forum', function (Blueprint $tbl) {
                    if (!Schema::hasColumn('forum', 'is_pinned')) {
                        $tbl->boolean('is_pinned')->default(false);
                    }
                    if (!Schema::hasColumn('forum', 'pinned_at')) {
                        $tbl->unsignedBigInteger('pinned_at')->nullable();
                    }
                    if (!Schema::hasColumn('forum', 'pinned_by')) {
                        $tbl->unsignedBigInteger('pinned_by')->nullable();
                    }
                    if (!Schema::hasColumn('forum', 'is_locked')) {
                        $tbl->boolean('is_locked')->default(false);
                    }
                    if (!Schema::hasColumn('forum', 'locked_at')) {
                        $tbl->unsignedBigInteger('locked_at')->nullable();
                    }
                    if (!Schema::hasColumn('forum', 'locked_by')) {
                        $tbl->unsignedBigInteger('locked_by')->nullable();
                    }
                });
                $log[] = '✅ Forum table updated with moderation columns';
            }

            if (!Schema::hasTable('forum_moderators')) {
                Schema::create('forum_moderators', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->unsignedBigInteger('user_id')->unique();
                    $tbl->boolean('is_global')->default(false);
                    $tbl->text('permissions')->nullable();
                    $tbl->boolean('is_active')->default(true);
                    $tbl->unsignedBigInteger('created_by')->nullable();
                    $tbl->timestamps();

                    $tbl->index('is_global');
                    $tbl->index('is_active');
                    $tbl->index('created_by');
                });
                $log[] = '✅ Created: forum_moderators';
            }

            if (!Schema::hasTable('forum_moderator_categories')) {
                Schema::create('forum_moderator_categories', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->unsignedBigInteger('moderator_id');
                    $tbl->unsignedBigInteger('category_id');

                    $tbl->unique(['moderator_id', 'category_id'], 'forum_moderator_category_unique');
                    $tbl->index('moderator_id');
                    $tbl->index('category_id');
                });
                $log[] = '✅ Created: forum_moderator_categories';
            }

            if (!Schema::hasTable('forum_attachments')) {
                Schema::create('forum_attachments', function (Blueprint $tbl) {
                    $tbl->id();
                    $tbl->unsignedBigInteger('topic_id');
                    $tbl->unsignedBigInteger('user_id');
                    $tbl->string('file_path');
                    $tbl->string('original_name');
                    $tbl->string('mime_type')->nullable();
                    $tbl->unsignedBigInteger('file_size')->default(0);
                    $tbl->unsignedInteger('sort_order')->default(0);
                    $tbl->timestamps();

                    $tbl->index('topic_id');
                    $tbl->index('user_id');
                });
                $log[] = '✅ Created: forum_attachments';
            }

            // ============================================================
            // STEP 6: Run any pending Laravel migrations
            // ============================================================
            try {
                Artisan::call('migrate', ['--force' => true]);
                $log[] = '✅ Laravel migrations completed';
            } catch (\Exception $e) {
                $log[] = '⚠️ Migrations: ' . $e->getMessage();
            }

            // ============================================================
            // STEP 7: Seed default data if missing
            // ============================================================
            try {
                Artisan::call('db:seed', ['--force' => true]);
                $log[] = '✅ Default data seeded';
            } catch (\Exception $e) {
                $log[] = '⚠️ Seeder: ' . $e->getMessage();
            }

            // ============================================================
            // STEP 8: Create storage link
            // ============================================================
            $this->createStorageLink();
            $log[] = '✅ Storage link created';

            // ============================================================
            // STEP 9: Copy old upload directory if exists
            // ============================================================
            try {
                $possibleUploadPaths = [
                    base_path('../old/upload'),
                    base_path('../upload'),
                    base_path('upload'),        // same directory
                ];
                $newUploadPath = public_path('upload');
                foreach ($possibleUploadPaths as $oldUploadPath) {
                    if (@is_dir($oldUploadPath) && realpath($oldUploadPath) !== realpath($newUploadPath)) {
                        if (!is_dir($newUploadPath)) {
                            @mkdir($newUploadPath, 0775, true);
                        }
                        File::copyDirectory($oldUploadPath, $newUploadPath);
                        $log[] = '✅ Old upload files copied';
                        break;
                    }
                }
            } catch (\Throwable $e) {
                $log[] = '⚠️ Upload copy skipped (hosting restriction)';
            }

            // ============================================================
            // STEP 10: Generate APP_KEY if missing
            // ============================================================
            if (empty(env('APP_KEY'))) {
                $key = 'base64:' . base64_encode(random_bytes(32));
                $this->writeEnv(['APP_KEY' => $key]);
                $log[] = '✅ APP_KEY generated';
            }

            // ============================================================
            // STEP 11: Update version in DB
            // ============================================================
            $versionOption = \App\Models\Option::where('o_type', 'version')->first();
            if ($versionOption) {
                $versionOption->update(['o_valuer' => '4.1.2']);
            } else {
                \App\Models\Option::create([
                    'name' => 'version',
                    'o_valuer' => '4.1.2',
                    'o_type' => 'version',
                    'o_parent' => 0,
                    'o_order' => 0,
                    'o_mode' => '0',
                ]);
            }
            $log[] = '✅ Version updated to 4.1.2';

            // ============================================================
            // STEP 12: Clear caches
            // ============================================================
            try {
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
            } catch (\Exception $e) {
                // Non-critical
            }

            // Mark as installed
            File::put(storage_path('installed'), date('Y-m-d H:i:s') . ' (upgraded from v3.x to v4.1.2)');

            return redirect()->route('installer.finish')
                ->with('success', 'Upgrade completed successfully!')
                ->with('log', $log);

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Update failed: ' . $e->getMessage())
                ->with('log', $log);
        }
    }

    /**
     * Create a storage symlink, with fallback for shared hosting.
     */
    private function createStorageLink()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        // Ensure target directory exists
        if (!is_dir($target)) {
            @mkdir($target, 0775, true);
        }

        // If the link already exists, skip
        if (file_exists($link) || is_link($link)) {
            return;
        }

        // Only try Artisan if exec() is available (many shared hosts disable it)
        if (function_exists('exec')) {
            try {
                Artisan::call('storage:link');
                return;
            } catch (\Throwable $e) {
                // Artisan failed, try manual approaches
            }
        }

        // Try manual symlink (may fail on some shared hosts)
        try {
            if (function_exists('symlink')) {
                @symlink($target, $link);
                if (is_link($link)) {
                    return;
                }
            }
        } catch (\Throwable $e) {
            // symlink not available or failed
        }

        // Last resort: create a directory (files will need to be copied manually)
        if (!is_dir($link)) {
            @mkdir($link, 0775, true);
        }
    }

    /**
     * Write key-value pairs to the .env file.
     */
    private function writeEnv($data = [])
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $env = file_get_contents($path);
        } else {
            // Copy from .env.example if .env doesn't exist
            $examplePath = base_path('.env.example');
            if (file_exists($examplePath)) {
                copy($examplePath, $path);
                $env = file_get_contents($path);
            } else {
                $env = '';
            }
        }

        foreach ($data as $key => $value) {
            // Escape value if it contains special characters
            if ($value !== '' && (strpos($value, ' ') !== false || strpos($value, '#') !== false)) {
                $value = '"' . $value . '"';
            }

            // Check if key exists
            if (preg_match("/^" . preg_quote($key, '/') . "=/m", $env)) {
                $env = preg_replace("/^" . preg_quote($key, '/') . "=.*/m", "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($path, $env);

        // Clear config cache
        try {
            Artisan::call('config:clear');
        } catch (\Exception $e) {
            // Non-critical on shared hosting
        }
    }
}
