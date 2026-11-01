<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminPurchaseNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_dashboard_shows_purchase_activity_from_the_last_24_hours_only(): void
    {
        Carbon::setTestNow('2026-11-01 10:00:00');

        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['name' => 'Recent Investor']);

        $approved = $this->createInvestment($user, [
            'payment_method' => 'bank_transfer',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now()->subHours(2),
            'created_at' => now()->subDays(3),
        ]);
        $balancePurchase = $this->createInvestment($user, [
            'payment_method' => 'account_balance',
            'status' => 'approved',
            'created_at' => now()->subHours(4),
        ]);
        $newDeposit = $this->createInvestment($user, [
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
            'created_at' => now()->subHours(6),
        ]);
        $adminSentPackage = $this->createInvestment($user, [
            'payment_method' => 'admin_transfer',
            'status' => 'approved',
            'created_at' => now()->subHours(8),
        ]);
        $this->createInvestment($user, [
            'payment_method' => 'account_balance',
            'status' => 'approved',
            'created_at' => now()->subHours(25),
        ]);

        $this->actingAs($admin)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Recent bond activity')
            ->assertSee('Bond purchase approved')
            ->assertSee('Bond purchased using account balance')
            ->assertSee('New deposit submitted')
            ->assertSee('Package sent by admin')
            ->assertSee('data-notification-id="'.$approved->id.'-approved"', false)
            ->assertSee('data-notification-id="'.$balancePurchase->id.'-balance_purchase"', false)
            ->assertSee('data-notification-id="'.$newDeposit->id.'-deposit"', false)
            ->assertSee('data-notification-id="'.$adminSentPackage->id.'-admin_package"', false)
            ->assertSee('Visible for 24 hours');

        $response = $this->get(route('admin.dashboard.purchase-notifications'));

        $response->assertOk()
            ->assertJsonCount(4, 'notifications');
    }

    public function test_non_admin_cannot_read_purchase_notifications(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['investor_pin_verified' => true])
            ->get(route('admin.dashboard.purchase-notifications'))
            ->assertForbidden();
    }

    private function createInvestment(User $user, array $attributes): Investment
    {
        $investment = $user->investments()->create(array_merge([
            'package_key' => 'crunch',
            'package_name' => 'Silver Bond',
            'package_price' => 129,
            'amount' => 100,
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now(),
        ], $attributes));

        if (array_key_exists('created_at', $attributes)) {
            $investment->forceFill(['created_at' => $attributes['created_at']])->save();
        }

        return $investment;
    }
}
