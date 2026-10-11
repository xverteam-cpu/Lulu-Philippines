<?php

namespace Tests\Feature;

use App\Models\AccountActivity;
use App\Models\IdentityVerification;
use App\Models\Investment;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_center_shows_account_transactions_interest_and_verification_updates(): void
    {
        $user = User::factory()->create();
        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 100,
            'amount' => 100,
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now(),
            'payment_method' => 'bank_transfer',
            'status' => 'approved',
            'approved_at' => now(),
            'interest_days_credited' => 1,
        ]);
        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 25,
            'payment_method' => 'bank_transfer',
            'status' => 'approved',
            'approved_at' => now(),
        ]);
        IdentityVerification::create([
            'user_id' => $user->id,
            'id_type' => 'national_id',
            'status' => 'verified',
            'submitted_at' => now()->subDay(),
            'reviewed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notifications')
            ->assertSee('Daily interest credited')
            ->assertSee('Deposit approved')
            ->assertSee('Withdrawal approved')
            ->assertSee('Identity verification approved')
            ->assertSee('$1.00')
            ->assertSee('aria-label="Notifications, 4 unread"', false)
            ->assertSee('name="ids[]"', false);
    }

    public function test_notification_center_marks_the_displayed_items_as_read(): void
    {
        $user = User::factory()->create();
        $activity = AccountActivity::create([
            'user_id' => $user->id,
            'type' => 'funds_received',
            'title' => 'Funds received',
            'description' => 'Funds were added to your account by the LuLu team.',
            'amount' => 50,
            'occurred_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read_all'), [
                'ids' => ['activity-'.$activity->id],
            ])
            ->assertRedirect(route('notifications.index'))
            ->assertSessionHas('status', 'Notifications marked as read.');

        $this->assertContains('activity-'.$activity->id, $user->fresh()->getNotificationsReadIds());
    }

    public function test_interest_and_admin_sent_funds_are_recorded_as_user_activities(): void
    {
        $user = User::factory()->create([
            'balance' => 0,
        ]);
        $investment = Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 100,
            'amount' => 100,
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now()->subDays(2),
            'status' => 'approved',
            'payment_method' => 'account_balance',
        ]);

        $investment->accrueDailyInterest();

        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)
            ->post(route('admin.send-funds'), [
                'user_id' => $user->id,
                'amount' => 25,
                'reason' => 'Account credit',
            ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('investments', [
            'id' => $investment->id,
            'interest_days_credited' => 2,
        ]);
        $this->assertDatabaseHas('account_activities', [
            'user_id' => $user->id,
            'type' => 'funds_received',
            'title' => 'Funds received',
            'amount' => '25.00',
        ]);
    }
}
