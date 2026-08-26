<?php

namespace Tests\Feature\Plugins;

use App\Models\User;
use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\SeedsSiteSettings;
use Tests\TestCase;

class MonetizationLicensingTest extends TestCase
{
    use RefreshDatabase;
    use SeedsSiteSettings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedSiteSettings();
    }

    private function createAdmin(): User
    {
        return User::factory()->create([
            'id' => 1,
            'username' => 'admin_user',
            'email' => 'admin@example.com',
            'pts' => 1000,
        ]);
    }

    private function createMember(): User
    {
        return User::factory()->create([
            'id' => 2,
            'username' => 'member_user',
            'email' => 'member@example.com',
            'pts' => 5000,
        ]);
    }

    /**
     * Test verification API endpoint.
     */
    public function test_api_license_verification_flow(): void
    {
        // Create product option so the verification API finds it
        $product = Option::create([
            'name' => 'monetization',
            'o_type' => 'store',
            'o_valuer' => 'Monetization Plugin',
            'o_order' => 100,
            'o_parent' => 1,
        ]);

        // 1. Missing fields should return validation error
        $response = $this->postJson('/api/license/verify', []);
        $response->assertStatus(422);

        // 2. Non-existent license should fail (returns 400 now that product exists)
        $response = $this->postJson('/api/license/verify', [
            'license_key' => 'ADSTN-MOCK-KEY-1234',
            'domain' => 'localhost',
            'plugin' => 'monetization',
        ]);
        $response->assertStatus(400);
        $response->assertJsonPath('success', false);

        // 3. Insert license into DB and verify successfully
        DB::table('product_licenses')->insert([
            'product_id' => $product->id,
            'user_id' => 2,
            'license_key' => 'ADSTN-MOCK-KEY-1234',
            'domain' => null, // not activated yet
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->postJson('/api/license/verify', [
            'license_key' => 'ADSTN-MOCK-KEY-1234',
            'domain' => 'localhost',
            'plugin' => 'monetization',
        ]);
        $response->assertOk();
        $response->assertJsonPath('success', true);
    }

    /**
     * Test store purchase generates license.
     */
    public function test_store_purchase_generates_license_key(): void
    {
        $member = $this->createMember();

        // Create product option
        $product = Option::create([
            'id' => 15,
            'name' => 'monetization',
            'o_type' => 'store',
            'o_valuer' => 'Monetization Plugin',
            'o_order' => 100, // price in PTS
            'o_parent' => 1, // publisher id
        ]);

        // Purchase and download via StoreController (POST route)
        $response = $this->actingAs($member)->post(route('store.download', $product->id));
        
        // Check database for generated license
        $license = DB::table('product_licenses')->where('user_id', $member->id)->where('product_id', $product->id)->first();
        $this->assertNotNull($license);
        $this->assertStringStartsWith('ADSTN-', $license->license_key);

        // Member's points should be deducted by 100
        $member->refresh();
        $this->assertEquals(4900, $member->pts);

        // Try downloading again - points should not be deducted again
        $response2 = $this->actingAs($member)->post(route('store.download', $product->id));
        $member->refresh();
        $this->assertEquals(4900, $member->pts); // Still 4900
    }

    /**
     * Test license guard and admin activation flows.
     */
    public function test_license_guard_and_activation_flows(): void
    {
        $admin = $this->createAdmin();
        $member = $this->createMember();

        // Enable plugin in options to boot it
        Option::updateOrCreate(
            ['name' => 'monetization', 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        // 1. Member requests monetization route -> returns 403 because no license is active
        $response = $this->actingAs($member)->get('/monetization');
        $response->assertStatus(403);

        // 2. Admin requests monetization settings route -> redirects to license activation page
        $response2 = $this->actingAs($admin)->get('/admin/monetization/settings');
        $response2->assertRedirect(route('admin.monetization.license'));

        // 3. Mock licensing API response for activation
        Http::fake([
            '*/api/license/verify' => Http::response([
                'success' => true,
                'message' => 'License activated successfully.',
            ], 200),
        ]);

        // 4. Admin submits the activation form
        $response3 = $this->actingAs($admin)->post('/admin/monetization/license/activate', [
            'license_key' => 'ADSTN-ACTIVATE-TEST',
        ]);
        $response3->assertRedirect(route('admin.monetization.settings'));

        // Assert database has saved the active license parameters
        $status = DB::table('options')->where('o_type', 'plugin_license')->where('name', 'monetization_status')->value('o_valuer');
        $key = DB::table('options')->where('o_type', 'plugin_license')->where('name', 'monetization_key')->value('o_valuer');
        $sig = DB::table('options')->where('o_type', 'plugin_license')->where('name', 'monetization_signature')->value('o_valuer');

        $this->assertEquals('active', $status);
        $this->assertEquals('ADSTN-ACTIVATE-TEST', $key);
        $this->assertEquals(hash('sha256', 'ADSTN-ACTIVATE-TESTlocalhostmyads_secret_salt'), $sig);

        // 5. Member requests monetization route again -> should pass through the guard successfully
        // We'll mock the index page to not crash (if it redirects or succeeds, we assert we don't get 403)
        $response4 = $this->actingAs($member)->get('/monetization');
        $response4->assertStatus(200);

        // 6. Admin settings page should also load fine now
        $response5 = $this->actingAs($admin)->get('/admin/monetization/settings');
        $response5->assertStatus(200);
    }
}
