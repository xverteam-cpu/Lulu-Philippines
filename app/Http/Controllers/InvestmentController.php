<?php

namespace App\Http\Controllers;

use App\Mail\PackagePurchaseNotification;
use App\Support\InvestmentPackages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class InvestmentController extends Controller
{
    public function store(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'package' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string', 'in:USD,PHP'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,e_wallet,account_balance,crypto'],
            'agreement_signature_name' => ['nullable', 'string', 'max:150'],
            'agreement_signature_data' => ['required', 'string', 'max:500000'],
            'agreement_accepted' => ['required', 'accepted'],
        ]);

        $package = InvestmentPackages::find($data['package']);

        if (! $package) {
            throw ValidationException::withMessages([
                'package' => 'Please select a valid package.',
            ]);
        }

        if (! $this->isValidSignatureData($data['agreement_signature_data'])) {
            throw ValidationException::withMessages([
                'agreement_signature_data' => 'Please draw your signature before submitting this purchase.',
            ]);
        }

        $currency = $data['currency'] ?? 'USD';
        $amountInUsd = $currency === 'PHP'
            ? (float) $data['amount'] / (float) config('currency.usd_to_php', 61.31)
            : (float) $data['amount'];

        if ($amountInUsd < $package['min_amount']) {
            throw ValidationException::withMessages([
                'amount' => 'Minimum investment for '.$package['name'].' is $'.number_format($package['min_amount'], 2).'.',
            ]);
        }

        if ($amountInUsd > $package['max_amount']) {
            throw ValidationException::withMessages([
                'amount' => 'Maximum investment for '.$package['name'].' is $'.number_format($package['max_amount'], 2).'.',
            ]);
        }

        if ($data['payment_method'] === 'account_balance' && $request->user()->balance < $amountInUsd) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient account balance for this purchase.',
            ]);
        }

        $isPending = in_array($data['payment_method'], ['bank_transfer', 'e_wallet'], true);

        $investment = DB::transaction(function () use ($request, $data, $package, $isPending, $amountInUsd) {
            if (! $isPending && ! InvestmentPackages::reserveSlot($data['package'])) {
                throw ValidationException::withMessages([
                    'package' => 'This package is currently sold out.',
                ]);
            }

            $investment = $request->user()->investments()->create([
                'package_key' => $data['package'],
                'package_name' => $package['name'],
                'package_price' => $package['price'],
                'amount' => round($amountInUsd, 2),
                'payment_method' => $data['payment_method'],
                'daily_interest_rate' => $package['daily_interest_rate'],
                'duration_days' => $package['duration_days'],
                'starts_at' => $isPending ? null : now(),
                'status' => $isPending ? 'pending' : 'approved',
                'agreement_version' => '2026-09-20',
                'agreement_signature_name' => $request->user()->name ?: $request->user()->email,
                'agreement_signature_data' => $data['agreement_signature_data'],
                'agreement_signed_at' => now(),
            ]);

            if ($data['payment_method'] === 'account_balance') {
                $user = $request->user();
                $user->balance = max(0, ($user->balance ?? 0) - (float) $data['amount']);
                $user->save();
            }

            return $investment;
        });

        // If investment is immediately approved, process referral commission
        if ($investment->status === 'approved') {
            $investment->processReferralCommission();
        }

        try {
            Mail::to($investment->user->email)
                ->send(new PackagePurchaseNotification($investment));
        } catch (\Throwable $e) {
            Log::warning('Failed to send package purchase notification', [
                'investment_id' => $investment->id,
                'user_id' => $investment->user_id,
                'error' => $e->getMessage(),
            ]);
        }

        $message = $isPending
            ? $package['name'].' investment has been submitted and is pending admin approval.'
            : $package['name'].' investment has been activated successfully.';

        if ($request->expectsJson() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => $message,
                'receipt' => [
                    'reference' => 'LOT-INV-'.str_pad((string) $investment->id, 6, '0', STR_PAD_LEFT),
                    'package_name' => $investment->package_name,
                    'amount' => number_format((float) $investment->amount, 2),
                    'daily_interest_rate' => number_format((float) $investment->daily_interest_rate, 2),
                    'duration_days' => (int) $investment->duration_days,
                    'payment_method' => match ($investment->payment_method) {
                        'bank_transfer' => 'Bank Transfer',
                        'account_balance' => 'Account Balance',
                        'crypto' => 'Crypto',
                        default => ucfirst(str_replace('_', ' ', $investment->payment_method)),
                    },
                    'status' => $investment->status === 'pending' ? 'Pending Approval' : 'Active',
                    'submitted_at' => $investment->created_at?->format('M d, Y H:i') ?? now()->format('M d, Y: i'),
                ],
                'investment' => [
                    'id' => $investment->id,
                    'package_key' => $investment->package_key,
                    'package_name' => $investment->package_name,
                    'amount' => (float) $investment->amount,
                    'daily_interest_rate' => (float) $investment->daily_interest_rate,
                    'duration_days' => (int) $investment->duration_days,
                    'payment_method' => $investment->payment_method,
                    'status' => $investment->status,
                ],
            ]);
        }

        return redirect()
            ->route('dashboard')
            ->with('status', $message);
    }

    private function isValidSignatureData(string $signatureData): bool
    {
        return (bool) preg_match(
            '#^data:image/png;base64,[A-Za-z0-9+/]+={0,2}$#',
            $signatureData
        );
    }
}
