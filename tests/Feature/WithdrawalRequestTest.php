<?php

namespace Tests\Feature;

use App\Mail\WithdrawalConfirmation;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WithdrawalRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_withdraw_page_uses_reference_layout_and_live_withdrawal_data(): void
    {
        $user = User::factory()->create([
            'balance' => 284.77,
            'bank_name' => 'BPI',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Withdrawal Client',
            'withdrawal_account_type' => 'bank',
            'pin_hash' => Hash::make('1234'),
        ]);

        Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 25,
            'payment_method' => 'bank_transfer',
            'bank_name' => 'BPI',
            'account_number' => '1234567890',
            'account_holder' => 'Withdrawal Client',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->withSession(['pin_verified' => true])
            ->get(route('withdraw'))
            ->assertOk()
            ->assertSee('Withdraw Funds')
            ->assertSee('$284.77')
            ->assertSee('data-withdrawal-amount="20"', false)
            ->assertSee('Saved bank account')
            ->assertSee('BPI · ••••7890')
            ->assertSee('$25.00 · BPI')
            ->assertSee('Pending')
            ->assertSee(route('withdrawals.store'), false)
            ->assertSee(route('history'), false)
            ->assertSee('safe-area-inset-top', false)
            ->assertSee('name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"', false)
            ->assertDontSee('1234567890</span>');
    }

    public function test_admin_withdrawal_details_does_not_render_detached_css_as_page_text(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create();
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 25,
            'payment_method' => 'bank_transfer',
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => $user->name,
            'status' => 'rejected',
        ]);

        $this->actingAs($admin)
            ->withSession(['status' => 'Withdrawal rejected successfully.'])
            ->get(route('admin.withdrawals.show', $withdrawal))
            ->assertOk()
            ->assertSee('Withdrawal rejected successfully.')
            ->assertSee('.grid-2 {', false)
            ->assertSee('.alert-error {', false)
            ->assertDontSee('background:#dcfce7;')
            ->assertDontSee('</style>background:#dcfce7;', false);
    }

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

    public function test_withdrawal_email_uses_the_users_details_and_shows_the_five_percent_net_amount(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'name' => 'Profile Client Name',
            'balance' => 500,
            'pin_hash' => Hash::make('1234'),
        ]);

        $this->actingAs($user);
        $this->withSession(['pin_verified' => true]);
        $this->post('/withdrawals', [
            'amount' => 125,
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => 'Different Account Holder',
        ]);

        $withdrawal = Withdrawal::query()->firstOrFail();

        Mail::assertSent(WithdrawalConfirmation::class, function (WithdrawalConfirmation $mail) use ($user, $withdrawal): bool {
            $html = $mail->render();

            $this->assertTrue($mail->hasTo($user->email));
            $this->assertStringContainsString('withdrawal request has been received successfully', $html);
            $this->assertStringNotContainsString('Withdrawal Successful', $html);
            $this->assertStringContainsString('data:image/jpeg;base64,', $html);
            $this->assertStringContainsString('Profile Client Name', $html);
            $this->assertStringContainsString((string) $withdrawal->transaction_reference, $html);
            $this->assertStringContainsString($withdrawal->created_at->format('F j, Y'), $html);
            $this->assertStringContainsString('Pending', $html);
            $this->assertStringContainsString('Test Bank', $html);
            $this->assertStringContainsString('1234567890', $html);
            $this->assertStringNotContainsString('Dwell Realty', $html);
            $this->assertStringNotContainsString('SD5461SFDF', $html);
            $this->assertStringContainsString('$125.00', $html);
            $this->assertStringContainsString('-$6.25', $html);
            $this->assertStringContainsString('$118.75', $html);

            return true;
        });
    }

    public function test_approval_sends_the_supplied_success_image_template_with_current_withdrawal_details(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['name' => 'Approved Client']);
        $withdrawal = Withdrawal::create([
            'user_id' => $user->id,
            'amount' => 125,
            'transaction_reference' => 'WD-APPROVED-TEST',
            'processing_fee' => 6.25,
            'total_withdrawn' => 118.75,
            'payment_method' => 'bank_transfer',
            'bank_name' => 'Test Bank',
            'account_number' => '1234567890',
            'account_holder' => 'Approved Client',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.approve', $withdrawal))
            ->assertSessionHas('status', 'Withdrawal approved successfully.');

        $this->assertSame('approved', $withdrawal->fresh()->status);
        Mail::assertSent(WithdrawalConfirmation::class, function (WithdrawalConfirmation $mail) use ($user, $withdrawal): bool {
            $html = $mail->render();

            $this->assertTrue($mail->hasTo($user->email));
            $this->assertStringContainsString('Withdrawal successful', $html);
            $this->assertStringContainsString('Approved Client', $html);
            $this->assertStringContainsString('WD-APPROVED-TEST', $html);
            $this->assertStringContainsString($withdrawal->fresh()->approved_at->format('F j, Y'), $html);
            $this->assertStringContainsString('Approved', $html);
            $this->assertStringContainsString('data:image/jpeg;base64,', $html);
            $this->assertStringContainsString('$125.00', $html);
            $this->assertStringContainsString('-$6.25', $html);
            $this->assertStringContainsString('$118.75', $html);

            return true;
        });
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
