<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="{{ $provider['background'] }}">
  <title>Payment Options | Lulu Philippines</title>
  <style>
    :root {
      color-scheme:light;
      --pay-bg:#f2f5f3;
      --pay-surface:#fff;
      --pay-ink:#0a1f17;
      --pay-muted:#4b5b54;
      --pay-subtle:#8a9892;
      --pay-line:#e2e9e5;
      --pay-shadow-sm:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06);
      --pay-shadow-md:0 1px 2px rgba(6,40,28,.05),0 6px 16px -6px rgba(6,40,28,.12),0 18px 36px -18px rgba(6,40,28,.18);
      --pay-display:"Plus Jakarta Sans",Inter,system-ui,sans-serif;
      --pay-ui:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      --provider-color:{{ $provider['background'] }};
      --provider-accent:{{ $provider['accent'] }};
    }
    * { box-sizing:border-box; }
    body { min-height:100vh; margin:0; color:var(--pay-ink); background:radial-gradient(900px 600px at 15% 10%,rgba(20,168,109,.16),transparent 60%),radial-gradient(700px 500px at 90% 90%,rgba(198,243,107,.12),transparent 60%),#e7ece9; font-family:var(--pay-ui); -webkit-font-smoothing:antialiased; }
    .payment-page { min-height:100vh; padding-bottom:28px; }
    .payment-chrome { position:sticky; top:0; z-index:5; background:rgba(242,245,243,.9); backdrop-filter:saturate(180%) blur(20px); }
    .payment-nav { display:grid; max-width:480px; min-height:60px; grid-template-columns:40px 1fr 40px; align-items:center; gap:12px; margin:auto; padding:6px 16px; }
    .payment-icon-button { display:grid; width:40px; height:40px; place-items:center; border-radius:50%; color:var(--pay-ink); background:#fff; box-shadow:var(--pay-shadow-sm),inset 0 0 0 1px rgba(10,31,23,.05); text-decoration:none; }
    .payment-brand { display:flex; align-items:center; justify-self:center; gap:8px; color:var(--pay-ink); font:750 16px/1 var(--pay-display); letter-spacing:-.02em; text-decoration:none; }
    .payment-brand-mark { display:grid; width:28px; height:28px; place-items:center; border-radius:9px; color:#fff; background:var(--pay-green,#087244); font-weight:800; }
    .payment-nav-spacer { width:40px; height:40px; }
    .payment-main { width:min(100% - 32px,430px); margin:0 auto; padding:15px 0 0; }
    .payment-hero { padding:8px 2px 4px; }
    .payment-eyebrow { color:var(--provider-color); font:700 10.5px/1 var(--pay-ui); letter-spacing:.14em; text-transform:uppercase; }
    .payment-title { margin:8px 0 0; font:800 30px/1.06 var(--pay-display); letter-spacing:-.045em; }
    .payment-intro { max-width:340px; margin:8px 0 0; color:var(--pay-muted); font:450 14px/1.5 var(--pay-ui); }
    .provider-card { position:relative; overflow:hidden; margin-top:18px; padding:18px; border-radius:24px; color:#fff; background:radial-gradient(100% 120% at 100% 0%,color-mix(in srgb,var(--provider-accent) 48%,transparent),transparent 54%),radial-gradient(75% 90% at 0% 100%,color-mix(in srgb,var(--provider-accent) 18%,transparent),transparent 58%),linear-gradient(160deg,color-mix(in srgb,var(--provider-color) 86%,white),var(--provider-color) 52%,color-mix(in srgb,var(--provider-color) 72%,#071b12)); box-shadow:0 2px 4px rgba(4,30,20,.08),0 12px 24px -8px color-mix(in srgb,var(--provider-color) 35%,transparent),0 32px 56px -24px rgba(2,26,18,.3),inset 0 1px 0 rgba(255,255,255,.2); }
    .provider-card::after { position:absolute; right:-72px; bottom:-92px; width:180px; height:180px; border:1px solid rgba(255,255,255,.12); border-radius:50%; box-shadow:0 0 0 24px rgba(255,255,255,.04),0 0 0 50px rgba(255,255,255,.025); content:""; }
    .provider-card-top { position:relative; z-index:1; display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .provider-identity { display:flex; min-width:0; align-items:center; gap:9px; }
    .provider-logo { display:grid; width:38px; height:38px; flex:0 0 38px; place-items:center; overflow:hidden; border-radius:12px; color:var(--provider-color); background:rgba(255,255,255,.94); box-shadow:inset 0 0 0 1px rgba(255,255,255,.5),0 6px 16px -10px rgba(0,0,0,.5); font:800 9px/1 var(--pay-ui); text-align:center; }
    .provider-name { overflow:hidden; font:800 18px/1 var(--pay-display); letter-spacing:-.03em; text-overflow:ellipsis; white-space:nowrap; }
    .secure-pill { display:inline-flex; height:28px; flex:none; align-items:center; gap:6px; padding:0 10px; border-radius:99px; color:#dffbef; background:rgba(255,255,255,.11); box-shadow:inset 0 0 0 1px rgba(255,255,255,.15); font:650 10.5px/1 var(--pay-ui); }
    .secure-pill i { width:6px; height:6px; border-radius:50%; background:var(--provider-accent); }
    .provider-copy { position:relative; z-index:1; margin:15px 0 0; color:rgba(232,255,244,.82); font:500 12.5px/1.45 var(--pay-ui); }
    .method-panel,.pay-card { margin-top:16px; padding:16px; border-radius:20px; background:var(--pay-surface); box-shadow:var(--pay-shadow-md),inset 0 0 0 1px rgba(10,31,23,.04); }
    .method-heading { margin:0; font:750 14px/1.2 var(--pay-display); letter-spacing:-.02em; }
    .method-group { margin-top:14px; }
    .method-group + .method-group { margin-top:15px; padding-top:14px; border-top:1px solid #eef2f0; }
    .method-group-title { margin:0 0 8px; color:var(--pay-subtle); font:700 9.5px/1 var(--pay-ui); letter-spacing:.12em; text-transform:uppercase; }
    .method-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:7px; }
    .method-button { display:flex; min-height:62px; flex-direction:column; align-items:center; justify-content:center; gap:5px; padding:7px 3px; border:1px solid var(--pay-line); border-radius:12px; color:var(--pay-muted); background:#fff; text-decoration:none; transition:border-color .18s ease,background .18s ease,transform .18s ease; }
    .method-button:hover { border-color:color-mix(in srgb,var(--provider-color) 45%,white); background:color-mix(in srgb,var(--provider-color) 5%,white); transform:translateY(-1px); }
    .method-button.is-active { border-color:color-mix(in srgb,var(--provider-color) 55%,white); color:color-mix(in srgb,var(--provider-color) 78%,#071b12); background:color-mix(in srgb,var(--provider-color) 9%,white); box-shadow:inset 0 0 0 1px color-mix(in srgb,var(--provider-color) 12%,transparent); }
    .method-logo { display:grid; width:34px; height:29px; place-items:center; overflow:hidden; border-radius:7px; color:#fff; background:var(--method-color); font:800 8px/1 var(--pay-ui); }
    .method-name { width:100%; overflow:hidden; font:650 9px/1.2 var(--pay-ui); text-align:center; text-overflow:ellipsis; white-space:nowrap; }
    .pay-card { padding:18px; border-radius:24px; }
    .pay-head { text-align:center; }
    .pay-heading { margin:0; font:800 23px/1.1 var(--pay-display); letter-spacing:-.035em; }
    .pay-description { max-width:320px; margin:8px auto 0; color:var(--pay-muted); font:500 13px/1.45 var(--pay-ui); }
    .qr-wrap { width:min(100%,286px); margin:18px auto 0; padding:12px; border-radius:22px; background:#fff; box-shadow:inset 0 0 0 1px var(--pay-line),var(--pay-shadow-sm); }
    .qr-image { display:block; width:100%; aspect-ratio:1; border-radius:14px; object-fit:contain; }
    .qr-caption { margin:10px 0 0; color:var(--pay-subtle); font:550 11px/1.4 var(--pay-ui); text-align:center; }
    .payment-summary { margin-top:16px; padding:4px 0; border-top:1px solid #eef2f0; border-bottom:1px solid #eef2f0; }
    .summary-row { display:flex; min-height:48px; align-items:center; justify-content:space-between; gap:16px; }
    .summary-row span { color:var(--pay-muted); font:500 13.5px/1.25 var(--pay-ui); }
    .summary-row strong { font:700 14px/1.2 var(--pay-ui); text-align:right; }
    .download-button { display:flex; width:100%; min-height:54px; align-items:center; justify-content:center; gap:9px; margin-top:16px; border-radius:17px; color:#fff; background:linear-gradient(180deg,color-mix(in srgb,var(--provider-color) 84%,white),color-mix(in srgb,var(--provider-color) 90%,black)); box-shadow:0 10px 24px -8px color-mix(in srgb,var(--provider-color) 55%,transparent),0 2px 4px color-mix(in srgb,var(--provider-color) 30%,transparent),inset 0 1px 0 rgba(255,255,255,.25),inset 0 -1px 0 rgba(0,0,0,.15); font:700 14px/1 var(--pay-ui); text-decoration:none; }
    .helper { display:flex; align-items:flex-start; gap:9px; margin-top:12px; padding:12px 13px; border-radius:14px; color:var(--pay-muted); background:color-mix(in srgb,var(--provider-color) 5%,white); font:500 11.5px/1.45 var(--pay-ui); }
    .payment-trust { margin:14px 0 0; color:var(--pay-subtle); font:500 10.5px/1.4 var(--pay-ui); text-align:center; }
    a:focus-visible { outline:3px solid #b8cc56; outline-offset:3px; }
    @media(max-width:360px) { .method-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .method-button { min-height:54px; flex-direction:row; justify-content:flex-start; padding:7px 9px; } .method-name { text-align:left; } .payment-main { width:min(100% - 24px,430px); } .pay-card,.provider-card { padding:15px; } }
  </style>
</head>
<body>
<div class="payment-page">
  <header class="payment-chrome">
    <nav class="payment-nav" aria-label="Payment navigation">
      <a class="payment-icon-button" href="{{ route('public.home') }}" aria-label="Back to Lulu Philippines">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
      </a>
      <a class="payment-brand" href="{{ route('public.home') }}"><span class="payment-brand-mark" aria-hidden="true">L</span>LuLu Payments</a>
      <span class="payment-nav-spacer" aria-hidden="true"></span>
    </nav>
  </header>

  <main class="payment-main">
    <section class="payment-hero">
      <div class="payment-eyebrow">Secure payment</div>
      <h1 class="payment-title">Scan to pay</h1>
      <p class="payment-intro">Choose your bank or e-wallet, then scan the QR code to make a payment.</p>
    </section>

    <section class="provider-card" aria-label="{{ $provider['name'] }} payment method">
      <div class="provider-card-top">
        <div class="provider-identity">
          <span class="provider-logo">{{ $provider['initials'] }}</span>
          <span class="provider-name">{{ $provider['name'] }}</span>
        </div>
        <span class="secure-pill"><i aria-hidden="true"></i>Secure provider</span>
      </div>
      <p class="provider-copy">Use the official QR shown here. Please verify the recipient and amount before confirming in your payment app.</p>
    </section>

    <section class="method-panel" aria-label="Choose a bank or e-wallet">
      <h2 class="method-heading">Choose a payment method</h2>
      <div class="method-group">
        <h3 class="method-group-title">Banks</h3>
        <div class="method-grid">
          @foreach (['bpi', 'landbank'] as $key)
            <a class="method-button {{ $providerKey === $key ? 'is-active' : '' }}" href="{{ route('payment-options', ['provider' => $key]) }}" @if ($providerKey === $key) aria-current="true" @endif>
              <span class="method-logo" style="--method-color:{{ $providers[$key]['background'] }}">{{ $providers[$key]['initials'] }}</span>
              <span class="method-name">{{ $providers[$key]['name'] }}</span>
            </a>
          @endforeach
        </div>
      </div>
      <div class="method-group">
        <h3 class="method-group-title">E-wallets</h3>
        <div class="method-grid">
          <a class="method-button {{ $providerKey === 'palawanpay' ? 'is-active' : '' }}" href="{{ route('payment-options', ['provider' => 'palawanpay']) }}" @if ($providerKey === 'palawanpay') aria-current="true" @endif>
            <span class="method-logo" style="--method-color:{{ $providers['palawanpay']['background'] }}">{{ $providers['palawanpay']['initials'] }}</span>
            <span class="method-name">Palawan Pay</span>
          </a>
        </div>
      </div>
    </section>

    <section class="pay-card" aria-label="{{ $provider['name'] }} payment QR code">
      <div class="pay-head">
        <h2 class="pay-heading">Payment QR</h2>
        <p class="pay-description">Scan with {{ $provider['name'] }} to make your payment.</p>
      </div>
      <div class="qr-wrap">
        <img class="qr-image" src="{{ asset($provider['qr']) }}" alt="{{ $provider['name'] }} payment QR code">
      </div>
      <p class="qr-caption">On mobile, save the QR image and select it from your payment app.</p>
      <div class="payment-summary">
        <div class="summary-row">
          <span>Payment provider</span>
          <strong>{{ $provider['name'] }}</strong>
        </div>
      </div>
      <a class="download-button" href="{{ asset($provider['qr']) }}" download="{{ $provider['qr'] }}">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/></svg>
        Download {{ $provider['name'] }} QR
      </a>
      <div class="helper">Confirm the payment details with Lulu through an official channel before sending funds.</div>
    </section>
    <p class="payment-trust">This page is public and does not require an account sign-in.</p>
  </main>
</div>
</body>
</html>
