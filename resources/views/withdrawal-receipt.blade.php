@extends('layouts.app')

@section('content')
@php
    $withdrawalStatus = match ($withdrawal->status) {
        'pending' => 'Pending review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        default => ucfirst($withdrawal->status),
    };
    $maskedAccount = $withdrawal->account_number
        ? '••••'.substr($withdrawal->account_number, -4)
        : '—';
    $processingFee = (float) ($withdrawal->processing_fee ?? round((float) $withdrawal->amount * 0.05, 2));
    $netAmount = (float) ($withdrawal->total_withdrawn ?? ((float) $withdrawal->amount - $processingFee));
    $receiptReference = $withdrawal->transaction_reference ?: 'WD-'.$withdrawal->id;
    $shareText = 'Withdrawal receipt '.$receiptReference.' — amount $'.number_format((float) $withdrawal->amount, 2).', processing fee $'.number_format($processingFee, 2).', net $'.number_format($netAmount, 2).', status '.$withdrawalStatus.'.';
    $receiptImageConfig = [
        'title' => 'LuLu withdrawal receipt',
        'status' => $withdrawalStatus,
        'statusTone' => $withdrawal->status === 'approved' ? 'approved' : ($withdrawal->status === 'rejected' ? 'rejected' : 'pending'),
        'heroLabel' => 'Net amount to receive',
        'heroValue' => '$'.number_format($netAmount, 2),
        'subtitle' => 'After the 5% processing fee',
        'rows' => [
            ['label' => 'Withdrawal amount (USD)', 'value' => '$'.number_format((float) $withdrawal->amount, 2)],
            ['label' => 'Processing fee (5%)', 'value' => '−$'.number_format($processingFee, 2)],
            ['label' => 'Net amount', 'value' => '$'.number_format($netAmount, 2)],
            ['label' => 'Bank / e-wallet', 'value' => $withdrawal->bank_name ?: ucfirst(str_replace('_', ' ', $withdrawal->payment_method))],
            ['label' => 'Account number', 'value' => $maskedAccount],
            ['label' => 'Recipient', 'value' => $withdrawal->account_holder ?: '—'],
            ['label' => 'Receipt reference', 'value' => $receiptReference],
            ['label' => 'Submitted', 'value' => $withdrawal->created_at?->format('M j, Y g:i A') ?? now()->format('M j, Y g:i A')],
        ],
        'note' => $withdrawal->status === 'pending'
            ? 'Withdrawal request received and awaiting review. Net amount is an estimate based on the 5% processing fee.'
            : ($withdrawal->status === 'rejected'
                ? 'This withdrawal request was rejected. Contact support if you need more information.'
                : 'This withdrawal request has been approved. The net amount reflects the recorded 5% processing fee.'),
        'shareText' => $shareText,
    ];
