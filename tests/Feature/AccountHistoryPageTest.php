<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountHistoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_history_displays_live_transactions_and_omits_decorative_status_bar(): void
    {
        $user = User::factory()->create([
            'signup_bonus_claimed_at' => now(),
        ]);

        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'loaded',
            'package_name' => 'Gold Fixed-Rate Bond',
            'package_price' => 799,
            'amount' => 100,
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now()->subDays(3),
            'status' => 'approved',
            'interest_days_credited' => 2,
            'last_interest_accrued_at' => now(),
        ]);

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 25,
            'payment_method' => 'bank_transfer',
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => 'History User',
            'status' => 'pending',
        ]);

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 5,
            'payment_method' => 'account_balance',
            'bank_name' => 'Welcome Bonus',
            'account_number' => 'signup-bonus',
            'account_holder' => 'Sign Up Bonus',
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->get(route('history'))
            ->assertOk()
            ->assertSee('class="history-phone"', false)
            ->assertSee('Account History')
            ->assertSee('Interest earned to date')
            ->assertSee('$2.00')
            ->assertSee('Gold Fixed-Rate Bond')
            ->assertSee('Investment History')
            ->assertSee('Withdrawal History')
            ->assertSee('Daily Interest History')
            ->assertSee('Rewards History')
            ->assertSee('$5 Sign Up Bonus')
            ->assertSee('Welcome reward')
            ->assertSee('Pending')
            ->assertDontSee('class="statusbar"', false)
            ->assertDontSee('id="clock"', false)
            ->assertDontSee('11:23 PM')
            ->assertDontSee('Wi-Fi');
    }

    public function test_guest_is_redirected_to_login_from_account_history(): void
    {
        $this->get(route('history'))
            ->assertRedirect(route('login'));
    }
}
