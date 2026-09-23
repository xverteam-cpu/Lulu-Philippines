@extends('layouts.app')

@section('content')
<style>
  body { margin:0; background:#eef1f4 !important; font-family:Inter, 'Plus Jakarta Sans', system-ui, sans-serif; }
  .container { max-width:none !important; margin:0 !important; padding:0 !important; }
  .payment-page { min-height:100vh; background:#eef1f4; }
  .payment-brand-header { min-height:255px; padding:28px 20px 52px; box-sizing:border-box; color:#fff; background:{{ $provider['background'] }}; text-align:center; }
  .payment-brand-logo { display:block; width:min(280px, 78vw); max-width:280px; height:84px; margin:0 auto 28px; object-fit:contain; background:#fff; border-radius:10px; padding:10px 22px; box-sizing:border-box; }
  .payment-brand-text-logo { display:flex; align-items:center; justify-content:center; width:max-content; min-width:190px; height:84px; margin:0 auto 28px; padding:10px 28px; box-sizing:border-box; border-radius:10px; background:#fff; color:{{ $provider['background'] }}; font-size:38px; font-weight:950; letter-spacing:-1px; }
  .payment-brand-name { margin:0; font-size:28px; font-weight:900; }
  .payment-brand-copy { margin:8px 0 0; font-size:15px; line-height:22px; opacity:.92; }
  .payment-card { width:min(calc(100% - 32px), 510px); margin:-135px auto 40px; position:relative; padding:28px 20px 22px; box-sizing:border-box; border-radius:10px; background:#fff; box-shadow:0 16px 40px rgba(15,23,42,.14); text-align:center; }
  .payment-card h1 { margin:0; color:#17202a; font-size:24px; font-weight:900; }
  .payment-card p { margin:10px 0 18px; color:#64748b; font-size:14px; line-height:21px; }
  .payment-qr { display:block; width:min(100%, 300px); max-height:52vh; margin:16px auto 20px; object-fit:contain; }
  .payment-summary { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-top:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; color:#17202a; font-size:14px; font-weight:800; }
  .payment-actions { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; margin-top:18px; }
  .payment-actions a, .payment-actions button { min-height:48px; border:1px solid #d9dee5; border-radius:10px; background:#f7f8fa; color:#64748b; font-size:14px; font-weight:900; text-decoration:none; cursor:pointer; }
  .payment-actions .payment-continue { border-color:{{ $provider['background'] }}; background:{{ $provider['background'] }}; color:#fff; }
  .payment-launch { display:flex; align-items:center; justify-content:center; width:100%; min-height:48px; margin:18px 0 12px; border:0; border-radius:10px; background:{{ $provider['background'] }}; color:#fff; font-size:14px; font-weight:900; text-decoration:none; }
  .payment-launch:hover { filter:brightness(.94); }
  .payment-help { margin:0 !important; font-size:12px !important; line-height:18px !important; }
  .payment-form { margin:0; }
</style>

<main class="payment-page">
  <header class="payment-brand-header">
    @if ($provider['logo'])
      <img class="payment-brand-logo" src="{{ asset($provider['logo']) }}" alt="{{ $provider['name'] }} logo">
    @else
      <div class="payment-brand-text-logo" aria-label="{{ $provider['name'] }} logo">{{ $provider['name'] }}</div>
    @endif
    <h1 class="payment-brand-name">{{ $provider['name'] }}</h1>
    <p class="payment-brand-copy">Complete your payment securely using {{ $provider['name'] }}.</p>
  </header>

  <section class="payment-card" aria-label="{{ $provider['name'] }} payment QR code">
    <h1>Scan to pay</h1>
    <p>Open your {{ $provider['name'] }} app or scan this QR code to complete your investment payment.</p>
    <img class="payment-qr" src="{{ asset($provider['qr']) }}" alt="{{ $provider['name'] }} payment QR code">
    <div class="payment-summary">
      <span>{{ $package['name'] }} Bond</span>
      <span>{{ $currency === 'PHP' ? '₱' : '$' }}{{ number_format($amount, 2) }}</span>
    </div>
    <a class="payment-launch" href="{{ $provider['launch_url'] }}" target="_blank" rel="noopener noreferrer">
      Open {{ $provider['name'] }}
    </a>
    <p class="payment-help">On mobile, choose to open the {{ $provider['name'] }} app if prompted. You can enter the payment details there or upload this QR code from your gallery.</p>
    <div class="payment-actions">
      <a href="{{ route('invest.purchase', ['package' => $packageKey]) }}">Back</a>
      <a class="payment-continue" href="{{ route('invest.agreement.sign', [
        'package' => $packageKey,
        'amount' => $amount,
        'currency' => $currency,
        'payment_method' => $provider['payment_method'] ?? 'bank_transfer',
      ]) }}">Payment completed</a>
    </div>
  </section>
</main>
@endsection
