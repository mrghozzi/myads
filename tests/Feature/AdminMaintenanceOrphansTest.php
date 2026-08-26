<?php

namespace Tests\Feature;

use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Option;
use App\Models\Page;
use App\Models\State;
use App\Models\User;
use App\Services\OrphanCleanupService;
use App\Support\MaintenanceSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class AdminMaintenanceOrphansTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_repair_orphaned_records_does_not_delete_legitimate_content_reactions(): void
    {
        $this->seedSiteSettings();

        $admin = User::factory()->create(['id' => 1, 'username' => 'admin-user']);
        $user = User::factory()->create(['id' => 2, 'username' => 'regular-user']);

        // Create a forum topic
        $topic = ForumTopic::create([
            'uid' => $user->id,
            'name' => 'Test Topic Non User ID',
            'txt' => 'Sample content',
            'cat' => 1,
            'statu' => 1,
            'date' => time(),
        ]);

        // Create a store product
        $product = Option::create([
            'name' => 'Test Product',
            'o_type' => 'store',
            'o_parent' => $user->id,
            'o_order' => 10,
            'o_valuer' => 'Description',
        ]);

        // User reacts to the topic (type 2, sid = topic id)
        $topicLike = Like::create([
            'uid' => $user->id,
            'sid' => $topic->id,
            'type' => 2,
            'time_t' => time(),
        ]);

        // User reacts to the product (type 3, sid = product id)
        $productLike = Like::create([
            'uid' => $user->id,
            'sid' => $product->id,
            'type' => 3,
            'time_t' => time(),
        ]);

        // Create an orphaned follow record (sid = 7777 which does not exist in users)
        $orphanedFollow = Like::create([
            'uid' => $user->id,
            'sid' => 7777,
            'type' => 1,
            'time_t' => time(),
        ]);

        // Execute repair orphaned records via admin endpoint
        $response = $this->actingAs($admin)->post(route('admin.maintenance.repair_orphaned'));
        $response->assertRedirect()->assertSessionHas('success');

        // CRITICAL CHECK: Legitimate topic and product reactions MUST NOT be deleted!
        $this->assertDatabaseHas('like', [
            'id' => $topicLike->id,
            'sid' => $topic->id,
            'type' => 2,
        ]);

        $this->assertDatabaseHas('like', [
            'id' => $productLike->id,
            'sid' => $product->id,
            'type' => 3,
        ]);

        // Orphaned follow MUST be deleted
        $this->assertDatabaseMissing('like', [
            'id' => $orphanedFollow->id,
            'sid' => 7777,
            'type' => 1,
        ]);
    }

    public function test_repair_orphaned_content_cleans_only_orphans(): void
    {
        $this->seedSiteSettings();

        $admin = User::factory()->create(['id' => 1, 'username' => 'admin-user']);
        $user = User::factory()->create(['id' => 2, 'username' => 'regular-user']);

        // Legitimate product and comment
        $product = Option::create([
            'name' => 'Existing Product',
            'o_type' => 'store',
            'o_parent' => $user->id,
            'o_order' => 10,
        ]);

        $legitComment = Option::create([
            'name' => 'comment',
            'o_type' => 's_coment',
            'o_parent' => $product->id,
            'o_order' => $user->id,
            'o_valuer' => 'Legit Comment',
        ]);

        // Orphaned comment (parent product 99999 does NOT exist)
        $orphanComment = Option::create([
            'name' => 'comment',
            'o_type' => 's_coment',
            'o_parent' => 99999,
            'o_order' => $user->id,
            'o_valuer' => 'Orphan Comment',
        ]);

        // Reaction on orphaned comment
        $orphanCommentReaction = Like::create([
            'uid' => $user->id,
            'sid' => $orphanComment->id,
            'type' => 444,
            'time_t' => time(),
        ]);

        $orphanReactionOption = Option::create([
            'name' => 'like',
            'o_type' => 'data_reaction',
            'o_parent' => $orphanCommentReaction->id,
            'o_valuer' => 'like',
        ]);

        // Execute repair orphaned content
        $response = $this->actingAs($admin)->post(route('admin.maintenance.repair_orphaned_content'));
        $response->assertRedirect()->assertSessionHas('success');

        // Legit comment must remain
        $this->assertDatabaseHas('options', ['id' => $legitComment->id]);

        // Orphan comment & its reaction & data_reaction must be deleted
        $this->assertDatabaseMissing('options', ['id' => $orphanComment->id]);
        $this->assertDatabaseMissing('like', ['id' => $orphanCommentReaction->id]);
        $this->assertDatabaseMissing('options', ['id' => $orphanReactionOption->id]);
    }

    public function test_admin_can_save_extended_maintenance_settings(): void
    {
        $this->seedSiteSettings();

        $admin = User::factory()->create(['id' => 1, 'username' => 'admin-user']);

        $response = $this->actingAs($admin)->post(route('admin.maintenance.settings.update'), [
            'maintenance_enabled' => '1',
            'maintenance_message' => 'Custom maintenance in progress',
            'allowed_ips' => "127.0.0.1\n192.168.1.100",
            'emergency_token' => 'my-secret-token-999',
            'estimated_duration' => '45 minutes',
        ]);

        $response->assertRedirect(route('admin.maintenance'))->assertSessionHas('success');

        $settings = MaintenanceSettings::all();
        $this->assertEquals(1, $settings['enabled']);
        $this->assertEquals('Custom maintenance in progress', $settings['message']);
        $this->assertEquals("127.0.0.1\n192.168.1.100", $settings['allowed_ips']);
        $this->assertEquals('my-secret-token-999', $settings['emergency_token']);
        $this->assertEquals('45 minutes', $settings['estimated_duration']);
    }

    public function test_whitelisted_ip_can_access_site_during_maintenance(): void
    {
        $this->seedSiteSettings();

        MaintenanceSettings::save([
            'enabled' => 1,
            'allowed_ips' => '192.168.5.55, 10.0.0.1',
        ]);

        // Guest with whitelisted IP accessing a public content page should bypass 503
        $response = $this->withServerVariables(['REMOTE_ADDR' => '192.168.5.55'])->get('/terms');
        $this->assertNotEquals(503, $response->getStatusCode());

        // Guest with non-whitelisted IP should get 503
        $blockedResponse = $this->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])->get('/terms');
        $blockedResponse->assertStatus(503);
    }

    public function test_admin_can_prune_sessions_and_logs(): void
    {
        $this->seedSiteSettings();

        $admin = User::factory()->create(['id' => 1, 'username' => 'admin-user']);

        // Prune sessions
        $sessionResponse = $this->actingAs($admin)->post(route('admin.maintenance.prune_sessions'));
        $sessionResponse->assertRedirect()->assertSessionHas('success');

        // Prune logs
        $logsResponse = $this->actingAs($admin)->post(route('admin.maintenance.prune_logs'));
        $logsResponse->assertRedirect()->assertSessionHas('success');

        // Clear cache
        $cacheResponse = $this->actingAs($admin)->post(route('admin.maintenance.clear_cache'));
        $cacheResponse->assertRedirect()->assertSessionHas('success');
    }
}
