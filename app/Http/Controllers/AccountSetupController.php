<?php

namespace App\Http\Controllers;

use App\Models\AccountInvitation;
use App\Models\User;
use App\Services\FirebasePhoneVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccountSetupController extends Controller
{
    public function show(string $token): View
    {
        $invitation = $this->findValidInvitation($token);

        return view('auth.account-setup', ['invitation' => $invitation, 'token' => $token]);
    }

    public function store(Request $request, string $token, FirebasePhoneVerificationService $verification): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'max:16'],
            'phone_verification_token' => ['required', 'string', 'max:5000'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);

        $verifiedPhone = $verification->verifiedPhone($data['phone_verification_token']);
        if (! $verifiedPhone || ! hash_equals($data['phone'], $verifiedPhone)) {
            throw ValidationException::withMessages([
                'phone' => 'The verified number does not match the mobile number entered.',
            ]);
        }

        DB::transaction(function () use ($token, $data): void {
            $invitation = AccountInvitation::query()
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->first();

            abort_unless(
                $invitation
                    && ! $invitation->accepted_at
                    && $invitation->expires_at->isFuture()
                    && $invitation->referrer_id
                    && ! User::whereRaw('LOWER(email) = ?', [Str::lower($invitation->email)])->exists(),
                404
            );

            $user = User::create([
                'name' => $invitation->name,
                'username' => $this->generateUniqueUsername($invitation->email),
                'email' => $invitation->email,
                'phone' => $data['phone'],
                'phone_verified_at' => now(),
                'password' => $data['password'],
                'is_admin' => false,
                'referred_by' => $invitation->referrer_id,
            ]);
            $user->forceFill([
                'email_verified_at' => now(),
                'pin_hash' => Hash::make($data['pin']),
                'pin_set_at' => now(),
            ])->save();

            $invitation->forceFill(['accepted_at' => now()])->save();
        });

        return redirect()->route('login')->with('status', 'Your account is ready. Sign in with your email and password.');
    }

    private function findValidInvitation(string $token): AccountInvitation
    {
        $invitation = AccountInvitation::query()
            ->where('token_hash', hash('sha256', $token))
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        abort_unless(
            $invitation
                && ! User::whereRaw('LOWER(email) = ?', [Str::lower($invitation->email)])->exists(),
            404
        );
        abort_unless(User::whereKey($invitation->referrer_id)->exists(), 404);

        return $invitation;
    }

    private function generateUniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '_');
        $base = $base !== '' ? Str::limit($base, 180, '') : 'client';

        do {
            $username = $base.'_'.Str::lower(Str::random(8));
        } while (User::where('username', $username)->exists());

        return $username;
    }
}
