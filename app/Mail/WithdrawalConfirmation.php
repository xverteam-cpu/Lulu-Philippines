<?php

namespace App\Mail;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawalConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    private const PROCESSING_FEE_RATE = 0.05;

    public function __construct(public Withdrawal $withdrawal) {}

    public function build(): self
    {
        $user = $this->withdrawal->user;
        $accountType = $this->withdrawal->payment_method === 'mobile_money'
            ? 'E-wallet'
            : 'Bank';
        $withdrawalAmount = (float) $this->withdrawal->amount;
        $processingFee = round($withdrawalAmount * self::PROCESSING_FEE_RATE, 2);
        $totalWithdrawn = round($withdrawalAmount - $processingFee, 2);
        $clientName = $user->name ?: $user->email;
        $view = $this->withdrawal->status === 'approved'
            ? 'emails.withdrawal-confirmation'
            : 'emails.withdrawal-request-confirmation';

        return $this->from(
            config('mail.from.address', 'official@luluphilippines.com'),
            config('mail.from.name', 'Lulu')
        )
            ->to($user->email, $clientName)
            ->subject('Your Lulu withdrawal confirmation')
            ->view($view)
            ->with([
                'clientName' => $clientName,
                'transactionReference' => $this->withdrawal->transaction_reference
                    ?: 'WD-'.$this->withdrawal->getKey(),
                'transactionType' => $accountType,
                'accountProvider' => $this->withdrawal->bank_name,
                'accountNumber' => $this->withdrawal->account_number,
                'withdrawalAmount' => number_format($withdrawalAmount, 2),
                'processingFee' => number_format($processingFee, 2),
                'totalWithdrawn' => number_format($totalWithdrawn, 2),
                'status' => ucfirst($this->withdrawal->status),
                'withdrawalDate' => ($this->withdrawal->approved_at ?? $this->withdrawal->created_at)?->format('F j, Y')
                    ?? now()->format('F j, Y'),
                'dashboardUrl' => url('/dashboard'),
            ]);
    }
}
