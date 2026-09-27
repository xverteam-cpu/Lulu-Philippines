<?php

namespace Tests\Feature;

use App\Models\BlockedIp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlockedIpTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_block_a_users_recorded_ip_for_all_website_visitors(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['last_ip_address' => '203.0.113.25']);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
            ->actingAs($admin)
            ->post(route('admin.users.block-ip', $user))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('blocked_ips', [
            'ip_address' => '203.0.113.25',
            'user_id' => $user->id,
            'blocked_by' => $admin->id,
        ]);

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.25'])
            ->get('/signup')
            ->assertForbidden();
    }

    public function test_admin_cannot_block_the_ip_address_they_are_using(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['last_ip_address' => '198.51.100.10']);

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
            ->actingAs($admin)
            ->post(route('admin.users.block-ip', $user))
            ->assertSessionHasErrors('ip_address');

        $this->assertDatabaseCount('blocked_ips', 0);
    }

    public function test_admin_can_unblock_a_previously_blocked_ip(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $blockedIp = BlockedIp::create([
            'ip_address' => '203.0.113.26',
            'blocked_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.blocked-ips.destroy', $blockedIp))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseMissing('blocked_ips', ['id' => $blockedIp->id]);
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.26'])
            ->get('/signup')
            ->assertOk();
    }

    public function test_non_admin_cannot_block_an_ip(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create(['last_ip_address' => '203.0.113.27']);

        $this->actingAs($user)
            ->post(route('admin.users.block-ip', $target))
            ->assertForbidden();

        $this->assertDatabaseCount('blocked_ips', 0);
    }
}
