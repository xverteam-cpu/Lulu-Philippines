<?php

namespace App\Mail;

use App\Models\Withdrawal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawalConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Withdrawal $withdrawal)
    {
    }

    public function build(): self
    {
        $user = $this->withdrawal->user;
        $accountType = $this->withdrawal->payment_method === 'mobile_money'
            ? 'E-wallet'
            : 'Bank';

        return $this->from(
            config('mail.from.address', 'lotteriaph@gmail.com'),
            config('mail.from.name', 'Lulu')
        )
            ->to($user->email, $user->name)
            ->subject('Your Lulu withdrawal confirmation')
            ->view('emails.withdrawal-confirmation')
            ->with([
                'clientName' => $user->name ?: $user->email,
                'transactionReference' => $this->withdrawal->transaction_reference,
                'transactionType' => $accountType,
                'accountProvider' => $this->withdrawal->bank_name,
                'accountNumber' => $this->withdrawal->account_number,
                'withdrawalAmount' => number_format((float) $this->withdrawal->amount, 2),
                'processingFee' => number_format((float) $this->withdrawal->processing_fee, 2),
                'totalWithdrawn' => number_format((float) $this->withdrawal->total_withdrawn, 2),
                'status' => ucfirst($this->withdrawal->status),
                'withdrawalDate' => $this->withdrawal->created_at?->format('F j, Y') ?? now()->format('F j, Y'),
                'dashboardUrl' => url('/dashboard'),
            ]);
    }
}
