@extends('layouts.app')

@section('content')
@php
    $estimatedInterest = round((float) $investment->amount * ((float) $investment->daily_interest_rate / 100) * (int) $investment->duration_days, 2);
    $estimatedMaturity = $investment->status === 'approved'
        ? $investment->effectiveStartDate()->copy()->addDays((int) $investment->duration_days)
        : now()->addDays((int) $investment->duration_days);
    $paymentMethod = match ($investment->payment_method) {
        'bank_transfer' => 'Bank transfer',
        'e_wallet' => 'E-wallet',
        'account_balance' => 'Account balance',
        'admin_transfer' => 'Admin transfer',
        'crypto' => 'Crypto',
        default => ucfirst(str_replace('_', ' ', $investment->payment_method)),
    };
    $paymentStatus = match ($investment->status) {
        'pending' => 'Pending admin review',
        'approved' => 'Confirmed',
        'rejected' => 'Rejected',
        default => ucfirst($investment->status),
    };
    $receiptReference = 'LOT-INV-'.str_pad((string) $investment->id, 6, '0', STR_PAD_LEFT);
    $receiptShareText = $investment->package_name.' bond receipt '.$receiptReference.' — amount $'.number_format((float) $investment->amount, 2).', status '.$paymentStatus.'.';
    $receiptImageConfig = [
        'title' => 'LuLu investment receipt',
        'status' => $paymentStatus,
        'statusTone' => $investment->status === 'approved' ? 'confirmed' : ($investment->status === 'rejected' ? 'rejected' : 'pending'),
        'heroLabel' => 'Estimated value at maturity',
        'heroValue' => '$'.number_format((float) $investment->amount + $estimatedInterest, 2),
        'subtitle' => 'Principal + estimated interest · '.$investment->package_name.' bond',
        'rows' => [
            ['label' => 'Amount invested (USD)', 'value' => '$'.number_format((float) $investment->amount, 2)],
            ['label' => 'Payment method', 'value' => $paymentMethod],
            ['label' => 'Rate', 'value' => number_format((float) $investment->daily_interest_rate, 2).'% daily'],
            ['label' => 'Term', 'value' => $investment->duration_days.' days'],
            ['label' => 'Interest payout', 'value' => 'Daily accrual'],
            ['label' => 'Estimated interest', 'value' => '$'.number_format($estimatedInterest, 2)],
            ['label' => 'Estimated maturity date', 'value' => $estimatedMaturity->format('M j, Y')],
            ['label' => 'Receipt reference', 'value' => $receiptReference],
            ['label' => 'Submitted', 'value' => $investment->created_at?->format('M j, Y g:i A') ?? now()->format('M j, Y g:i A')],
        ],
        'note' => $investment->status === 'pending'
            ? 'Payment claim pending admin review. Transfer has not been verified. Returns are estimates before taxes and fees; actual terms are governed by your signed agreement.'
            : 'Returns are estimates before taxes and fees. Actual terms and maturity date are governed by your signed agreement.',
        'shareText' => $receiptShareText,
    ];
