<?php

namespace App\Mail;

use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BondPurchaseApprovedEmail extends Mailable
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

        return $this->from(config('mail.from.address', 'official@luluphilippines.com'), config('mail.from.name', 'Lulu'))
            ->subject('Your bond purchase has been approved')
            ->view('emails.bond-purchase-approved')
            ->with([
                'recipient_name' => $user->name ?: $user->username,
                'package_name' => $this->investment->package_name,
                'package_amount' => number_format((float) $this->investment->amount, 2),
                'dashboard_link' => url('/dashboard'),
            ]);
    }
}
