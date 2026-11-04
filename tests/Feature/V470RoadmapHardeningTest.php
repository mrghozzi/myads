<?php

namespace Tests\Feature;

use App\Models\DeveloperApp;
use App\Models\DeveloperWebhook;
use App\Models\Option;
use App\Models\SiteAdmin;
use App\Models\User;
use App\Services\SupportDeflectionService;
use App\Services\TurnstileService;
use App\Services\WebhookDispatcherService;
use App\Support\SecuritySettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class V470RoadmapHardeningTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    /**
     * Pillar 1: Dual DocumentRoot Path Resolution in public/index.php and root index.php.
     */
    public function test_dual_document_root_resolution_in_entry_points(): void
    {
        $publicIndex = base_path('public/index.php');
        $rootIndex = base_path('index.php');

        $this->assertFileExists($publicIndex);
        $this->assertFileExists($rootIndex);

        $publicContent = File::get($publicIndex);
        $rootContent = File::get($rootIndex);

        // Verify dynamic base directory determination
        $this->assertStringContainsString("file_exists(__DIR__ . '/../vendor/autoload.php')", $publicContent);
        $this->assertStringContainsString('$baseDir = file_exists(__DIR__ . \'/../vendor/autoload.php\') ? dirname(__DIR__) : __DIR__;', $publicContent);
        $this->assertStringContainsString('$baseDir . \'/storage/framework/maintenance.php\'', $publicContent);
        $this->assertStringContainsString('$baseDir . \'/bootstrap/app.php\'', $publicContent);

        // Verify root index has identical resilient resolution
        $this->assertStringContainsString('$baseDir = file_exists(__DIR__ . \'/../vendor/autoload.php\') ? dirname(__DIR__) : __DIR__;', $rootContent);
        $this->assertStringContainsString('$baseDir . \'/storage/framework/maintenance.php\'', $rootContent);
        $this->assertStringContainsString('$baseDir . \'/bootstrap/app.php\'', $rootContent);
    }

    /**
     * Pillar 2: Webhook Replay Attack Defense & Signature v2 (X-MYADS-Timestamp & X-MYADS-Signature).
     */
    public function test_webhook_delivery_sends_timestamp_and_v2_signature(): void
    {
        Http::fake([
            'https://example.com/webhook' => Http::response(['received' => true], 200),
        ]);

        $user = User::factory()->create();
        $app = DeveloperApp::create([
            'user_id' => $user->id,
            'name' => 'Test App',
            'client_id' => 'client_test_123',
            'client_secret' => hash('sha256', 'secret'),
            'redirect_uris' => ['https://example.com/callback'],
            'status' => 'approved',
        ]);

        $webhook = DeveloperWebhook::create([
            'developer_app_id' => $app->id,
            'url' => 'https://example.com/webhook',
            'secret' => 'whsec_test_secret_key_1234567890',
            'is_active' => true,
            'events' => ['post.created'],
            'failure_count' => 0,
        ]);

        $service = new WebhookDispatcherService();
        $delivery = $service->deliver($webhook, 'post.created', ['title' => 'Test Post']);

        $this->assertEquals(200, $delivery->status_code);

        Http::assertSent(function (\Illuminate\Http\Client\Request $request) use ($webhook) {
            $headers = $request->headers();

            // Check X-MYADS-Timestamp presence
            if (!isset($headers['X-MYADS-Timestamp'][0])) {
                return false;
            }
            $timestamp = (int) $headers['X-MYADS-Timestamp'][0];
            if ($timestamp <= 0 || abs(time() - $timestamp) > 10) {
                return false;
            }

            // Check X-MYADS-Signature format: t={ts},v1={hash}
            if (!isset($headers['X-MYADS-Signature'][0])) {
                return false;
            }
            $v2Signature = $headers['X-MYADS-Signature'][0];
            if (!str_starts_with($v2Signature, "t={$timestamp},v1=")) {
                return false;
            }

            // Check legacy X-MYADS-Signature-256 header backward compatibility
            if (!isset($headers['X-MYADS-Signature-256'][0]) || !str_starts_with($headers['X-MYADS-Signature-256'][0], 'sha256=')) {
                return false;
            }

            // Verify v2 hash computation matches
            $expectedHash = hash_hmac('sha256', "{$timestamp}." . $request->body(), $webhook->secret);
            $expectedSignature = "t={$timestamp},v1={$expectedHash}";

            return $v2Signature === $expectedSignature;
        });
    }

    public function test_webhook_signature_verification_and_replay_tolerance(): void
    {
        $secret = 'whsec_sample_secret_key';
        $payload = json_encode(['event' => 'order.completed', 'order_id' => 456]);
        $now = time();

        // 1. Valid v2 signature within tolerance
        $validHash = hash_hmac('sha256', "{$now}.{$payload}", $secret);
        $validSignatureHeader = "t={$now},v1={$validHash}";
        $this->assertTrue(WebhookDispatcherService::verifySignature($payload, $validSignatureHeader, $now, $secret, 300));

        // 2. Tampered payload
        $this->assertFalse(WebhookDispatcherService::verifySignature($payload . 'tampered', $validSignatureHeader, $now, $secret, 300));

        // 3. Replay attack: timestamp older than 300s
        $oldTimestamp = $now - 350;
        $oldHash = hash_hmac('sha256', "{$oldTimestamp}.{$payload}", $secret);
        $expiredSignatureHeader = "t={$oldTimestamp},v1={$oldHash}";
        $this->assertFalse(WebhookDispatcherService::verifySignature($payload, $expiredSignatureHeader, $oldTimestamp, $secret, 300));

        // 4. Legacy v1 signature verification
        $legacyHash = hash_hmac('sha256', $payload, $secret);
        $legacyHeader = "sha256={$legacyHash}";
        $this->assertTrue(WebhookDispatcherService::verifySignature($payload, $legacyHeader, null, $secret));

        // 5. Wrong secret key
        $this->assertFalse(WebhookDispatcherService::verifySignature($payload, $validSignatureHeader, $now, 'wrong_secret', 300));
    }

    /**
     * Pillar 3: Content-Security-Policy (CSP) and Cryptographic Nonce Generation.
     */
    public function test_csp_and_cryptographic_nonce_generation(): void
    {
        $response = $this->get('/');

        $response->assertSuccessful();

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertNotEmpty($csp);

        // Verify fundamental CSP directives protecting scripts and styles
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("script-src 'self'", $csp);
        $this->assertStringContainsString("style-src 'self' 'unsafe-inline'", $csp);

        // Verify helper function csp_nonce() is callable and generates a valid 16-byte base64 nonce
        $this->assertTrue(function_exists('csp_nonce'));
        $nonce = csp_nonce();
        $this->assertNotEmpty($nonce);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9+\/]+=*$/', $nonce);
    }

    /**
     * Pillar 4: AI Support Deflection Engine (Semantic, Text Normalization & Solved Topics).
     */
    public function test_support_deflection_text_normalization(): void
    {
        $service = app(SupportDeflectionService::class);

        // Arabic normalization: strips diacritics and normalizes letters
        $normalizedAr = $service->normalizeText('كَيْفَ يُمْكِنُنِي إِنْشَاءُ حَمْلَةٍ إِعْلَانِيَّةٍ؟');
        $this->assertEquals('كيف يمكنني انشاء حمله اعلانيه', $normalizedAr);

        // Keyword extraction filters out common stop words
        $keywords = $service->extractKeywords('كيف يمكنني إنشاء إعلانات ذكية في الموقع؟');
        $this->assertContains('انشاء', $keywords);
        $this->assertContains('اعلانات', $keywords);
        $this->assertContains('ذكيه', $keywords);
        $this->assertNotContains('كيف', $keywords);
        $this->assertNotContains('في', $keywords);
    }

    public function test_support_deflection_finds_relevant_help_articles(): void
    {
        $service = app(SupportDeflectionService::class);

        $results = $service->deflect('البنرات');

        $this->assertTrue($results['enabled']);
        $this->assertTrue($results['has_matches']);
        $this->assertNotEmpty($results['articles']);
        $this->assertNotNull($results['top_match']);
    }

    public function test_support_deflect_ajax_endpoints(): void
    {
        $user = User::factory()->create();

        // 1. /tickets/deflect endpoint
        $responseTickets = $this->actingAs($user)->postJson('/tickets/deflect', [
            'query' => 'البنرات',
            'department' => 'technical',
        ]);
        $responseTickets->assertOk();
        $responseTickets->assertJsonStructure([
            'enabled',
            'has_matches',
            'articles',
            'topics',
            'count',
        ]);

        // 2. /support/deflect endpoint
        $responseSupport = $this->actingAs($user)->postJson('/support/deflect', [
            'query' => 'how to earn points through visit exchange',
            'category' => 'general',
        ]);
        $responseSupport->assertOk();
        $responseSupport->assertJsonStructure([
            'enabled',
            'has_matches',
            'articles',
            'topics',
        ]);

        // 3. API endpoint /api/support/deflect
        $responseApi = $this->postJson('/api/support/deflect', [
            'query' => 'Smart Ads targeting options',
        ]);
        $responseApi->assertOk();
        $responseApi->assertJsonStructure([
            'enabled',
            'has_matches',
        ]);
    }

    /**
     * Pillar 5: Cloudflare Turnstile Integration & Admin Security Settings.
     */
    public function test_turnstile_service_configuration_and_verification(): void
    {
        $turnstile = app(TurnstileService::class);

        // Turnstile is disabled by default
        $this->assertFalse($turnstile->isEnabled());

        // Test tokens pass in testing environment
        $this->assertTrue($turnstile->verify('1x00000000000000000000AA'));
        $this->assertTrue($turnstile->verify('XXXX.DUMMY.TOKEN.XXXX'));
        $this->assertTrue($turnstile->verify('test-turnstile-token'));
    }

    public function test_admin_can_update_turnstile_and_v470_security_settings(): void
    {
        $this->seedSiteSettings();

        $admin = User::factory()->create();
        SiteAdmin::create([
            'user_id' => $admin->id,
            'permissions' => ['security'],
            'is_active' => true,
            'has_full_access' => true,
        ]);

        $response = $this->actingAs($admin)->post('/admin/security', [
            'turnstile_enabled' => '1',
            'turnstile_site_key' => '0x4AAAAAAATestSiteKey',
            'turnstile_secret_key' => '0x4AAAAAAATestSecretKey',
            'turnstile_mode' => 'managed',
            'turnstile_forms' => 'login,register,tickets,support',
            'support_ai_deflection_enabled' => '1',
            'csp_nonce_enabled' => '1',
            'cooldown_post_seconds' => 15,
        ]);

        $response->assertRedirect(route('admin.security.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(1, SecuritySettings::get('turnstile_enabled'));
        $this->assertEquals('0x4AAAAAAATestSiteKey', SecuritySettings::get('turnstile_site_key'));
        $this->assertEquals('0x4AAAAAAATestSecretKey', SecuritySettings::get('turnstile_secret_key'));
        $this->assertEquals('managed', SecuritySettings::get('turnstile_mode'));
        $this->assertEquals('login,register,tickets,support', SecuritySettings::get('turnstile_forms'));
        $this->assertEquals(1, SecuritySettings::get('support_ai_deflection_enabled'));
        $this->assertEquals(1, SecuritySettings::get('csp_nonce_enabled'));

        // View index includes the new Turnstile and Deflection sections
        $viewResponse = $this->actingAs($admin)->get('/admin/security');
        $viewResponse->assertOk();
        $viewResponse->assertSee(__('messages.security_turnstile_section'));
        $viewResponse->assertSee(__('messages.security_ai_deflection_section'));
    }
}
