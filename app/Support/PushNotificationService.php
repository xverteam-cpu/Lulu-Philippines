<?php

namespace App\Support;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Throwable;

class PushNotificationService
{
    public function sendDailyInterestCredit(User $user, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        $subscriptions = PushSubscription::query()
            ->where('user_id', $user->id)
            ->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $publicKey = config('services.webpush.vapid_public_key');
        $privateKey = config('services.webpush.vapid_private_key');

        if (! $publicKey || ! $privateKey) {
            Log::warning('Daily-interest push notifications are disabled because VAPID keys are not configured.', [
                'user_id' => $user->id,
            ]);

            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.subject'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ], [], 5);

        $payload = json_encode([
            'title' => 'Daily interest credited',
            'body' => '$'.number_format($amount, 2).' has been added to your Lulu balance.',
            'url' => route('dashboard'),
        ], JSON_THROW_ON_ERROR);

        foreach ($subscriptions as $pushSubscription) {
            try {
                $report = $webPush->sendOneNotification(
                    Subscription::create([
                        'endpoint' => $pushSubscription->endpoint,
                        'keys' => [
                            'p256dh' => $pushSubscription->public_key,
                            'auth' => $pushSubscription->auth_token,
                        ],
                    ]),
                    $payload
                );

                if ($report->isSuccess()) {
                    continue;
                }

                if ($report->isSubscriptionExpired()) {
                    $pushSubscription->delete();

                    continue;
                }

                Log::warning('Daily-interest push notification delivery failed.', [
                    'user_id' => $user->id,
                    'subscription_id' => $pushSubscription->id,
                    'reason' => $report->getReason(),
                ]);
            } catch (Throwable $exception) {
                Log::error('Daily-interest push notification delivery raised an exception.', [
                    'user_id' => $user->id,
                    'subscription_id' => $pushSubscription->id,
                    'exception' => $exception,
                ]);
            }
        }
    }
}
