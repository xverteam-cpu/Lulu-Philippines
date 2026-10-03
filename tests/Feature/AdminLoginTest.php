<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_reach_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@lotteria.test',
            'password' => 'adminpassword',
            'is_admin' => true,
        ]);

        $loginResponse = $this->post('/login', [
            'email' => $user->email,
            'password' => 'adminpassword',
        ]);

        $loginResponse->assertRedirect('/pin/setup');

        $pinResponse = $this->post('/pin/setup', [
            'pin' => '1234',
        ]);

        $pinResponse->assertRedirect('/admin/dashboard');
        $this->assertTrue(Hash::check('1234', $user->fresh()->pin_hash));
    }

    public function test_admin_dashboard_shows_user_detail_modal_sections(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Jane Partner',
            'email' => 'jane@example.com',
            'last_ip_address' => '203.0.113.10',
            'region' => 'California',
            'address' => '123 Market St',
            'balance' => 150.25,
        ]);

        $user->investments()->create([
            'package_key' => 'crunch',
            'package_name' => 'Crunch',
            'package_price' => 100,
            'amount' => 100,
            'daily_interest_rate' => 2,
            'duration_days' => 30,
            'payment_method' => 'bank_transfer',
            'status' => 'approved',
            'starts_at' => now(),
        ]);

        $user->withdrawals()->create([
            'amount' => 40,
            'payment_method' => 'bank_transfer',
            'bank_name' => 'Test Bank',
            'account_number' => '123456',
            'account_holder' => 'Jane Partner',
            'status' => 'approved',
        ]);

        $user->referralEarnings()->create([
            'referred_user_id' => $user->id,
            'investment_id' => null,
            'amount' => 5,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Deposit / Investment History');
        $response->assertSee('Withdraw History');
        $response->assertSee('Income History');
        $response->assertSee('IP Location');
        $response->assertSee('data-user-online-status', false);
        $response->assertSee('data-online-users-count', false);
        $response->assertSee('user-activity', false);
        $response->assertSee('setInterval(refreshOnlineStatus, 30000)', false);
        $response->assertSee('id="userDetailsModalBody" class="modal-card"', false);
        $response->assertDontSee('<template id="userModal-'.$user->id.'"><div class="modal-card">', false);
        $response->assertSee("body.querySelector(':scope > .modal-card')", false);
        $response->assertSee('#userDetailsModalBody > .modal-card', false);
        $response->assertSee('View details');
        $response->assertSee('Block IP address');
        $response->assertSee('class="users-page"', false);
        $response->assertSee('class="users-page-header"', false);
        $response->assertSee('class="users-page-heading"', false);
        $response->assertSee('class="user-details-btn"', false);
        $response->assertSee('matching accounts');
        $response->assertDontSee('Admin Dashboard');
        $response->assertDontSee('class="summary-grid"', false);
        $response->assertDontSee('class="users-panel"', false);
        $response->assertSee('ACTIONS');
    }

    public function test_admin_dashboard_user_list_is_ordered_by_newest_accounts_first(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $oldUser = User::factory()->create([
            'name' => 'Oldest User',
            'email' => 'oldest@example.com',
            'created_at' => now()->subDays(7),
        ]);

        $newUser = User::factory()->create([
            'name' => 'Newest User',
            'email' => 'newest@example.com',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSeeInOrder(['Newest User', 'Oldest User']);
    }

    public function test_admin_user_search_suggestions_match_user_details(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Jane Partner',
            'email' => 'jane@example.com',
            'phone' => '555-0199',
        ]);

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->getJson(route('admin.user-search-suggestions', ['q' => 'jane']))
            ->assertOk()
            ->assertJsonPath('users.0.id', $user->id)
            ->assertJsonPath('users.0.name', 'Jane Partner')
            ->assertJsonPath('users.0.email', 'jane@example.com');

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->getJson(route('admin.user-search-suggestions', ['q' => '5']))
            ->assertOk()
            ->assertExactJson(['users' => []]);
    }

    public function test_non_admin_cannot_get_user_search_suggestions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('admin.user-search-suggestions', ['q' => 'ja']))
            ->assertForbidden();
    }

    public function test_admin_can_download_a_backup_snapshot(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Backup User',
            'email' => 'backup@example.com',
            'balance' => 125.5,
        ]);

        $user->investments()->create([
            'package_key' => 'crunch',
            'package_name' => 'Crunch',
            'package_price' => 100,
            'amount' => 100,
            'daily_interest_rate' => 2,
            'duration_days' => 30,
            'payment_method' => 'bank_transfer',
            'status' => 'approved',
            'starts_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post(route('admin.backup'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/json');
        $response->assertSee('Backup User');
        $response->assertSee('backup@example.com');
    }

    public function test_admin_can_update_package_slot_counts(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post('/admin/package-slots', [
                'slots' => [
                    'crunch' => 123,
                    'loaded' => 234,
                    'supreme' => 12,
                    'premium_plus' => 34,
                ],
            ]);

        $response->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('package_slots', [
            'package_key' => 'crunch',
            'remaining_slots' => 123,
        ]);

        $this->assertDatabaseHas('package_slots', [
            'package_key' => 'premium_plus',
            'remaining_slots' => 34,
        ]);
    }

    public function test_admin_can_delete_a_user_account_from_the_details_page(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $userToDelete = User::factory()->create([
            'name' => 'Delete Me',
            'email' => 'delete-me@example.com',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->delete(route('admin.users.destroy', $userToDelete));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('status', 'User account deleted successfully.');

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_restrict_a_user_from_accessing_the_site(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $user = User::factory()->create([
            'name' => 'Blocked User',
            'email' => 'blocked@example.com',
            'last_ip_address' => '203.0.113.10',
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post(route('admin.users.restrict', $user));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('status', 'User access restricted successfully.');

        $this->assertTrue($user->fresh()->is_restricted);
        $this->assertSame('203.0.113.10', $user->fresh()->restricted_ip_address);

        $blockedResponse = $this->actingAs($user->fresh())
            ->withSession(['pin_verified' => true])
            ->get('/dashboard');

        $blockedResponse->assertRedirect('/unavailable');
    }
}
