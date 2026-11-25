<?php

namespace Tests\Feature\Security;

use App\Models\ForumAttachment;
use App\Models\ForumTopic;
use App\Models\Message;
use App\Models\Option;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Short;
use App\Models\SiteAdmin;
use App\Models\Status;
use App\Models\User;
use App\Services\AdminAccessService;
use App\Services\GroupMembershipService;
use App\Services\Security\FileUploadSecurityService;
use App\Support\SystemVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;
use ZipArchive;

class V472SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected FileUploadSecurityService $securityService;
    protected User $user;
    protected User $admin;

    /** @var array<int, string> */
    protected array $cleanupPaths = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
        $this->securityService = new FileUploadSecurityService();
        $this->admin = User::find(1) ?? User::factory()->create(['id' => 1, 'username' => 'admin_v472']);
        $this->user = User::factory()->create(['username' => 'member_v472']);

        SiteAdmin::updateOrCreate(
            ['user_id' => $this->admin->id],
            [
                'is_super' => true,
                'has_full_access' => true,
                'is_active' => true,
                'permissions' => AdminAccessService::MODULES,
            ]
        );
    }

    protected function tearDown(): void
    {
        foreach ($this->cleanupPaths as $path) {
            if (is_dir($path)) {
                File::deleteDirectory($path);
            } elseif (file_exists($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    private function createCanaryRootFile(string $prefix = '__v472_canary_'): string
    {
        $path = base_path($prefix . uniqid('', true) . '.txt');
        file_put_contents($path, 'CANARY_DO_NOT_DELETE');
        $this->cleanupPaths[] = $path;
        return $path;
    }

    public function test_system_version_is_4_7_2(): void
    {
        $this->assertSame('4.7.2', SystemVersion::CURRENT);
        $this->assertSame('v4.7.2', SystemVersion::tag());
    }

    public function test_status_gallery_legacy_img_rejects_arbitrary_paths_and_prevents_root_file_deletion(): void
    {
        $canaryPath = $this->createCanaryRootFile();
        $canaryRel = basename($canaryPath);

        // 1. Creating a gallery status post pointing img to a root/arbitrary file must fail
        $response = $this->actingAs($this->user)->post(route('status.create'), [
            'post_kind' => 'gallery',
            's_type' => 4,
            'txt' => 'Attempted root file deletion via img',
            'img' => $canaryRel,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('forum_attachments', ['file_path' => $canaryRel]);
        $this->assertFileExists($canaryPath);

        // 2. Even if an attachment row pre-existed pointing outside upload/, deleting topic must NOT delete root files
        $topic = ForumTopic::create([
            'uid' => $this->user->id,
            'name' => 'Test Topic',
            'txt' => 'Body',
            'cat' => 0,
            'statu' => 1,
            'date' => time(),
        ]);
        Status::create([
            'uid' => $this->user->id,
            'tp_id' => $topic->id,
            's_type' => 4,
            'date' => time(),
            'statu' => 1,
        ]);
        ForumAttachment::create([
            'topic_id' => $topic->id,
            'user_id' => $this->user->id,
            'file_path' => $canaryRel,
            'original_name' => $canaryRel,
            'mime_type' => 'text/plain',
            'file_size' => 10,
            'sort_order' => 1,
        ]);

        $delResponse = $this->actingAs($this->user)->postJson(route('forum.delete'), [
            'id' => $topic->id,
        ]);

        $delResponse->assertOk();
        $this->assertFileExists($canaryPath);

        // 3. Valid real image inside upload/ MUST still work via legacy `img` input
        FileUploadSecurityService::ensureUploadDirectoryProtected(base_path('upload'));
        $validImgRel = 'upload/__v472_valid_' . uniqid() . '.png';
        $validImgPath = base_path($validImgRel);
        file_put_contents(
            $validImgPath,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO7Z0XQAAAAASUVORK5CYII=')
        );
        $this->cleanupPaths[] = $validImgPath;

        $validRes = $this->actingAs($this->user)->postJson(route('status.create'), [
            'post_kind' => 'gallery',
            's_type' => 4,
            'txt' => 'Valid legacy gallery image',
            'img' => $validImgRel,
        ]);

        $validRes->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('forum_attachments', ['file_path' => $validImgRel]);
    }

    public function test_safe_delete_file_never_deletes_htaccess_or_sibling_directory_files(): void
    {
        FileUploadSecurityService::ensureUploadDirectoryProtected(base_path('upload'));
        $htaccess = base_path('upload/.htaccess');
        $this->assertFileExists($htaccess);

        $this->assertFalse(
            $this->securityService->safeDeleteFile('upload/.htaccess', [base_path('upload')])
        );
        $this->assertFileExists($htaccess);

        $siblingDir = base_path('upload_sibling_canary');
        File::makeDirectory($siblingDir, 0755, true);
        $siblingFile = $siblingDir . '/secret.txt';
        file_put_contents($siblingFile, 'keep');
        $this->cleanupPaths[] = $siblingDir;

        $this->assertFalse($this->securityService->isPathWithinDirectories($siblingFile, [base_path('upload')]));
        $this->assertFalse($this->securityService->safeDeleteFile('../upload_sibling_canary/secret.txt', [base_path('upload')]));
        $this->assertFileExists($siblingFile);
    }

    public function test_store_linkzip_and_download_short_block_arbitrary_file_disclosure(): void
    {
        $canaryPath = $this->createCanaryRootFile('__store_canary_');
        $canaryRel = basename($canaryPath);

        // 1. Store creation must reject local root/sensitive paths in linkzip
        $createRes = $this->actingAs($this->user)->post(route('store.store'), [
            'name' => 'SecretLeakProduct',
            'desc' => 'Description with enough characters to validate',
            'vnbr' => '1.0',
            'pts' => 0,
            'cat_s' => 'themes',
            'sc_cat' => 'others',
            'txt' => 'Detailed description for the product body text',
            'linkzip' => $canaryRel,
            'img' => 'upload/cover.png',
        ]);

        $createRes->assertSessionHasErrors(['linkzip']);

        // 2. Even if a legacy DB row in `short` points to a root/sensitive file, /download/{hash} returns 404
        $product = Product::create([
            'name' => 'LegacyPathLeak',
            'o_valuer' => 'Product description long enough',
            'o_type' => 'store',
            'o_parent' => $this->user->id,
            'o_order' => 0,
            'o_mode' => 'cover.png',
        ]);
        $fileOption = ProductFile::create([
            'name' => '1.0',
            'o_valuer' => 'Release',
            'o_type' => 'store_file',
            'o_parent' => $product->id,
            'o_order' => 0,
            'o_mode' => $canaryRel,
        ]);
        $short = Short::create([
            'uid' => $this->user->id,
            'url' => $canaryRel,
            'sho' => 'leakhash_' . uniqid(),
            'clik' => 0,
            'sh_type' => 7867,
            'tp_id' => $fileOption->id,
        ]);

        $this->get(route('store.download.hash', $short->sho))->assertStatus(404);
    }

    public function test_store_upload_zip_rejects_fake_zip_and_traversal_entries_and_accepts_valid_zip(): void
    {
        // 1. Plain text/PHP marker disguised as .zip must fail
        $fakeZip = UploadedFile::fake()->createWithContent('badpkg.zip', "<?php echo 'not a zip'; ?>");
        $res1 = $this->actingAs($this->user)->post(route('store.upload_zip'), ['fzip' => $fakeZip]);
        $res1->assertStatus(422);

        // 2. Real ZIP with ../ traversal entry must fail
        $badZipPath = tempnam(sys_get_temp_dir(), 'badzip');
        $zip = new ZipArchive();
        $zip->open($badZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('../../outside.txt', 'traversal test');
        $zip->close();
        $this->cleanupPaths[] = $badZipPath;

        $traversalZip = new UploadedFile($badZipPath, 'bad.zip', 'application/zip', null, true);
        $res2 = $this->actingAs($this->user)->post(route('store.upload_zip'), ['fzip' => $traversalZip]);
        $res2->assertStatus(422);

        // 3. Valid real ZIP must succeed cleanly
        $goodZipPath = tempnam(sys_get_temp_dir(), 'goodzip');
        $zip2 = new ZipArchive();
        $zip2->open($goodZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip2->addFromString('README.txt', 'Valid plugin package');
        $zip2->close();
        $this->cleanupPaths[] = $goodZipPath;

        $validZip = new UploadedFile($goodZipPath, 'good.zip', 'application/zip', null, true);
        $res3 = $this->actingAs($this->user)->post(route('store.upload_zip'), ['fzip' => $validZip]);
        $res3->assertOk();
        $this->assertMatchesRegularExpression('/upload\/store_\d+_[a-zA-Z0-9]+\.zip/', $res3->getContent());

        if (preg_match('/(upload\/store_\d+_[a-zA-Z0-9]+\.zip)/', $res3->getContent(), $m)) {
            $this->cleanupPaths[] = base_path($m[1]);
        }
    }

    public function test_admin_language_traversal_is_blocked_and_cannot_delete_base_path(): void
    {
        $canaryPath = $this->createCanaryRootFile('__lang_canary_');

        // 1. Admin storeLanguage rejects `..`
        $storeRes = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post(route('admin.languages.store'), [
                'name' => 'Traversal',
                'o_valuer' => '..',
            ]);

        $storeRes->assertSessionHasErrors(['o_valuer']);

        // 2. Even if an option row with o_valuer = '..' exists in DB, deleting it MUST NOT touch base_path()
        $evilOption = Option::create([
            'name' => 'EvilLang',
            'o_valuer' => '..',
            'o_type' => 'languages',
            'o_parent' => 0,
            'o_order' => 0,
            'o_mode' => '',
        ]);

        $delRes = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->delete(route('admin.languages.delete', $evilOption->id));

        $delRes->assertRedirect();
        $this->assertFileExists($canaryPath);
        $this->assertDirectoryExists(base_path('app'));
        $this->assertDirectoryExists(base_path('lang/en'));
    }

    public function test_banner_upload_rejects_svg_xss_and_polyglots(): void
    {
        $svg = UploadedFile::fake()->createWithContent(
            'xss_banner.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
        );
        $resSvg = $this->actingAs($this->user)->post(route('ads.banners.store'), [
            'name' => 'SVG Banner',
            'url' => 'https://example.com',
            'px' => '468x60',
            'img_file' => $svg,
        ]);
        $resSvg->assertSessionHasErrors(['img_file']);

        $polyglot = UploadedFile::fake()->createWithContent('poly.gif', "GIF89a<?php echo 'poly'; ?>");
        $resPoly = $this->actingAs($this->user)->post(route('ads.banners.store'), [
            'name' => 'Poly Banner',
            'url' => 'https://example.com',
            'px' => '468x60',
            'img_file' => $polyglot,
        ]);
        $resPoly->assertSessionHasErrors(['img_file']);
    }

    public function test_profile_comment_group_and_news_uploads_reject_polyglot_payloads(): void
    {
        $polyglot = UploadedFile::fake()->createWithContent('evil.gif', "GIF89a<?php echo 1; ?>");

        // 1. Profile avatar
        $profRes = $this->actingAs($this->user)->post(route('profile.update'), [
            'email' => $this->user->email,
            'avatar' => $polyglot,
        ]);
        $profRes->assertSessionHasErrors(['avatar']);

        // 2. Comment attachment
        $topic = ForumTopic::create([
            'uid' => $this->user->id,
            'name' => 'Comment Target',
            'txt' => 'Body',
            'cat' => 0,
            'statu' => 1,
            'date' => time(),
        ]);
        $commentRes = $this->actingAs($this->user)->postJson(route('comment.store'), [
            'id' => $topic->id,
            'type' => 'forum',
            'txt' => 'Nice post',
            'image' => $polyglot,
        ]);
        $commentRes->assertStatus(422);

        // 3. GroupMembershipService::saveGroupImage
        $groupService = app(GroupMembershipService::class);
        $threw = false;
        try {
            $groupService->saveGroupImage($polyglot, 'avatar', 999);
        } catch (ValidationException $e) {
            $threw = true;
        }
        $this->assertTrue($threw, 'GroupMembershipService::saveGroupImage must reject polyglot payloads.');

        // 4. Admin news image
        $newsRes = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post(route('admin.news.store'), [
                'name' => 'News Title',
                'text' => 'News body content',
                'img' => $polyglot,
            ]);
        $newsRes->assertSessionHas('error');
    }

    public function test_message_and_order_and_admin_media_reject_executable_and_htaccess_actions(): void
    {
        $recipient = User::factory()->create(['username' => 'msg_receiver_v472']);
        $routeKey = Message::encodeConversationRouteKey($this->user, $recipient);

        $phpText = UploadedFile::fake()->createWithContent('brief.txt', "<?php echo 'test'; ?>");
        $msgRes = $this->actingAs($this->user)->postJson(route('messages.send', $routeKey), [
            'message' => '',
            'attachment' => $phpText,
        ]);
        $msgRes->assertStatus(422);

        $orderRes = $this->actingAs($this->user)->post(route('orders.store'), [
            'title' => 'Test Order',
            'description' => 'Order description details',
            'category' => 'development',
            'pricing_model' => OrderRequest::PRICING_FIXED,
            'budget_min' => 50,
            'budget_max' => 50,
            'budget_currency' => 'USD',
            'delivery_window_days' => 5,
            'attachment' => $phpText,
        ]);
        $orderRes->assertSessionHasErrors(['attachment']);

        // AdminMediaController must reject .phar / executable text AND refuse deleting .htaccess
        $pharFile = UploadedFile::fake()->createWithContent('bad.phar', "<?php echo 'phar'; ?>");
        $mediaUpload = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post(route('admin.media.upload'), [
                'target_dir' => 'public_upload',
                'files' => [$pharFile],
            ]);
        $mediaUpload->assertSessionHas('error');

        FileUploadSecurityService::ensureUploadDirectoryProtected(base_path('upload'));
        $delHtaccess = $this->actingAs($this->admin)
            ->withSession(['admin_password_confirmed_at' => time()])
            ->post(route('admin.media.delete'), [
                'path' => 'upload/.htaccess',
            ]);
        $delHtaccess->assertSessionHas('error');
        $this->assertFileExists(base_path('upload/.htaccess'));
    }

    public function test_block_sensitive_paths_denies_upload_phtml_and_other_scripts(): void
    {
        $this->get('/upload/backdoor.phtml')->assertStatus(403);
        $this->get('/upload/backdoor.php8')->assertStatus(403);
        $this->get('/upload/.htaccess')->assertStatus(403);
    }

    public function test_entry_scripts_do_not_unlink_installed_marker_when_env_missing(): void
    {
        foreach ([base_path('index.php'), public_path('index.php')] as $entryFile) {
            $content = file_get_contents($entryFile);
            $this->assertStringContainsString("!file_exists(\$baseDir . '/storage/installed')", $content);
            $this->assertStringNotContainsString("@unlink(\$baseDir . '/storage/installed')", $content);
        }
    }
}
