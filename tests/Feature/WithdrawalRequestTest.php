<?php

namespace Tests\Feature;

use App\Mail\WithdrawalConfirmation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WithdrawalRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_must_provide_bank_details_before_requesting_withdrawal(): void
    {
        $user = User::factory()->create([
            'balance' => 500,
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user);
        $this->withSession(['pin_verified' => true]);

        $response = $this->from('/withdraw')->post('/withdrawals', [
            'amount' => 50,
        ]);

        $response->assertSessionHasErrors(['bank_name', 'account_number', 'account_holder']);
        $this->assertDatabaseCount('withdrawals', 0);
    }

    public function test_user_can_submit_withdrawal_request_with_bank_details(): void
    {
        $user = User::factory()->create([
            'balance' => 500,
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user);
        $this->withSession(['pin_verified' => true]);

        $response = $this->from('/withdraw')->post('/withdrawals', [
            'amount' => 50,
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => 'Test User',
        ]);

        $response->assertRedirect(route('withdraw'));
        $response->assertSessionHas('status', 'Withdrawal request submitted successfully.');
        $response->assertSessionHas('receipt.reference');
        $response->assertSessionHas('receipt.amount');
        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $user->id,
            'amount' => 50.00,
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => 'Test User',
            'status' => 'pending',
        ]);

        $this->assertSame('Test Bank', $user->fresh()->bank_name);
        $this->assertSame('1234567890', $user->fresh()->bank_account_number);
        $this->assertSame('Test User', $user->fresh()->bank_account_holder);
        $this->assertEquals(450.0, (float) $user->fresh()->balance);
    }

    public function test_user_cannot_withdraw_below_the_minimum_or_above_the_maximum(): void
    {
        $user = User::factory()->create([
            'balance' => 1000,
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user);
        $this->withSession(['pin_verified' => true]);

        $response = $this->from('/withdraw')->post('/withdrawals', [
            'amount' => 10,
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => 'Test User',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertEquals(1000.0, (float) $user->fresh()->balance);

        $response = $this->from('/withdraw')->post('/withdrawals', [
            'amount' => 600,
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => 'Test User',
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseCount('withdrawals', 0);
        $this->assertEquals(1000.0, (float) $user->fresh()->balance);
    }

    public function test_debug_route_can_send_a_sample_withdrawal_email(): void
    {
        Mail::fake();

        $response = $this->get(route('debug.test-email', ['email' => 'debug@example.com']));

        $response->assertRedirect(route('withdraw'));
        $response->assertSessionHas('status', 'Test withdrawal email sent.');

        Mail::assertSent(WithdrawalConfirmation::class, function (WithdrawalConfirmation $mail): bool {
            return $mail->hasTo('debug@example.com');
        });
    }
}
