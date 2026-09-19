<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false));
        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->from(config('mail.from.address', 'lotteriaph@gmail.com'), config('mail.from.name', 'Lulu'))
            ->subject('Reset Your Lulu Password')
            ->view('emails.reset-password', [
                'resetUrl' => $resetUrl,
                'email' => $notifiable->getEmailForPasswordReset(),
                'expireMinutes' => $expireMinutes,
            ]);
    }
}
