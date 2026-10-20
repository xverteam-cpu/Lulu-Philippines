<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use App\Support\DailyInterestAccrualService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminInvestmentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_can_delete_an_active_package_without_removing_credited_income_or_history(): void
    {
        Carbon::setTestNow('2026-10-20 12:00:00');

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['balance' => 2]);
        $investment = Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 100,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now()->subDays(3),
            'approved_at' => now()->subDays(3),
            'status' => 'approved',
            'interest_days_credited' => 2,
            'last_interest_accrued_at' => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Delete package')
            ->assertSee(route('admin.users.investments.destroy', [$user, $investment]), false);

        $this->delete(route('admin.users.investments.destroy', [$user, $investment]))
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('status');

        $investment->refresh();
        $this->assertNotNull($investment->cancelled_at);
        $this->assertSame(3, (int) $investment->interest_days_credited);
        $this->assertSame(3.0, (float) $user->fresh()->balance);
        $this->assertSame(3.0, $investment->earnedInterest());

        Carbon::setTestNow('2026-10-21 12:00:00');

        $this->assertSame(0.0, DailyInterestAccrualService::accrueDueInterestForUser($user->fresh()));
        $this->assertSame(3.0, (float) $user->fresh()->balance);

        $this->actingAs($user)
            ->get(route('history'))
            ->assertOk()
            ->assertSee('Deleted')
            ->assertViewHas('dailyInterestEntries', fn ($entries) => $entries->count() === 3);
    }

    public function test_non_admin_cannot_delete_a_users_package(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $investment = Investment::create([
            'user_id' => $owner->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 100,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now(),
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->delete(route('admin.users.investments.destroy', [$owner, $investment]))
            ->assertForbidden();

        $this->assertNull($investment->fresh()->cancelled_at);
    }
}
