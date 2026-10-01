<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = trim((string) $credentials['email']);
        $password = $credentials['password'];

        $user = User::where('username', $loginField)
            ->orWhere('email', $loginField)
            ->first();

        if (! $user || ! Auth::attempt(['email' => $user->email, 'password' => $password], true)) {
            return back()
                ->withErrors(['email' => 'The provided login details are incorrect.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        return redirect()->intended(
            Auth::user()->is_admin ? route('admin.dashboard') : route('dashboard')
        );
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'referral' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $referrer = null;

        if (! empty($data['referral'])) {
            $referrer = User::where('username', $data['referral'])
                ->orWhere('email', $data['referral'])
                ->first();
        }

        $user = User::create([
            'name' => $data['fullname'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => null,
            'address' => '',
            'region' => 'ncr',
            'message' => null,
            'password' => $data['password'],
            'is_admin' => false,
            'referred_by' => $referrer?->id,
        ]);

        Mail::to($user->email)->send(new WelcomeEmail($user));

        Auth::login($user, true);
        $request->session()->regenerate();
        return redirect()->route($user->is_admin ? 'admin.dashboard' : 'dashboard');
    }

    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $request->session()->forget('google_referrer_id');

        $referral = trim((string) $request->query('ref', ''));
        if ($referral !== '') {
            $referrer = User::query()
                ->where('username', $referral)
                ->orWhere('email', $referral)
                ->first();

            if ($referrer) {
                $request->session()->put('google_referrer_id', $referrer->id);
            }
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $exception) {
            report($exception);
            $request->session()->forget('google_referrer_id');

            return redirect()->route('investors')
                ->withErrors(['google' => 'Google sign-in could not be completed. Please try again.']);
        }

        $email = $googleUser->getEmail();
        if (! is_string($email) || $email === '' || ($googleUser->getRaw()['verified_email'] ?? false) !== true) {
            $request->session()->forget('google_referrer_id');

            return redirect()->route('investors')
                ->withErrors(['google' => 'A verified Google email address is required.']);
        }

        $referrerId = $request->session()->pull('google_referrer_id');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => Str::limit($googleUser->getName() ?: $email, 255, ''),
                'username' => $this->generateUniqueUsername($email),
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Str::random(40),
                'is_admin' => false,
                'referred_by' => $referrerId,
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(
            $user->is_admin ? route('admin.dashboard') : route('dashboard')
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('investors');
    }

    private function generateUniqueEmail(string $username): string
    {
        $base = strtolower((string) preg_replace('/[^a-z0-9]+/i', '', $username));
        $base = $base !== '' ? $base : 'user';
        $email = $base.'@lotteria.local';
        $counter = 1;

        while (User::where('email', $email)->exists()) {
            $email = $base.$counter.'@lotteria.local';
            $counter++;
        }

        return $email;
    }

    private function generateUniqueUsername(string $email): string
    {
        $base = Str::slug((string) Str::before($email, '@')) ?: 'google-user';
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.'-'.$counter;
            $counter++;
        }

        return $username;
    }
}
