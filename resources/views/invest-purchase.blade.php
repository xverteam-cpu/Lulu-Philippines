@extends('layouts.app')

@section('content')
<style>
  html.scroll-locked, html.scroll-locked body { overflow:hidden; overscroll-behavior:none; }
  body { background:#f4f6f9 !important; font-family:Inter, 'Plus Jakarta Sans', system-ui, sans-serif; }
  .container { max-width:none !important; margin:0 !important; padding:0 !important; }
  .purchase-page { min-height:100vh; color:#17202a; }
  .purchase-header { display:flex; align-items:center; gap:12px; min-height:80px; padding:14px 16px; box-sizing:border-box; color:#fff; background:#098a58; }
  .purchase-back { display:inline-flex; align-items:center; justify-content:center; width:42px; height:42px; color:#fff; font-size:32px; font-weight:800; line-height:1; text-decoration:none; }
  .purchase-header h1 { margin:0; font-size:25px; line-height:1.1; font-weight:900; }
  .purchase-shell { width:min(100% - 32px, 520px); margin:24px auto 40px; }
  .purchase-card { background:transparent; padding:0; box-shadow:none; }
  .purchase-title { margin:0; color:#101010; font-size:28px; line-height:1.1; font-weight:900; }
  .purchase-copy { margin:14px 0 24px; color:#64748b; font-size:16px; line-height:24px; }
  .purchase-label { display:block; margin-bottom:8px; color:#111827; font-size:13px; font-weight:900; text-transform:uppercase; }
  .purchase-input { width:100%; box-sizing:border-box; border:1px solid #d9dee5; border-radius:12px; padding:18px 14px; color:#17202a; background:#fafafa; font-size:22px; font-weight:800; }
  .purchase-range { display:flex; justify-content:space-between; gap:12px; margin:8px 0 18px; color:#17202a; font-size:13px; font-weight:800; }
  .currency-toggle, .payment-options { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; margin:18px 0; }
  .currency-button, .payment-choice, .purchase-submit { min-height:48px; border:1px solid #d9dee5; border-radius:12px; background:#f7f8fa; color:#64748b; font-size:15px; font-weight:900; cursor:pointer; }
  .currency-button.is-active, .purchase-submit { border-color:#166534; background:#166534; color:#fff; }
  .estimate-grid { display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:8px; margin:24px 0 18px; }
  .estimate-card { min-height:76px; padding:12px 10px; border:1px solid #e2e8f0; border-radius:12px; background:#fafafa; }
  .estimate-label { color:#64748b; font-size:11px; font-weight:900; text-transform:uppercase; }
  .estimate-value { margin-top:5px; color:#166534; font-size:16px; font-weight:900; }
  .estimate-note { margin:0 0 22px; color:#64748b; font-size:14px; line-height:21px; }
  .payment-section { display:none; }
  .payment-choice { padding:10px; text-align:left; }
  .payment-choice span { display:block; margin-top:4px; color:#64748b; font-size:12px; font-weight:500; }
  .payment-choice.is-selected { border-color:#166534; background:#e8f8ee; color:#166534; }
  .purchase-submit { width:100%; margin-top:12px; }
  .agreement-box { margin:22px 0 8px; padding:16px; border:1px solid #d9dee5; border-radius:14px; background:#f8fafc; }
  .agreement-box h3 { margin:0 0 6px; font-size:16px; color:#111827; }
  .agreement-box p { margin:0 0 12px; color:#64748b; font-size:13px; line-height:19px; }
  .agreement-link { color:#166534; font-weight:900; text-decoration:underline; }
  .agreement-signature { width:100%; box-sizing:border-box; margin-top:10px; padding:12px; border:1px solid #d9dee5; border-radius:10px; background:#fff; font-size:15px; }
  .signature-pad { display:block; width:100%; height:150px; margin-top:10px; border:1px solid #94a3b8; border-radius:10px; background:#fff; touch-action:none; cursor:crosshair; }
  .signature-actions { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:8px; color:#64748b; font-size:12px; }
  .signature-clear { border:0; background:transparent; color:#166534; font-weight:900; cursor:pointer; }
  .agreement-check { display:flex; gap:8px; align-items:flex-start; margin-top:12px; color:#334155; font-size:13px; line-height:19px; }
  .agreement-preview-link { display:inline-block; margin-top:10px; color:#166534; font-size:13px; font-weight:900; text-decoration:underline; }
  .form-error { margin-bottom:16px; padding:12px; border-radius:10px; background:#fee2e2; color:#991b1b; font-size:14px; }
  .payment-modal { position:fixed; inset:0; z-index:50; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(15,23,42,.58); }
  .payment-modal.is-open { display:flex; }
  .payment-modal-card { width:min(100%, 420px); border-radius:16px; background:#fff; padding:24px 16px 18px; box-shadow:0 24px 70px rgba(15,23,42,.24); }
  .payment-modal-title { margin:0; color:#111827; font-size:24px; font-weight:900; }
  .payment-modal-copy { margin:8px 0 18px; color:#64748b; font-size:14px; line-height:20px; }
  .payment-modal-actions { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; margin-top:18px; }
  .payment-modal-actions button { min-height:48px; border:1px solid #d9dee5; border-radius:12px; background:#f7f8fa; color:#64748b; font-size:14px; font-weight:900; cursor:pointer; }
  .payment-modal-actions .payment-modal-submit { border-color:#166534; background:#166534; color:#fff; }
  .bank-list { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; }
  .bank-option { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; min-height:128px; padding:12px; border:1px solid #d9dee5; border-radius:12px; background:#f7f8fa; color:#17202a; font-size:14px; font-weight:900; cursor:pointer; }
  .bank-option img { width:min(100%, 150px); height:64px; object-fit:contain; }
  .bank-text-logo { display:flex; align-items:center; justify-content:center; width:100px; height:36px; border-radius:6px; color:#fff; font-size:22px; font-weight:950; letter-spacing:-1px; }
  .bank-text-logo-bdo { background:#003b70; }
  .bank-text-logo-unionbank { background:#e21b2d; }
  .wallet-text-logo-gcash { background:#007cff; }
  .wallet-text-logo-maya { background:#00a86b; }
  .wallet-text-logo-grabpay { background:#00b14f; }
  .wallet-text-logo-shopeepay { background:#ee4d2d; }
  .qr-image { display:block; width:min(100%, 280px); max-height:52vh; margin:18px auto; object-fit:contain; border-radius:10px; }
  @media (min-width:760px) { .purchase-shell { margin-top:36px; } }
</style>

<style>
  :root {
    --bp-ink:#0a1f17; --bp-muted:#617169; --bp-line:#e2e9e5; --bp-green:#075a3b;
    --bp-green-bright:#0e8a5a; --bp-mint:#e6f7ef; --bp-surface:#fff;
    --bp-shadow:0 12px 32px rgba(2,26,18,.08);
  }
  html { scroll-behavior:smooth; }
  body { display:block !important; min-height:100vh; padding:0 !important; background:#e7ece9 !important; color:var(--bp-ink); }
  .container { max-width:none !important; margin:0 !important; padding:0 !important; }
  .purchase-page { width:min(100%, 520px); min-height:100vh; margin:0 auto; padding-bottom:32px; background:#f2f5f3; color:var(--bp-ink); box-shadow:0 0 40px rgba(2,26,18,.07); }
  .bp-topbar { position:sticky; top:0; z-index:20; display:grid; grid-template-columns:42px 1fr auto; align-items:center; gap:12px; padding:14px 18px; background:rgba(242,245,243,.94); backdrop-filter:blur(16px); border-bottom:1px solid rgba(10,31,23,.06); }
  .bp-back { display:grid; place-items:center; width:40px; height:40px; border-radius:50%; background:#fff; color:var(--bp-ink); box-shadow:0 2px 8px rgba(2,26,18,.08); text-decoration:none; font-size:22px; }
  .bp-brand { display:flex; align-items:center; justify-self:center; gap:8px; font-size:15px; font-weight:800; letter-spacing:-.02em; }
  .bp-brand img { width:26px; height:26px; object-fit:contain; border-radius:8px; }
  .bp-top-tag { color:var(--bp-muted); font-size:11px; font-weight:700; }
  .bp-content { padding:22px 20px 32px; }
  .bp-eyebrow { color:#718078; font-size:10px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
  .bp-title { margin:8px 0 0; color:var(--bp-ink); font-size:28px; font-weight:850; line-height:1.1; letter-spacing:-.04em; }
  .bp-card { position:relative; isolation:isolate; display:flex; min-height:156px; flex-direction:column; justify-content:space-between; overflow:hidden; margin-top:18px; padding:18px; border-radius:22px; color:#3a2706; background:radial-gradient(120% 90% at 0 0,rgba(255,247,214,.92),transparent 48%),linear-gradient(140deg,#fcebb9 0%,#e6c26a 28%,#b98a33 56%,#f1d48a 78%,#a57426 100%); box-shadow:0 10px 24px rgba(65,46,11,.2),inset 0 1px 0 rgba(255,255,255,.72); }
  .bp-card::before { position:absolute; z-index:-1; right:-44px; bottom:-105px; width:240px; height:240px; border:1px solid rgba(255,255,255,.55); border-radius:50%; box-shadow:0 0 0 17px rgba(142,100,32,.12),0 0 0 35px rgba(255,255,255,.18),0 0 0 53px rgba(142,100,32,.09); content:""; }
  .bp-card-top,.bp-card-stats { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }
  .bp-card-name { font-size:22px; font-weight:850; letter-spacing:-.03em; }
  .bp-card-caption { margin-top:5px; color:rgba(58,39,6,.68); font-size:10px; font-weight:750; letter-spacing:.13em; text-transform:uppercase; }
  .bp-card-chip { display:grid; width:39px; height:29px; place-items:center; border:1px solid rgba(90,60,10,.3); border-radius:7px; background:linear-gradient(135deg,#fff4cf,#d9ae55 52%,#f5dd98); }
  .bp-card-chip::before { width:17px; height:17px; border:1px solid rgba(90,60,10,.38); border-radius:4px; content:""; }
  .bp-stat-label { color:rgba(58,39,6,.7); font-size:9px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
  .bp-stat-value { margin-top:5px; color:#3a2706; font-size:22px; font-weight:850; font-variant-numeric:tabular-nums; }
  .bp-stat-value small { margin-left:4px; font-size:11px; font-weight:750; }
  .bp-lede { margin:16px 2px 20px; color:#4b5b54; font-size:14px; line-height:1.55; }
  .bp-lede strong { color:var(--bp-ink); }
  .bp-panel { padding:17px; border:1px solid rgba(10,31,23,.06); border-radius:20px; background:#fff; box-shadow:0 2px 10px rgba(6,40,28,.04); }
  .bp-panel-head { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:13px; }
  .bp-label { color:#65746d; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
  .bp-currency { display:flex; gap:3px; padding:3px; border:1px solid var(--bp-line); border-radius:11px; background:#f3f6f4; }
  .bp-currency button { min-width:47px; padding:7px 9px; border-radius:8px; color:#64746c; font-size:11px; font-weight:800; }
  .bp-currency button.is-active { background:#fff; color:var(--bp-green); box-shadow:0 1px 4px rgba(2,26,18,.12); }
  .bp-amount-field { display:flex; align-items:center; gap:8px; min-height:63px; padding:0 15px; border:1px solid #dce5df; border-radius:15px; background:#fbfcfb; }
  .bp-amount-field:focus-within { border-color:#0e8a5a; box-shadow:0 0 0 3px rgba(14,138,90,.12); }
  .bp-symbol { color:#617169; font-size:17px; font-weight:800; }
  .bp-amount { min-width:0; width:100%; border:0; outline:0; background:transparent; color:var(--bp-ink); font-size:27px; font-weight:850; letter-spacing:-.035em; font-variant-numeric:tabular-nums; }
  .bp-limits { display:flex; justify-content:space-between; gap:8px; margin:9px 1px 0; color:#7d8983; font-size:10px; }
  .bp-limits strong { color:#495b52; font-variant-numeric:tabular-nums; }
  .bp-error { display:none; margin-top:8px; color:#b42318; font-size:12px; line-height:1.45; }
  .bp-error.is-visible { display:block; }
  .bp-chips { display:flex; gap:7px; overflow-x:auto; margin:14px 0 6px; scrollbar-width:none; }
  .bp-chip { flex:0 0 auto; padding:8px 10px; border:1px solid var(--bp-line); border-radius:99px; background:#fff; color:#53645b; font-size:10px; font-weight:750; font-variant-numeric:tabular-nums; }
  .bp-chip.is-active { border-color:#bce8d2; background:var(--bp-mint); color:var(--bp-green); }
  .bp-range { width:100%; margin:7px 0 0; accent-color:var(--bp-green-bright); }
  .bp-range-scale { display:flex; justify-content:space-between; margin-top:2px; color:#87948d; font-size:9px; font-variant-numeric:tabular-nums; }
  .bp-fx { display:flex; gap:7px; align-items:flex-start; margin-top:12px; padding:10px; border-radius:11px; background:#f2f7f4; color:#617169; font-size:10px; line-height:1.45; }
  .bp-fx[hidden] { display:none; }
  .bp-fx strong { color:#3b5145; }
  .bp-section-heading { display:flex; align-items:baseline; justify-content:space-between; gap:8px; margin:23px 2px 10px; }
  .bp-section-heading h2 { color:var(--bp-ink); font-size:16px; font-weight:850; letter-spacing:-.02em; }
  .bp-section-heading span { color:#85928b; font-size:9px; }
  .bp-projection { display:grid; grid-template-columns:1fr 1fr; gap:9px; }
  .bp-tile { min-height:91px; padding:13px; border:1px solid rgba(10,31,23,.05); border-radius:16px; background:#fff; }
  .bp-tile-dark { color:#fff; border-color:#06462e; background:radial-gradient(120% 90% at 100% 0,rgba(62,224,161,.35),transparent 55%),linear-gradient(150deg,#0b6a47,#04321f); }
  .bp-tile-wide { grid-column:1/-1; display:flex; align-items:center; justify-content:space-between; gap:12px; min-height:76px; }
  .bp-tile-label { color:#84928a; font-size:9px; font-weight:800; letter-spacing:.11em; text-transform:uppercase; }
  .bp-tile-dark .bp-tile-label { color:rgba(232,255,244,.65); }
  .bp-tile-value { margin-top:8px; color:var(--bp-green); font-size:20px; font-weight:850; font-variant-numeric:tabular-nums; letter-spacing:-.03em; }
  .bp-tile-dark .bp-tile-value { color:#fff; }
  .bp-tile-sub { margin-top:4px; color:#829087; font-size:9px; }
  .bp-tile-dark .bp-tile-sub { color:rgba(232,255,244,.65); }
  .bp-formula { margin:10px 3px 0; color:#87948d; font-size:10px; line-height:1.45; }
  .bp-disclosure { margin-top:17px; padding:14px; border:1px solid #e5e8dc; border-radius:15px; background:#fffdf6; }
  .bp-disclosure h3 { margin:0 0 6px; color:#3a3424; font-size:12px; font-weight:850; }
  .bp-disclosure p { margin:0; color:#736b58; font-size:10px; line-height:1.5; }
  .bp-doc-link { display:inline-block; margin-top:9px; color:var(--bp-green); font-size:10px; font-weight:800; text-decoration:underline; }
  .agreement-box { margin:13px 0 0; padding:14px; border:1px solid var(--bp-line); border-radius:15px; background:#fff; }
  .agreement-box h3 { margin:0 0 6px; color:var(--bp-ink); font-size:13px; font-weight:850; }
  .agreement-box p { margin:0 0 9px; color:#64746c; font-size:10px; line-height:1.5; }
  .agreement-link,.agreement-preview-link { color:var(--bp-green); font-weight:800; }
  .signature-pad { height:110px; margin-top:8px; border:1px solid #cdd8d1; border-radius:10px; background:#fbfcfb; }
  .signature-actions { color:#7d8983; font-size:10px; }
  .signature-clear { color:var(--bp-green); font-size:10px; }
  .agreement-check { color:#43534b; font-size:10px; line-height:1.45; }
  .agreement-preview-link { font-size:10px; }
  .form-error { margin-bottom:13px; padding:11px; border-radius:10px; background:#fee2e2; color:#991b1b; font-size:12px; }
  .bp-cta-wrap { position:sticky; bottom:0; z-index:10; margin:18px -20px -32px; padding:12px 20px 17px; border-top:1px solid rgba(10,31,23,.06); background:rgba(242,245,243,.96); backdrop-filter:blur(14px); }
  .bp-cta-hint { margin-bottom:8px; color:#78867e; font-size:10px; }
  .purchase-submit { display:flex; align-items:center; justify-content:center; gap:9px; width:100%; min-height:52px; margin:0; border:0; border-radius:15px; background:linear-gradient(135deg,#0e8a5a,#04321f); color:#fff; font-size:14px; font-weight:850; box-shadow:0 7px 18px rgba(7,90,59,.2); }
  .purchase-submit:disabled { opacity:.48; cursor:not-allowed; box-shadow:none; }
  .bond-review { position:fixed; inset:0; z-index:60; display:flex; align-items:flex-end; justify-content:center; padding:0; background:rgba(2,26,18,.42); -webkit-backdrop-filter:blur(3px); backdrop-filter:blur(3px); opacity:0; visibility:hidden; transition:opacity .35s ease,visibility 0s linear .5s; }
  .bond-review.is-open { opacity:1; visibility:visible; transition:opacity .35s ease,visibility 0s; }
  .bond-review-sheet { width:min(100%, 520px); max-height:calc(100% - 24px); overflow-y:auto; overscroll-behavior:contain; scrollbar-width:none; padding:10px 20px max(30px, env(safe-area-inset-bottom)); border-radius:30px 30px 0 0; background:#f2f5f3; box-shadow:0 -20px 50px -10px rgba(2,26,18,.35),inset 0 1px 0 rgba(255,255,255,.9); transform:translateY(105%); visibility:hidden; transition:transform .5s cubic-bezier(.2,.9,.2,1),visibility 0s linear .5s; }
  .bond-review-sheet::-webkit-scrollbar { display:none; }
  .bond-review.is-open .bond-review-sheet { transform:none; visibility:visible; transition:transform .5s cubic-bezier(.2,.9,.2,1),visibility 0s; }
  .bond-review-handle { width:40px; height:5px; margin:0 auto 16px; border-radius:99px; background:#c9d3ce; }
  .bond-review-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; }
  .bond-review-heading h2 { margin:0; color:#0a1f17; font-size:21px; font-weight:800; line-height:1.1; letter-spacing:-.03em; }
  .bond-review-close { display:grid; width:34px; height:34px; flex:0 0 34px; place-items:center; border-radius:50%; background:#e6ece9; color:#4b5b54; font-size:20px; transition:transform .2s ease; }
  .bond-review-close:active { transform:scale(.9); }
  .bond-review-hero { position:relative; overflow:hidden; margin-top:16px; padding:18px; border-radius:22px; color:#fff; background:radial-gradient(120% 90% at 105% -10%,rgba(62,224,161,.5),transparent 55%),linear-gradient(160deg,#0b6a47 0%,#054a31 45%,#022418 100%); box-shadow:0 2px 4px rgba(4,30,20,.08),0 12px 24px -8px rgba(4,30,20,.22),0 32px 56px -24px rgba(4,30,20,.38),inset 0 1px 0 rgba(255,255,255,.2); }
  .bond-review-hero::before { position:absolute; inset:0; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 .5 0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); opacity:.08; mix-blend-mode:overlay; content:""; pointer-events:none; }
  .bond-review-hero-label { color:rgba(232,255,244,.65); font-size:10px; font-weight:600; letter-spacing:.14em; text-transform:uppercase; }
  .bond-review-hero-value { margin-top:10px; color:#fff; font-size:clamp(30px,8.7vw,36px); font-weight:750; letter-spacing:-.04em; line-height:1; font-variant-numeric:tabular-nums; overflow-wrap:anywhere; }
  .bond-review-hero-sub { margin-top:8px; color:rgba(232,255,244,.65); font-size:12px; line-height:1.3; }
  .bond-review-rows { margin-top:14px; padding:4px 16px; border-radius:20px; background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06),inset 0 0 0 1px rgba(10,31,23,.04); }
  .bond-review-row { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:13px 0; color:#4b5b54; font-size:13.5px; line-height:1.2; }
  .bond-review-row + .bond-review-row { border-top:1px solid #eef2f0; }
  .bond-review-row strong { color:#0a1f17; text-align:right; font-weight:650; font-variant-numeric:tabular-nums; }
  .bond-review-note { margin:12px 2px 16px; color:#8a9892; font-size:11.5px; line-height:1.45; }
  .bond-review-actions { position:sticky; bottom:-30px; display:grid; grid-template-columns:1fr 2fr; gap:10px; margin:0 -20px -30px; padding:0 20px max(30px, env(safe-area-inset-bottom)); background:#f2f5f3; }
  .bond-review-actions button { height:56px; border-radius:18px; font-size:15px; font-weight:650; }
  .bond-review-edit { background:#fff; color:#0a1f17; box-shadow:inset 0 0 0 1px #e2e9e5,0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06); transition:transform .2s ease; }
  .bond-review-edit:active { transform:scale(.97); }
  .bond-review-confirm { background:linear-gradient(135deg,#1fb97a,#054a31); color:#fff; box-shadow:0 7px 18px rgba(7,90,59,.25),inset 0 1px 0 rgba(255,255,255,.2); }
  @media (min-width:600px) { .bond-review { align-items:center; padding:24px; } .bond-review-sheet { max-height:min(88vh,820px); border-radius:30px; } .bond-review-actions { bottom:-30px; } }

  .payment-modal { display:flex; align-items:flex-end; justify-content:center; padding:0; background:rgba(2,26,18,.42); -webkit-backdrop-filter:blur(3px); backdrop-filter:blur(3px); opacity:0; visibility:hidden; pointer-events:none; transition:opacity .3s ease,visibility 0s linear .45s; }
  .payment-modal.is-open { display:flex; opacity:1; visibility:visible; pointer-events:auto; transition:opacity .3s ease,visibility 0s; }
  .payment-modal-card { width:min(100%,520px); max-height:calc(100% - 24px); overflow-y:auto; overscroll-behavior:contain; padding:10px 20px max(24px,env(safe-area-inset-bottom)); border-radius:30px 30px 0 0; background:#f2f5f3; box-shadow:0 -20px 50px -10px rgba(2,26,18,.35),inset 0 1px 0 rgba(255,255,255,.9); transform:translateY(105%); transition:transform .4s cubic-bezier(.2,.9,.2,1); }
  .payment-modal.is-open .payment-modal-card { transform:none; }
  .payment-modal-handle { width:40px; height:5px; margin:0 auto 16px; border-radius:99px; background:#c9d3ce; }
  .payment-modal-title { margin:0; color:#0a1f17; font-size:21px; font-weight:800; line-height:1.1; letter-spacing:-.03em; }
  .payment-modal-copy { margin:7px 0 14px; color:#4b5b54; font-size:13px; line-height:1.4; }
  .payment-options { display:grid; grid-template-columns:1fr; gap:8px; margin:14px 0 12px; }
  .payment-choice { display:flex; align-items:center; gap:11px; min-height:68px; padding:11px 13px 11px 12px; border:0; border-radius:18px; background:#fff; color:#0a1f17; text-align:left; box-shadow:inset 0 0 0 1px #e2e9e5,0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06); transition:transform .15s ease,box-shadow .2s ease,background .2s ease; }
  .payment-choice:hover { box-shadow:inset 0 0 0 1px #cbd8d1,0 6px 16px -6px rgba(6,40,28,.12); }
  .payment-choice:active { transform:scale(.985); }
  .payment-choice.is-selected { background:#f2fbf6; box-shadow:inset 0 0 0 2px #0e8a5a,0 4px 12px -6px rgba(14,138,90,.35); }
  .payment-choice-icon { display:grid; width:40px; height:40px; flex:0 0 40px; place-items:center; border-radius:13px; color:#0e8a5a; background:#e6f7ef; }
  .payment-choice-copy { display:block; flex:1; min-width:0; }
  .payment-choice-title { display:block; font-size:13px; font-weight:750; }
  .payment-choice-description { display:block; margin-top:4px; color:#718078; font-size:11px; font-weight:500; line-height:1.35; }
  .payment-choice-check { display:grid; width:20px; height:20px; flex:0 0 20px; place-items:center; border:1.5px solid #c9d3ce; border-radius:50%; color:transparent; }
  .payment-choice.is-selected .payment-choice-check { border-color:#0e8a5a; color:#fff; background:#0e8a5a; }
  .payment-order-summary { margin-top:12px; padding:4px 16px; border-radius:20px; background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.06),inset 0 0 0 1px rgba(10,31,23,.04); }
  .payment-order-row { display:flex; justify-content:space-between; gap:12px; padding:12px 0; color:#4b5b54; font-size:13px; }
  .payment-order-row + .payment-order-row { border-top:1px solid #eef2f0; }
  .payment-order-row strong { color:#0a1f17; font-weight:700; text-align:right; font-variant-numeric:tabular-nums; }
  .payment-method-note { margin:11px 2px 12px; color:#8a9892; font-size:11px; line-height:1.4; }
  .payment-modal-actions { position:sticky; bottom:-24px; display:grid; grid-template-columns:1fr 2fr; gap:10px; margin:0 -20px -24px; padding:12px 20px max(24px,env(safe-area-inset-bottom)); background:#f2f5f3; }
  .payment-modal-actions button { min-height:54px; border:0; border-radius:18px; background:#fff; color:#0a1f17; box-shadow:inset 0 0 0 1px #e2e9e5,0 1px 2px rgba(6,40,28,.06); font-size:14px; font-weight:650; }
  .payment-modal-actions .payment-modal-submit { color:#fff; background:linear-gradient(135deg,#1fb97a,#054a31); box-shadow:0 7px 18px rgba(7,90,59,.25),inset 0 1px 0 rgba(255,255,255,.2); }
  .payment-modal-actions .payment-modal-submit:disabled { color:#8fa39a; background:#dfe7e3; box-shadow:inset 0 0 0 1px rgba(10,31,23,.05); cursor:not-allowed; }
  .bank-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
  .bank-option { min-height:112px; border:0; border-radius:18px; background:#fff; box-shadow:inset 0 0 0 1px #e2e9e5,0 1px 2px rgba(6,40,28,.06); }
  .payment-modal-actions button:focus-visible,.payment-choice:focus-visible,.bank-option:focus-visible { outline:3px solid rgba(20,168,109,.38); outline-offset:2px; }
  @media (min-width:600px) {
    .payment-modal { align-items:center; padding:24px; }
    .payment-modal-card { max-height:min(88vh,820px); border-radius:30px; }
    .payment-modal-actions { bottom:-24px; }
  }
  @media (max-width:380px) { .bp-content { padding-right:15px; padding-left:15px; } .bp-topbar { padding-right:14px; padding-left:14px; } .bp-cta-wrap { margin-right:-15px; margin-left:-15px; padding-right:15px; padding-left:15px; } .bp-amount { font-size:24px; } }
</style>

@php
  $initialCurrency = old('currency') === 'PHP' ? 'PHP' : 'USD';
  $initialAmount = old('amount', $package['price']);
  $midpointAmount = round(($package['min_amount'] + $package['max_amount']) / 2, 2);
@endphp

<main class="purchase-page">
  <header class="bp-topbar">
    <a class="bp-back" href="{{ route('invest') }}" aria-label="Back to investment packages">&lsaquo;</a>
    <div class="bp-brand"><img src="{{ asset('logo.png') }}" alt="">LuLu Philippines</div>
    <span class="bp-top-tag">Bond purchase</span>
  </header>

  <div class="bp-content">
    <div class="bp-eyebrow">Investment package</div>
    <h1 class="bp-title">Purchase {{ $package['name'] }} Bond</h1>

    <article class="bp-card" aria-label="{{ $package['name'] }} package: {{ number_format($package['daily_interest_rate'], 2) }} percent daily for {{ $package['duration_days'] }} days">
      <div class="bp-card-top">
        <div>
          <div class="bp-card-name">{{ $package['name'] }}</div>
          <div class="bp-card-caption">LuLu investment bond</div>
        </div>
        <span class="bp-card-chip" aria-hidden="true"></span>
      </div>
      <div class="bp-card-stats">
        <div>
          <div class="bp-stat-label">Daily interest</div>
          <div class="bp-stat-value">{{ number_format($package['daily_interest_rate'], 2) }}<small>% daily</small></div>
        </div>
        <div>
          <div class="bp-stat-label">Duration</div>
          <div class="bp-stat-value">{{ $package['duration_days'] }}<small>days</small></div>
        </div>
      </div>
    </article>

    <p class="bp-lede">Choose an amount from <strong>${{ number_format($package['min_amount'], 2) }}</strong> to <strong>${{ number_format($package['max_amount'], 2) }}</strong>. The estimates below use this package's <strong>{{ number_format($package['daily_interest_rate'], 2) }}% daily rate</strong> and {{ $package['duration_days'] }}-day duration.</p>

    <form method="post" action="{{ route('investments.store') }}" id="purchaseForm">
      @csrf
      <input type="hidden" name="package" value="{{ $packageKey }}">
      <input type="hidden" name="currency" id="purchaseCurrency" value="{{ $initialCurrency }}">
      <input type="hidden" name="payment_method" id="purchasePaymentMethod" value="">
      @if ($errors->any())
        <div class="form-error" role="alert">{{ $errors->first() }}</div>
      @endif

      <section class="bp-panel" aria-label="Investment amount">
        <div class="bp-panel-head">
          <label class="bp-label" id="amountLabel" for="purchaseAmount">Amount in {{ $initialCurrency }}</label>
          <div class="bp-currency" role="group" aria-label="Display currency">
            <button type="button" data-currency="USD" aria-pressed="{{ $initialCurrency === 'USD' ? 'true' : 'false' }}">USD</button>
            <button type="button" data-currency="PHP" aria-pressed="{{ $initialCurrency === 'PHP' ? 'true' : 'false' }}">PHP</button>
          </div>
        </div>
        <label class="bp-amount-field" for="purchaseAmount">
          <span class="bp-symbol" id="purchaseCurrencySymbol">$</span>
          <input class="bp-amount" id="purchaseAmount" type="text" name="amount" inputmode="decimal" autocomplete="off" value="{{ $initialAmount }}" aria-labelledby="amountLabel" aria-describedby="purchaseLimits purchaseAmountError" required>
        </label>
        <div class="bp-limits" id="purchaseLimits">
          <span>Min <strong id="purchaseMin">${{ number_format($package['min_amount'], 2) }}</strong></span>
          <span>Max <strong id="purchaseMax">${{ number_format($package['max_amount'], 2) }}</strong></span>
        </div>
        <p class="bp-error" id="purchaseAmountError" role="alert"></p>
        <div class="bp-chips" role="group" aria-label="Suggested investment amounts">
          <button class="bp-chip" type="button" data-amount="{{ $package['min_amount'] }}">Minimum</button>
          <button class="bp-chip" type="button" data-amount="{{ $midpointAmount }}">Midpoint</button>
          <button class="bp-chip" type="button" data-amount="{{ $package['max_amount'] }}">Maximum</button>
        </div>
        <input class="bp-range" id="purchaseRange" type="range" min="{{ $package['min_amount'] }}" max="{{ $package['max_amount'] }}" step="0.01" value="{{ $package['price'] }}" aria-label="Adjust investment amount in US dollars">
        <div class="bp-range-scale"><span>${{ number_format($package['min_amount'], 2) }}</span><span>${{ number_format($package['max_amount'], 2) }}</span></div>
        <div class="bp-fx" id="purchaseFx" hidden>
          <span aria-hidden="true">i</span>
          <span>Converted using the live rate of <strong>US$1 = ₱{{ number_format($phpRate, 4) }}</strong> (updated {{ $phpRateUpdatedAt }}). The selected currency and amount will be validated at checkout.</span>
        </div>
      </section>

      <div class="bp-section-heading"><h2>Estimated projection</h2><span>Simple interest estimate</span></div>
      <div class="bp-projection" aria-live="polite">
        <div class="bp-tile">
          <div class="bp-tile-label">Daily interest</div>
          <div class="bp-tile-value" id="purchaseDaily">$0.00</div>
          <div class="bp-tile-sub">At the package daily rate</div>
        </div>
        <div class="bp-tile">
          <div class="bp-tile-label">Estimated interest</div>
          <div class="bp-tile-value" id="purchaseInterest">$0.00</div>
          <div class="bp-tile-sub">Over the full package duration</div>
        </div>
        <div class="bp-tile bp-tile-dark">
          <div class="bp-tile-label">Estimated at maturity</div>
          <div class="bp-tile-value" id="purchaseTotal">$0.00</div>
          <div class="bp-tile-sub">Principal plus simple interest</div>
        </div>
        <div class="bp-tile bp-tile-wide">
          <div>
            <div class="bp-tile-label">Estimated maturity date</div>
            <div class="bp-tile-value" id="purchaseMaturityDate">—</div>
          </div>
          <div>
            <div class="bp-tile-label">Duration</div>
            <div class="bp-tile-value">{{ $package['duration_days'] }} days</div>
          </div>
        </div>
      </div>
      <p class="bp-formula" id="purchaseNote"></p>

      <section class="bp-disclosure" aria-label="Risks and disclosures">
        <h3>Important information</h3>
        <p>Returns shown are estimates based on the selected amount, package daily rate, and duration. The maturity date is estimated from today; actual dates and investment terms are governed by the signed agreement.</p>
        <a class="bp-doc-link" href="{{ route('invest.agreement.sample.download') }}">Read the bond agreement</a>
      </section>

      @if ($requiresAgreement)
        <div class="agreement-box">
          <h3>Sign your bond purchase agreement</h3>
          <p>This purchase will be recorded using the <a class="agreement-link" href="{{ route('invest.agreement.sample.download') }}">uploaded agreement</a>. Confirm that the name below is yours, review the populated agreement, then draw your signature in the box.</p>
          <p><strong>Name on contract:</strong> {{ auth()->user()->name ?: auth()->user()->email }}</p>
          <input type="hidden" name="agreement_signature_name" value="{{ auth()->user()->name ?: auth()->user()->email }}">
          <canvas class="signature-pad" id="purchaseSignaturePad" width="900" height="300" aria-label="Draw your signature"></canvas>
          <input type="hidden" name="agreement_signature_data" id="purchaseSignatureData">
          <div class="signature-actions"><span>Use your finger, mouse, or stylus.</span><button class="signature-clear" type="button" id="clearPurchaseSignature">Clear signature</button></div>
          <label class="agreement-check"><input type="checkbox" name="agreement_accepted" value="1" {{ old('agreement_accepted') ? 'checked' : '' }} required> <span>I have read and understood the agreement and voluntarily accept its terms.</span></label>
          <a class="agreement-preview-link" id="agreementPreviewLink" href="{{ route('invest.agreement.preview', ['package' => $packageKey, 'amount' => $initialAmount, 'currency' => $initialCurrency]) }}" target="_blank" rel="noopener">Review your populated agreement</a>
        </div>
      @endif

      <div class="bp-cta-wrap">
        <p class="bp-cta-hint" id="purchaseHint">Review the estimate and continue to choose a payment method.</p>
        <button class="purchase-submit" type="button" id="showPayment" disabled>
          Continue to payment
          <span aria-hidden="true">&rarr;</span>
        </button>
      </div>
    </form>
  </div>
</main>

<div class="bond-review" id="bondReview" aria-hidden="true">
  <section class="bond-review-sheet" role="dialog" aria-modal="true" aria-labelledby="bondReviewTitle">
    <div class="bond-review-handle" aria-hidden="true"></div>
    <div class="bond-review-heading">
      <h2 id="bondReviewTitle" tabindex="-1">Review your bond</h2>
      <button class="bond-review-close" type="button" id="bondReviewClose" aria-label="Close review">&times;</button>
    </div>
    <div class="bond-review-hero">
      <div class="bond-review-hero-label">Estimated value at maturity</div>
      <div class="bond-review-hero-value" id="reviewMaturityValue">$0.00</div>
      <div class="bond-review-hero-sub">Principal + estimated interest</div>
    </div>
    <div class="bond-review-rows">
      <div class="bond-review-row"><span>Amount</span><strong id="reviewAmount">$0.00</strong></div>
      <div class="bond-review-row"><span>Rate</span><strong>{{ number_format($package['daily_interest_rate'], 2) }}% daily</strong></div>
      <div class="bond-review-row"><span>Term</span><strong>{{ $package['duration_days'] }} days</strong></div>
      <div class="bond-review-row"><span>Interest payout</span><strong>Daily accrual</strong></div>
      <div class="bond-review-row"><span>Estimated interest</span><strong id="reviewInterest">$0.00</strong></div>
      <div class="bond-review-row"><span>Estimated maturity date</span><strong id="reviewMaturityDate">—</strong></div>
    </div>
    <p class="bond-review-note">Estimates use simple interest before taxes and fees. Actual terms and maturity date are governed by your signed agreement.</p>
    <div class="bond-review-actions">
      <button class="bond-review-edit" type="button" id="bondReviewEdit">Edit</button>
      <button class="bond-review-confirm" type="button" id="bondReviewConfirm">Confirm purchase</button>
    </div>
  </section>
</div>

<div class="payment-modal" id="paymentModal" aria-hidden="true">
  <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle">
    <div class="payment-modal-handle" aria-hidden="true"></div>
    <h2 class="payment-modal-title" id="paymentModalTitle" tabindex="-1">Choose payment method</h2>
    <p class="payment-modal-copy">Choose how you want to pay for this {{ strtolower($package['name']) }} bond.</p>
    <div class="payment-options" role="group" aria-label="Payment methods">
      <button class="payment-choice" type="button" data-payment="account_balance" aria-pressed="false">
        <span class="payment-choice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V5.5A1.5 1.5 0 0 0 17.5 4h-12A2.5 2.5 0 0 0 3 6.5v11A2.5 2.5 0 0 0 5.5 20h13a1.5 1.5 0 0 0 1.5-1.5V15"/><path d="M3 6.5A2.5 2.5 0 0 0 5.5 9h13A1.5 1.5 0 0 1 20 10.5V15h-4a2 2 0 0 1 0-4h4"/></svg></span>
        <span class="payment-choice-copy"><span class="payment-choice-title">Account balance</span><span class="payment-choice-description">Use your available LuLu account funds</span></span>
        <span class="payment-choice-check" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>
      </button>
      <button class="payment-choice" type="button" data-payment="bank_transfer" aria-pressed="false">
        <span class="payment-choice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10h18M5 10v8M9.5 10v8M14.5 10v8M19 10v8M3 21h18M12 3l9 5H3l9-5Z"/></svg></span>
        <span class="payment-choice-copy"><span class="payment-choice-title">Bank transfer</span><span class="payment-choice-description">Choose Landbank, BPI, BDO, or UnionBank</span></span>
        <span class="payment-choice-check" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>
      </button>
      <button class="payment-choice" type="button" data-payment="e_wallet" aria-pressed="false">
        <span class="payment-choice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2.5" width="12" height="19" rx="3"/><path d="M10.5 18.5h3M9.5 9.5h5"/></svg></span>
        <span class="payment-choice-copy"><span class="payment-choice-title">E-wallet</span><span class="payment-choice-description">Choose GCash, Maya, GrabPay, or ShopeePay</span></span>
        <span class="payment-choice-check" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>
      </button>
      <button class="payment-choice" type="button" data-payment="crypto" aria-pressed="false">
        <span class="payment-choice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 8h4a2 2 0 0 1 0 4H9m0 0h5a2 2 0 0 1 0 4H9m1-10v12m4-12v1m0 10v1"/></svg></span>
        <span class="payment-choice-copy"><span class="payment-choice-title">Crypto</span><span class="payment-choice-description">Continue to review and sign your agreement</span></span>
        <span class="payment-choice-check" aria-hidden="true"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg></span>
      </button>
    </div>
    <div class="payment-order-summary" aria-live="polite">
      <div class="payment-order-row"><span>Investment amount</span><strong id="paymentModalAmount">$0.00</strong></div>
      <div class="payment-order-row"><span>Payment provider fees</span><strong>Shown by provider, if applicable</strong></div>
    </div>
    <p class="payment-method-note">No additional payment fees are calculated on this screen.</p>
    <div class="payment-modal-actions">
      <button type="button" id="paymentModalCancel">Back</button>
      <button class="payment-modal-submit" type="button" id="submitPurchase" disabled>Continue</button>
    </div>
  </div>
</div>

<div class="payment-modal" id="walletModal" aria-hidden="true">
  <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="walletModalTitle">
    <div class="payment-modal-handle" aria-hidden="true"></div>
    <h2 class="payment-modal-title" id="walletModalTitle">Choose your e-wallet</h2>
    <p class="payment-modal-copy">Select the wallet you will use for your payment.</p>
    <div class="bank-list">
      <button class="bank-option" type="button" data-wallet-name="GCash"><strong class="bank-text-logo wallet-text-logo-gcash">GCash</strong><span>GCash</span></button>
      <button class="bank-option" type="button" data-wallet-name="Maya"><strong class="bank-text-logo wallet-text-logo-maya">Maya</strong><span>Maya</span></button>
      <button class="bank-option" type="button" data-wallet-name="GrabPay"><strong class="bank-text-logo wallet-text-logo-grabpay">GrabPay</strong><span>GrabPay</span></button>
      <button class="bank-option" type="button" data-wallet-name="ShopeePay"><strong class="bank-text-logo wallet-text-logo-shopeepay">ShopeePay</strong><span>ShopeePay</span></button>
    </div>
    <div class="payment-modal-actions">
      <button type="button" id="walletModalBack">Back</button>
    </div>
  </div>
</div>

<div class="payment-modal" id="bankModal" aria-hidden="true">
  <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="bankModalTitle">
    <div class="payment-modal-handle" aria-hidden="true"></div>
    <h2 class="payment-modal-title" id="bankModalTitle">Choose your bank</h2>
    <p class="payment-modal-copy">Select the bank you will use for your transfer.</p>
    <div class="bank-list">
      <button class="bank-option" type="button" aria-label="Landbank" data-bank-name="Landbank" data-bank-qr="{{ asset('LandbankQR.png') }}">
        <img src="{{ asset('Landbank.svg') }}" alt="Landbank logo">
      </button>
      <button class="bank-option" type="button" aria-label="BPI" data-bank-name="BPI" data-bank-qr="{{ asset('BPIQR.png') }}">
        <img src="{{ asset('Bpi.svg') }}" alt="BPI logo">
      </button>
      <button class="bank-option" type="button" aria-label="BDO" data-bank-name="BDO" data-bank-qr="{{ asset('BPIQR.png') }}">
        <img src="{{ asset('BDO.svg') }}" alt="BDO logo">
      </button>
      <button class="bank-option" type="button" aria-label="UnionBank" data-bank-name="UnionBank" data-bank-qr="{{ asset('BPIQR.png') }}">
        <img src="{{ asset('UnionBank.svg') }}" alt="UnionBank logo">
      </button>
    </div>
    <div class="payment-modal-actions">
      <button type="button" id="bankModalBack">Back</button>
    </div>
  </div>
</div>

<div class="payment-modal" id="qrModal" aria-hidden="true">
  <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="qrModalTitle">
    <div class="payment-modal-handle" aria-hidden="true"></div>
    <h2 class="payment-modal-title" id="qrModalTitle">Bank QR code</h2>
    <p class="payment-modal-copy">Scan this QR code to complete your bank transfer.</p>
    <img class="qr-image" id="qrImage" src="" alt="Bank transfer QR code">
    <div class="payment-modal-actions">
      <button type="button" id="qrModalBack">Back</button>
      <button class="payment-modal-submit" type="button" id="qrModalConfirm">Continue</button>
    </div>
  </div>
</div>

<script>
  (function () {
    var rate = {{ (float) $package['daily_interest_rate'] }};
    var days = {{ (int) $package['duration_days'] }};
    var phpRate = {{ (float) $phpRate }};
    var minUsd = {{ (float) $package['min_amount'] }};
    var maxUsd = {{ (float) $package['max_amount'] }};
    var currency = document.getElementById('purchaseCurrency').value;
    var amount = document.getElementById('purchaseAmount');
    var currencyInput = document.getElementById('purchaseCurrency');
    var paymentInput = document.getElementById('purchasePaymentMethod');
    var daily = document.getElementById('purchaseDaily');
    var total = document.getElementById('purchaseTotal');
    var note = document.getElementById('purchaseNote');
    var range = document.getElementById('purchaseRange');
    var amountError = document.getElementById('purchaseAmountError');
    var minLabel = document.getElementById('purchaseMin');
    var maxLabel = document.getElementById('purchaseMax');
    var currencySymbol = document.getElementById('purchaseCurrencySymbol');
    var currencyLabel = document.getElementById('amountLabel');
    var fxNote = document.getElementById('purchaseFx');
    var maturityDate = document.getElementById('purchaseMaturityDate');
    var confirmButton = document.getElementById('showPayment');
    var purchaseHint = document.getElementById('purchaseHint');
    var bondReview = document.getElementById('bondReview');
    var reviewAmount = document.getElementById('reviewAmount');
    var reviewInterest = document.getElementById('reviewInterest');
    var reviewMaturityValue = document.getElementById('reviewMaturityValue');
    var reviewMaturityDate = document.getElementById('reviewMaturityDate');
    var paymentModal = document.getElementById('paymentModal');
    var paymentModalCancel = document.getElementById('paymentModalCancel');
    var submitPurchase = document.getElementById('submitPurchase');
    var bankModal = document.getElementById('bankModal');
    var walletModal = document.getElementById('walletModal');
    var qrModal = document.getElementById('qrModal');
    var selectedBank = null;
    var agreementPreviewLink = document.getElementById('agreementPreviewLink');
    var signaturePad = document.getElementById('purchaseSignaturePad');
    var signatureData = document.getElementById('purchaseSignatureData');
    var agreementCheckbox = document.querySelector('[name="agreement_accepted"]');
    var signatureContext = signaturePad ? signaturePad.getContext('2d') : null;
    var drawing = false;
    var signatureHasInk = false;
    var presets = document.querySelectorAll('[data-amount]');

    function currentAmount() {
      var value = Number(String(amount.value).replace(/,/g, '').trim());
      return Number.isFinite(value) ? value : NaN;
    }
    function asUsd(displayAmount) {
      return currency === 'PHP' ? displayAmount / phpRate : displayAmount;
    }
    function fromUsd(usdAmount) {
      return currency === 'PHP' ? usdAmount * phpRate : usdAmount;
    }
    function roundUpCent(value) { return Math.ceil(value * 100 - 1e-8) / 100; }
    function roundDownCent(value) { return Math.floor(value * 100 + 1e-8) / 100; }
    function limits() {
      return currency === 'PHP'
        ? { min: roundUpCent(minUsd * phpRate), max: roundDownCent(maxUsd * phpRate) }
        : { min: minUsd, max: maxUsd };
    }
    function inputForUsd(usdAmount) {
      var displayAmount = fromUsd(usdAmount);
      if (currency === 'PHP') {
        var currentLimits = limits();
        displayAmount = usdAmount <= minUsd + 1e-7 ? currentLimits.min :
          usdAmount >= maxUsd - 1e-7 ? currentLimits.max :
            Math.min(currentLimits.max, Math.max(currentLimits.min, Math.round(displayAmount * 100) / 100));
      }
      return displayAmount.toFixed(2);
    }
    function formatMoney(value) {
      return (currency === 'PHP' ? '₱' : '$') + Number(value).toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2});
    }
    function hasValidSignature() {
      return !signaturePad || (signatureHasInk && Boolean(signatureData.value));
    }
    function amountIsValid() {
      var entered = currentAmount();
      var usd = asUsd(entered);
      return Number.isFinite(usd) && usd >= minUsd - 1e-7 && usd <= maxUsd + 1e-7;
    }
    function updateConfirmation() {
      var valid = amountIsValid();
      var signed = hasValidSignature();
      var accepted = !agreementCheckbox || agreementCheckbox.checked;
      confirmButton.disabled = !(valid && signed && accepted);
      amountError.classList.toggle('is-visible', !valid && amount.value.trim() !== '');
      amountError.textContent = valid ? '' : 'Enter an amount between ' + formatMoney(limits().min) + ' and ' + formatMoney(limits().max) + '.';
      if (!valid) {
        purchaseHint.textContent = 'Choose an amount within the package limits.';
      } else if (!signed) {
        purchaseHint.textContent = 'Draw your signature and accept the agreement to continue.';
      } else if (!accepted) {
        purchaseHint.textContent = 'Accept the agreement to continue.';
      } else {
        purchaseHint.textContent = 'Review the estimate and continue to choose a payment method.';
      }
    }
    function continueToAgreement() {
      var agreementUrl = new URL('{{ route('invest.agreement.sign') }}', window.location.origin);
      agreementUrl.searchParams.set('package', '{{ $packageKey }}');
      agreementUrl.searchParams.set('amount', Number.isFinite(currentAmount()) ? currentAmount().toFixed(2) : '');
      agreementUrl.searchParams.set('currency', currency);
      agreementUrl.searchParams.set('payment_method', paymentInput.value);
      window.location.href = agreementUrl.toString();
    }
    function openBondReview() {
      if (!amountIsValid() || !hasValidSignature() || (agreementCheckbox && !agreementCheckbox.checked)) {
        updateConfirmation();
        return;
      }
      bondReview.classList.add('is-open');
      bondReview.setAttribute('aria-hidden', 'false');
      document.documentElement.classList.add('scroll-locked');
      document.getElementById('bondReviewConfirm').focus();
    }
    function closeBondReview() {
      bondReview.classList.remove('is-open');
      bondReview.setAttribute('aria-hidden', 'true');
      document.documentElement.classList.remove('scroll-locked');
      confirmButton.focus();
    }
    function openPaymentMethods() {
      closeBondReview();
      document.documentElement.classList.add('scroll-locked');
      document.getElementById('paymentModalAmount').textContent = formatMoney(currentAmount());
      paymentModal.classList.add('is-open');
      paymentModal.setAttribute('aria-hidden', 'false');
      document.getElementById('paymentModalTitle').focus();
    }

    function signaturePoint(event) {
      var rect = signaturePad.getBoundingClientRect();
      return { x: (event.clientX - rect.left) * signaturePad.width / rect.width, y: (event.clientY - rect.top) * signaturePad.height / rect.height };
    }
    function saveSignature() {
      if (!signatureContext) return;
      signatureData.value = signaturePad.toDataURL('image/png');
      updateConfirmation();
    }
    if (signaturePad) {
      signatureContext.lineWidth = 4;
      signatureContext.lineCap = 'round';
      signatureContext.lineJoin = 'round';
      signatureContext.strokeStyle = '#17202a';
      signaturePad.addEventListener('pointerdown', function (event) { drawing = true; signaturePad.setPointerCapture(event.pointerId); var point = signaturePoint(event); signatureContext.beginPath(); signatureContext.moveTo(point.x, point.y); });
      signaturePad.addEventListener('pointermove', function (event) { if (!drawing) return; signatureHasInk = true; var point = signaturePoint(event); signatureContext.lineTo(point.x, point.y); signatureContext.stroke(); saveSignature(); });
      signaturePad.addEventListener('pointerup', function () { drawing = false; if (signatureHasInk) saveSignature(); else updateConfirmation(); });
      signaturePad.addEventListener('pointercancel', function () { drawing = false; });
      document.getElementById('clearPurchaseSignature').addEventListener('click', function () { signatureContext.clearRect(0, 0, signaturePad.width, signaturePad.height); signatureData.value = ''; signatureHasInk = false; updateConfirmation(); });
    }
    function update() {
      var entered = currentAmount();
      var base = Number.isFinite(entered) ? asUsd(entered) : 0;
      var dailyValue = Math.round(base * rate + Number.EPSILON) / 100;
      var interestValue = dailyValue * days;
      var maturityValue = base + interestValue;
      daily.textContent = formatMoney(fromUsd(dailyValue));
      document.getElementById('purchaseInterest').textContent = formatMoney(fromUsd(interestValue));
      total.textContent = formatMoney(fromUsd(maturityValue));
      reviewAmount.textContent = formatMoney(entered || 0);
      reviewInterest.textContent = formatMoney(fromUsd(interestValue));
      reviewMaturityValue.textContent = formatMoney(fromUsd(maturityValue));
      note.textContent = formatMoney(entered || 0) + ' x ' + rate.toFixed(2) + '% daily for ' + days + ' days. Estimated simple interest: ' + formatMoney(fromUsd(interestValue)) + '. Estimates are not guaranteed returns.';
      currencyInput.value = currency;
      currencySymbol.textContent = currency === 'PHP' ? '₱' : '$';
      currencyLabel.textContent = 'Amount in ' + currency;
      var currentLimits = limits();
      minLabel.textContent = formatMoney(currentLimits.min);
      maxLabel.textContent = formatMoney(currentLimits.max);
      fxNote.hidden = currency !== 'PHP';
      range.value = Math.min(maxUsd, Math.max(minUsd, Number.isFinite(base) ? base : minUsd));
      presets.forEach(function (preset) {
        var presetUsd = Number(preset.dataset.amount);
        var presetValue = currency === 'PHP' && presetUsd <= minUsd + 1e-7 ? currentLimits.min :
          currency === 'PHP' && presetUsd >= maxUsd - 1e-7 ? currentLimits.max : fromUsd(presetUsd);
        preset.textContent = preset.dataset.amount === String(minUsd) ? 'Minimum ' + formatMoney(presetValue) :
          preset.dataset.amount === String(maxUsd) ? 'Maximum ' + formatMoney(presetValue) : 'Midpoint ' + formatMoney(presetValue);
        preset.classList.toggle('is-active', Number.isFinite(base) && Math.abs(base - presetUsd) < 0.005);
      });
      if (maturityDate) {
        var maturity = new Date();
        maturity.setDate(maturity.getDate() + days);
        var formattedMaturityDate = maturity.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
        maturityDate.textContent = formattedMaturityDate;
        reviewMaturityDate.textContent = formattedMaturityDate;
      }
      if (agreementPreviewLink) {
        var previewUrl = new URL(agreementPreviewLink.href, window.location.origin);
        previewUrl.searchParams.set('amount', Number.isFinite(entered) ? entered.toFixed(2) : '0');
        previewUrl.searchParams.set('currency', currency);
        agreementPreviewLink.href = previewUrl.toString();
      }
      document.querySelectorAll('[data-currency]').forEach(function (button) {
        var active = button.dataset.currency === currency;
        button.classList.toggle('is-active', active);
        button.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
      updateConfirmation();
    }
    amount.addEventListener('input', update);
    range.addEventListener('input', function () {
      amount.value = inputForUsd(Number(range.value));
      update();
    });
    presets.forEach(function (preset) {
      preset.addEventListener('click', function () {
        amount.value = inputForUsd(Number(preset.dataset.amount));
        update();
      });
    });
    document.querySelectorAll('[data-currency]').forEach(function (button) {
      button.addEventListener('click', function () {
        var usdAmount = asUsd(currentAmount());
        currency = button.dataset.currency;
        amount.value = Number.isFinite(usdAmount) ? inputForUsd(usdAmount) : '';
        update();
      });
    });
    if (agreementCheckbox) agreementCheckbox.addEventListener('change', updateConfirmation);
    amount.addEventListener('blur', function () {
      if (Number.isFinite(currentAmount())) amount.value = currentAmount().toFixed(2);
      update();
    });
    document.getElementById('showPayment').addEventListener('click', function () {
      openBondReview();
    });
    document.getElementById('bondReviewClose').addEventListener('click', closeBondReview);
    document.getElementById('bondReviewEdit').addEventListener('click', closeBondReview);
    document.getElementById('bondReviewConfirm').addEventListener('click', openPaymentMethods);
    document.querySelectorAll('#paymentModal [data-payment]').forEach(function (button) {
      button.addEventListener('click', function () {
        paymentInput.value = button.dataset.payment;
        document.querySelectorAll('#paymentModal [data-payment]').forEach(function (item) {
          var selected = item === button;
          item.classList.toggle('is-selected', selected);
          item.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });
        submitPurchase.disabled = false;
      });
    });
    paymentModalCancel.addEventListener('click', function () {
      paymentModal.classList.remove('is-open');
      paymentModal.setAttribute('aria-hidden', 'true');
      document.documentElement.classList.remove('scroll-locked');
      confirmButton.focus();
    });
    document.getElementById('bankModalBack').addEventListener('click', function () {
      bankModal.classList.remove('is-open');
      bankModal.setAttribute('aria-hidden', 'true');
      paymentModal.classList.add('is-open');
      paymentModal.setAttribute('aria-hidden', 'false');
      document.getElementById('paymentModalTitle').focus();
    });
    document.getElementById('walletModalBack').addEventListener('click', function () {
      walletModal.classList.remove('is-open');
      walletModal.setAttribute('aria-hidden', 'true');
      paymentModal.classList.add('is-open');
      paymentModal.setAttribute('aria-hidden', 'false');
      document.getElementById('paymentModalTitle').focus();
    });
    document.querySelectorAll('[data-bank-name]').forEach(function (button) {
      button.addEventListener('click', function () {
        selectedBank = button.dataset.bankName;
        var providerKey = selectedBank.toLowerCase().replace(/\s+/g, '');
        var paymentUrl = new URL('{{ url('/invest/payment') }}/' + providerKey, window.location.origin);
        paymentUrl.searchParams.set('package', '{{ $packageKey }}');
        paymentUrl.searchParams.set('amount', Number.isFinite(currentAmount()) ? currentAmount().toFixed(2) : '');
        paymentUrl.searchParams.set('currency', currency);
        window.location.href = paymentUrl.toString();
      });
    });
    document.querySelectorAll('[data-wallet-name]').forEach(function (button) {
      button.addEventListener('click', function () {
        var providerKey = button.dataset.walletName.toLowerCase().replace(/\s+/g, '');
        var paymentUrl = new URL('{{ url('/invest/payment') }}/' + providerKey, window.location.origin);
        paymentUrl.searchParams.set('package', '{{ $packageKey }}');
        paymentUrl.searchParams.set('amount', Number.isFinite(currentAmount()) ? currentAmount().toFixed(2) : '');
        paymentUrl.searchParams.set('currency', currency);
        window.location.href = paymentUrl.toString();
      });
    });
    document.getElementById('qrModalBack').addEventListener('click', function () {
      qrModal.classList.remove('is-open');
      qrModal.setAttribute('aria-hidden', 'true');
      bankModal.classList.add('is-open');
      bankModal.setAttribute('aria-hidden', 'false');
    });
    document.getElementById('qrModalConfirm').addEventListener('click', function () {
      qrModal.classList.remove('is-open');
      qrModal.setAttribute('aria-hidden', 'true');
      continueToAgreement();
    });
    submitPurchase.addEventListener('click', function () {
      if (!paymentInput.value) return;
      if (paymentInput.value === 'bank_transfer') {
        paymentModal.classList.remove('is-open');
        paymentModal.setAttribute('aria-hidden', 'true');
        bankModal.classList.add('is-open');
        bankModal.setAttribute('aria-hidden', 'false');
        document.getElementById('bankModalTitle').focus();
        return;
      }
      if (paymentInput.value === 'e_wallet') {
        paymentModal.classList.remove('is-open');
        paymentModal.setAttribute('aria-hidden', 'true');
        walletModal.classList.add('is-open');
        walletModal.setAttribute('aria-hidden', 'false');
        document.getElementById('walletModalTitle').focus();
        return;
      }
      paymentModal.classList.remove('is-open');
      paymentModal.setAttribute('aria-hidden', 'true');
      document.documentElement.classList.remove('scroll-locked');
      continueToAgreement();
    });
    document.addEventListener('keydown', function (event) {
      if (event.key !== 'Escape') return;
      if (bankModal.classList.contains('is-open')) {
        document.getElementById('bankModalBack').click();
      } else if (walletModal.classList.contains('is-open')) {
        document.getElementById('walletModalBack').click();
      } else if (paymentModal.classList.contains('is-open')) {
        paymentModalCancel.click();
      }
    });
    update();
  })();
</script>
@endsection
