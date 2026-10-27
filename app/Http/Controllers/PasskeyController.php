<?php

namespace App\Http\Controllers;

use App\Models\Passkey;
use App\Models\User;
use App\Support\WebAuthnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerException;
use Throwable;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\Exception\AuthenticatorResponseVerificationException;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialUserEntity;

class PasskeyController extends Controller
{
    private const REGISTRATION_CHALLENGE = 'passkey.registration';

    private const AUTHENTICATION_CHALLENGE = 'passkey.authentication';

    public function registrationOptions(Request $request, WebAuthnService $webAuthn): JsonResponse
    {
        $webAuthn->assertSecureContext();
        $user = $request->user();

        if (! $user->webauthn_user_handle) {
            $user->webauthn_user_handle = $webAuthn->encodeBase64Url(random_bytes(32));
            $user->save();
        }

        $challenge = random_bytes(32);
        $options = PublicKeyCredentialCreationOptions::create(
            PublicKeyCredentialRpEntity::create('Lulu Philippines', $webAuthn->rpId()),
            PublicKeyCredentialUserEntity::create(
                $user->email,
                $webAuthn->decodeBase64Url($user->webauthn_user_handle),
                $user->name,
            ),
            $challenge,
            [
                PublicKeyCredentialParameters::createPk(-7),
                PublicKeyCredentialParameters::createPk(-257),
            ],
            AuthenticatorSelectionCriteria::create(
                null,
                AuthenticatorSelectionCriteria::USER_VERIFICATION_REQUIREMENT_REQUIRED,
                AuthenticatorSelectionCriteria::RESIDENT_KEY_REQUIREMENT_REQUIRED,
            ),
            'none',
            $user->passkeys->map(fn (Passkey $passkey) => PublicKeyCredentialDescriptor::create(
                PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                $webAuthn->decodeBase64Url($passkey->credential_id),
            ))->all(),
            60000,
        );

        $serializedOptions = $webAuthn->serializer()->serialize($options, 'json');
        $request->session()->put(self::REGISTRATION_CHALLENGE, [
            'options' => $serializedOptions,
            'issued_at' => now()->timestamp,
        ]);

        return response()->json(['publicKey' => json_decode($serializedOptions, true, flags: JSON_THROW_ON_ERROR)]);
    }

