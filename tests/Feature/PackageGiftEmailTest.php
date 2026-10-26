<?php

namespace Tests\Feature;

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
}
