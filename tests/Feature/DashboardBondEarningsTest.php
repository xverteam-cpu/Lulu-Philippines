<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
