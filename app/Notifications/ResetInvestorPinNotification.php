<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetInvestorPinNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('pin.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->from(config('mail.from.address', 'official@luluphilippines.com'), config('mail.from.name', 'Lulu'))
            ->subject('Reset Your Lulu Investor PIN')
            ->greeting('Hello '.($notifiable->name ?: 'there').',')
            ->line('We received a request to reset the four-digit PIN on your Lulu account.')
            ->action('Reset PIN', $resetUrl)
            ->line('This link expires in '.$expireMinutes.' minutes and can only be used once.')
            ->line('If you did not request a PIN reset, you can ignore this email.');
    }
}
