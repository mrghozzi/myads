<?php

namespace Tests\Feature;

use App\Services\PluginManager;
use App\Services\ThemeManager;
use App\Services\Admin\AdminNotificationService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminUpdateChecksTimeoutTest extends TestCase
{
    public function test_plugin_check_for_updates_respects_time_budget(): void
    {
        Cache::forget('plugin_updates');

        // Fake Http so connections delay slightly
        Http::fake([
            '*' => function () {
                usleep(500000); // 0.5 sec delay per request
                return Http::response(['version' => '99.0.0'], 200);
            },
        ]);

        $pluginManager = app(PluginManager::class);

        $start = microtime(true);
        $updates = $pluginManager->checkForUpdates();
        $duration = microtime(true) - $start;

        $this->assertIsArray($updates);
        // Ensure total time is well below PHP's 30 second limit (should cap around 3s time budget)
        $this->assertLessThan(10.0, $duration, 'Plugin update check took too long!');
    }

    public function test_theme_check_for_updates_respects_time_budget(): void
    {
        Cache::forget('theme_updates');

        Http::fake([
            '*' => function () {
                usleep(500000);
                return Http::response(['version' => '99.0.0'], 200);
            },
        ]);

        $themeManager = app(ThemeManager::class);

        $start = microtime(true);
        $updates = $themeManager->checkForUpdates();
        $duration = microtime(true) - $start;

        $this->assertIsArray($updates);
        $this->assertLessThan(10.0, $duration, 'Theme update check took too long!');
    }

    public function test_admin_notification_service_handles_http_failures_gracefully(): void
    {
        Cache::forget('plugin_updates');
        Cache::forget('theme_updates');

        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $adminUser = new User();
        $adminUser->adm = 1;
        $adminUser->id = 1;

        $service = app(AdminNotificationService::class);

        $notifications = $service->getNotifications($adminUser);
        $this->assertIsArray($notifications);
    }
}
