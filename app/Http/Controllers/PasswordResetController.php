<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ResetInvestorPinNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    public function requestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $email = $request->input('email');
        $request->merge([
            'email' => is_string($email) ? Str::lower(trim($email)) : $email,
        ]);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$request->input('email')])
            ->first();

        if ($user) {
            Password::sendResetLink(['email' => $user->email]);
        }

        return back()->with('status', 'If an account matches that email address, a password reset link will be sent.');
    }

    public function resetForm(Request $request, ?string $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $email = $request->input('email');
        $request->merge([
            'email' => is_string($email) ? Str::lower(trim($email)) : $email,
        ]);

        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$data['email']])
            ->first();

        if (! $user) {
            return back()->withErrors(['email' => 'This password reset link is invalid or has expired.']);
        }

        $status = Password::reset(
            [
                ...$data,
                'email' => $user->email,
            ],
            function (User $user, string $password) {
                $user->password = $password;
                $user->temporary_password_hash = null;
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('order')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function pinRequestForm()
    {
        return view('auth.pins.email');
    }

    public function sendPinResetLink(Request $request): RedirectResponse
    {
        $email = $request->input('email');
        $request->merge([
            'email' => is_string($email) ? Str::lower(trim($email)) : $email,
        ]);

        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$data['email']])
            ->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('pin_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                ]
            );

            $user->notify(new ResetInvestorPinNotification($token));
        }

        return back()->with('status', 'If an account matches that email address, a PIN reset link will be sent.');
    }

    public function pinResetForm(Request $request, string $token)
    {
        $email = $request->query('email');
        abort_unless(is_string($email) && $this->validPinResetToken($email, $token), 403);

        return view('auth.pins.reset', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPin(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);

        $reset = DB::transaction(function () use ($data): bool {
            $resetToken = DB::table('pin_reset_tokens')
                ->where('email', $data['email'])
                ->lockForUpdate()
                ->first();

            if (
                ! $resetToken
                || ! hash_equals($resetToken->token, hash('sha256', $data['token']))
                || ! $this->pinResetTokenHasNotExpired($resetToken->created_at)
            ) {
                return false;
            }

            $user = User::query()
                ->where('email', $data['email'])
                ->lockForUpdate()
                ->first();

            if (! $user) {
                return false;
            }

            $user->forceFill([
                'pin_hash' => Hash::make($data['pin']),
                'pin_set_at' => now(),
            ])->save();

            DB::table('pin_reset_tokens')
                ->where('email', $data['email'])
                ->delete();

            return true;
        });

        if (! $reset) {
            throw ValidationException::withMessages([
                'email' => 'This PIN reset link is invalid or has expired. Request a new link and try again.',
            ]);
        }

        $request->session()->forget(['investor_pin_verified', 'pin_verified']);

        return redirect()->route('investors')->with('status', 'Your PIN has been reset. Sign in and enter your new PIN.');
    }

    private function validPinResetToken(string $email, string $token): bool
    {
        $resetToken = DB::table('pin_reset_tokens')->where('email', $email)->first();

        return $resetToken
            && hash_equals($resetToken->token, hash('sha256', $token))
            && $this->pinResetTokenHasNotExpired($resetToken->created_at);
    }

    private function pinResetTokenHasNotExpired(string $createdAt): bool
    {
        $expiresAt = \Illuminate\Support\Carbon::parse($createdAt)
            ->addMinutes(config('auth.passwords.'.config('auth.defaults.passwords').'.expire'));

        return $expiresAt->isFuture();
    }
}
