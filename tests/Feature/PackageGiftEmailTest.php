<?php

namespace Tests\Feature;

use App\Mail\BondPurchaseApprovedEmail;
use App\Mail\PackageGiftedEmail;
use App\Models\Investment;
use App\Models\User;
use App\Support\DailyInterestAccrualService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PackageGiftEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_send_package_still_succeeds_when_email_delivery_fails(): void
    {
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('SMTP unavailable'));

        $admin = User::factory()->create(['is_admin' => true, 'pin_hash' => 'test-pin']);
        $recipient = User::factory()->create(['email' => 'recipient@example.com', 'balance' => 0]);

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true]);

        $response = $this->post(route('admin.send-package'), [
            'user_id' => $recipient->id,
            'package' => 'loaded',
            'amount' => '799.00',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertDatabaseHas('investments', [
            'user_id' => $recipient->id,
            'package_key' => 'loaded',
            'status' => 'approved',
            'payment_method' => 'admin_transfer',
        ]);
    }

    public function test_admin_send_package_is_saved_and_shown_to_user_with_invalid_email(): void
    {
        Mail::shouldReceive('to')->never();

        $admin = User::factory()->create(['is_admin' => true, 'pin_hash' => 'test-pin']);
        $recipient = User::factory()->create([
            'email' => 'not-a-valid-email',
            'balance' => 0,
        ]);

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post(route('admin.send-package'), [
                'user_id' => $recipient->id,
                'package' => 'loaded',
                'amount' => '799.00',
            ])
            ->assertRedirect('/admin/dashboard')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('investments', [
            'user_id' => $recipient->id,
            'package_key' => 'loaded',
            'amount' => 799.00,
            'status' => 'approved',
            'payment_method' => 'admin_transfer',
        ]);

        $this->assertSame(
            799.0,
            (float) Investment::query()
                ->where('user_id', $recipient->id)
                ->where('status', 'approved')
                ->whereNull('cancelled_at')
                ->sum('amount')
        );

        $this->actingAs($recipient)
            ->withSession(['investor_pin_verified' => true])
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('asset-readable">$799<span class="decimal">.00', false);
    }

    public function test_admin_gift_requires_a_full_client_signature_and_is_listed_as_a_contract(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true, 'pin_hash' => 'test-pin']);
        $recipient = User::factory()->create([
            'name' => 'Gifted Client',
            'email' => 'gifted@example.com',
        ]);

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post(route('admin.send-package'), [
                'user_id' => $recipient->id,
                'package' => 'loaded',
                'amount' => '799.00',
            ])
            ->assertRedirect('/admin/dashboard');

        $investment = Investment::query()->where('user_id', $recipient->id)->firstOrFail();
        $this->assertSame('admin_transfer', $investment->payment_method);
        $this->assertNull($investment->agreement_signed_at);
        $this->assertNull($investment->agreement_details);

        $this->actingAs($recipient)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Sign Bond Agreement')
            ->assertSee('class="agreement-notification-badge" aria-hidden="true">1</span>', false)
            ->assertSee(route('invest.agreement.sign', [
                'package' => 'loaded',
                'amount' => '799.00',
                'currency' => 'USD',
                'payment_method' => 'admin_transfer',
                'investment_id' => $investment->id,
            ]));

        $this->get(route('invest.agreement.sign', ['investment_id' => $investment->id]))
            ->assertOk()
            ->assertSee('Complete your agreement')
            ->assertSee('"payment_method":"admin_transfer"', false)
            ->assertSee('"investment_id":'.$investment->id, false);

        $signature = 'data:image/png;base64,YWJj';
        $this->post(route('investments.store'), [
            'investment_id' => $investment->id,
            'package' => 'loaded',
            'amount' => 799,
            'currency' => 'USD',
            'payment_method' => 'admin_transfer',
            'agreement_signature_data' => $signature,
            'agreement_accepted' => '1',
            'agreement_details' => [
                'full_name' => 'Gifted Client',
                'civil_status' => 'Single',
                'civil_status_other' => '',
                'address' => '12 Gift Street, Manila',
                'sign_date' => '2026-11-01',
                'id_type' => 'Passport',
                'id_type_other' => '',
                'id_number' => 'P7654321',
                'issue_date' => '2024-01-15',
                'no_issue_date' => '0',
            ],
        ])->assertRedirect('/dashboard');

        $investment->refresh();
        $this->assertNotNull($investment->agreement_signed_at);
        $this->assertSame('12 Gift Street, Manila', $investment->agreement_details['address']);
        $this->assertSame('P7654321', $investment->agreement_details['id_number']);
        $this->assertSame($signature, $investment->agreement_signature_data);

        $this->actingAs($admin)
            ->get(route('admin.contracts'))
            ->assertOk()
            ->assertSee('purchased or admin-gifted bonds')
            ->assertSee('Gifted Client')
            ->assertSee(route('admin.contracts.show', $investment));

        $this->get(route('admin.contracts.show', $investment))
            ->assertOk()
            ->assertSee('Client-submitted details')
            ->assertSee('12 Gift Street, Manila')
            ->assertSee('P7654321')
            ->assertSee('YWJj')
            ->assertSee('"address":"12 Gift Street, Manila"', false)
            ->assertSee('"idNumber":"P7654321"', false);
    }

    public function test_admin_approval_sends_a_package_gift_email_and_starts_daily_interest(): void
    {
        Mail::fake();
        Carbon::setTestNow(Carbon::parse('2026-07-01 09:00:00'));

        $admin = User::factory()->create(['is_admin' => true, 'pin_hash' => 'test-pin']);
        $recipient = User::factory()->create(['email' => 'recipient@example.com', 'balance' => 0]);

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true]);

        $response = $this->post(route('admin.send-package'), [
            'user_id' => $recipient->id,
            'package' => 'loaded',
            'amount' => '799.00',
        ]);

        $response->assertRedirect();
        $investment = Investment::where('user_id', $recipient->id)->latest()->firstOrFail();

        $this->assertSame('approved', $investment->status);
        $this->assertNotNull($investment->starts_at);

        Carbon::setTestNow(Carbon::parse('2026-07-02 09:00:00'));
        $accrued = DailyInterestAccrualService::accrueDueInterestForUser($recipient->fresh());

        $this->assertEquals(6.39, round($accrued, 2));
        $this->assertEquals(6.39, round((float) $recipient->fresh()->balance, 2));

        Mail::assertSent(PackageGiftedEmail::class, function (PackageGiftedEmail $mail) use ($recipient): bool {
            return $mail->hasTo($recipient->email);
        });
    }

    public function test_admin_approval_sends_purchase_approval_email_with_usd_amount(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true, 'pin_hash' => 'test-pin']);
        $referrer = User::factory()->create();
        $recipient = User::factory()->create([
            'email' => 'buyer@example.com',
            'referred_by' => $referrer->id,
        ]);
        $investment = Investment::create([
            'user_id' => $recipient->id,
            'package_key' => 'loaded',
            'package_name' => 'Gold',
            'package_price' => 799,
            'amount' => 799,
            'payment_method' => 'bank_transfer',
            'daily_interest_rate' => 0.8,
            'duration_days' => 120,
            'starts_at' => null,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post(route('admin.investments.approve', $investment))
            ->assertRedirect()
            ->assertSessionHas('status', 'Investment approved successfully.');

        $this->assertSame(39.95, round((float) $referrer->fresh()->balance, 2));
        $this->assertDatabaseHas('referral_earnings', [
            'user_id' => $referrer->id,
            'referred_user_id' => $recipient->id,
            'investment_id' => $investment->id,
            'amount' => 39.95,
        ]);

        Mail::assertSent(BondPurchaseApprovedEmail::class, function (BondPurchaseApprovedEmail $mail) use ($recipient): bool {
            $content = $mail->render();

            return $mail->hasTo($recipient->email)
                && $mail->subject === 'Your bond purchase has been approved'
                && str_contains($content, 'Hello ' . $recipient->name . ',')
                && str_contains($content, 'alt="Lulu Philippines"')
                && str_contains($content, 'data:image/png;base64,')
                && str_contains($content, 'LuLu Gold package')
                && str_contains($content, '$799.00')
                && str_contains($content, 'Daily interest rate')
                && str_contains($content, '0.8% per day')
                && str_contains($content, 'Approved')
                && str_contains($content, url('/dashboard'))
                && ! str_contains($content, 'example.com/dashboard')
                && ! str_contains($content, 'Rodrigo M. Sanorjo')
                && ! str_contains($content, '₱');
        });

        Mail::assertNotSent(PackageGiftedEmail::class);
    }

    public function test_admin_can_set_a_custom_amount_for_a_selected_package(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['is_admin' => true, 'pin_hash' => 'test-pin']);
        $recipient = User::factory()->create(['email' => 'recipient@example.com', 'balance' => 0]);

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post(route('admin.send-package'), [
                'user_id' => $recipient->id,
                'package' => 'loaded',
                'amount' => '1250.50',
            ])
            ->assertRedirect('/admin/dashboard');

        $this->assertDatabaseHas('investments', [
            'user_id' => $recipient->id,
            'package_key' => 'loaded',
            'package_price' => 1250.50,
            'amount' => 1250.50,
            'daily_interest_rate' => 0.8,
            'duration_days' => 120,
        ]);
    }

    public function test_admin_gift_amount_must_fit_the_selected_package_range(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'pin_hash' => 'test-pin']);
        $recipient = User::factory()->create();

        $this->actingAs($admin)
            ->withSession(['pin_verified' => true])
            ->post(route('admin.send-package'), [
                'user_id' => $recipient->id,
                'package' => 'loaded',
                'amount' => '125.50',
            ])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('investments', 0);
    }

    public function test_signature_cannot_be_submitted_without_complete_agreement_details(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('investments.store'), [
                'package' => 'crunch',
                'amount' => 129,
                'payment_method' => 'bank_transfer',
                'agreement_signature_data' => 'data:image/png;base64,YWJj',
                'agreement_accepted' => '1',
            ])
            ->assertSessionHasErrors('agreement_details');

        $this->assertDatabaseCount('investments', 0);
    }
}
