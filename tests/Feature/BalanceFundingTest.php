<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BalanceFundingTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_start_with_zero_balance_and_signup_bonus_routes_are_removed(): void
    {
        $user = User::factory()->create();

        $this->assertSame(0.0, (float) $user->balance);
        $this->assertFalse(Route::has('rewards'));
        $this->assertFalse(Route::has('rewards.claim-signup-bonus'));

        $this->actingAs($user)
            ->get('/rewards')
            ->assertNotFound();

        $this->post('/rewards/claim-signup-bonus')
            ->assertStatus(405);

        $this->assertSame(0.0, (float) $user->fresh()->balance);
    }

    public function test_account_history_has_no_signup_bonus_offer(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('history'));

        $response->assertOk()
            ->assertDontSee('Sign Up Bonus')
            ->assertDontSee('Signup Bonus')
            ->assertDontSee('claim-signup-bonus');
    }
}
