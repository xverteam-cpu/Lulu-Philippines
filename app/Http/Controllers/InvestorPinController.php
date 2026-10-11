<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class InvestorPinController extends Controller
{
    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pin' => ['required', 'digits_between:4,6'],
        ]);

        $user = $request->user();

        if (! $user->pin_hash || ! Hash::check($data['pin'], $user->pin_hash)) {
            throw ValidationException::withMessages([
                'pin' => 'That PIN was not accepted. Please try again.',
            ]);
        }

        $request->session()->put('investor_pin_verified', true);
        $request->session()->put('pin_verified', true);

        return redirect()->route($user->is_admin ? 'admin.dashboard' : 'dashboard');
    }

    public function setup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pin' => ['required', 'digits:4'],
            'pin_confirmation' => ['required', 'same:pin', 'digits:4'],
        ]);

        $user = $request->user();

        if ($user->pin_hash) {
            throw ValidationException::withMessages([
                'pin' => 'A PIN is already set for this account. Please verify it instead.',
            ]);
        }

        $user->forceFill([
            'pin_hash' => Hash::make($data['pin']),
            'pin_set_at' => now(),
        ])->save();

        $request->session()->put('investor_pin_verified', true);
        $request->session()->put('pin_verified', true);

        return redirect()->route($user->is_admin ? 'admin.dashboard' : 'dashboard');
    }
}
