<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->from(config('mail.from.address', 'official@luluphilippines.com'), config('mail.from.name', 'Lulu'))
            ->subject('Welcome to Lulu')
            ->view('emails.welcome-email')
            ->with([
                'user_name' => $this->user->name ?: $this->user->username,
                'login_link' => url('/login'),
                'dashboard_link' => url('/dashboard'),
                'investors_link' => route('investors'),
                'invest_link' => route('invest'),
                'notification_settings_link' => url('/profile/notifications'),
            ]);
    }
}
