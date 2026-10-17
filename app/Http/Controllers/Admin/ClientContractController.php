<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientContractController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()?->is_admin, 403);

            return $next($request);
        });
    }

    public function index(): View
    {
        $contracts = Investment::query()
            ->with('user')
            ->whereNotNull('agreement_details')
            ->whereNotNull('agreement_signature_data')
            ->orderByDesc('agreement_signed_at')
            ->paginate(20);

        return view('admin.client-contracts', ['contracts' => $contracts]);
    }

    public function show(Investment $investment, Request $request): View
    {
        abort_unless($investment->agreement_details && $investment->agreement_signature_data, 404);

        $details = $investment->agreement_details;
        $commencementDate = $details['sign_date'];
        $signedDate = \Illuminate\Support\Carbon::parse($commencementDate);

        return view('bond-agreement', [
            'isSample' => false,
            'isAdminContract' => true,
            'autoPrint' => $request->boolean('print'),
            'agreementDetails' => $details,
            'investment' => $investment->load('user'),
            'agreement' => [
                'package_name' => $investment->package_name,
                'amount' => '$'.number_format((float) $investment->amount, 2),
                'daily_interest_rate' => number_format((float) $investment->daily_interest_rate, 2).'%',
                'daily_interest_income' => '$'.number_format($investment->dailyInterestAmount(), 2),
                'duration_days' => $investment->duration_days.' days',
                'commencement_date' => $signedDate->toDateString(),
                'maturity_date' => $signedDate->copy()->addDays($investment->duration_days)->toDateString(),
                'bondholder' => $details['full_name'],
                'civil_status' => $details['civil_status'] === 'Other'
                    ? ($details['civil_status_other'] ?? 'Other')
                    : $details['civil_status'],
                'residence' => $details['address'],
                'signature_name' => $investment->agreement_signature_name,
                'reference' => 'LOT-INV-'.str_pad((string) $investment->id, 6, '0', STR_PAD_LEFT),
                'contract_number' => 'LOT-INV-'.str_pad((string) $investment->id, 6, '0', STR_PAD_LEFT),
            ],
        ]);
    }
}
