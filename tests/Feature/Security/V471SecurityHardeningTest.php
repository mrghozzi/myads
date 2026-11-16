<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Services\Security\FileUploadSecurityService;
use App\Support\SystemVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class V471SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected FileUploadSecurityService $securityService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
        $this->securityService = new FileUploadSecurityService();
        $this->user = User::factory()->create();
    }

    public function test_system_version_is_4_7_1(): void
    {
        $this->assertSame('4.7.1', SystemVersion::CURRENT);
        $this->assertSame('v4.7.1', SystemVersion::tag());
    }

    public function test_image_upload_blocks_polyglot_and_php_scripts(): void
    {
        // 1. File with GIF magic bytes header but containing executable PHP code (polyglot exploit)
        $polyglotContent = "GIF89a\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00!\xf9\x04\x01\x00\x00\x00\x00,\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02D\x01\x00;<?php echo 'polyglot test'; ?>";
        $polyglotFile = UploadedFile::fake()->createWithContent('avatar.gif', $polyglotContent);

        $result = $this->securityService->validateImageUpload($polyglotFile);
        $this->assertFalse($result['valid']);
        $this->assertNotEmpty($result['error']);

        // 2. Direct PHP script with spoofed image name
        $phpFile = UploadedFile::fake()->createWithContent('shell.php', "<?php echo 'hello'; ?>");
        $resultPhp = $this->securityService->validateImageUpload($phpFile);
        $this->assertFalse($resultPhp['valid']);
    }

    public function test_image_upload_blocks_forbidden_extensions_and_double_extensions(): void
    {
        // Double extension: exploit.php.jpg
        $file = UploadedFile::fake()->createWithContent('exploit.php.jpg', "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x01\x00`\x00`\x00\x00");
        $result = $this->securityService->validateImageUpload($file);
        $this->assertFalse($result['valid']);

        // Forbidden extension
        $pharFile = UploadedFile::fake()->createWithContent('archive.phar', "dummy");
        $resultPhar = $this->securityService->validateImageUpload($pharFile);
        $this->assertFalse($resultPhar['valid']);
    }

    public function test_valid_image_upload_generates_safe_random_filename_and_safe_extension(): void
    {
        $realPngContent = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15c4";
        $pngFile = UploadedFile::fake()->createWithContent('my_photo.png', $realPngContent);

        $result = $this->securityService->validateImageUpload($pngFile, FileUploadSecurityService::ALLOWED_IMAGE_EXTENSIONS, 'test_img');
        $this->assertTrue($result['valid']);
        $this->assertNull($result['error']);
        $this->assertSame('png', $result['extension']);
        $this->assertStringStartsWith('test_img_', $result['filename']);
        $this->assertStringEndsWith('.png', $result['filename']);
    }

    public function test_status_upload_image_endpoint_rejects_malicious_polyglot(): void
    {
        $polyglotContent = "GIF89a<?php echo 'hacked'; ?>";
        $fakeFile = UploadedFile::fake()->createWithContent('shell.php', $polyglotContent);

        $response = $this->actingAs($this->user)
            ->post('/status/upload-image', [
                'fimg' => $fakeFile,
            ]);

        $this->assertTrue(in_array($response->status(), [302, 422], true));
    }

    public function test_status_upload_image_endpoint_accepts_clean_images(): void
    {
        $realPngContent = "\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15c4";
        $cleanPng = UploadedFile::fake()->createWithContent('valid.png', $realPngContent);

        $response = $this->actingAs($this->user)
            ->post('/status/upload-image', [
                'fimg' => $cleanPng,
            ]);

        $response->assertStatus(200);
        $this->assertStringContainsString('upload/', $response->getContent());
        $this->assertStringNotContainsString('.php', $response->getContent());
    }

    public function test_upload_htaccess_and_storage_htaccess_exist_and_block_execution(): void
    {
        $uploadHtaccess = base_path('upload/.htaccess');
        $this->assertFileExists($uploadHtaccess);
        $uploadContent = file_get_contents($uploadHtaccess);
        $this->assertStringContainsString('Options -Indexes -ExecCGI', $uploadContent);
        $this->assertStringContainsString('php_flag engine off', $uploadContent);
        $this->assertStringContainsString('RemoveHandler .php', $uploadContent);

        $storageHtaccess = storage_path('.htaccess');
        $this->assertFileExists($storageHtaccess);
        $storageContent = file_get_contents($storageHtaccess);
        $this->assertStringContainsString('Options -Indexes', $storageContent);
        $this->assertStringContainsString('Deny from all', $storageContent);

        $logsHtaccess = storage_path('logs/.htaccess');
        $this->assertFileExists($logsHtaccess);
        $this->assertStringContainsString('Deny from all', file_get_contents($logsHtaccess));
    }

    public function test_block_sensitive_paths_middleware_denies_direct_upload_php_requests(): void
    {
        $response = $this->get('/upload/backdoor.php');
        $response->assertStatus(403);

        $response2 = $this->get('/storage/logs/laravel-2026-11-16.log');
        $response2->assertStatus(403);

        $response3 = $this->get('/storage/framework/sessions/test');
        $response3->assertStatus(403);
    }

    public function test_self_healing_htaccess_recreates_missing_files(): void
    {
        $tempLogsHtaccess = storage_path('logs/.htaccess');
        if (File::exists($tempLogsHtaccess)) {
            File::delete($tempLogsHtaccess);
        }

        $this->assertFileDoesNotExist($tempLogsHtaccess);

        FileUploadSecurityService::ensureStorageDirectoriesProtected();

        $this->assertFileExists($tempLogsHtaccess);
        $this->assertStringContainsString('Deny from all', file_get_contents($tempLogsHtaccess));
    }
}
