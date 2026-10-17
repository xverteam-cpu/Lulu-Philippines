<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountSetupInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $setupLink,
    ) {}

    public function build(): self
    {
        return $this->from(config('mail.from.address', 'official@luluphilippines.com'), config('mail.from.name', 'Lulu'))
            ->subject('Set up your Lulu account')
            ->view('emails.account-setup-invitation');
    }
}
