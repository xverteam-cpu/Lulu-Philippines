<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_referral_page_displays_the_users_live_referral_summary_and_partners(): void
    {
        $referrer = User::factory()->create([
            'name' => 'Referral Owner',
            'username' => 'referral-owner',
        ]);
        $partner = User::factory()->create([
            'name' => 'Referred Partner',
            'username' => 'partner-one',
            'email' => 'partner@example.com',
            'referred_by' => $referrer->id,
        ]);
        $otherUser = User::factory()->create([
            'name' => 'Unrelated User',
        ]);

        Investment::create([
            'user_id' => $partner->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 250,
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now(),
        ]);

        Investment::create([
            'user_id' => $otherUser->id,
            'package_key' => 'loaded',
            'package_name' => 'Gold',
            'package_price' => 799,
            'amount' => 999,
            'daily_interest_rate' => 0.8,
            'duration_days' => 120,
            'starts_at' => now(),
        ]);

        $referrer->referralEarnings()->create([
            'referred_user_id' => $partner->id,
            'investment_id' => null,
            'amount' => 12.5,
        ]);

        $this->actingAs($referrer)
            ->get(route('referrals'))
            ->assertOk()
            ->assertSee('class="referral-phone"', false)
            ->assertSee(route('signup', ['ref' => 'referral-owner']), false)
            ->assertSee('$12.50')
            ->assertSee('Invited partners')
            ->assertSee('>1</div>', false)
            ->assertSee('Referred Partner')
            ->assertSee('partner-one')
            ->assertSee('partner@example.com')
            ->assertSee('Invested: $250.00')
            ->assertSee('5%')
            ->assertDontSee('Unrelated User')
            ->assertDontSee('Invested: $999.00');
    }

    public function test_guest_is_redirected_to_login_from_the_referral_page(): void
    {
        $this->get(route('referrals'))
            ->assertRedirect(route('login'));
    }
}
