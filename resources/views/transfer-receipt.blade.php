@extends('layouts.app')

@section('content')
@php
    $amount = '$'.number_format((float) $activity->amount, 2);
    $senderName = $sender->name ?: $sender->email;
    $recipientName = $recipient->name ?: $recipient->email;
    $reference = 'TRF-'.strtoupper(str_replace('-', '', $activity->transfer_reference));
    $submittedAt = ($activity->occurred_at ?? $activity->created_at)->copy()->timezone('Asia/Manila')->format('M j, Y g:i A');
    $status = 'Completed';
    $receiptImageConfig = [
        'title' => 'LuLu transfer receipt',
        'status' => $status,
        'statusTone' => 'approved',
        'heroLabel' => $isSender ? 'Amount sent' : 'Amount received',
        'heroValue' => $amount,
        'subtitle' => $isSender ? 'Funds sent successfully' : 'Funds received successfully',
        'rows' => [
            ['label' => 'From', 'value' => $senderName],
            ['label' => 'To', 'value' => $recipientName],
            ['label' => 'Transfer reference', 'value' => $reference],
            ['label' => 'Completed', 'value' => $submittedAt],
        ],
        'note' => 'This transfer was completed between LuLu accounts.',
        'shareText' => 'LuLu transfer receipt '.$reference.' — '.$amount.' sent from '.$senderName.' to '.$recipientName.' on '.$submittedAt.'.',
    ];
@endphp
<style>
  body { min-height:100vh; margin:0; color:#0a1f17; background:#e7ece9 !important; font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif; -webkit-font-smoothing:antialiased; }
  .container { max-width:none !important; margin:0 !important; padding:0 !important; }
  .transfer-receipt-page { box-sizing:border-box; display:flex; min-height:100vh; min-height:100dvh; flex-direction:column; width:min(100%,520px); margin:0 auto; padding:max(10px,env(safe-area-inset-top)) 20px max(16px,env(safe-area-inset-bottom)); background:#f2f5f3; }
  .transfer-receipt-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:8px 0 0; }
  .transfer-receipt-heading h1 { margin:0; color:#0a1f17; font-size:21px; font-weight:800; line-height:1.1; letter-spacing:-.03em; }
  .transfer-receipt-back { display:grid; width:36px; height:36px; flex:0 0 36px; place-items:center; border-radius:50%; color:#4b5b54; background:#e6ece9; font-size:22px; line-height:1; text-decoration:none; }
  .transfer-receipt-status { width:max-content; max-width:100%; margin-top:12px; padding:7px 11px; border:1px solid #b9e2cd; border-radius:999px; color:#075a3b; background:#e8f7ef; font-size:11px; font-weight:750; }
  .transfer-receipt-hero { position:relative; overflow:hidden; margin-top:14px; padding:22px 18px; border-radius:22px; color:#fff; background:radial-gradient(120% 90% at 105% -10%,rgba(62,224,161,.5),transparent 55%),linear-gradient(160deg,#0b6a47 0%,#054a31 45%,#022418 100%); box-shadow:0 12px 24px -8px rgba(4,30,20,.22),0 32px 56px -24px rgba(2,26,18,.38); }
  .transfer-receipt-hero-label { color:rgba(232,255,244,.72); font-size:10px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; }
  .transfer-receipt-hero-value { margin-top:9px; color:#fff; font-size:clamp(30px,8.7vw,36px); font-weight:800; letter-spacing:-.04em; line-height:1; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
  .transfer-receipt-hero-sub { margin-top:8px; color:rgba(232,255,244,.76); font-size:12px; line-height:1.4; }
  .transfer-receipt-rows { margin-top:14px; padding:4px 16px; border-radius:20px; background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06),inset 0 0 0 1px rgba(10,31,23,.04); }
  .transfer-receipt-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:13px 0; color:#4b5b54; font-size:13px; line-height:1.25; }
  .transfer-receipt-row + .transfer-receipt-row { border-top:1px solid #eef2f0; }
  .transfer-receipt-row strong { color:#0a1f17; text-align:right; font-weight:700; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
  .transfer-receipt-note { margin:11px 2px 12px; color:#7e8d85; font-size:11px; line-height:1.45; }
  .transfer-receipt-actions { display:grid; gap:10px; margin-top:auto; padding-top:16px; }
  .transfer-receipt-share,.transfer-receipt-download { display:flex; min-height:50px; align-items:center; justify-content:center; border:0; border-radius:15px; color:#fff; background:linear-gradient(135deg,#1fb97a,#054a31); font:750 14px/1 Inter,system-ui,sans-serif; cursor:pointer; }
  .transfer-receipt-download { min-height:46px; border:1px solid #d6e0da; color:#075a3b; background:#fff; }
  .transfer-receipt-share-status { min-height:16px; margin:0; color:#52655b; text-align:center; font-size:11px; }
  .transfer-receipt-done { display:flex; min-height:42px; align-items:center; justify-content:center; color:#075a3b; font-size:13px; font-weight:700; text-decoration:underline; text-underline-offset:3px; }
</style>

<main class="transfer-receipt-page">
  <header class="transfer-receipt-heading">
    <h1>Transfer receipt</h1>
    <a class="transfer-receipt-back" href="{{ route('send') }}" aria-label="Back to send">&times;</a>
  </header>
  <div class="transfer-receipt-status">{{ $status }}</div>
  <section class="transfer-receipt-hero">
    <div class="transfer-receipt-hero-label">{{ $isSender ? 'Amount sent' : 'Amount received' }}</div>
    <div class="transfer-receipt-hero-value">{{ $amount }}</div>
    <div class="transfer-receipt-hero-sub">{{ $isSender ? 'Funds sent successfully' : 'Funds received successfully' }}</div>
  </section>
  <section class="transfer-receipt-rows" aria-label="Transfer details">
    <div class="transfer-receipt-row"><span>From</span><strong>{{ $senderName }}</strong></div>
    <div class="transfer-receipt-row"><span>To</span><strong>{{ $recipientName }}</strong></div>
    <div class="transfer-receipt-row"><span>Transfer reference</span><strong>{{ $reference }}</strong></div>
    <div class="transfer-receipt-row"><span>Completed</span><strong>{{ $submittedAt }}</strong></div>
  </section>
  <p class="transfer-receipt-note">This transfer was completed between LuLu accounts.</p>
  <div class="transfer-receipt-actions">
    <button class="transfer-receipt-share" id="shareTransferReceipt" type="button">Share receipt</button>
    <button class="transfer-receipt-download" id="downloadTransferReceipt" type="button">Download receipt image</button>
    <p class="transfer-receipt-share-status" id="transferReceiptShareStatus" aria-live="polite"></p>
    <a class="transfer-receipt-done" href="{{ route('send') }}">Done</a>
  </div>
</main>

<script src="{{ asset('receipt-image.js') }}?v={{ filemtime(public_path('receipt-image.js')) }}"></script>
<script>
  (function () {
    var config = @json($receiptImageConfig);
    var filename = 'lulu-transfer-receipt-{{ $activity->id }}.png';
    var status = document.getElementById('transferReceiptShareStatus');

    document.getElementById('shareTransferReceipt').addEventListener('click', function () {
      window.LuluReceiptImage.share(config, filename, status);
    });
    document.getElementById('downloadTransferReceipt').addEventListener('click', function () {
      window.LuluReceiptImage.download(config, filename, status);
    });
  })();
</script>
@endsection
