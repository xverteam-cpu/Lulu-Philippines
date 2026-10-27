<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class FirebasePhoneVerificationService
{
    public function verifiedPhone(string $idToken): ?string
    {
        $apiKey = config('services.firebase.api_key');
        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('Firebase phone verification is not configured.');
        }

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(10)
            ->post('https://identitytoolkit.googleapis.com/v1/accounts:lookup?'.http_build_query([
                'key' => $apiKey,
            ]), [
                'idToken' => $idToken,
            ]);

        if ($response->serverError()) {
            report(new RuntimeException('Firebase phone token lookup failed with HTTP '.$response->status().'.'));
            throw ValidationException::withMessages([
                'phone_verification_token' => 'Phone verification could not be completed. Please try again.',
            ]);
        }

        if (! $response->successful()) {
            return null;
        }

        $phone = $response->json('users.0.phoneNumber');

        return is_string($phone) ? $phone : null;
    }
}
