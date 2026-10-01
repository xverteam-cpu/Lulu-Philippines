<?php

namespace Tests\Feature;

use App\Models\PackageSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class InvestmentPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_loaded_purchase_page_uses_live_package_details_and_preserves_the_agreement_flow(): void
    {
        $this->fakeUsdToPhpRate();
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/invest/purchase/loaded');

        $response->assertOk()
            ->assertSee('Purchase Gold Bond')
            ->assertSee('0.80% daily')
            ->assertSee('120 days')
            ->assertSee('$799.00')
            ->assertSee('$7,998.99')
            ->assertSee('Estimated at maturity')
            ->assertSee('Review your bond')
            ->assertSee('Estimated value at maturity')
            ->assertSee('reviewMaturityValue', false)
            ->assertSee('reviewMaturityDate', false)
            ->assertSee('invest/agreement/sign')
            ->assertDontSee('id="purchaseSignaturePad"')
            ->assertSee('Continue to payment')
            ->assertSee('Choose payment method')
            ->assertSee('id="paymentModalAmount"', false)
            ->assertSee('data-payment="bank_transfer"', false)
            ->assertSee('data-payment="e_wallet"', false)
            ->assertSee('data-payment="account_balance"', false)
            ->assertSee('data-payment="crypto"', false)
            ->assertSee('.payment-choice-icon svg { display:block; width:22px; height:22px; }', false)
            ->assertSee('.payment-choice-check svg { display:block; width:12px; height:12px; }', false)
            ->assertDontSee('.payment-choice span {', false)
            ->assertDontSee('1.5% fee');

        Cache::forget('usd_to_php_rate_meta_v2');
    }

    public function test_crunch_purchase_payment_sheet_continues_to_existing_payment_choices(): void
    {
        $this->fakeUsdToPhpRate();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/invest/purchase/crunch')
            ->assertOk()
            ->assertSee('Purchase Silver Bond')
            ->assertSee('Choose payment method')
            ->assertSee('paymentModalAmount', false)
            ->assertSee('data-payment="bank_transfer"', false)
            ->assertSee('data-payment="e_wallet"', false)
            ->assertSee('paymentInput.value === \'bank_transfer\'', false)
            ->assertSee('paymentInput.value === \'e_wallet\'', false)
            ->assertSee('continueToAgreement();', false);

        Cache::forget('usd_to_php_rate_meta_v2');
    }

    public function test_direct_payment_methods_continue_to_the_existing_agreement_signing_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/invest/agreement/sign?package=loaded&amount=799&currency=USD&payment_method=account_balance')
            ->assertOk()
            ->assertSee('Gold')
            ->assertSee('agreement_signature_data')
            ->assertSee('agreement_accepted')
            ->assertSee('Sign agreement and submit investment');
    }

    public function test_bank_transfer_investment_is_created_as_pending_and_returns_receipt_payload(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
        ]);

        $response = $this->actingAs($user)
            ->withSession(['pin_verified' => true])
            ->post('/investments', [
                'package' => 'crunch',
                'amount' => 129,
                'payment_method' => 'bank_transfer',
                'agreement_signature_data' => 'data:image/png;base64,YWJj',
                'agreement_accepted' => '1',
            ], [
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('investment.status', 'pending')
            ->assertJsonPath('investment.payment_method', 'bank_transfer')
            ->assertJsonPath('investment.package_name', 'Silver');

        $this->assertDatabaseHas('investments', [
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);
    }

    public function test_php_amount_is_converted_to_usd_before_investment_is_stored(): void
    {
        $this->fakeUsdToPhpRate();
        $user = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'balance' => 5000,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['pin_verified' => true])
            ->post('/investments', [
                'package' => 'crunch',
                'amount' => 8000,
                'currency' => 'PHP',
                'payment_method' => 'account_balance',
                'agreement_signature_data' => 'data:image/png;base64,YWJj',
                'agreement_accepted' => '1',
            ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('investments', [
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'amount' => 130.48,
            'status' => 'approved',
            'payment_method' => 'account_balance',
        ]);
        $investment = $user->investments()->firstOrFail();
        $this->assertEquals(5000 - (float) $investment->amount, (float) $user->fresh()->balance);
    }

    public function test_account_balance_activation_reduces_package_slots(): void
    {
        $user = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'balance' => 5000,
        ]);

        $response = $this->actingAs($user)
            ->withSession(['pin_verified' => true])
            ->post('/investments', [
                'package' => 'loaded',
                'amount' => 800,
                'payment_method' => 'account_balance',
                'agreement_signature_data' => 'data:image/png;base64,YWJj',
                'agreement_accepted' => '1',
            ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('investments', [
            'user_id' => $user->id,
            'package_key' => 'loaded',
            'status' => 'approved',
            'payment_method' => 'account_balance',
        ]);

        $this->assertDatabaseHas('package_slots', [
            'package_key' => 'loaded',
            'remaining_slots' => 249,
        ]);
    }

    public function test_admin_send_package_activation_reduces_package_slots(): void
    {
        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $target = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post('/admin/send-package', [
                'user_id' => $target->id,
                'package' => 'supreme',
            ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertDatabaseHas('investments', [
            'user_id' => $target->id,
            'package_key' => 'supreme',
            'status' => 'approved',
            'payment_method' => 'admin_transfer',
        ]);

        $this->assertDatabaseHas('package_slots', [
            'package_key' => 'supreme',
            'remaining_slots' => 249,
        ]);
    }

    public function test_admin_send_package_triggers_referral_commission_and_starts_immediately(): void
    {
        $referrer = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
        ]);

        $user = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'referred_by' => $referrer->id,
        ]);

        $admin = User::factory()->create([
            'pin_hash' => Hash::make('123456'),
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post('/admin/send-package', [
                'user_id' => $user->id,
                'package' => 'crunch',
            ]);

        $response->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('investments', [
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'status' => 'approved',
            'payment_method' => 'admin_transfer',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $referrer->id,
            'balance' => 6.45,
        ]);
    }

    private function fakeUsdToPhpRate(): void
    {
        Cache::forget('usd_to_php_rate_meta_v2');
        Http::fake([
            'api.frankfurter.dev/v1/latest*' => Http::response([
                'date' => '2026-06-15',
                'rates' => ['PHP' => 61.31],
            ]),
        ]);
    }
}
