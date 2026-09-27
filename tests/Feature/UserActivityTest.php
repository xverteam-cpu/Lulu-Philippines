<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function test_non_admin_cannot_read_live_user_activity(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('admin.user-activity'))
            ->assertForbidden();
    }
}
