<?php

namespace Tests\Feature;

use App\Models\Investment;
use App\Models\PushSubscription;
use App\Models\User;
use App\Support\DailyInterestAccrualService;
use App\Support\PushNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class DailyInterestPushNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_device_can_enable_and_disable_daily_interest_push_notifications(): void
    {
        $user = User::factory()->create();
        $endpoint = 'https://fcm.googleapis.com/subscription/abc123';

        $this->actingAs($user)
            ->postJson(route('push-subscriptions.store'), [
                'endpoint' => 'https://untrusted.example.test/subscription/abc123',
                'keys' => [
                    'p256dh' => 'browser-public-key',
                    'auth' => 'browser-auth-token',
                ],
            ])
            ->assertUnprocessable();
        $this->assertDatabaseCount('push_subscriptions', 0);

        $this->actingAs($user)
            ->postJson(route('push-subscriptions.store'), [
                'endpoint' => $endpoint,
                'keys' => [
                    'p256dh' => 'browser-public-key',
                    'auth' => 'browser-auth-token',
                ],
            ])
            ->assertOk()
            ->assertJson(['message' => 'Daily interest notifications enabled.']);

        $subscription = PushSubscription::query()->sole();
        $this->assertSame($user->id, $subscription->user_id);
        $this->assertSame(hash('sha256', $endpoint), $subscription->endpoint_hash);

        $this->actingAs($user)
            ->deleteJson(route('push-subscriptions.destroy'), ['endpoint' => $endpoint])
            ->assertOk()
            ->assertJson(['message' => 'Daily interest notifications disabled.']);

        $this->assertDatabaseCount('push_subscriptions', 0);
    }

    public function test_actual_interest_credits_trigger_a_push_for_that_user(): void
    {
        Carbon::setTestNow('2026-11-08 00:05:00');
        $user = User::factory()->create(['balance' => 0]);

        Investment::create([
            'user_id' => $user->id,
            'package_key' => 'crunch',
            'package_name' => 'Silver',
            'package_price' => 129,
            'amount' => 1000,
            'payment_method' => 'account_balance',
            'daily_interest_rate' => 1,
            'duration_days' => 30,
            'starts_at' => now()->subDay(),
            'status' => 'approved',
        ]);

        $pushNotifications = Mockery::mock(PushNotificationService::class);
        $pushNotifications->shouldReceive('sendDailyInterestCredit')
            ->once()
            ->withArgs(fn (User $creditedUser, float $amount) => $creditedUser->is($user) && $amount === 10.0);
        $this->instance(PushNotificationService::class, $pushNotifications);

        $this->assertSame(10.0, DailyInterestAccrualService::accrueDueInterestForUser($user));
        $this->assertSame(10.0, (float) $user->fresh()->balance);
    }

    public function test_notification_settings_explain_daily_interest_push_opt_in(): void
    {
        config([
            'services.webpush.vapid_public_key' => 'test-public-key',
            'services.webpush.vapid_private_key' => 'test-private-key',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('profile.notifications'))
            ->assertOk()
            ->assertSee('Enable daily interest notifications')
            ->assertSee('/service-worker.js')
            ->assertSee('Notifications appear when daily interest is credited.');
    }
}