    public function register(Request $request, WebAuthnService $webAuthn): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'credential' => ['required', 'array'],
        ]);
        $challenge = $this->consumeChallenge($request, self::REGISTRATION_CHALLENGE);

        if ($challenge === null) {
            return $this->invalidCeremonyResponse();
        }

        try {
            $serializer = $webAuthn->serializer();
            $credential = $serializer->deserialize(
                json_encode($validated['credential'], JSON_THROW_ON_ERROR),
                PublicKeyCredential::class,
                'json',
            );
            $options = $serializer->deserialize(
                $challenge['options'],
                PublicKeyCredentialCreationOptions::class,
                'json',
            );

            if (! $credential->response instanceof AuthenticatorAttestationResponse) {
                return $this->invalidCeremonyResponse();
            }

            $this->assertExpectedOrigin($credential->response->clientDataJSON->origin, $webAuthn);

            $source = AuthenticatorAttestationResponseValidator::create(
                $webAuthn->ceremonyStepManagerFactory()->creationCeremony(),
            )->check($credential->response, $options, $webAuthn->rpId());
        } catch (Throwable $exception) {
            report($exception);

            return $this->invalidCeremonyResponse();
        }

        $credentialId = $webAuthn->encodeBase64Url($credential->rawId);
        $credentialHash = hash('sha256', $credential->rawId);

        if (Passkey::where('credential_id_hash', $credentialHash)->exists()) {
            return response()->json(['message' => 'This passkey is already registered.'], 409);
        }

        $request->user()->passkeys()->create([
            'name' => $validated['name'],
            'credential_id' => $credentialId,
            'credential_id_hash' => $credentialHash,
            'credential_source' => $serializer->serialize($source, 'json'),
        ]);

        return response()->json(['message' => 'Passkey added to your account.']);
    }

    public function authenticationOptions(Request $request, WebAuthnService $webAuthn): JsonResponse
    {
        $webAuthn->assertSecureContext();
        $challenge = random_bytes(32);
        $options = PublicKeyCredentialRequestOptions::create(
            $challenge,
            $webAuthn->rpId(),
            [],
            PublicKeyCredentialRequestOptions::USER_VERIFICATION_REQUIREMENT_REQUIRED,
            60000,
        );
        $serializedOptions = $webAuthn->serializer()->serialize($options, 'json');

        $request->session()->put(self::AUTHENTICATION_CHALLENGE, [
            'options' => $serializedOptions,
            'issued_at' => now()->timestamp,
        ]);

        return response()->json(['publicKey' => json_decode($serializedOptions, true, flags: JSON_THROW_ON_ERROR)]);
    }

    public function authenticate(Request $request, WebAuthnService $webAuthn): JsonResponse
    {
        $validated = $request->validate([
            'credential' => ['required', 'array'],
        ]);
        $challenge = $this->consumeChallenge($request, self::AUTHENTICATION_CHALLENGE);

        if ($challenge === null) {
            return $this->invalidCeremonyResponse();
        }

        try {
            $serializer = $webAuthn->serializer();
            $credential = $serializer->deserialize(
                json_encode($validated['credential'], JSON_THROW_ON_ERROR),
                PublicKeyCredential::class,
                'json',
            );
            $options = $serializer->deserialize(
                $challenge['options'],
                PublicKeyCredentialRequestOptions::class,
                'json',
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->invalidCeremonyResponse();
        }

        if (! $credential->response instanceof AuthenticatorAssertionResponse) {
            return $this->invalidCeremonyResponse();
        }

        $passkey = Passkey::where('credential_id_hash', hash('sha256', $credential->rawId))->first();

        if (! $passkey || ! hash_equals($passkey->credential_id, $webAuthn->encodeBase64Url($credential->rawId))) {
            return $this->invalidCeremonyResponse();
        }

        $user = User::find($passkey->user_id);

        if (! $user || ! $user->webauthn_user_handle) {
            return $this->invalidCeremonyResponse();
        }

        try {
            $source = $serializer->deserialize(
                $passkey->credential_source,
                PublicKeyCredentialSource::class,
                'json',
            );
            $this->assertExpectedOrigin($credential->response->clientDataJSON->origin, $webAuthn);

            if (! hash_equals(
                $webAuthn->decodeBase64Url($user->webauthn_user_handle),
                $source->userHandle,
            )) {
                return $this->invalidCeremonyResponse();
            }

            $source = AuthenticatorAssertionResponseValidator::create(
                $webAuthn->ceremonyStepManagerFactory()->requestCeremony(),
            )->check(
                $source,
                $credential->response,
                $options,
                $webAuthn->rpId(),
                null,
            );
        } catch (Throwable $exception) {
            report($exception);

            return $this->invalidCeremonyResponse();
        }

        $passkey->credential_source = $serializer->serialize($source, 'json');
        $passkey->save();

        Auth::login($user, true);
        $request->session()->regenerate();
        $request->session()->forget('investor_pin_verified');

        $defaultDestination = $user->is_admin ? route('admin.dashboard') : route('dashboard');
        $destination = redirect()->intended($defaultDestination)->getTargetUrl();
        if (! $user->phone_verified_at) {
            $request->session()->put('phone_capture_redirect', $destination);
            $destination = route('phone.capture');
        }

        return response()->json([
            'redirect_url' => $destination,
        ]);
    }

    public function destroy(Request $request, Passkey $passkey): JsonResponse
    {
        abort_unless($passkey->user_id === $request->user()->id, 404);
        $passkey->delete();

        return response()->json(['message' => 'Passkey removed from your account.']);
    }

    private function consumeChallenge(Request $request, string $key): ?array
    {
        $challenge = $request->session()->pull($key);

        if (! is_array($challenge)
            || ! isset($challenge['options'], $challenge['issued_at'])
            || ! is_string($challenge['options'])
            || ! is_int($challenge['issued_at'])
            || $challenge['issued_at'] < now()->subMinutes(5)->timestamp
        ) {
            return null;
        }

        return $challenge;
    }

    private function assertExpectedOrigin(string $origin, WebAuthnService $webAuthn): void
    {
        if (! hash_equals($webAuthn->origin(), $origin)) {
            throw AuthenticatorResponseVerificationException::create('The passkey origin did not match APP_URL.');
        }
    }

    private function invalidCeremonyResponse(): JsonResponse
    {
        return response()->json([
            'message' => 'Passkey verification failed. Try again or sign in with your password.',
        ], 422);
    }
}
