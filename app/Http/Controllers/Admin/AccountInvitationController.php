<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountSetupInvitation;
use App\Models\AccountInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AccountInvitationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->is_admin, 403);

        $request->merge([
            'email' => is_string($request->input('email'))
                ? Str::lower(trim($request->input('email')))
                : $request->input('email'),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'referrer_id' => ['required', 'integer', 'exists:users,id'],
            'email' => [
                'required',
                'email',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (is_string($value) && User::whereRaw('LOWER(email) = ?', [Str::lower($value)])->exists()) {
                        $fail('An account already exists for this email address.');
                    }
                },
            ],
        ]);

        $token = Str::random(64);
        $invitation = AccountInvitation::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addDay(),
                'accepted_at' => null,
                'invited_by' => $request->user()->id,
                'referrer_id' => $data['referrer_id'],
            ]
        );

        try {
            Mail::to($invitation->email)->send(new AccountSetupInvitation(
                $invitation->name,
                route('account-setup.show', ['token' => $token]),
            ));
        } catch (TransportExceptionInterface $exception) {
            report($exception);
            $invitation->delete();

            return back()
                ->withErrors(['email' => 'The invitation email could not be sent. Please check the mail configuration and try again.'])
                ->withInput($request->only('name', 'email'));
        }

        return back()->with('status', 'Account setup invitation sent to '.$invitation->email.'. The link expires in 24 hours.');
    }
}
