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
            ->assertSee('data-payment="bank_or_wallet"', false)
            ->assertSee('Bank transfer / E-wallet')
            ->assertDontSee('data-payment="bank_transfer"', false)
            ->assertDontSee('data-payment="e_wallet"', false)
            ->assertSee('data-payment="account_balance"', false)
            ->assertSee('data-payment="crypto"', false)
            ->assertSee('.payment-choice-icon svg { display:block; width:22px; height:22px; }', false)
            ->assertSee('.payment-choice-check svg { display:block; width:12px; height:12px; }', false)
            ->assertDontSee('.payment-choice span {', false)
            ->assertDontSee('1.5% fee');

        Cache::forget('usd_to_php_rate_meta_v2');
    }

    public function test_crunch_purchase_payment_sheet_combines_bank_and_wallet_choices(): void
    {
        $this->fakeUsdToPhpRate();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/invest/purchase/crunch')
            ->assertOk()
            ->assertSee('Purchase Silver Bond')
            ->assertSee('Choose payment method')
            ->assertSee('paymentModalAmount', false)
            ->assertSee('data-payment="bank_or_wallet"', false)
            ->assertSee('Choose bank transfer or e-wallet')
            ->assertSee('Choose BPI, LandBank, or Palawan Pay')
            ->assertSee('data-provider-name="BPI"', false)
            ->assertSee('data-bank-qr="'.asset('bpi.png').'"', false)
            ->assertSee('data-provider-name="LandBank"', false)
            ->assertSee('data-bank-qr="'.asset('LandBank.png').'"', false)
            ->assertSee('data-provider-name="PalawanPay"', false)
            ->assertSee('data-bank-qr="'.asset('PalawanPay.png').'"', false)
            ->assertDontSee('data-provider-name="BDO"', false)
            ->assertDontSee('data-provider-name="GCash"', false)
            ->assertSee('data-payment-method="bank_transfer"', false)
            ->assertSee('data-payment-method="e_wallet"', false)
            ->assertSee("paymentInput.value = button.dataset.paymentMethod;", false)
            ->assertDontSee('id="walletModal"', false)
            ->assertSee('continueToAgreement();', false);

        Cache::forget('usd_to_php_rate_meta_v2');
    }

    public function test_qr_payment_page_lists_banks_and_wallets_while_preserving_purchase_details(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/invest/payment/landbank?package=crunch&amount=129&currency=USD')
            ->assertOk()
            ->assertSee('Choose a payment method')
            ->assertSee('Banks')
            ->assertSee('E-wallets')
            ->assertSee('BPI')
            ->assertSee('LandBank')
            ->assertSee('Palawan Pay')
            ->assertDontSee('BDO')
            ->assertDontSee('UnionBank')
            ->assertDontSee('GCash')
            ->assertDontSee('Maya')
            ->assertDontSee('GrabPay')
            ->assertDontSee('ShopeePay')
            ->assertSee('Scan to pay')
            ->assertSee('class="method-button is-active"', false)
            ->assertSee('$129.00')
            ->assertDontSee('class="statusbar"', false)
            ->assertSee(route('invest.payment', [
                'provider' => 'palawanpay',
                'package' => 'crunch',
                'amount' => 129,
                'currency' => 'USD',
            ]))
            ->assertSee(route('invest.agreement.sign', [
                'package' => 'crunch',
                'amount' => 129,
                'currency' => 'USD',
                'payment_method' => 'bank_transfer',
            ]));

        $this->get('/invest/payment/palawanpay?package=crunch&amount=129&currency=USD')
            ->assertOk()
            ->assertSee('Palawan Pay')
            ->assertSee(route('invest.agreement.sign', [
                'package' => 'crunch',
                'amount' => 129,
                'currency' => 'USD',
                'payment_method' => 'e_wallet',
            ]));

        $providerThemes = [
            'landbank' => ['#006b3f', '#f5c542'],
            'bpi' => ['#005baa', '#d71920'],
            'palawanpay' => ['#078b4f', '#f4c542'],
        ];

        foreach ($providerThemes as $provider => [$color, $accent]) {
            $response = $this->get("/invest/payment/{$provider}?package=crunch&amount=129&currency=USD");

            $response
                ->assertOk()
                ->assertSee("--provider-color: {$color}", false)
                ->assertSee("--provider-accent: {$accent}", false);

            if ($provider === 'bpi') {
                $response->assertSee(asset('bpi.png'), false);
            } elseif ($provider === 'landbank') {
                $response->assertSee(asset('LandBank.png'), false);
            } elseif ($provider === 'palawanpay') {
                $response->assertSee(asset('PalawanPay.png'), false);
            }
        }

        $this->assertFileExists(public_path('bpi.png'));
        $this->assertFileExists(public_path('LandBank.png'));
        $this->assertFileExists(public_path('PalawanPay.png'));
        $this->get('/invest/payment/gcash?package=crunch&amount=129&currency=USD')
            ->assertNotFound();
    }

    public function test_direct_payment_methods_continue_to_the_replacement_agreement_signing_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/invest/agreement/sign?package=loaded&amount=799&currency=USD&payment_method=account_balance')
            ->assertOk()
            ->assertSee('Complete your agreement')
            ->assertSee('id="contractForm"', false)
            ->assertSee("submitButton.id = 'lulu-submit-purchase'", false)
            ->assertSee('"package_name":"Gold"', false)
            ->assertSee('agreement_signature_data')
            ->assertSee('agreement_accepted');
    }

    public function test_submitted_client_contract_is_saved_and_available_to_admin(): void
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($user)
            ->post('/investments', [
                'package' => 'crunch',
                'amount' => 129,
                'payment_method' => 'bank_transfer',
                'agreement_signature_name' => 'Test Client',
                'agreement_signature_data' => 'data:image/png;base64,YWJj',
                'agreement_accepted' => '1',
                'agreement_details' => [
                    'full_name' => 'Test Client',
                    'civil_status' => 'Single',
                    'civil_status_other' => '',
                    'address' => '12 Test Street, Manila',
                    'sign_date' => '2026-10-18',
                    'id_type' => 'Passport',
                    'id_type_other' => '',
                    'id_number' => 'P1234567',
                    'issue_date' => '2024-01-15',
                    'no_issue_date' => '0',
                ],
            ])
            ->assertRedirect('/dashboard');

        $investment = $user->investments()->firstOrFail();
        $this->assertSame('12 Test Street, Manila', $investment->agreement_details['address']);
        $this->assertSame('P1234567', $investment->agreement_details['id_number']);

        $this->actingAs($user)
            ->get(route('admin.contracts'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.contracts'))
            ->assertOk()
            ->assertSee('Client Contracts')
            ->assertSee('Test Client')
            ->assertSee(route('admin.contracts.show', $investment))
            ->assertSee(route('admin.contracts.show', ['investment' => $investment, 'print' => 1]))
            ->assertSee('Print');

        $this->get(route('admin.contracts.show', $investment))
            ->assertOk()
            ->assertSee('Client-submitted details')
            ->assertSee('12 Test Street, Manila')
            ->assertSee('P1234567')
            ->assertSee('window.LuluContractInitialData', false)
            ->assertSee('"civilStatus":"Single"', false)
            ->assertSee('"address":"12 Test Street, Manila"', false)
            ->assertSee('"idNumber":"P1234567"', false)
            ->assertSee('id="pageImage"', false)
            ->assertSee('id="previewOverlay"', false)
            ->assertSee('YWJj')
            ->assertDontSee('id="lulu-submit-purchase"', false);

        $this->get(route('admin.contracts.show', ['investment' => $investment, 'print' => 1]))
            ->assertOk()
            ->assertSee('window.LuluContractPrintOnLoad = true', false);
    }

    public function test_large_mobile_signature_is_saved_without_truncation(): void
    {
        $user = User::factory()->create();
        $signatureData = 'data:image/png;base64,'.str_repeat('A', 100_000);

        $this->actingAs($user)
            ->post('/investments', [
                'package' => 'crunch',
                'amount' => 129,
                'payment_method' => 'bank_transfer',
                'agreement_signature_data' => $signatureData,
                'agreement_accepted' => '1',
            ])
            ->assertRedirect('/dashboard');

        $this->assertSame(
            $signatureData,
            $user->investments()->firstOrFail()->agreement_signature_data,
        );
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
                'amount' => '7999.00',
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
                'amount' => '129.00',
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
