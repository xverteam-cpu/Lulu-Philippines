<?php

namespace App\Mail;

use App\Models\Investment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackagePurchaseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Investment $investment;

    public function __construct(Investment $investment)
    {
        $this->investment = $investment;
    }

    public function build()
    {
        $user = $this->investment->user;
        $dashboardUrl = url('/dashboard');

        return $this->from(config('mail.from.address', 'lotteriaph@gmail.com'), config('mail.from.name', 'Lulu'))
            ->subject('Your Lulu Package Purchase Confirmation')
            ->view('emails.package-purchase-notification')
            ->with([
                'member_name' => $user->name,
                'member_email' => $user->email,
                'member_id' => $user->id,
                'package_name' => $this->investment->package_name,
                'package_amount' => number_format((float) $this->investment->amount * (float) config('currency.usd_to_php', 61.31), 2),
                'purchase_date' => $this->investment->created_at?->format('F j, Y') ?? now()->format('F j, Y'),
                'payment_method' => match ($this->investment->payment_method) {
                    'bank_transfer' => 'Bank Transfer',
                    'account_balance' => 'Account Balance',
                    'crypto' => 'Crypto',
                    default => ucfirst(str_replace('_', ' ', $this->investment->payment_method)),
                },
                'invoice_id' => 'LOT-JV-INV-'.str_pad($this->investment->id, 6, '0', STR_PAD_LEFT),
                'reference_number' => 'REF-'.str_pad($this->investment->id, 9, '0', STR_PAD_LEFT),
                'dashboard_link' => $dashboardUrl,
                'status_text' => $this->investment->status === 'pending'
                    ? 'Payment Verification in Progress'
                    : 'Payment Completed',
            ]);
    }
}
