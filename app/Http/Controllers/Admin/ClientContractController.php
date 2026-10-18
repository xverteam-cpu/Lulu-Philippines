<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function show(Investment $investment, Request $request): Response
    {
        abort_unless($investment->agreement_details && $investment->agreement_signature_data, 404);

        $details = $investment->agreement_details;
        $investment->load('user');
        $contractPath = base_path('LuLu-Contract-Form.html');
        abort_unless(is_file($contractPath), 500, 'The bond agreement form is unavailable.');

        $contractHtml = file_get_contents($contractPath);
        abort_if($contractHtml === false, 500, 'The bond agreement form could not be read.');

        $headIntegration = view('contracts.investment-admin-head', [
            'initialData' => [
                'fullName' => $details['full_name'],
                'civilStatus' => $details['civil_status'],
                'civilOther' => $details['civil_status_other'] ?? '',
                'address' => $details['address'],
                'signDate' => $details['sign_date'],
                'idType' => $details['id_type'],
                'idOther' => $details['id_type_other'] ?? '',
                'idNumber' => $details['id_number'],
                'issueDate' => $details['issue_date'] ?? '',
                'noIssueDate' => filter_var($details['no_issue_date'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'signatureMode' => 'upload',
                'signature' => $investment->agreement_signature_data,
            ],
            'autoPrint' => $request->boolean('print'),
        ])->render();
        $contractPanel = view('contracts.investment-admin-panel', [
            'investment' => $investment,
            'agreementDetails' => $details,
        ])->render();

        abort_unless(
            str_contains($contractHtml, '</head>')
                && str_contains($contractHtml, '<div class="intro">')
                && str_contains($contractHtml, '</body>'),
            500,
            'The bond agreement form is incomplete.',
        );

        $contractHtml = str_replace('</head>', $headIntegration.'</head>', $contractHtml);
        $contractHtml = str_replace('<div class="intro">', $contractPanel.'<div class="intro">', $contractHtml);

        return response($contractHtml, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
