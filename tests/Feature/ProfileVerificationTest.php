<?php

namespace Tests\Feature;

use App\Models\Like;
use App\Models\Notification;
use App\Models\ProfileVerificationRequest;
use App\Models\SiteAdmin;
use App\Models\User;
use App\Services\ProfileVerificationService;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class ProfileVerificationTest extends TestCase
{
    use RefreshDatabase, SeedsSiteSettings;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
        $this->admin = User::factory()->create(['id' => 1, 'username' => 'superadmin']);
        SiteAdmin::create([
            'user_id' => $this->admin->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);
    }

    private function eligibleMember(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge([
            'username' => 'eligible-member',
            'email_verified_at' => now(),
        ], $attributes));
        $user->created_at = now()->subDays(45);
        $user->save();
        return $user;
    }

    private function openRequests(int $minAge = 30, int $minFollowers = 2): void
    {
        $settings = app(ProfileVerificationService::class);
        $settings->set('enabled', '1');
        $settings->set('require_verified_email', '1');
        $settings->set('min_account_age_days', $minAge);
        $settings->set('min_followers_count', $minFollowers);
        $settings->set('terms', 'Verification terms are required for this test.');
    }

    private function follow(User $follower, User $target): void
    {
        Like::create(['uid' => $follower->id, 'sid' => $target->id, 'type' => 1, 'time_t' => time()]);
    }

    private function applicationData(): array
    {
        return [
            'reason' => 'I represent this public community and would like my profile identity reviewed.',
            'evidence_links' => ['https://example.com/about'],
            'accept_terms' => '1',
        ];
    }

    public function test_member_can_submit_when_all_eligibility_rules_are_met(): void
    {
        $this->openRequests();
        $member = $this->eligibleMember();
        $this->follow(User::factory()->create(), $member);
        $this->follow(User::factory()->create(), $member);

        $this->actingAs($member)->post(route('profile.verification.submit'), $this->applicationData())
            ->assertRedirect(route('profile.verification'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('profile_verification_requests', [
            'user_id' => $member->id,
            'status' => 'pending',
        ]);
    }

    public function test_member_cannot_submit_when_disabled_or_ineligible(): void
    {
        $member = $this->eligibleMember();
        $this->actingAs($member)->post(route('profile.verification.submit'), $this->applicationData())
            ->assertSessionHas('error');

        $this->openRequests(90, 5);
        $this->actingAs($member)->post(route('profile.verification.submit'), $this->applicationData())
            ->assertSessionHas('error');

        $unverified = $this->eligibleMember(['email_verified_at' => null]);
        $this->actingAs($unverified)->post(route('profile.verification.submit'), $this->applicationData())
            ->assertSessionHas('error');
    }

    public function test_pending_request_blocks_duplicates_and_rejected_request_can_be_resubmitted(): void
    {
        $this->openRequests(0, 0);
        $member = $this->eligibleMember();
        $this->actingAs($member)->post(route('profile.verification.submit'), $this->applicationData())->assertSessionHas('success');
        $this->actingAs($member)->post(route('profile.verification.submit'), $this->applicationData())->assertSessionHas('error');

        ProfileVerificationRequest::where('user_id', $member->id)->update(['status' => 'rejected']);
        $this->actingAs($member)->post(route('profile.verification.submit'), $this->applicationData())->assertSessionHas('success');
        $this->assertSame(2, ProfileVerificationRequest::where('user_id', $member->id)->count());
    }

    public function test_admin_approval_grants_badge_and_rejection_saves_reviewer_note(): void
    {
        $member = $this->eligibleMember();
        $approved = ProfileVerificationRequest::create(['user_id' => $member->id, 'reason' => 'Approval test reason.', 'status' => 'pending']);
        $this->actingAs($this->admin)->withSession(['security.admin_password_confirmed_at' => time()])
            ->post(route('admin.profile_verification.review', $approved), ['decision' => 'approved', 'reviewer_note' => 'Identity checked.'])
            ->assertRedirect();
        $this->assertSame(1, (int) $member->fresh()->ucheck);
        $this->assertDatabaseHas('profile_verification_requests', ['id' => $approved->id, 'status' => 'approved', 'reviewed_by' => $this->admin->id]);
        $approvedNotification = Notification::where('uid', $member->id)->latest('id')->firstOrFail();
        $this->assertSame('@profile_verification_approved', $approvedNotification->name);
        $this->assertSame(__('messages.profile_verification_approved'), $approvedNotification->display_name);

        $anotherMember = $this->eligibleMember(['username' => 'rejected-member']);
        $rejected = ProfileVerificationRequest::create(['user_id' => $anotherMember->id, 'reason' => 'Rejection test reason.', 'status' => 'pending']);
        $this->actingAs($this->admin)->withSession(['security.admin_password_confirmed_at' => time()])
            ->post(route('admin.profile_verification.review', $rejected), ['decision' => 'rejected', 'reviewer_note' => 'Please provide stronger links.'])
            ->assertRedirect();
        $this->assertSame(0, (int) $anotherMember->fresh()->ucheck);
        $this->assertDatabaseHas('profile_verification_requests', ['id' => $rejected->id, 'status' => 'rejected', 'reviewer_note' => 'Please provide stronger links.']);
        $rejectedNotification = Notification::where('uid', $anotherMember->id)->latest('id')->firstOrFail();
        $this->assertSame(__('messages.profile_verification_rejected'), $rejectedNotification->display_name);

        app()->setLocale('ar');
        $this->assertSame('تم رفض طلب توثيق ملفك الشخصي. راجع ملاحظة الإدارة وأعد التقديم عند الاستعداد.', $rejectedNotification->display_name);
        app()->setLocale('fa');
        $this->assertSame('Your profile verification request was declined. Please review the note and submit again when ready.', $rejectedNotification->display_name);
        app()->setLocale('en');
    }

    public function test_admin_can_save_eligibility_and_terms_and_non_admin_is_denied(): void
    {
        $this->actingAs($this->admin)->withSession(['security.admin_password_confirmed_at' => time()])
            ->post(route('admin.profile_verification.settings.update'), [
                'enabled' => '1', 'require_verified_email' => '1', 'min_account_age_days' => 14, 'min_followers_count' => 8,
            ])->assertRedirect()->assertSessionHas('success');
        $settings = app(ProfileVerificationService::class)->settings();
        $this->assertTrue($settings['enabled']);
        $this->assertSame(14, $settings['min_account_age_days']);
        $this->assertSame(8, $settings['min_followers_count']);

        $this->actingAs($this->admin)->withSession(['security.admin_password_confirmed_at' => time()])
            ->post(route('admin.profile_verification.terms.update'), ['locale' => 'en', 'terms' => 'These are the updated verification terms.'])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertSame('These are the updated verification terms.', app(ProfileVerificationService::class)->settings()['terms']);
        $this->assertNotEmpty(app(ProfileVerificationService::class)->termsForLocale('ar'));

        $member = User::factory()->create(['id' => 15]);
        $this->actingAs($member)->get(route('admin.profile_verification.settings'))->assertRedirect('/');
    }

    public function test_member_and_admin_verification_pages_render(): void
    {
        $member = $this->eligibleMember();
        $this->actingAs($member)->get(route('profile.verification'))->assertOk();

        $this->actingAs($this->admin)->withSession(['security.admin_password_confirmed_at' => time()])
            ->get(route('admin.profile_verification.requests'))->assertOk();
        $this->actingAs($this->admin)->withSession(['security.admin_password_confirmed_at' => time()])
            ->get(route('admin.profile_verification.settings'))->assertOk();
        $this->actingAs($this->admin)->withSession(['security.admin_password_confirmed_at' => time()])
            ->get(route('admin.profile_verification.terms'))->assertOk();
    }

    public function test_admin_dashboard_notification_counts_pending_requests_for_users_module_only(): void
    {
        $staff = User::factory()->create(['username' => 'user-manager']);
        SiteAdmin::create([
            'user_id' => $staff->id,
            'permissions' => ['users', 'dashboard', 'updates'],
            'is_active' => true,
            'has_full_access' => false,
            'is_super' => false,
            'created_by' => $this->admin->id,
        ]);
        $member = $this->eligibleMember();
        ProfileVerificationRequest::create(['user_id' => $member->id, 'reason' => 'Pending request for admin dashboard.', 'status' => 'pending']);

        $notifications = app(AdminNotificationService::class)->getNotifications($staff);
        $verification = collect($notifications)->firstWhere('id', 'profile_verification');

        $this->assertNotNull($verification);
        $this->assertSame(1, $verification['count']);
        $this->assertSame(route('admin.profile_verification.requests'), $verification['url']);
        $this->actingAs($staff)->withSession(['security.admin_password_confirmed_at' => time()])
            ->get(route('admin.profile_verification.requests'))
            ->assertOk()
            ->assertSee(__('messages.new_profile_verification_requests', ['count' => 1]));
        $this->actingAs($staff)->withSession(['security.admin_password_confirmed_at' => time()])->followingRedirects()
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee(__('messages.new_profile_verification_requests', ['count' => 1]));

        ProfileVerificationRequest::where('user_id', $member->id)->update(['status' => 'approved']);
        $notifications = app(AdminNotificationService::class)->getNotifications($staff);
        $this->assertNull(collect($notifications)->firstWhere('id', 'profile_verification'));
    }
}
