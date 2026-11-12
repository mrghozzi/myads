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
        $response->assertSee('150');
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

    public function test_history_page_filters_by_credits_and_debits(): void
    {
        $this->seedSiteSettings();
        $user = User::factory()->create();

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => 80,
            'balance_after' => 180,
            'type' => 'ad_visit',
            'description_key' => 'bonus_credit_entry',
        ]);

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => -30,
            'balance_after' => 150,
            'type' => 'ad_campaign',
            'description_key' => 'campaign_debit_entry',
        ]);

        // Filter: Credits only
        $creditsResponse = $this->actingAs($user)->get(route('profile.history', ['filter' => 'credits']));
        $creditsResponse->assertOk();
        $creditsResponse->assertSee('bonus_credit_entry');
        $creditsResponse->assertDontSee('campaign_debit_entry');

        // Filter: Debits only
        $debitsResponse = $this->actingAs($user)->get(route('profile.history', ['filter' => 'debits']));
        $debitsResponse->assertOk();
        $debitsResponse->assertSee('campaign_debit_entry');
        $debitsResponse->assertDontSee('bonus_credit_entry');
    }

    public function test_history_page_filters_by_keyword_search(): void
    {
        $this->seedSiteSettings();
        $user = User::factory()->create();

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => 10,
            'balance_after' => 110,
            'type' => 'reward',
            'description_key' => 'unique_quest_complete',
        ]);

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => 25,
            'balance_after' => 135,
            'type' => 'reward',
            'description_key' => 'another_standard_bonus',
        ]);

        $searchResponse = $this->actingAs($user)->get(route('profile.history', ['search' => 'unique_quest']));
        $searchResponse->assertOk();
        $searchResponse->assertSee('unique_quest_complete');
        $searchResponse->assertDontSee('another_standard_bonus');
    }

    public function test_history_page_handles_ajax_requests_returning_json(): void
    {
        $this->seedSiteSettings();
        $user = User::factory()->create();

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => 50,
            'balance_after' => 150,
            'type' => 'reward',
            'description_key' => 'ajax_test_reward',
        ]);

        $response = $this->actingAs($user)->getJson(route('profile.history'));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'html',
            'positive_total',
            'negative_total',
            'total_count',
            'current_page',
            'last_page',
        ]);
        $response->assertJson([
            'success' => true,
            'positive_total' => '50',
            'total_count' => 1,
        ]);
    }

    public function test_history_statement_csv_export(): void
    {
        $this->seedSiteSettings();
        $user = User::factory()->create();

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => 100,
            'balance_after' => 200,
            'type' => 'transfer_received',
            'description_key' => 'pts_transfer_received',
        ]);

        PointTransaction::create([
            'user_id' => $user->id,
            'amount' => -40,
            'balance_after' => 160,
            'type' => 'transfer_sent',
            'description_key' => 'pts_transfer_sent',
        ]);

        $response = $this->actingAs($user)->get(route('profile.history.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('attachment;', (string) $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Running Balance (PTS)', $content);
        $this->assertStringContainsString('Amount (PTS)', $content);
        $this->assertStringContainsString('transfer_received', $content);
        $this->assertStringContainsString('transfer_sent', $content);
        $this->assertStringContainsString('100.00', $content);
        $this->assertStringContainsString('200.00', $content);
        $this->assertStringContainsString('-40.00', $content);
        $this->assertStringContainsString('160.00', $content);
    }
}