@endphp
<style>
  body { min-height:100vh; margin:0; color:#0a1f17; background:#e7ece9 !important; font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; -webkit-font-smoothing:antialiased; }
  .container { max-width:none !important; margin:0 !important; padding:0 !important; }
  .withdrawal-receipt-page { box-sizing:border-box; display:flex; min-height:100vh; min-height:100dvh; flex-direction:column; width:min(100%,520px); margin:0 auto; padding:max(10px,env(safe-area-inset-top)) 20px max(16px,env(safe-area-inset-bottom)); background:#f2f5f3; }
  .withdrawal-receipt-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:8px 0 0; }
  .withdrawal-receipt-heading h1 { margin:0; color:#0a1f17; font-size:21px; font-weight:800; line-height:1.1; letter-spacing:-.03em; }
  .withdrawal-receipt-back { display:grid; width:36px; height:36px; flex:0 0 36px; place-items:center; border-radius:50%; color:#4b5b54; background:#e6ece9; font-size:22px; line-height:1; text-decoration:none; }
  .withdrawal-receipt-status { display:inline-flex; align-items:center; gap:7px; width:max-content; max-width:100%; margin-top:12px; padding:7px 11px; border:1px solid #f1d59c; border-radius:999px; color:#77520a; background:#fff7df; font-size:11px; font-weight:750; }
  .withdrawal-receipt-status::before { width:7px; height:7px; flex:0 0 7px; border-radius:50%; background:#db9a16; content:""; }
  .withdrawal-receipt-status.is-approved { border-color:#b9e2cd; color:#075a3b; background:#e8f7ef; }
  .withdrawal-receipt-status.is-approved::before { background:#0e8a5a; }
  .withdrawal-receipt-status.is-rejected { border-color:#f0c4c0; color:#982f27; background:#fff0ee; }
  .withdrawal-receipt-status.is-rejected::before { background:#c33d31; }
  .withdrawal-receipt-hero { position:relative; overflow:hidden; margin-top:14px; padding:18px; border-radius:22px; color:#fff; background:radial-gradient(120% 90% at 105% -10%,rgba(62,224,161,.5),transparent 55%),linear-gradient(160deg,#0b6a47 0%,#054a31 45%,#022418 100%); box-shadow:0 2px 4px rgba(4,30,20,.08),0 12px 24px -8px rgba(4,30,20,.22),0 32px 56px -24px rgba(4,30,20,.38),inset 0 1px 0 rgba(255,255,255,.2); }
  .withdrawal-receipt-hero-label { color:rgba(232,255,244,.72); font-size:10px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; }
  .withdrawal-receipt-hero-value { margin-top:9px; color:#fff; font-size:clamp(30px,8.7vw,36px); font-weight:800; letter-spacing:-.04em; line-height:1; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
  .withdrawal-receipt-hero-sub { margin-top:8px; color:rgba(232,255,244,.76); font-size:12px; line-height:1.4; }
  .withdrawal-receipt-rows { margin-top:14px; padding:4px 16px; border-radius:20px; background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06),inset 0 0 0 1px rgba(10,31,23,.04); }
  .withdrawal-receipt-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:11px 0; color:#4b5b54; font-size:13px; line-height:1.25; }
  .withdrawal-receipt-row + .withdrawal-receipt-row { border-top:1px solid #eef2f0; }
  .withdrawal-receipt-row strong { color:#0a1f17; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
  .withdrawal-receipt-net { color:#075a3b !important; }
  .withdrawal-receipt-note { margin:11px 2px 12px; color:#7e8d85; font-size:11px; line-height:1.45; }
  .withdrawal-receipt-actions { display:grid; gap:10px; margin-top:auto; padding-top:8px; }
  .withdrawal-receipt-share { display:flex; min-height:54px; align-items:center; justify-content:center; gap:9px; border:0; border-radius:16px; color:#fff; background:linear-gradient(135deg,#1fb97a,#054a31); box-shadow:0 7px 18px rgba(7,90,59,.25),inset 0 1px 0 rgba(255,255,255,.2); font:750 15px/1 Inter,system-ui,sans-serif; cursor:pointer; }
  .withdrawal-receipt-share svg { width:18px; height:18px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:2; }
  .withdrawal-receipt-download { display:flex; min-height:48px; align-items:center; justify-content:center; border:1px solid #d6e0da; border-radius:15px; color:#075a3b; background:#fff; font:700 13px/1 Inter,system-ui,sans-serif; cursor:pointer; }
  .withdrawal-receipt-done { display:flex; min-height:44px; align-items:center; justify-content:center; color:#075a3b; background:transparent; font-size:13px; font-weight:700; text-decoration:underline; text-underline-offset:3px; }
  .withdrawal-receipt-share-status { min-height:16px; margin:0; color:#52655b; text-align:center; font-size:11px; }
  @media(max-height:700px) { .withdrawal-receipt-page { padding-top:max(6px,env(safe-area-inset-top)); }.withdrawal-receipt-heading { padding-top:3px; }.withdrawal-receipt-status { margin-top:8px; }.withdrawal-receipt-hero { margin-top:9px; padding:15px 17px; }.withdrawal-receipt-rows { margin-top:9px; }.withdrawal-receipt-row { padding:8px 0; }.withdrawal-receipt-note { margin:8px 2px; } }
</style>

<main class="withdrawal-receipt-page">
  <header class="withdrawal-receipt-heading">
    <h1>Withdrawal receipt</h1>
    <a class="withdrawal-receipt-back" href="{{ route('withdraw') }}" aria-label="Back to withdrawals">&times;</a>
  </header>

  <div class="withdrawal-receipt-status {{ $withdrawal->status === 'approved' ? 'is-approved' : ($withdrawal->status === 'rejected' ? 'is-rejected' : '') }}" role="status">
    {{ $withdrawalStatus }}
  </div>

  <section class="withdrawal-receipt-hero" aria-label="Net withdrawal amount">
    <div class="withdrawal-receipt-hero-label">Net amount to receive</div>
    <div class="withdrawal-receipt-hero-value">${{ number_format($netAmount, 2) }}</div>
    <div class="withdrawal-receipt-hero-sub">After the 5% processing fee</div>
  </section>

  <section class="withdrawal-receipt-rows" aria-label="Withdrawal details">
    <div class="withdrawal-receipt-row"><span>Withdrawal amount (USD)</span><strong>${{ number_format((float) $withdrawal->amount, 2) }}</strong></div>
    <div class="withdrawal-receipt-row"><span>Processing fee (5%)</span><strong>−${{ number_format($processingFee, 2) }}</strong></div>
    <div class="withdrawal-receipt-row"><span>Net amount</span><strong class="withdrawal-receipt-net">${{ number_format($netAmount, 2) }}</strong></div>
    <div class="withdrawal-receipt-row"><span>Bank / e-wallet</span><strong>{{ $withdrawal->bank_name ?: ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}</strong></div>
    <div class="withdrawal-receipt-row"><span>Account number</span><strong>{{ $maskedAccount }}</strong></div>
    <div class="withdrawal-receipt-row"><span>Recipient</span><strong>{{ $withdrawal->account_holder ?: '—' }}</strong></div>
    <div class="withdrawal-receipt-row"><span>Receipt reference</span><strong>{{ $receiptReference }}</strong></div>
    <div class="withdrawal-receipt-row"><span>Submitted</span><strong>{{ $withdrawal->created_at?->format('M j, Y g:i A') ?? now()->format('M j, Y g:i A') }}</strong></div>
  </section>

  <p class="withdrawal-receipt-note">
    @if ($withdrawal->status === 'pending')
      Your withdrawal request has been received and is awaiting review. The net amount shown is an estimate based on the 5% processing fee.
    @elseif ($withdrawal->status === 'rejected')
      This withdrawal request was rejected. Contact support if you need more information.
    @else
      This withdrawal request has been approved. The net amount shown reflects the recorded 5% processing fee.
    @endif
  </p>

  <div class="withdrawal-receipt-actions">
    <button class="withdrawal-receipt-share" id="shareWithdrawalReceipt" type="button">
      <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.7 10.7 6.6-4.4m-6.6 7 6.6 4.4"/></svg>
      Share
    </button>
    <button class="withdrawal-receipt-download" id="downloadWithdrawalReceipt" type="button">Download receipt image</button>
    <p class="withdrawal-receipt-share-status" id="withdrawalReceiptShareStatus" aria-live="polite"></p>
    <a class="withdrawal-receipt-done" href="{{ route('withdraw') }}">Done</a>
  </div>
</main>

<script src="{{ asset('receipt-image.js') }}?v={{ filemtime(public_path('receipt-image.js')) }}"></script>
<script>
  (function () {
    var shareButton = document.getElementById('shareWithdrawalReceipt');
    var downloadButton = document.getElementById('downloadWithdrawalReceipt');
    var status = document.getElementById('withdrawalReceiptShareStatus');
    var config = @json($receiptImageConfig);
    var filename = 'lulu-withdrawal-receipt-{{ $withdrawal->id }}.png';

    shareButton.addEventListener('click', function () {
      window.LuluReceiptImage.share(config, filename, status);
    });
    downloadButton.addEventListener('click', function () {
      window.LuluReceiptImage.download(config, filename, status);
    });
  })();
</script>
@endsection
