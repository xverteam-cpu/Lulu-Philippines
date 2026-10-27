<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\IpLocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_heartbeat_keeps_an_open_user_session_online(): void
    {
        $user = User::factory()->create([
            'last_seen_at' => now()->subMinutes(6),
        ]);

        $this->actingAs($user)
            ->post(route('activity.heartbeat'))
            ->assertNoContent();

        $user->refresh();

        $this->assertTrue($user->isOnline());
        $this->assertTrue($user->last_seen_at->greaterThanOrEqualTo(now()->subSeconds(5)));
    }

    public function test_public_ip_location_is_saved_and_refreshed_at_most_daily(): void
    {
        $user = User::factory()->create();
        $this->mock(IpLocationService::class)
            ->shouldReceive('lookup')
            ->twice()
            ->with('8.8.8.8', (int) $user->id)
            ->andReturn('Manila, National Capital Region, Philippines');

        $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '8.8.8.8'])
            ->withHeader('User-Agent', 'Mozilla/5.0 (Linux; Android 14; Pixel 8) AppleWebKit/537.36 Chrome/130.0.0.0 Mobile Safari/537.36')
            ->post(route('activity.heartbeat'))
            ->assertNoContent();

        $user->refresh();
        $this->assertSame('Manila, National Capital Region, Philippines', $user->ip_location);
        $this->assertNotNull($user->ip_location_updated_at);
        $this->assertSame('8.8.8.8', $user->last_ip_address);
        $this->assertSame('Android phone', $user->last_device);
        $this->post(route('activity.heartbeat'))->assertNoContent();
        $this->post(route('activity.heartbeat'))->assertNoContent();

        $this->travel(1)->days();
        $this->post(route('activity.heartbeat'))->assertNoContent();
    }

    public function test_private_ip_addresses_are_not_looked_up(): void
    {
        $user = User::factory()->create();
        $this->assertNull(app(IpLocationService::class)->lookup('127.0.0.1', (int) $user->id));

        $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->post(route('activity.heartbeat'))
            ->assertNoContent();

        $this->assertNull($user->fresh()->ip_location);
    }

    public function test_activity_heartbeat_detects_iphone_device_type(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->withHeader('User-Agent', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 Version/18.0 Mobile/15E148 Safari/604.1')
            ->post(route('activity.heartbeat'))
            ->assertNoContent();

        $this->assertSame('iPhone (iOS)', $user->fresh()->last_device);
    }

    public function test_location_is_unavailable_until_a_local_database_is_configured(): void
    {
        config(['services.geoip.database_path' => null]);

        $this->assertNull(app(IpLocationService::class)->lookup('8.8.8.8', 1));
    }

    public function test_admin_activity_endpoint_returns_only_users_with_recent_activity(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $onlineUser = User::factory()->create(['last_seen_at' => now()->subMinute()]);
        $offlineUser = User::factory()->create(['last_seen_at' => now()->subMinutes(6)]);

        $this->actingAs($admin)
            ->getJson(route('admin.user-activity'))
            ->assertOk()
            ->assertJsonPath('online_users_count', 2)
            ->assertJsonFragment(['online_user_ids' => [$admin->id, $onlineUser->id]])
            ->assertJsonMissing(['online_user_ids' => [$offlineUser->id]]);
    }

    public function test_admin_activity_endpoint_returns_live_last_seen_timestamps_for_requested_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['last_seen_at' => now()->subSeconds(20)]);
        $unrequestedUser = User::factory()->create(['last_seen_at' => now()]);

        $this->actingAs($admin)
            ->getJson(route('admin.user-activity', ['user_ids' => [$user->id]]))
            ->assertOk()
            ->assertJsonPath('last_seen_at.'.$user->id, $user->last_seen_at->toIso8601String())
            ->assertJsonMissingPath('last_seen_at.'.$unrequestedUser->id);
    }

    public function test_non_admin_cannot_read_live_user_activity(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('admin.user-activity'))
            ->assertForbidden();
    }
}
