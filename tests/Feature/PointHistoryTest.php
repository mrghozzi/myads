<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class PointHistoryTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    public function test_history_page_loads_successfully_for_authenticated_user(): void
    {
        $this->seedSiteSettings();
        $user = User::factory()->create();

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => 50,
            'balance_after' => 150,
            'type' => 'reward',
            'description_key' => 'test_reward',
        ]);

        $response = $this->actingAs($user)->get(route('profile.history'));

        $response->assertOk();
        $response->assertViewIs('theme::profile.history');
        $response->assertSee('test_reward');
    }

    public function test_history_page_does_not_crash_500_if_created_at_is_missing(): void
    {
        $this->seedSiteSettings();
        $user = User::factory()->create();

        // Drop created_at and updated_at to simulate the legacy/corrupted production schema
        if (Schema::hasColumn('point_transactions', 'created_at')) {
            Schema::table('point_transactions', function ($table) {
                $table->dropColumn(['created_at', 'updated_at']);
            });
        }

        $response = $this->actingAs($user)->get(route('profile.history'));

        $response->assertOk();
        $response->assertViewIs('theme::profile.history');
    }

    public function test_history_page_loads_with_empty_ledger_and_legacy_options(): void
    {
        $this->seedSiteSettings();
        $user = User::factory()->create();

        // User has NO point transactions, but HAS legacy option
        Option::create([
            'name' => 'legacy_reward',
            'o_valuer' => '100',
            'o_type' => 'hest_pts',
            'o_parent' => $user->id,
            'o_order' => 0,
            'o_mode' => time(),
        ]);

        $response = $this->actingAs($user)->get(route('profile.history'));

        $response->assertOk();
        $response->assertViewIs('theme::profile.history');
        $response->assertSee('legacy_reward');
    }
}
