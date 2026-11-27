<?php

namespace Tests\Feature\Plugins;

use App\Helpers\Hooks;
use App\Models\Option;
use App\Models\SecurityIpBan;
use App\Models\SiteAdmin;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use MyAds\Plugins\MyAdsGuardian\Models\GuardianEvent;
use MyAds\Plugins\MyAdsGuardian\Services\GuardianAlertService;
use MyAds\Plugins\MyAdsGuardian\Services\GuardianFileIntegrityService;
use MyAds\Plugins\MyAdsGuardian\Services\GuardianSettings;
use MyAds\Plugins\MyAdsGuardian\Services\GuardianWafService;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class MyAdsGuardianTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        Hooks::reset();
        $this->seedSiteSettings();

        Option::updateOrCreate(
            ['name' => 'myads-guardian', 'o_type' => 'plugins'],
            ['o_valuer' => '1']
        );

        app(GuardianSettings::class)->forgetCache();
    }

    protected function tearDown(): void
    {
        Hooks::reset();
        parent::tearDown();
    }

    private function createSecurityAdmin(array $modules = ['security', 'dashboard']): User
    {
        $user = User::factory()->create();
        SiteAdmin::create([
            'user_id' => $user->id,
            'has_full_access' => false,
            'is_active' => 1,
            'permissions' => $modules,
        ]);

        return $user;
    }

    public function test_admin_guardian_dashboard_requires_security_permission(): void
    {
        // First user (id=1) is Super Admin in MYADS; create id=1 before creating regular members/limited admins.
        $superAdmin = User::factory()->create();
        $member = User::factory()->create();
        $limitedAdmin = $this->createSecurityAdmin(['dashboard']);
        $securityAdmin = $this->createSecurityAdmin(['security']);

        $this->actingAs($member)
            ->get('/admin/guardian')
            ->assertStatus(302);

        $this->actingAs($limitedAdmin)
            ->get('/admin/guardian')
            ->assertStatus(403);

        $this->actingAs($superAdmin)
            ->get('/admin/guardian')
            ->assertStatus(200)
            ->assertSee('MyAds Guardian');

        $this->actingAs($securityAdmin)
            ->get('/admin/guardian')
            ->assertStatus(200)
            ->assertSee('MyAds Guardian');
    }

    public function test_waf_blocks_probes_sqli_malicious_uploads_and_auto_bans_attacker_ip(): void
    {
        $attackerIp = '203.0.113.50';
        app(GuardianSettings::class)->save([
            'is_enabled' => '1',
            'waf_enabled' => '1',
            'waf_mode' => 'block',
            'behavior_enabled' => '1',
            'ban_threshold' => 100,
            'ban_duration_minutes' => 60,
        ]);

        // 1. Sensitive WordPress/exploit probe hits (+50 pts)
        $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
            ->get('/wp-login.php')
            ->assertStatus(403)
            ->assertSee('sensitive_probe_path');

        $this->assertFalse(
            SecurityIpBan::where('ip_address', $attackerIp)->where('is_active', true)->exists()
        );

        // 2. Executable code hidden inside uploaded image is stopped before controller runs (+100 pts)
        $fakeUpload = UploadedFile::fake()->createWithContent('avatar.jpg', "<?php echo 'benign_payload'; ?>");
        $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
            ->post('/login', ['avatar' => $fakeUpload])
            ->assertStatus(403)
            ->assertSee('executable_code_in_upload');

        // Total score = 150 >= 100 -> IP is auto-banned in core SecurityIpBan & cache is cleared
        $ban = SecurityIpBan::where('ip_address', $attackerIp)->where('is_active', true)->first();
        $this->assertNotNull($ban);
        $this->assertStringContainsString('MyAds Guardian', (string) $ban->reason);
        $this->assertFalse(Cache::has('security_ip_banned_' . md5($attackerIp)));

        // 3. Subsequent benign request from banned IP is blocked by core BlockBannedIp
        $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
            ->get('/')
            ->assertStatus(403);

        // 4. Security admin (from clean admin IP) unbans the IP via Guardian dashboard -> restores access
        $admin = $this->createSecurityAdmin(['security']);
        $this->actingAs($admin)
            ->withServerVariables(['REMOTE_ADDR' => '198.51.100.1'])
            ->post('/admin/guardian/ip/unban', ['ban_id' => $ban->id])
            ->assertStatus(302);

        $this->assertDatabaseMissing('security_ip_bans', ['id' => $ban->id]);
        $this->assertSame(0, (int) Cache::get('guardian_threat_score_' . md5($attackerIp), 0));

        // 5. SQLi in query string is also blocked on a fresh attacker IP
        $sqliIp = '203.0.113.51';
        $this->withServerVariables(['REMOTE_ADDR' => $sqliIp])
            ->get('/?q=1%20UNION%20ALL%20SELECT%20username,pass%20FROM%20users')
            ->assertStatus(403)
            ->assertSee('sqli_query_payload');
    }

    public function test_whitelisted_ip_and_cidr_bypass_waf_checks(): void
    {
        app(GuardianSettings::class)->save([
            'is_enabled' => '1',
            'waf_enabled' => '1',
            'ip_whitelist' => "198.51.100.77\n198.51.100.0/24",
        ]);

        $waf = app(GuardianWafService::class);
        $this->assertTrue($waf->isIpWhitelisted('198.51.100.77'));
        $this->assertTrue($waf->isIpWhitelisted('198.51.100.42'));
        $this->assertFalse($waf->isIpWhitelisted('203.0.113.99'));

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.42'])
            ->get('/?q=1%20UNION%20SELECT%201')
            ->assertStatus(200);

        $this->assertSame(0, GuardianEvent::where('ip_address', '198.51.100.42')->count());
    }

    public function test_failed_login_attempts_admin_anomaly_and_privilege_auditing(): void
    {
        $bruteIp = '203.0.113.88';
        app(GuardianSettings::class)->save([
            'is_enabled' => '1',
            'behavior_enabled' => '1',
            'track_auth_failures' => '1',
            'auth_failure_points' => 25,
            'ban_threshold' => 50,
        ]);

        request()->server->set('REMOTE_ADDR', $bruteIp);
        Event::dispatch(new Failed('web', null, ['email' => 'victim@example.com']));
        $this->assertFalse(SecurityIpBan::where('ip_address', $bruteIp)->exists());

        Event::dispatch(new Failed('web', null, ['email' => 'victim@example.com']));
        $this->assertTrue(SecurityIpBan::where('ip_address', $bruteIp)->where('is_active', true)->exists());

        // Admin unseen-IP anomaly tracking
        $admin = $this->createSecurityAdmin(['security']);
        request()->server->set('REMOTE_ADDR', '198.51.100.10');
        Event::dispatch(new Login('web', $admin, false));
        $this->assertSame(0, GuardianEvent::where('event_type', 'admin_anomaly')->count());

        request()->server->set('REMOTE_ADDR', '198.51.100.20');
        Event::dispatch(new Login('web', $admin, false));
        $this->assertTrue(
            GuardianEvent::where('event_type', 'admin_anomaly')
                ->where('rule_matched', 'admin_login_new_ip')
                ->where('ip_address', '198.51.100.20')
                ->exists()
        );

        // Privilege grant audit log
        $otherUser = User::factory()->create();
        $siteAdmin = SiteAdmin::create([
            'user_id' => $otherUser->id,
            'has_full_access' => false,
            'is_active' => 1,
            'permissions' => ['dashboard'],
        ]);

        $this->assertTrue(
            GuardianEvent::where('event_type', 'privilege_audit')
                ->where('rule_matched', 'site_admin_granted')
                ->exists()
        );

        $siteAdmin->delete();
        $this->assertTrue(
            GuardianEvent::where('event_type', 'privilege_audit')
                ->where('rule_matched', 'site_admin_revoked')
                ->exists()
        );
    }

    public function test_file_integrity_monitor_detects_suspicious_upload_and_quarantines_safely(): void
    {
        $testDir = base_path('upload/guardian_test_dir_' . time());
        $cleanFile = $testDir . DIRECTORY_SEPARATOR . 'clean.txt';
        $evilRel = 'upload/' . basename($testDir) . '/drop_shell.php';
        $evilFile = base_path($evilRel);
        $quarantinedAbs = null;

        if (!is_dir($testDir)) {
            @mkdir($testDir, 0755, true);
        }
        file_put_contents($cleanFile, "Clean text asset\n");

        try {
            $fim = app(GuardianFileIntegrityService::class);
            $baseline = $fim->createBaseline(['upload/' . basename($testDir)]);
            $this->assertTrue($baseline['success']);
            $this->assertSame(1, $baseline['recorded']);

            // Drop unauthorized PHP payload inside upload zone & tamper existing file
            file_put_contents($evilFile, "<?php echo 'test_shell'; ?>");
            file_put_contents($cleanFile, "Tampered text asset\n");

            $scan = $fim->scanIntegrity(['upload/' . basename($testDir)]);
            $this->assertTrue($scan['success']);
            $this->assertCount(1, $scan['suspicious']);
            $this->assertSame($evilRel, $scan['suspicious'][0]['path']);

            // Quarantine via admin endpoint
            $admin = $this->createSecurityAdmin(['security']);
            $this->actingAs($admin)
                ->post('/admin/guardian/fim/quarantine', ['file_path' => $evilRel])
                ->assertStatus(302)
                ->assertSessionHas('success');

            $this->assertFileDoesNotExist($evilFile);

            $quarantinedEvent = GuardianEvent::where('rule_matched', 'fim_file_quarantined')->latest('id')->first();
            $this->assertNotNull($quarantinedEvent);
            if (preg_match('#->\s*(storage/app/guardian-quarantine/[^\s]+)#', (string) $quarantinedEvent->payload_snippet, $m)) {
                $quarantinedAbs = base_path($m[1]);
                $this->assertFileExists($quarantinedAbs);
            }
        } finally {
            if (file_exists($evilFile)) {
                @unlink($evilFile);
            }
            if (file_exists($cleanFile)) {
                @unlink($cleanFile);
            }
            if (is_dir($testDir)) {
                @rmdir($testDir);
            }
            if ($quarantinedAbs && file_exists($quarantinedAbs)) {
                @unlink($quarantinedAbs);
            }
        }
    }

    public function test_panic_lockdown_blocks_public_mutations_or_maintenance_while_allowing_admin(): void
    {
        $visitorIp = '203.0.113.11';
        app(GuardianSettings::class)->save([
            'is_enabled' => '1',
            'panic_lockdown' => '1',
            'panic_level' => 'restrict_mutations',
        ]);

        // Public POST mutation blocked with 503 during restrict_mutations mode
        $this->withServerVariables(['REMOTE_ADDR' => $visitorIp])
            ->post('/register', ['name' => 'Test'])
            ->assertStatus(503);

        // Full maintenance lockdown returns 503 for public GET pages
        app(GuardianSettings::class)->save([
            'panic_level' => 'maintenance',
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => $visitorIp])
            ->get('/')
            ->assertStatus(503);

        // Security admin retains dashboard access to disable lockdown
        $admin = $this->createSecurityAdmin(['security']);
        $this->actingAs($admin)
            ->withServerVariables(['REMOTE_ADDR' => $visitorIp])
            ->get('/admin/guardian')
            ->assertStatus(200);
    }

    public function test_settings_validate_discord_webhook_and_cron_requires_valid_token(): void
    {
        $admin = $this->createSecurityAdmin(['security']);

        $this->assertFalse(GuardianAlertService::isValidDiscordWebhookUrl('http://127.0.0.1/api/webhooks/1/2'));
        $this->assertFalse(GuardianAlertService::isValidDiscordWebhookUrl('https://evil.example/api/webhooks/1/2'));
        $this->assertTrue(GuardianAlertService::isValidDiscordWebhookUrl('https://discord.com/api/webhooks/123456/tokenabc'));

        // SSRF / untrusted Discord URL is rejected cleanly
        $this->actingAs($admin)
            ->post('/admin/guardian/settings', [
                'is_enabled' => '1',
                'discord_webhook_url' => 'http://127.0.0.1/api/webhooks/1/2',
            ])
            ->assertStatus(302)
            ->assertSessionHas('error');

        // Valid config + cron token
        $this->actingAs($admin)
            ->post('/admin/guardian/settings', [
                'is_enabled' => '1',
                'fim_enabled' => '0',
                'discord_alerts_enabled' => '1',
                'discord_webhook_url' => 'https://discord.com/api/webhooks/123456/tokenabc',
                'cron_token' => 'grdn_secret_777',
            ])
            ->assertStatus(302)
            ->assertSessionHas('success');

        auth()->logout();

        $this->getJson('/guardian/cron')->assertStatus(403);
        $this->getJson('/guardian/cron?token=wrong_tok')->assertStatus(403);
        $this->getJson('/guardian/cron?token=grdn_secret_777')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        // Alert delivery test endpoint uses HTTP client without verifying disabled
        Http::fake([
            'discord.com/api/webhooks/*' => Http::response(null, 204),
        ]);

        $this->actingAs($admin)
            ->postJson('/admin/guardian/test-alert', [
                'webhook_url' => 'https://discord.com/api/webhooks/123456/tokenabc',
            ])
            ->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_404_scanner_burst_increments_behavior_score(): void
    {
        $scannerIp = '203.0.113.77';
        app(GuardianSettings::class)->save([
            'is_enabled' => '1',
            'waf_enabled' => '0',
            'behavior_enabled' => '1',
            'max_404_burst' => 5,
            'ban_threshold' => 100,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $scannerIp])
                ->get("/nonexistent-scanner-probe-{$i}")
                ->assertStatus(404);
        }

        $this->assertTrue(
            GuardianEvent::where('ip_address', $scannerIp)
                ->where('rule_matched', '404_scanner_burst')
                ->exists()
        );
    }

    public function test_fim_does_not_false_positive_on_valid_binary_images_and_supports_admin_trust(): void
    {
        $testDirName = 'guardian_binary_test_' . time();
        $testDir = base_path('upload/' . $testDirName);
        $validPngRel = 'upload/' . $testDirName . '/photo.png';
        $validPngAbs = base_path($validPngRel);
        $polyglotJpgRel = 'upload/' . $testDirName . '/polyglot.jpg';
        $polyglotJpgAbs = base_path($polyglotJpgRel);
        $dropPhpRel = 'upload/' . $testDirName . '/shell.php';
        $dropPhpAbs = base_path($dropPhpRel);

        if (!is_dir($testDir)) {
            @mkdir($testDir, 0755, true);
        }

        // Valid PNG bytes containing random 3-byte sequences (0x3C 0x3F 0x20, 0x3C 0x3F 0x3D, 0x3C 0x25 0x20)
        // followed by non-ASCII pixel bytes that caused false positives in naive scanners.
        $cleanPngBytes = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15c4"
            . "\x3C\x3F\x20\x90\xFF\xAA\x3C\x3F\x3D\x80\xEE\x12\x3C\x25\x20\x99\xDE\xAD";
        file_put_contents($validPngAbs, $cleanPngBytes);

        // Real polyglot image with embedded PHP tag
        file_put_contents($polyglotJpgAbs, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00<?php echo 'poly'; ?>");

        try {
            $fim = app(GuardianFileIntegrityService::class);
            $scan = $fim->scanIntegrity(['upload/' . $testDirName]);

            $this->assertTrue($scan['success']);
            $flaggedPaths = array_column($scan['suspicious'], 'path');
            $this->assertNotContains($validPngRel, $flaggedPaths);
            $this->assertContains($polyglotJpgRel, $flaggedPaths);

            // Simulate an older false-positive 'suspicious' snapshot row and confirm scan heals it to 'verified'
            \MyAds\Plugins\MyAdsGuardian\Models\GuardianFileSnapshot::where('path_hash', hash('sha256', $validPngRel))
                ->update(['status' => 'suspicious']);

            $rescan = $fim->scanIntegrity(['upload/' . $testDirName]);
            $this->assertNotContains($validPngRel, array_column($rescan['suspicious'], 'path'));
            $healedRow = \MyAds\Plugins\MyAdsGuardian\Models\GuardianFileSnapshot::where('path_hash', hash('sha256', $validPngRel))->first();
            $this->assertSame('verified', $healedRow->status);

            // Admin can mark non-executable files safe/trusted, while executable .php in upload/ is refused
            file_put_contents($dropPhpAbs, "<?php echo 'test'; ?>");
            $admin = $this->createSecurityAdmin(['security']);

            $this->actingAs($admin)
                ->post('/admin/guardian/fim/trust', ['file_path' => $validPngRel])
                ->assertStatus(302)
                ->assertSessionHas('success');

            $this->actingAs($admin)
                ->post('/admin/guardian/fim/trust', ['file_path' => $dropPhpRel])
                ->assertStatus(302)
                ->assertSessionHas('error');
        } finally {
            @unlink($validPngAbs);
            @unlink($polyglotJpgAbs);
            @unlink($dropPhpAbs);
            if (is_dir($testDir)) {
                @rmdir($testDir);
            }
        }
    }
}
