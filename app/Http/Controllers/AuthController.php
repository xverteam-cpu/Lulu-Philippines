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

        if (! $user || ! Auth::attempt(['email' => $user->email, 'password' => $password])) {
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

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route($user->is_admin ? 'admin.dashboard' : 'dashboard');
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
}
