<?php

namespace Tests\Feature;

use App\Models\SiteAdmin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AdminUsersAjaxTest extends TestCase
{
    use RefreshDatabase, SeedsSiteSettings;

    private User $admin;
    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
        $this->admin = User::factory()->create(['id' => 1, 'username' => 'superadmin', 'ucheck' => 1, 'pts' => 100]);
        SiteAdmin::create([
            'user_id' => $this->admin->id,
            'permissions' => ['*'],
            'is_active' => true,
            'has_full_access' => true,
            'is_super' => true,
            'created_by' => 1,
        ]);

        $this->member = User::factory()->create(['id' => 2, 'username' => 'testmember', 'ucheck' => 0, 'pts' => 20]);
    }

    public function test_quick_update_toggles_verification(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.users.quick_update', $this->member->id), [
                'action' => 'toggle_verification',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'ucheck' => 1,
            ]);

        $this->assertEquals(1, $this->member->fresh()->ucheck);
    }

    public function test_quick_update_adjusts_balances(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.users.quick_update', $this->member->id), [
                'action' => 'adjust_balances',
                'mode' => 'increment',
                'pts' => 50,
                'vu' => 10,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals(70, (float)$this->member->fresh()->pts);
    }

    public function test_quick_details_user_returns_dossier(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->getJson(route('admin.users.details', $this->member->id));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'user' => [
                    'id' => $this->member->id,
                    'username' => 'testmember',
                ],
            ]);
    }

    public function test_notify_user_dispatches_notification(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.users.notify', $this->member->id), [
                'message' => 'Important admin test notification',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_bulk_action_verifies_and_adds_points(): void
    {
        // Bulk verify
        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.users.bulk_action'), [
                'ids' => [$this->member->id],
                'action' => 'verify',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals(1, $this->member->fresh()->ucheck);

        // Bulk points
        $responsePoints = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->postJson(route('admin.users.bulk_action'), [
                'ids' => [$this->member->id],
                'action' => 'add_points',
                'amount' => 15,
            ]);

        $responsePoints->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals(35, (float)$this->member->fresh()->pts);
    }

    public function test_bulk_delete_deletes_users_and_safeguards_superadmin(): void
    {
        $user3 = User::factory()->create(['id' => 3, 'username' => 'tobedeleted']);

        $response = $this->actingAs($this->admin)
            ->withSession(['security.admin_password_confirmed_at' => time()])
            ->deleteJson(route('admin.users.bulk_delete'), [
                'ids' => [$this->admin->id, $user3->id],
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        // Superadmin must NOT be deleted
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
        // user3 must be deleted
        $this->assertDatabaseMissing('users', ['id' => 3]);
    }
}
