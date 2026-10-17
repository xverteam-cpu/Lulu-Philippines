<?php

namespace App\Http\Controllers;

use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountSetupController extends Controller
{
    public function show(string $token): View
    {
        $invitation = $this->findValidInvitation($token);

        return view('auth.account-setup', ['invitation' => $invitation, 'token' => $token]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);

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
