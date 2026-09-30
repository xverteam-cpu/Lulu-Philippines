<?php

namespace App\Http\Controllers;

use App\Models\FranchiseApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FranchiseApplicationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:40'],
            'preferred_package' => ['nullable', 'string', Rule::in(['40', '60'])],
            'location' => ['required', 'string', 'max:255'],
            'business_background' => ['nullable', 'string', 'max:5000'],
            'investment_capacity' => ['nullable', 'string', 'max:100'],
            'additional_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        FranchiseApplication::create([
            ...$validated,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()
            ->to(route('franchising').'#franchise-application')
            ->with('status', 'Your franchise application has been submitted successfully.');
    }
}
