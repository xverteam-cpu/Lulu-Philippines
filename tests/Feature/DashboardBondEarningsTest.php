<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class DashboardBondEarningsTest extends TestCase
{
    use RefreshDatabase;

    public function test_bond_earnings_are_grouped_by_package_and_reflected_once_in_available_balance(): void
    {
        $user = User::factory()->create([
            'balance' => 50,
        ]);

        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 1000,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now()->subDays(2),
            'status' => 'approved',
            'interest_days_credited' => 1,
            'last_interest_accrued_at' => now()->subDay(),
        ]);

        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'loaded',
            'package_name' => 'Gold',
            'package_price' => 799,
            'amount' => 200,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 0.5,
            'duration_days' => 30,
            'starts_at' => now(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk()
            ->assertSee('Available Bonds')
            ->assertSee('EARNINGS')
            ->assertSee('Silver')
            ->assertSee('Gold')
            ->assertSee('Platinum')
            ->assertSee('$60.00')
            ->assertSee('$20.00')
            ->assertDontSee('$80.00');
    }

    public function test_dashboard_purchase_bonds_action_shows_advertisement_before_investing(): void
    {
        $user = User::factory()->create();

        $this->get(route('invest.advertisement'))
            ->assertRedirect(route('login'));

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee(route('invest.advertisement'), false);

        $this->get(route('invest.advertisement'))
            ->assertOk()
            ->assertSee(asset('images/lulu-bonds-investment-advertisement.png'), false)
            ->assertSee('href="'.route('invest').'"', false)
            ->assertSee('>Next</a>', false);

        $this->assertFileExists(public_path('images/lulu-bonds-investment-advertisement.png'));
    }

    public function test_approved_investments_without_starts_at_use_approved_at_for_accrual(): void
    {
        $user = User::factory()->create([
            'balance' => 0,
        ]);

        $investment = Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 100,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => null,
            'approved_at' => now()->subDays(2),
            'status' => 'approved',
        ]);

        $accrued = \App\Support\DailyInterestAccrualService::accrueDueInterestForUser($user);

        $this->assertGreaterThan(0, $accrued);
        $this->assertGreaterThan(0, (float) $investment->fresh()->interest_days_credited);
        $this->assertGreaterThan(0, (float) $user->fresh()->balance);
    }

    public function test_interest_starts_the_day_after_investment_and_history_shows_each_credited_day(): void
    {
        Carbon::setTestNow('2026-09-23 15:00:00');
        $user = User::factory()->create(['balance' => 0]);
        $investment = Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 129,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 0.7,
            'duration_days' => 30,
            'starts_at' => now(),
            'approved_at' => now(),
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('history'))
            ->assertOk()
            ->assertViewHas('dailyInterestEntries', fn ($entries) => $entries->isEmpty())
            ->assertViewHas('dailyInterest', 0.0);

        $this->assertSame(0, (int) $investment->fresh()->interest_days_credited);
        $this->assertSame(0.0, (float) $user->fresh()->balance);

        Carbon::setTestNow('2026-10-01 15:00:00');

        $this->get(route('history'))
            ->assertOk()
            ->assertViewHas('dailyInterest', 7.2)
            ->assertViewHas('dailyInterestEntries', function ($entries) {
                return $entries->count() === 8
                    && $entries->first()['date']->toDateString() === '2026-10-01'
                    && $entries->last()['date']->toDateString() === '2026-09-24'
                    && $entries->every(fn (array $entry) => $entry['amount'] === 0.9);
            })
            ->assertSee('Interest Earned To Date')
            ->assertSee('$7.20');

        $this->assertSame(8, (int) $investment->fresh()->interest_days_credited);
        $this->assertSame(7.2, (float) $user->fresh()->balance);
    }
}
