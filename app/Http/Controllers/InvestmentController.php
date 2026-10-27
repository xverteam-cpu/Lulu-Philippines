<?php

namespace App\Http\Controllers;

use App\Mail\PackagePurchaseNotification;
use App\Support\CurrencyRateService;
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
            'investment_id' => ['nullable', 'integer', 'exists:investments,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable', 'string', 'in:USD,PHP'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,e_wallet,account_balance,crypto'],
            'agreement_signature_name' => ['nullable', 'string', 'max:150'],
            'agreement_signature_data' => ['required', 'string', 'max:500000'],
            'agreement_accepted' => ['required', 'accepted'],
            'agreement_details' => [
                'sometimes',
                'array:full_name,civil_status,civil_status_other,address,sign_date,id_type,id_type_other,id_number,issue_date,no_issue_date',
            ],
            'agreement_details.full_name' => ['required_with:agreement_details', 'string', 'max:100'],
            'agreement_details.civil_status' => ['required_with:agreement_details', 'string', 'max:30'],
            'agreement_details.civil_status_other' => ['nullable', 'string', 'max:30'],
            'agreement_details.address' => ['required_with:agreement_details', 'string', 'max:220'],
            'agreement_details.sign_date' => ['required_with:agreement_details', 'date'],
            'agreement_details.id_type' => ['required_with:agreement_details', 'string', 'max:60'],
            'agreement_details.id_type_other' => ['nullable', 'string', 'max:60'],
            'agreement_details.id_number' => ['required_with:agreement_details', 'string', 'max:60'],
            'agreement_details.issue_date' => ['nullable', 'date'],
            'agreement_details.no_issue_date' => ['required_with:agreement_details', 'boolean'],
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
        $amountInUsd = round($currency === 'PHP'
            ? (float) $data['amount'] / CurrencyRateService::latestUsdToPhp()
            : (float) $data['amount'], 2);

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
            if (! empty($data['investment_id'])) {
                $investment = $request->user()->investments()
                    ->whereKey($data['investment_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    ! $investment->agreement_signed_at
                        && in_array($investment->status, ['pending', 'approved'], true)
                        && $investment->package_key === $data['package']
                        && $investment->payment_method === $data['payment_method']
                        && (float) $investment->amount === $amountInUsd,
                    422
                );

                $investment->update([
                    'agreement_version' => '2026-09-20',
                    'agreement_signature_name' => $data['agreement_details']['full_name']
                        ?? ($request->user()->name ?: $request->user()->email),
                    'agreement_signature_data' => $data['agreement_signature_data'],
                    'agreement_details' => $data['agreement_details'] ?? null,
                    'agreement_signed_at' => now(),
                ]);

                return $investment;
            }

            if (! $isPending && ! InvestmentPackages::reserveSlot($data['package'])) {
                throw ValidationException::withMessages([
                    'package' => 'This package is currently sold out.',
                ]);
            }

            $investment = $request->user()->investments()->create([
                'package_key' => $data['package'],
                'package_name' => $package['name'],
                'package_price' => $package['price'],
                'amount' => $amountInUsd,
                'payment_method' => $data['payment_method'],
                'daily_interest_rate' => $package['daily_interest_rate'],
                'duration_days' => $package['duration_days'],
                'starts_at' => $isPending ? null : now(),
                'status' => $isPending ? 'pending' : 'approved',
                'agreement_version' => '2026-09-20',
                'agreement_signature_name' => $data['agreement_details']['full_name']
                    ?? ($request->user()->name ?: $request->user()->email),
                'agreement_signature_data' => $data['agreement_signature_data'],
                'agreement_details' => $data['agreement_details'] ?? null,
                'agreement_signed_at' => now(),
            ]);

            if ($data['payment_method'] === 'account_balance') {
                $user = $request->user();
                $user->balance = max(0, ($user->balance ?? 0) - $amountInUsd);
                $user->save();
            }

            return $investment;
        });

        // If investment is immediately approved, process referral commission
        if ($investment->status === 'approved' && empty($data['investment_id'])) {
            $investment->processReferralCommission();
        }

        if (empty($data['investment_id'])) {
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
        }

        $message = ! empty($data['investment_id'])
            ? ($investment->status === 'pending'
                ? 'Your signed agreement was submitted. The bond is pending admin approval.'
                : 'Your signed agreement was saved.')
            : ($isPending
                ? $package['name'].' investment has been submitted and is pending admin approval.'
                : $package['name'].' investment has been activated successfully.');

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

    public function confirmPayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'package' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'in:USD,PHP'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,e_wallet'],
        ]);

        $package = InvestmentPackages::find($data['package']);
        if (! $package) {
            throw ValidationException::withMessages([
                'package' => 'Please select a valid package.',
            ]);
        }

        $amountInUsd = round($data['currency'] === 'PHP'
            ? (float) $data['amount'] / CurrencyRateService::latestUsdToPhp()
            : (float) $data['amount'], 2);

        if ($amountInUsd < $package['min_amount'] || $amountInUsd > $package['max_amount']) {
            throw ValidationException::withMessages([
                'amount' => 'Enter an amount within the selected package limits.',
            ]);
        }

        $investment = DB::transaction(fn () => $request->user()->investments()->create([
            'package_key' => $data['package'],
            'package_name' => $package['name'],
            'package_price' => $package['price'],
            'amount' => $amountInUsd,
            'payment_method' => $data['payment_method'],
            'daily_interest_rate' => $package['daily_interest_rate'],
            'duration_days' => $package['duration_days'],
            'starts_at' => null,
            'status' => 'pending',
        ]));

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

        return redirect()
            ->route('dashboard')
            ->with('status', $package['name'].' bond payment was submitted for admin approval. Your agreement is ready to sign from the dashboard notification.');
    }

    private function isValidSignatureData(string $signatureData): bool
    {
        return (bool) preg_match(
            '#^data:image/png;base64,[A-Za-z0-9+/]+={0,2}$#',
            $signatureData
        );
    }
}
