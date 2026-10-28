<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SignupBonusTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_claim_a_one_time_signup_bonus(): void
    {
        $user = User::factory()->create([
            'balance' => 0,
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user);
        $this->withSession(['pin_verified' => true]);

        $response = $this->post(route('rewards.claim-signup-bonus'));

        $response->assertRedirect();
        $this->assertEquals(5.0, (float) $user->fresh()->balance);
        $this->assertNotNull($user->fresh()->signup_bonus_claimed_at);

        $secondResponse = $this->post(route('rewards.claim-signup-bonus'));
        $secondResponse->assertRedirect();
        $this->assertEquals(5.0, (float) $user->fresh()->balance);
    }

    public function test_claimed_signup_bonus_appears_in_account_history(): void
    {
        $user = User::factory()->create([
            'balance' => 0,
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user);
        $this->withSession(['pin_verified' => true]);

        $this->post(route('rewards.claim-signup-bonus'));

        $response = $this->get(route('history'));

        $response->assertOk();
        $response->assertSee('Signup Bonus');

        $html = $response->getContent();
        $withdrawalsStart = strpos($html, 'id="history-withdrawals"');
        $interestStart = strpos($html, 'id="history-interest"');

        $this->assertNotFalse($withdrawalsStart);
        $this->assertNotFalse($interestStart);
        $withdrawalHistory = substr($html, $withdrawalsStart, $interestStart - $withdrawalsStart);
        $this->assertStringNotContainsString('Signup Bonus', $withdrawalHistory);
    }
}
