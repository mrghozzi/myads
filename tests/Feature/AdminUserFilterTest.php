<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->admin = User::factory()->create([
            'ucheck' => 1,
            'pass' => Hash::make('password'),
        ]);
    }

    /** @test */
    public function it_can_filter_users_by_online_status_online()
    {
        // Create an online user
        $onlineUser = User::factory()->create([
            'username' => 'user_on',
            'online' => time(), // Current time
        ]);

        // Create an offline user
        $offlineUser = User::factory()->create([
            'username' => 'user_off',
            'online' => time() - 1000, // Older than 240 seconds
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users', ['online' => '1']));

        $response->assertStatus(200);
        $response->assertSee('user_on');
        $response->assertDontSee('user_off');
    }

    /** @test */
    public function it_can_filter_users_by_online_status_offline()
    {
        // Create an online user
        $onlineUser = User::factory()->create([
            'username' => 'user_on',
            'online' => time(),
        ]);

        // Create an offline user
        $offlineUser = User::factory()->create([
            'username' => 'user_off',
            'online' => time() - 1000,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users', ['online' => '0']));

        $response->assertStatus(200);
        $response->assertSee('user_off');
        $response->assertDontSee('user_on');
    }

    /** @test */
    public function it_can_filter_users_by_verification_status_verified()
    {
        // Create a verified user (ucheck = 1)
        $verifiedUser = User::factory()->create([
            'username' => 'user_v',
            'ucheck' => 1,
        ]);

        // Create an unverified user (ucheck != 1)
        $unverifiedUser = User::factory()->create([
            'username' => 'user_u',
            'ucheck' => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users', ['verified' => '1']));

        $response->assertStatus(200);
        $response->assertSee('user_v');
        $response->assertDontSee('user_u');
    }

    /** @test */
    public function it_can_filter_users_by_verification_status_unverified()
    {
        // Create a verified user (ucheck = 1)
        $verifiedUser = User::factory()->create([
            'username' => 'user_v',
            'ucheck' => 1,
        ]);

        // Create an unverified user (ucheck != 1)
        $unverifiedUser = User::factory()->create([
            'username' => 'user_u',
            'ucheck' => 0,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users', ['verified' => '0']));

        $response->assertStatus(200);
        $response->assertSee('user_u');
        $response->assertDontSee('user_v');
    }
}
