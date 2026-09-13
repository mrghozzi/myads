<?php

namespace Tests\Feature;

use App\Jobs\SendTransactionalEmailJob;
use App\Mail\SystemNotificationMail;
use App\Models\SiteAdmin;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AsyncQueueSystemTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    public function test_queue_configuration_defines_application_channels(): void
    {
        $channels = config('queue.channels');

        $this->assertIsArray($channels);
        $this->assertArrayHasKey('high', $channels);
        $this->assertArrayHasKey('default', $channels);
        $this->assertArrayHasKey('media', $channels);
        $this->assertArrayHasKey('maintenance', $channels);

        $this->assertSame('high', $channels['high']);
        $this->assertSame('default', $channels['default']);
        $this->assertSame('media', $channels['media']);
        $this->assertSame('maintenance', $channels['maintenance']);

        // Check database-high connection
        $dbHigh = config('queue.connections.database-high');
        $this->assertIsArray($dbHigh);
        $this->assertSame('high', $dbHigh['queue']);
    }

    public function test_system_notification_mail_implements_should_queue(): void
    {
        $user = User::factory()->create();
        $mailable = new SystemNotificationMail($user, 'Test notification message', 'https://example.com');

        $this->assertInstanceOf(ShouldQueue::class, $mailable);
        $this->assertSame('default', $mailable->queue);
    }

    public function test_send_transactional_email_job_dispatches_mailable_via_mail_facade(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $mailable = new SystemNotificationMail($user, 'Async queued email test', 'https://example.com/test');

        $job = new SendTransactionalEmailJob($user->email, $mailable, null, null, null, 'default');
        $job->handle();

        Mail::assertQueued(SystemNotificationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->messageText === 'Async queued email test';
        });
    }

    public function test_send_transactional_email_job_dispatches_raw_email_without_exceptions(): void
    {
        Mail::fake();

        $jobInstance = new SendTransactionalEmailJob(
            'security-test@example.com',
            null,
            'MYADS - Security Verification',
            'Your verification code is: 123456',
            null,
            'high'
        );

        // Assert job executes cleanly
        $this->assertSame('high', $jobInstance->queue);
        $this->assertSame(3, $jobInstance->tries);
        $jobInstance->handle();
        $this->assertTrue(true);
    }

    public function test_notification_service_queues_email_notification(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'receiver@example.com',
        ]);

        $service = new NotificationService();
        $service->send($user, 'You have a new comment on your post', 'https://example.com/post/1', 'comment');

        Mail::assertQueued(SystemNotificationMail::class, function ($mail) use ($user) {
            return $mail->hasTo('receiver@example.com');
        });
    }

    public function test_admin_system_monitor_displays_queue_engine_diagnostics(): void
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get(route('admin.system_monitor'));

        $response->assertOk();
        $response->assertSee(__('messages.queue_engine_title', ['default' => 'Queue Engine & Background Workers']));
        $response->assertSee(__('messages.queue_channel_high', ['default' => 'High Priority']));
        $response->assertSee(__('messages.queue_channel_default', ['default' => 'Default Queue']));
        $response->assertSee(__('messages.queue_channel_media', ['default' => 'Media Channel']));
        $response->assertSee(__('messages.queue_channel_maintenance', ['default' => 'Maintenance']));
    }

    public function test_admin_can_retry_and_flush_failed_jobs(): void
    {
        $admin = $this->createSuperAdmin();

        // Test retry failed jobs endpoint
        $retryResponse = $this->actingAs($admin)->post(route('admin.system_monitor.queue_retry'));
        $retryResponse->assertRedirect(route('admin.system_monitor'));
        $retryResponse->assertSessionHas('success');

        // Test flush failed jobs endpoint
        $flushResponse = $this->actingAs($admin)->post(route('admin.system_monitor.queue_flush'));
        $flushResponse->assertRedirect(route('admin.system_monitor'));
        $flushResponse->assertSessionHas('success');
    }

    public function test_non_admin_cannot_access_queue_management_actions(): void
    {
        $regularUser = User::factory()->create([
            'id' => 888,
        ]);

        $retryResponse = $this->actingAs($regularUser)->post(route('admin.system_monitor.queue_retry'));
        $retryResponse->assertRedirect('/');

        $flushResponse = $this->actingAs($regularUser)->post(route('admin.system_monitor.queue_flush'));
        $flushResponse->assertRedirect('/');
    }

    private function createSuperAdmin(): User
    {
        $user = User::factory()->create([
            'id' => 1,
            'username' => 'rootsystem',
            'email' => 'root@example.test',
        ]);

        SiteAdmin::create([
            'user_id' => $user->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);

        return $user;
    }
}
