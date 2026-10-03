@extends('layouts.app')

@section('content')
<style>
  :root {
    --pay-bg: #f2f5f3;
    --pay-surface: #fff;
    --pay-ink: #0a1f17;
    --pay-muted: #4b5b54;
    --pay-subtle: #8a9892;
    --pay-line: #e2e9e5;
    --pay-green: #0e8a5a;
    --pay-green-dark: #075a3b;
    --pay-mint: #e6f7ef;
    --pay-shadow-sm: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06);
    --pay-shadow-md: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18);
    --pay-display: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    --pay-ui: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  }

  body { min-height: 100vh; margin: 0; color: var(--pay-ink); background: radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .16), transparent 60%), radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .12), transparent 60%), #e7ece9; font-family: var(--pay-ui); -webkit-font-smoothing: antialiased; }
  .container { max-width: none; margin: 0; padding: 0; }
  .payment-page { min-height: 100vh; padding: 0 0 28px; }
  .payment-chrome { position: sticky; top: 0; z-index: 30; background: rgba(242, 245, 243, .88); backdrop-filter: saturate(180%) blur(20px); -webkit-backdrop-filter: saturate(180%) blur(20px); }
  .payment-nav { display: grid; max-width: 480px; min-height: 60px; grid-template-columns: 40px 1fr 40px; align-items: center; gap: 12px; margin: 0 auto; padding: 6px 16px; }
  .payment-icon-button { display: grid; width: 40px; height: 40px; place-items: center; border-radius: 50%; color: var(--pay-ink); background: #fff; box-shadow: var(--pay-shadow-sm), inset 0 0 0 1px rgba(10, 31, 23, .05); text-decoration: none; }
  .payment-brand { display: flex; align-items: center; justify-self: center; gap: 8px; color: var(--pay-ink); font: 750 16px/1 var(--pay-display); letter-spacing: -.02em; }
  .payment-brand-mark { display: grid; width: 28px; height: 28px; place-items: center; overflow: hidden; border-radius: 9px; background: #fff; box-shadow: var(--pay-shadow-sm); }
  .payment-brand-mark img { width: 100%; height: 100%; object-fit: contain; }
  .payment-nav-spacer { width: 40px; height: 40px; }
  .payment-main { width: min(100% - 32px, 430px); margin: 0 auto; padding: 15px 0 0; }
  .payment-hero { padding: 8px 2px 4px; }
  .payment-eyebrow { color: var(--provider-color, var(--pay-green)); font: 700 10.5px/1 var(--pay-ui); letter-spacing: .14em; text-transform: uppercase; }
  .payment-title { margin: 8px 0 0; color: var(--pay-ink); font: 800 30px/1.06 var(--pay-display); letter-spacing: -.045em; }
  .payment-intro { max-width: 340px; margin: 8px 0 0; color: var(--pay-muted); font: 450 14px/1.5 var(--pay-ui); }
  .provider-card { position: relative; overflow: hidden; margin-top: 18px; padding: 18px; border-radius: 24px; color: #fff; background: radial-gradient(100% 120% at 100% 0%, color-mix(in srgb, var(--provider-accent) 48%, transparent), transparent 54%), radial-gradient(75% 90% at 0% 100%, color-mix(in srgb, var(--provider-accent) 18%, transparent), transparent 58%), linear-gradient(160deg, color-mix(in srgb, var(--provider-color) 86%, white) 0%, var(--provider-color) 52%, color-mix(in srgb, var(--provider-color) 72%, #071b12) 100%); box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px color-mix(in srgb, var(--provider-color) 35%, transparent), 0 32px 56px -24px rgba(2, 26, 18, .3), inset 0 1px 0 rgba(255, 255, 255, .2); }
  .provider-card::after { position: absolute; right: -72px; bottom: -92px; width: 180px; height: 180px; border: 1px solid rgba(255, 255, 255, .12); border-radius: 50%; box-shadow: 0 0 0 24px rgba(255, 255, 255, .04), 0 0 0 50px rgba(255, 255, 255, .025); content: ""; }
  .provider-card-top { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
  .provider-identity { display: flex; min-width: 0; align-items: center; gap: 9px; }
  .provider-logo { display: grid; width: 38px; height: 38px; flex: 0 0 38px; place-items: center; overflow: hidden; border-radius: 12px; color: {{ $provider['background'] }}; background: rgba(255, 255, 255, .94); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .5), 0 6px 16px -10px rgba(0, 0, 0, .5); font: 800 9px/1 var(--pay-ui); text-align: center; }
  .provider-logo img { width: 100%; height: 100%; padding: 4px; object-fit: contain; }
  .provider-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font: 800 18px/1 var(--pay-display); letter-spacing: -.03em; }
  .secure-pill { display: inline-flex; height: 28px; flex: none; align-items: center; gap: 6px; padding: 0 10px; border-radius: 99px; color: #dffbef; background: rgba(255, 255, 255, .11); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .15); font: 650 10.5px/1 var(--pay-ui); }
  .secure-pill i { width: 6px; height: 6px; border-radius: 50%; background: var(--provider-accent); }
  .provider-copy { position: relative; z-index: 1; margin: 15px 0 0; color: rgba(232, 255, 244, .78); font: 500 12.5px/1.45 var(--pay-ui); }
  .method-panel { margin-top: 16px; padding: 16px; border-radius: 20px; background: var(--pay-surface); box-shadow: var(--pay-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .method-heading { margin: 0; color: var(--pay-ink); font: 750 14px/1.2 var(--pay-display); letter-spacing: -.02em; }
  .method-group { margin-top: 14px; }
  .method-group + .method-group { margin-top: 15px; padding-top: 14px; border-top: 1px solid #eef2f0; }
  .method-group-title { margin: 0 0 8px; color: var(--pay-subtle); font: 700 9.5px/1 var(--pay-ui); letter-spacing: .12em; text-transform: uppercase; }
  .method-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 7px; }
  .method-button { display: flex; min-height: 62px; flex-direction: column; align-items: center; justify-content: center; gap: 5px; padding: 7px 3px; border: 1px solid var(--pay-line); border-radius: 12px; color: var(--pay-muted); background: #fff; text-decoration: none; transition: border-color .18s ease, background .18s ease, transform .18s ease; }
  .method-button:hover { border-color: color-mix(in srgb, var(--provider-color) 45%, white); background: color-mix(in srgb, var(--provider-color) 5%, white); transform: translateY(-1px); }
  .method-button.is-active { border-color: color-mix(in srgb, var(--provider-color) 55%, white); color: color-mix(in srgb, var(--provider-color) 78%, #071b12); background: color-mix(in srgb, var(--provider-color) 9%, white); box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--provider-color) 12%, transparent); }
  .method-logo { display: grid; width: 31px; height: 27px; place-items: center; overflow: hidden; border-radius: 7px; color: #fff; background: var(--method-color, #075a3b); font: 800 8px/1 var(--pay-ui); }
  .method-logo img { width: 100%; height: 100%; padding: 3px; background: #fff; object-fit: contain; }
  .method-name { width: 100%; overflow: hidden; text-align: center; text-overflow: ellipsis; white-space: nowrap; font: 650 9px/1.2 var(--pay-ui); }
  .pay-card { margin-top: 16px; padding: 18px; border-radius: 24px; background: var(--pay-surface); box-shadow: var(--pay-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .pay-head { text-align: center; }
  .pay-heading { margin: 0; color: var(--pay-ink); font: 800 23px/1.1 var(--pay-display); letter-spacing: -.035em; }
  .pay-description { max-width: 320px; margin: 8px auto 0; color: var(--pay-muted); font: 500 13px/1.45 var(--pay-ui); }
  .qr-wrap { width: min(100%, 286px); margin: 18px auto 0; padding: 12px; border-radius: 22px; background: #fff; box-shadow: inset 0 0 0 1px var(--pay-line), var(--pay-shadow-sm); }
  .qr-image { display: block; width: 100%; aspect-ratio: 1; border-radius: 14px; object-fit: cover; }
  .qr-caption { margin: 10px 0 0; color: var(--pay-subtle); text-align: center; font: 550 11px/1.4 var(--pay-ui); }
  .payment-summary { margin-top: 16px; padding: 4px 0; border-top: 1px solid #eef2f0; border-bottom: 1px solid #eef2f0; }
  .summary-row { display: flex; min-height: 48px; align-items: center; justify-content: space-between; gap: 16px; }
  .summary-row + .summary-row { border-top: 1px solid #eef2f0; }
  .summary-row span { color: var(--pay-muted); font: 500 13.5px/1.25 var(--pay-ui); }
  .summary-row strong { color: var(--pay-ink); text-align: right; font: 700 14px/1.2 var(--pay-ui); font-variant-numeric: tabular-nums; }
  .summary-row .summary-amount { font: 800 18px/1.2 var(--pay-ui); letter-spacing: -.01em; }
  .open-provider { display: flex; width: 100%; min-height: 54px; align-items: center; justify-content: center; gap: 9px; margin-top: 16px; border-radius: 17px; color: #fff; background: linear-gradient(180deg, color-mix(in srgb, var(--provider-color) 84%, white), color-mix(in srgb, var(--provider-color) 90%, black)); box-shadow: 0 10px 24px -8px color-mix(in srgb, var(--provider-color) 55%, transparent), 0 2px 4px color-mix(in srgb, var(--provider-color) 30%, transparent), inset 0 1px 0 rgba(255, 255, 255, .25), inset 0 -1px 0 rgba(0, 0, 0, .15); text-decoration: none; font: 700 14px/1 var(--pay-ui); }
  .helper { display: flex; align-items: flex-start; gap: 9px; margin-top: 12px; padding: 12px 13px; border-radius: 14px; color: var(--pay-muted); background: color-mix(in srgb, var(--provider-color) 5%, white); font: 500 11.5px/1.45 var(--pay-ui); }
  .helper svg { flex: none; margin-top: 1px; color: var(--provider-color, var(--pay-green)); }
  .payment-actions { display: grid; grid-template-columns: 1fr 1.35fr; gap: 10px; margin-top: 14px; }
  .payment-action { display: flex; min-height: 50px; align-items: center; justify-content: center; padding: 0 10px; border-radius: 16px; color: var(--pay-muted); background: #fff; box-shadow: inset 0 0 0 1px var(--pay-line), var(--pay-shadow-sm); text-decoration: none; font: 650 12px/1 var(--pay-ui); }
  .payment-action-primary { color: #fff; background: linear-gradient(180deg, color-mix(in srgb, var(--provider-color) 84%, white), color-mix(in srgb, var(--provider-color) 90%, black)); box-shadow: 0 8px 18px -10px color-mix(in srgb, var(--provider-color) 70%, transparent), inset 0 1px 0 rgba(255, 255, 255, .22); }
  .payment-trust { margin: 14px 0 0; color: var(--pay-subtle); text-align: center; font: 500 10.5px/1.4 var(--pay-ui); }
  @media (max-width: 360px) {
    .method-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .method-button { min-height: 54px; flex-direction: row; justify-content: flex-start; padding: 7px 9px; }
    .method-name { text-align: left; }
    .payment-main { width: min(100% - 24px, 430px); }
    .pay-card,.provider-card { padding: 15px; }
  }
</style>

@php
  $query = [
      'package' => $packageKey,
      'amount' => $amount,
      'currency' => $currency,
  ];
  $providerColors = [
      'landbank' => '#006b3f',
      'bpi' => '#005baa',
      'bdo' => '#003b70',
      'unionbank' => '#f36f21',
      'gcash' => '#007cff',
      'maya' => '#00a86b',
      'grabpay' => '#00b14f',
      'shopeepay' => '#ee4d2d',
  ];
  $providerInitials = [
      'landbank' => 'LANDBANK',
      'bpi' => 'BPI',
      'bdo' => 'BDO',
      'unionbank' => 'UB',
      'gcash' => 'G',
      'maya' => 'M',
      'grabpay' => 'GRAB',
      'shopeepay' => 'SHOPEE',
  ];
@endphp

<div class="payment-page" style="--provider-color: {{ $provider['background'] }}; --provider-accent: {{ $provider['accent'] }};">
  <header class="payment-chrome">
    <nav class="payment-nav" aria-label="Payment navigation">
      <a class="payment-icon-button" href="{{ route('invest.purchase', ['package' => $packageKey]) }}" aria-label="Back to purchase">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
      </a>
      <a class="payment-brand" href="{{ route('invest') }}">
        <span class="payment-brand-mark"><img src="{{ asset('logo.png') }}" alt=""></span>
        LuLu Payments
      </a>
      <span class="payment-nav-spacer" aria-hidden="true"></span>
    </nav>
  </header>

  <main class="payment-main">
    <section class="payment-hero">
      <div class="payment-eyebrow">Secure payment</div>
      <h1 class="payment-title">Scan to pay</h1>
      <p class="payment-intro">Choose your bank or e-wallet, then scan the QR code to pay for your {{ $package['name'] }} Bond.</p>
    </section>

    <section class="provider-card" aria-label="{{ $provider['name'] }} payment method">
      <div class="provider-card-top">
        <div class="provider-identity">
          <span class="provider-logo">
            @if ($provider['logo'])
              <img src="{{ asset($provider['logo']) }}" alt="">
            @else
              {{ $providerInitials[$providerKey] ?? $provider['name'] }}
            @endif
          </span>
          <span class="provider-name">{{ $provider['name'] }}</span>
        </div>
        <span class="secure-pill"><i aria-hidden="true"></i>Secure provider</span>
      </div>
      <p class="provider-copy">Your selected payment method for this investment. Keep this page open until your transfer is completed.</p>
    </section>

    <section class="method-panel" aria-label="Choose a bank or e-wallet">
      <h2 class="method-heading">Choose a payment method</h2>
      @foreach (['banks' => 'Banks', 'wallets' => 'E-wallets'] as $groupKey => $groupLabel)
        <div class="method-group">
          <h3 class="method-group-title">{{ $groupLabel }}</h3>
          <div class="method-grid">
            @foreach ($paymentProviders[$groupKey] as $key => $option)
              <a
                class="method-button {{ $providerKey === $key ? 'is-active' : '' }}"
                href="{{ route('invest.payment', ['provider' => $key, ...$query]) }}"
                @if ($providerKey === $key) aria-current="true" @endif
              >
                <span class="method-logo" style="--method-color: {{ $providerColors[$key] ?? '#075a3b' }}">
                  @if ($option['logo'])
                    <img src="{{ asset($option['logo']) }}" alt="">
                  @else
                    {{ $providerInitials[$key] ?? $option['name'] }}
                  @endif
                </span>
                <span class="method-name">{{ $option['name'] }}</span>
              </a>
            @endforeach
          </div>
        </div>
      @endforeach
    </section>

    <section class="pay-card" aria-label="{{ $provider['name'] }} payment QR code">
      <div class="pay-head">
        <h2 class="pay-heading">Payment QR</h2>
        <p class="pay-description">Scan with {{ $provider['name'] }} to complete your investment payment.</p>
      </div>
      <div class="qr-wrap">
        <img class="qr-image" src="{{ asset($provider['qr']) }}" alt="{{ $provider['name'] }} payment QR code">
      </div>
      <p class="qr-caption">Keep this page open until your transfer is completed.</p>

      <div class="payment-summary">
        <div class="summary-row">
          <span>{{ $package['name'] }} Bond</span>
          <strong>{{ $package['name'] }}</strong>
        </div>
        <div class="summary-row">
          <span>Amount to pay</span>
          <strong class="summary-amount">{{ $currency === 'PHP' ? '₱' : '$' }}{{ number_format($amount, 2) }}</strong>
        </div>
      </div>

      <a class="open-provider" href="{{ $provider['launch_url'] }}" target="_blank" rel="noopener noreferrer">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3h7v7M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
        Open {{ $provider['name'] }}
      </a>
      <div class="helper">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg>
        <span>On mobile, open {{ $provider['name'] }} if prompted. You may also save or scan this QR from your payment app's gallery option.</span>
      </div>
      <div class="payment-actions">
        <a class="payment-action" href="{{ route('invest.purchase', ['package' => $packageKey]) }}">Back</a>
        <a class="payment-action payment-action-primary" href="{{ route('invest.agreement.sign', [
          'package' => $packageKey,
          'amount' => $amount,
          'currency' => $currency,
          'payment_method' => $provider['payment_method'] ?? 'bank_transfer',
        ]) }}">Payment completed</a>
      </div>
    </section>
    <p class="payment-trust">Use only the payment details shown for your selected provider. Keep your payment receipt for verification.</p>
  </main>
</div>
@endsection