@endphp
<style>
  body { min-height:100vh; margin:0; color:#0a1f17; background:#e7ece9 !important; font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; -webkit-font-smoothing:antialiased; }
  .container { max-width:none !important; margin:0 !important; padding:0 !important; }
  .receipt-page { box-sizing:border-box; display:flex; min-height:100vh; min-height:100dvh; flex-direction:column; width:min(100%,520px); margin:0 auto; padding: max(10px,env(safe-area-inset-top)) 20px max(16px,env(safe-area-inset-bottom)); background:#f2f5f3; }
  .receipt-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:8px 0 0; }
  .receipt-heading h1 { margin:0; color:#0a1f17; font-size:21px; font-weight:800; line-height:1.1; letter-spacing:-.03em; }
  .receipt-back { display:grid; width:36px; height:36px; flex:0 0 36px; place-items:center; border-radius:50%; color:#4b5b54; background:#e6ece9; font-size:22px; line-height:1; text-decoration:none; }
  .receipt-status { display:inline-flex; align-items:center; gap:7px; width:max-content; max-width:100%; margin-top:12px; padding:7px 11px; border:1px solid #f1d59c; border-radius:999px; color:#77520a; background:#fff7df; font-size:11px; font-weight:750; }
  .receipt-status::before { width:7px; height:7px; flex:0 0 7px; border-radius:50%; background:#db9a16; content:""; }
  .receipt-status.is-confirmed { border-color:#b9e2cd; color:#075a3b; background:#e8f7ef; }
  .receipt-status.is-confirmed::before { background:#0e8a5a; }
  .receipt-hero { position:relative; overflow:hidden; margin-top:14px; padding:18px; border-radius:22px; color:#fff; background:radial-gradient(120% 90% at 105% -10%,rgba(62,224,161,.5),transparent 55%),linear-gradient(160deg,#0b6a47 0%,#054a31 45%,#022418 100%); box-shadow:0 2px 4px rgba(4,30,20,.08),0 12px 24px -8px rgba(4,30,20,.22),0 32px 56px -24px rgba(4,30,20,.38),inset 0 1px 0 rgba(255,255,255,.2); }
  .receipt-hero-label { color:rgba(232,255,244,.72); font-size:10px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; }
  .receipt-hero-value { margin-top:9px; color:#fff; font-size:clamp(30px,8.7vw,36px); font-weight:800; letter-spacing:-.04em; line-height:1; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
  .receipt-hero-sub { margin-top:8px; color:rgba(232,255,244,.76); font-size:12px; line-height:1.4; }
  .receipt-rows { margin-top:14px; padding:4px 16px; border-radius:20px; background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06),inset 0 0 0 1px rgba(10,31,23,.04); }
  .receipt-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 0; color:#4b5b54; font-size:13px; line-height:1.25; }
  .receipt-row + .receipt-row { border-top:1px solid #eef2f0; }
  .receipt-row strong { color:#0a1f17; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
  .receipt-note { margin:11px 2px 12px; color:#7e8d85; font-size:11px; line-height:1.45; }
  .receipt-actions { display:grid; gap:10px; margin-top:auto; padding-top:8px; }
  .receipt-share { display:flex; min-height:54px; align-items:center; justify-content:center; gap:9px; border:0; border-radius:16px; color:#fff; background:linear-gradient(135deg,#1fb97a,#054a31); box-shadow:0 7px 18px rgba(7,90,59,.25),inset 0 1px 0 rgba(255,255,255,.2); font:750 15px/1 Inter,system-ui,sans-serif; cursor:pointer; }
  .receipt-share svg { width:18px; height:18px; fill:none; stroke:currentColor; stroke-linecap:round; stroke-linejoin:round; stroke-width:2; }
  .receipt-download { display:flex; min-height:48px; align-items:center; justify-content:center; border:1px solid #d6e0da; border-radius:15px; color:#075a3b; background:#fff; font:700 13px/1 Inter,system-ui,sans-serif; cursor:pointer; }
  .receipt-dashboard { min-height:44px; border:0; color:#075a3b; background:transparent; font:700 13px/1 Inter,system-ui,sans-serif; text-decoration:underline; text-underline-offset:3px; cursor:pointer; }
  .receipt-share-status { min-height:16px; margin:0; color:#52655b; text-align:center; font-size:11px; }
  @media(max-height:700px) { .receipt-page { padding-top:max(6px,env(safe-area-inset-top)); }.receipt-heading { padding-top:3px; }.receipt-status { margin-top:8px; }.receipt-hero { margin-top:9px; padding:15px 17px; }.receipt-rows { margin-top:9px; }.receipt-row { padding:9px 0; }.receipt-note { margin:8px 2px; } }
</style>

<main class="receipt-page">
  <header class="receipt-heading">
    <h1>Payment receipt</h1>
    <a class="receipt-back" href="{{ route('dashboard') }}" aria-label="Back to dashboard">&times;</a>
  </header>

  <div class="receipt-status {{ $investment->status === 'approved' ? 'is-confirmed' : '' }}" role="status">
    {{ $paymentStatus }}
  </div>

  <section class="receipt-hero" aria-label="Estimated bond value">
    <div class="receipt-hero-label">Estimated value at maturity</div>
    <div class="receipt-hero-value">${{ number_format((float) $investment->amount + $estimatedInterest, 2) }}</div>
    <div class="receipt-hero-sub">Principal + estimated interest · {{ $investment->package_name }} bond</div>
  </section>

  <section class="receipt-rows" aria-label="Receipt details">
    <div class="receipt-row"><span>Amount invested (USD)</span><strong>${{ number_format((float) $investment->amount, 2) }}</strong></div>
    <div class="receipt-row"><span>Payment method</span><strong>{{ $paymentMethod }}</strong></div>
    <div class="receipt-row"><span>Rate</span><strong>{{ number_format((float) $investment->daily_interest_rate, 2) }}% daily</strong></div>
    <div class="receipt-row"><span>Term</span><strong>{{ $investment->duration_days }} days</strong></div>
    <div class="receipt-row"><span>Interest payout</span><strong>Daily accrual</strong></div>
    <div class="receipt-row"><span>Estimated interest</span><strong>${{ number_format($estimatedInterest, 2) }}</strong></div>
    <div class="receipt-row"><span>Estimated maturity date</span><strong>{{ $estimatedMaturity->format('M j, Y') }}</strong></div>
    <div class="receipt-row"><span>Receipt reference</span><strong>{{ $receiptReference }}</strong></div>
    <div class="receipt-row"><span>Submitted</span><strong>{{ $investment->created_at?->format('M j, Y g:i A') ?? now()->format('M j, Y g:i A') }}</strong></div>
  </section>

  <p class="receipt-note">
    @if ($investment->status === 'pending')
      Your payment claim has been submitted for admin review; this receipt does not confirm that the transfer has been verified. Returns are estimates before taxes and fees. Actual terms are governed by your signed agreement.
    @else
      Returns are estimates before taxes and fees. Actual terms and maturity date are governed by your signed agreement.
    @endif
  </p>

  <div class="receipt-actions">
    <button class="receipt-share" id="shareReceipt" type="button">
      <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.7 10.7 6.6-4.4m-6.6 7 6.6 4.4"/></svg>
      Share
    </button>
    <button class="receipt-download" id="downloadReceipt" type="button">Download receipt image</button>
    <p class="receipt-share-status" id="receiptShareStatus" aria-live="polite"></p>
    <a class="receipt-dashboard" href="{{ route('dashboard') }}">Back to dashboard</a>
  </div>
</main>

<script src="{{ asset('receipt-image.js') }}?v={{ filemtime(public_path('receipt-image.js')) }}"></script>
<script>
  (function () {
    var shareButton = document.getElementById('shareReceipt');
    var downloadButton = document.getElementById('downloadReceipt');
    var status = document.getElementById('receiptShareStatus');
    var config = @json($receiptImageConfig);
    var filename = 'lulu-investment-receipt-{{ $investment->id }}.png';

    shareButton.addEventListener('click', function () {
      window.LuluReceiptImage.share(config, filename, status);
    });
    downloadButton.addEventListener('click', function () {
      window.LuluReceiptImage.download(config, filename, status);
    });
  })();
</script>
@endsection
