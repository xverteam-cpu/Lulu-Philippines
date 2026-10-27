<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhoneNumberController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        if (filled($user->phone)) {
            return redirect()->to(
                $request->session()->pull(
                    'phone_capture_redirect',
                    $user->is_admin ? route('admin.dashboard') : route('dashboard')
                )
            );
        }

        return view('auth.phone-required');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $request->user()->forceFill([
            'phone' => trim($data['phone']),
            'phone_verified_at' => null,
        ])->save();

        $destination = $request->session()->pull(
            'phone_capture_redirect',
            $request->user()->is_admin ? route('admin.dashboard') : route('dashboard')
        );

        return redirect()->to($destination)->with('status', 'Your mobile number has been saved.');
    }
}
