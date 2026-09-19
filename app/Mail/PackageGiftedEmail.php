<?php

namespace App\Mail;

use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackageGiftedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Investment $investment;

    public function __construct(Investment $investment)
    {
        $this->investment = $investment;
    }

    public function build(): self
    {
        $user = $this->investment->user;

        return $this->from(config('mail.from.address', 'lotteriaph@gmail.com'), config('mail.from.name', 'Lulu'))
            ->subject('Your Lulu Package Has Been Gifted to You')
            ->view('emails.package-gifted')
            ->with([
                'recipient_name' => $user->name ?: $user->username,
                'package_name' => $this->investment->package_name,
                'package_amount' => number_format((float) $this->investment->amount, 2),
                'daily_interest_rate' => rtrim(rtrim((string) $this->investment->daily_interest_rate, '0'), '.'),
                'dashboard_link' => url('/dashboard'),
            ]);
    }
}
