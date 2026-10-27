<?php

namespace App\Http\Controllers;

use App\Services\FirebasePhoneVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PhoneNumberController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if ($user->phone_verified_at) {
            return redirect()->to(
                $request->session()->pull(
                    'phone_capture_redirect',
                    $user->is_admin ? route('admin.dashboard') : route('dashboard')
                )
            );
        }

        return view('auth.phone-required');
    }

    public function store(Request $request, FirebasePhoneVerificationService $verification): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{7,14}$/', 'max:16'],
            'phone_verification_token' => ['required', 'string', 'max:5000'],
        ]);

        $verifiedPhone = $verification->verifiedPhone($data['phone_verification_token']);
        if (! $verifiedPhone || ! hash_equals($data['phone'], $verifiedPhone)) {
            throw ValidationException::withMessages([
                'phone' => 'The verified number does not match the mobile number entered.',
            ]);
        }

        $request->user()->update([
            'phone' => $data['phone'],
            'phone_verified_at' => now(),
        ]);

        $destination = $request->session()->pull(
            'phone_capture_redirect',
            $request->user()->is_admin ? route('admin.dashboard') : route('dashboard')
        );

        return redirect()->to($destination)->with('status', 'Your mobile number has been saved.');
    }
}
