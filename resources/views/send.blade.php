@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500..800&display=swap" rel="stylesheet">
<style>
  :root {
    --send-bg: #f2f5f3;
    --send-surface: #fff;
    --send-ink: #0a1f17;
    --send-muted: #4b5b54;
    --send-subtle: #8a9892;
    --send-line: #e2e9e5;
    --send-green: #0e8a5a;
    --send-green-dark: #075a3b;
    --send-mint: #3ee0a1;
    --send-display: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    --send-ui: Inter, system-ui, -apple-system, sans-serif;
  }

  html.send-page,
  html.send-page body {
    min-height: 100%;
    background: #e7ece9;
  }

  html.send-page body {
    display: flex;
    align-items: center;
    justify-content: center;
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9;
    color: var(--send-ink);
    font-family: var(--send-ui);
  }

  html.send-page body .container.send-page-container {
    display: flex;
    justify-content: center;
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
  }

  .send-shell,
  .send-shell * {
    box-sizing: border-box;
  }

  .send-shell {
    position: relative;
    width: 390px;
    height: 844px;
    margin: 40px 0;
    overflow-x: clip;
    overflow-y: auto;
    border-radius: 42px;
    background: var(--send-bg);
    box-shadow: 0 0 0 9px #0d1411, 0 0 0 10px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4);
    scrollbar-width: none;
  }

  .send-shell::-webkit-scrollbar {
    display: none;
  }

  .send-chrome {
    position: sticky;
    top: 0;
    z-index: 30;
    background: rgba(242, 245, 243, .88);
    -webkit-backdrop-filter: saturate(180%) blur(20px);
    backdrop-filter: saturate(180%) blur(20px);
  }

  .send-statusbar {
    height: calc(38px + env(safe-area-inset-top));
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: calc(4px + env(safe-area-inset-top)) 25px 0 28px;
  }

  .send-statusbar time {
    font-size: 14px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
  }

  .send-status-icons {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .send-nav {
    height: 62px;
    display: grid;
    grid-template-columns: 40px 1fr 40px;
    align-items: center;
    padding: 0 16px 6px;
  }

  .send-back {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: var(--send-surface);
    box-shadow: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06);
    color: var(--send-ink);
    text-decoration: none;
    transition: transform .2s ease;
  }

  .send-back:active {
    transform: scale(.92);
  }

  .send-title {
    text-align: center;
  }

  .send-title h1 {
    font: 750 18px/1.05 var(--send-display);
    letter-spacing: -.02em;
  }

  .send-title p {
    margin-top: 5px;
    color: var(--send-subtle);
    font-size: 11px;
    font-weight: 500;
  }

  .send-main {
    padding: 10px 20px 138px;
  }

  .send-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: 22px;
    border-radius: 28px;
    background:
      radial-gradient(120% 90% at 105% -10%, rgba(62, 224, 161, .55), transparent 55%),
      radial-gradient(70% 70% at -10% 110%, rgba(198, 243, 107, .28), transparent 60%),
      linear-gradient(160deg, #0b6a47 0%, #054a31 42%, #022418 100%);
    box-shadow: 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(2, 26, 18, .38), inset 0 1px 0 rgba(255, 255, 255, .22);
    color: #fff;
  }

  .send-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .send-eyebrow {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(232, 255, 244, .78);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
  }

  .send-eyebrow::before {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--send-mint);
    box-shadow: 0 0 0 3px rgba(62, 224, 161, .22);
    content: "";
  }

  .send-hero-icon {
    width: 36px;
    height: 36px;
    display: grid;
    place-items: center;
    border-radius: 12px;
    background: rgba(255, 255, 255, .12);
    color: #e8fff4;
  }

  .send-balance {
    display: flex;
    align-items: flex-start;
    margin-top: 16px;
    font-size: 42px;
    font-weight: 750;
    line-height: 1;
    letter-spacing: -.045em;
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
  }

  .send-balance-currency {
    margin: 5px 4px 0 0;
    color: rgba(255, 255, 255, .8);
    font-size: 24px;
    font-weight: 600;
    letter-spacing: 0;
  }

  .send-balance-decimal {
    color: rgba(255, 255, 255, .62);
  }

  .send-hero-sub {
    margin-top: 8px;
    color: rgba(232, 255, 244, .68);
    font-size: 12px;
    line-height: 1.4;
  }

  .send-panel {
    margin-top: 18px;
    padding: 20px;
    border-radius: 24px;
    background: var(--send-surface);
    box-shadow: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), inset 0 0 0 1px rgba(10, 31, 23, .04);
  }

  .send-panel h2 {
    font: 750 19px/1.15 var(--send-display);
    letter-spacing: -.025em;
  }

  .send-panel-description {
    margin-top: 5px;
    color: var(--send-muted);
    font-size: 13px;
    line-height: 1.45;
  }

  .send-field {
    margin-top: 18px;
  }

  .send-field label {
    display: block;
    margin: 0 2px 9px;
    font-size: 12px;
    font-weight: 700;
  }

  .send-control {
    min-height: 56px;
    display: flex;
    align-items: center;
    border-radius: 16px;
    background: #f8faf9;
    box-shadow: inset 0 0 0 1px var(--send-line);
    transition: box-shadow .2s ease, background .2s ease;
  }

  .send-control:focus-within {
    background: #fff;
    box-shadow: inset 0 0 0 1.5px #14a86d, 0 0 0 4px rgba(20, 168, 109, .1);
  }

  .send-control input {
    width: 100%;
    height: 56px;
    padding: 0 16px;
    border: 0;
    outline: 0;
    background: transparent;
    color: var(--send-ink);
    font: 600 15px/1 var(--send-ui);
  }

  .send-control input::placeholder {
    color: var(--send-subtle);
    font-weight: 500;
  }

  .send-amount-control {
    gap: 10px;
    padding: 0 16px;
  }

  .send-amount-control .send-currency {
    color: var(--send-green);
    font: 800 18px/1 var(--send-display);
  }

  .send-amount-control input {
    padding: 0;
    font-size: 24px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
  }

  .send-quick-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 9px;
    margin-top: 12px;
  }

  .send-quick {
    height: 40px;
    border-radius: 13px;
    background: linear-gradient(180deg, #edfaf3, #e4f5ec);
    color: var(--send-green-dark);
    font-size: 12px;
    font-weight: 700;
    transition: transform .2s ease, background .2s ease;
  }

  .send-quick:hover {
    transform: translateY(-1px);
  }

  .send-quick.is-active {
    background: linear-gradient(180deg, #d7f6e7, #c7efd9);
    box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .2);
  }

  .send-info {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 18px;
    padding: 14px;
    border-radius: 16px;
    background: linear-gradient(180deg, #f7faf8, #f2f7f4);
    color: var(--send-muted);
    font-size: 12px;
    line-height: 1.5;
  }

  .send-info-icon {
    width: 30px;
    height: 30px;
    display: grid;
    flex: 0 0 auto;
    place-items: center;
    border-radius: 10px;
    background: #e6f7ef;
    color: var(--send-green);
  }

  .send-cta {
    width: 100%;
    min-height: 58px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 18px;
    border-radius: 18px;
    background: linear-gradient(180deg, #11905e 0%, #0a6a45 100%);
    box-shadow: 0 10px 24px -8px rgba(10, 106, 69, .45), inset 0 1px 0 rgba(255, 255, 255, .25);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    transition: transform .2s ease, filter .2s ease;
  }

  .send-cta:hover {
    filter: brightness(1.05);
  }

  .send-cta:active {
    transform: scale(.98);
  }

  .send-section-head {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin: 28px 2px 12px;
  }

  .send-section-head h2 {
    font: 750 18px/1.1 var(--send-display);
    letter-spacing: -.025em;
  }

  .send-recipient-empty {
    padding: 17px 16px;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 1px 2px rgba(6, 40, 28, .06), inset 0 0 0 1px var(--send-line);
    color: var(--send-muted);
    font-size: 12px;
    line-height: 1.45;
  }

  .send-toast {
    position: fixed;
    z-index: 200;
    right: 20px;
    bottom: 112px;
    left: 20px;
    max-width: 350px;
    margin: auto;
    padding: 12px 16px;
    transform: translateY(12px);
    border-radius: 16px;
    background: #021a12;
    box-shadow: 0 12px 24px rgba(2, 26, 18, .2);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    opacity: 0;
    pointer-events: none;
    text-align: center;
    transition: opacity .2s ease, transform .2s ease;
  }

  .send-toast.is-visible {
    transform: translateY(0);
    opacity: 1;
  }

  .send-page .bottom-nav {
    position: fixed;
    right: 12px;
    bottom: 12px;
    z-index: 150;
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 12px;
    max-width: 640px;
    margin: 0 auto;
    border: 1px solid rgba(239, 239, 247, .9);
    border-radius: 30px;
    -webkit-backdrop-filter: blur(18px);
    backdrop-filter: blur(18px);
  }

  .send-page .bottom-nav a {
    text-decoration: none;
  }

  .send-page .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    font-weight: 600;
    text-decoration: none;
    transition: transform .2s ease, color .2s ease;
  }

  .send-page .nav-item:hover {
    transform: translateY(-2px);
  }

  .send-page .nav-item img {
    width: 22px;
    height: 22px;
    object-fit: contain;
  }

  .send-page .nav-scan {
    position: relative;
    top: -21px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    transition: transform .18s ease;
  }

  .send-page .nav-scan:hover {
    transform: translateY(-5px);
  }

  .send-page .nav-scan img {
    display: block;
    object-fit: contain;
  }

  .send-page .fab-scrim {
    position: fixed;
    z-index: 120;
    inset: 0;
    background: rgba(0, 0, 0, .52);
    opacity: 0;
    visibility: hidden;
    transition: opacity .28s ease;
  }

  .send-page .fab-scrim.is-open {
    opacity: 1;
    visibility: visible;
  }

  .send-page .fab-panel {
    position: fixed;
    z-index: 130;
    right: 0;
    bottom: 0;
    left: 0;
    transform: translateY(110%);
    transition: transform .34s cubic-bezier(.22, 1, .36, 1);
  }

  .send-page .fab-panel.is-open {
    transform: translateY(0);
  }

  .send-page .fab-sheet {
    padding: 18px 18px 28px;
    border-radius: 28px 28px 0 0;
    background: #fff;
    box-shadow: 0 -18px 60px rgba(3, 7, 18, .14);
  }

  .send-page .fab-sheet-handle {
    width: 68px;
    height: 6px;
    margin: 0 auto 14px;
    border-radius: 999px;
    background: #e9e9e9;
  }

  .send-page .fab-sheet-title {
    margin-bottom: 18px;
    color: #121212;
    font-size: 16px;
    font-weight: 900;
    text-align: center;
  }

  .send-page .fab-actions {
    display: grid;
    gap: 12px;
  }

  .send-page .fab-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 6px;
    border-radius: 0;
    background: transparent;
    font-weight: 800;
    text-decoration: none;
  }

  .send-page .fab-action-icon {
    width: 56px;
    height: 56px;
    display: flex;
    flex: 0 0 auto;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .send-page .fab-action-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .send-page .bottom-nav {
    left: 50%;
    width: min(calc(100% - 24px), 390px);
    height: 76px;
    padding: 0 18px;
    transform: translateX(-50%);
    border-color: rgba(226, 233, 229, .9);
    background: rgba(255, 255, 255, .96);
    box-shadow: 0 8px 24px rgba(15, 23, 42, .08);
  }

  .send-page .nav-item {
    color: #64746c;
    font-size: 11px;
  }

  .send-page .nav-item:hover {
    color: var(--send-green-dark);
  }

  .send-page .nav-scan {
    width: 66px;
    height: 66px;
  }

  .send-page .nav-scan img {
    width: 66px;
    height: 66px;
  }

  .send-page .fab-scrim {
    background: rgba(2, 26, 18, .45);
  }

  .send-page .fab-sheet {
    padding-bottom: max(24px, env(safe-area-inset-bottom));
  }

  .send-page .fab-action {
    color: var(--send-ink);
  }

  .send-page .fab-close {
    width: 100%;
    margin-top: 14px;
    padding: 12px;
    border-radius: 12px;
    background: #f2f5f3;
    color: var(--send-green-dark);
    font-weight: 700;
  }

  @media (max-width: 520px) {
    html.send-page,
    html.send-page body {
      background: var(--send-bg);
    }

    html.send-page body {
      display: block;
    }

    html.send-page body .container.send-page-container {
      display: block;
    }

    .send-shell {
      width: 100%;
      height: auto;
      min-height: 100dvh;
      margin: 0;
      overflow: visible;
      border-radius: 0;
      box-shadow: none;
    }

    .send-main {
      padding-bottom: calc(126px + env(safe-area-inset-bottom));
    }

    .send-page .bottom-nav {
      bottom: max(10px, env(safe-area-inset-bottom));
    }
  }

  @media (max-width: 360px) {
    .send-main {
      padding-right: 14px;
      padding-left: 14px;
    }

    .send-panel {
      padding: 17px;
    }

    .send-balance {
      font-size: 36px;
    }

    .send-quick-row {
      gap: 6px;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .send-shell *,
    .send-page .bottom-nav * {
      transition: none !important;
    }
  }
</style>

<div class="send-shell">
  <header class="send-chrome">
    <div class="send-statusbar" aria-hidden="true">
      <time id="sendLocalTime"></time>
      <div class="send-status-icons">
        <svg width="18" height="12" viewBox="0 0 18 12" fill="#0A1F17" aria-hidden="true">
          <rect x="0" y="8" width="3" height="4" rx="1"/><rect x="5" y="5.5" width="3" height="6.5" rx="1"/><rect x="10" y="3" width="3" height="9" rx="1"/><rect x="15" y="0" width="3" height="12" rx="1"/>
        </svg>
        <svg width="16" height="12" viewBox="0 0 16 12" fill="#0A1F17" aria-hidden="true">
          <path d="M8 2.3c2.3 0 4.4.9 6 2.4l1.2-1.2A10.1 10.1 0 0 0 8 .6C5.2.6 2.7 1.7.8 3.5L2 4.7a8.5 8.5 0 0 1 6-2.4Z"/><path d="M8 5.6c1.4 0 2.6.5 3.6 1.4l1.2-1.2A6.8 6.8 0 0 0 8 3.9 6.8 6.8 0 0 0 3.2 5.8L4.4 7c1-.9 2.2-1.4 3.6-1.4Z"/><path d="M8 8.9c.5 0 1 .2 1.3.5L8 11.4 6.7 9.4c.3-.3.8-.5 1.3-.5Z"/>
        </svg>
        <svg width="27" height="13" viewBox="0 0 27 13" fill="none" aria-hidden="true">
          <rect x=".5" y=".5" width="23" height="12" rx="3.8" stroke="#0A1F17" opacity=".4"/><rect x="2" y="2" width="17" height="9" rx="2.4" fill="#0A1F17"/><path d="M25 4.5v4c.8-.3 1.5-1.1 1.5-2s-.7-1.7-1.5-2Z" fill="#0A1F17" opacity=".45"/>
        </svg>
      </div>
    </div>
    <nav class="send-nav" aria-label="Send money navigation">
      <a href="{{ route('dashboard') }}" class="send-back" aria-label="Back to dashboard">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
      </a>
      <div class="send-title">
        <h1>Send Money</h1>
        <p>Transfer funds securely</p>
      </div>
      <span aria-hidden="true"></span>
    </nav>
  </header>

  <main class="send-main">
    <section class="send-hero" aria-label="Available balance">
      <div class="send-hero-top">
        <span class="send-eyebrow">Available balance</span>
        <span class="send-hero-icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3 9.5 14.5"/><path d="M21 3 14 21l-4.5-6.5L3 10z"/></svg>
        </span>
      </div>
      <div class="send-balance">
        <span class="send-balance-currency">$</span>{{ number_format((float) $availableBalance, 2, '.', '') }}
      </div>
      <p class="send-hero-sub">Your available Lulu balance.</p>
    </section>

    <section class="send-panel" aria-labelledby="sendTransferTitle">
      <h2 id="sendTransferTitle">Transfer details</h2>
      <p class="send-panel-description">Enter a recipient and amount to prepare a transfer.</p>

      <form id="sendMoneyForm">
        <div class="send-field">
          <label for="sendRecipient">Recipient Wallet ID</label>
          <div class="send-control">
            <input id="sendRecipient" type="text" autocomplete="off" placeholder="Enter wallet ID or mobile number">
          </div>
        </div>

        <div class="send-field">
          <label for="sendAmount">Amount to Send</label>
          <div class="send-control send-amount-control">
            <span class="send-currency" aria-hidden="true">$</span>
            <input id="sendAmount" class="send-number" type="number" inputmode="decimal" min="0.01" step="0.01" placeholder="0.00">
          </div>
          <div class="send-quick-row" aria-label="Quick amount">
            <button type="button" class="send-quick" data-amount="10">$10</button>
            <button type="button" class="send-quick" data-amount="25">$25</button>
            <button type="button" class="send-quick" data-amount="50">$50</button>
            <button type="button" class="send-quick" data-amount="100">$100</button>
          </div>
        </div>

        <div class="send-field">
          <label for="sendMessage">Message <span style="color:var(--send-subtle);font-weight:500">(optional)</span></label>
          <div class="send-control">
            <input id="sendMessage" type="text" maxlength="120" placeholder="Add a short note">
          </div>
        </div>

        <div class="send-info" role="note">
          <span class="send-info-icon" aria-hidden="true">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
          </span>
          <p>Wallet transfers are not available yet. This form is a preview and will not move funds.</p>
        </div>

        <button class="send-cta" type="submit">
          Continue to Send
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
      </form>
    </section>

    <section aria-labelledby="recentRecipientsTitle">
      <div class="send-section-head">
        <h2 id="recentRecipientsTitle">Recent recipients</h2>
      </div>
      <p class="send-recipient-empty">No recent recipients yet.</p>
    </section>
  </main>
</div>

<div class="send-toast" id="sendPreviewNotice" role="status" aria-live="polite">Wallet transfers are not available yet.</div>

<div class="fab-scrim" id="fabScrim" aria-hidden="true"></div>
<div class="fab-panel" id="fabPanel" aria-hidden="true">
  <div class="fab-sheet" role="dialog" aria-modal="true" aria-label="Quick actions menu">
    <div class="fab-sheet-handle"></div>
    <div class="fab-sheet-title">Quick actions</div>
    <div class="fab-actions">
      <a class="fab-action" href="{{ route('invest') }}">
        <span class="fab-action-icon">💰</span>
        <span>Buy shares</span>
      </a>
      <a class="fab-action" href="{{ route('send') }}">
        <span class="fab-action-icon"><img src="{{ asset('Send%20(1).png') }}" alt="Send"></span>
        <span>Send</span>
      </a>
      <a class="fab-action" href="{{ route('withdraw') }}">
        <span class="fab-action-icon"><img src="{{ asset('Withdraw.png') }}" alt="Withdraw"></span>
        <span>Withdraw</span>
      </a>
      <a class="fab-action" href="{{ route('referrals') }}">
        <span class="fab-action-icon"><img src="{{ asset('referrals.png') }}" alt="Referrals"></span>
        <span>Referrals</span>
      </a>
      <a class="fab-action" href="{{ route('franchising') }}">
        <span class="fab-action-icon"><img src="{{ asset('Franchise.png') }}" alt="Franchise"></span>
        <span>Franchise</span>
      </a>
      <a class="fab-action" href="{{ route('unavailable') }}">
        <span class="fab-action-icon"><img src="{{ asset('cards.png') }}" alt="Cards"></span>
        <span>Cards</span>
      </a>
      <a class="fab-action" href="{{ route('unavailable') }}">
        <span class="fab-action-icon"><img src="{{ asset('loan.png') }}" alt="Loans"></span>
        <span>Loans</span>
      </a>
    </div>
    <button class="fab-close" type="button" id="fabClose">Close menu</button>
  </div>
</div>

<nav class="bottom-nav" aria-label="Main navigation">
  <a class="nav-item" href="{{ route('dashboard') }}">
    <img src="{{ asset('home.png') }}" alt="" loading="eager" decoding="async">
    <div>Home</div>
  </a>
  <a class="nav-item" href="{{ route('history') }}">
    <img src="{{ asset('history.png') }}" alt="" loading="eager" decoding="async">
    <div>History</div>
  </a>
  <a class="nav-item" href="#" id="fabToggle" aria-label="Open quick actions">
    <div class="nav-scan"><img src="{{ asset('menu.png') }}" alt="" loading="eager" decoding="async"></div>
  </a>
  <a class="nav-item" href="{{ route('rewards') }}">
    <img src="{{ asset('reward.png') }}" alt="" loading="eager" decoding="async">
    <div>Rewards</div>
  </a>
  <a class="nav-item" href="{{ route('profile') }}">
    <img src="{{ asset('profile.png') }}" alt="" loading="eager" decoding="async">
    <div>Profile</div>
  </a>
</nav>

<script>
  (function () {
    var time = document.getElementById('sendLocalTime');
    if (time) {
      time.textContent = new Intl.DateTimeFormat(undefined, {
        hour: 'numeric',
        minute: '2-digit'
      }).format(new Date());
    }

    var form = document.getElementById('sendMoneyForm');
    var amount = document.getElementById('sendAmount');
    var notice = document.getElementById('sendPreviewNotice');
    var quickButtons = document.querySelectorAll('.send-quick');
    var noticeTimer;

    quickButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        amount.value = Number(button.dataset.amount).toFixed(2);
        quickButtons.forEach(function (quickButton) {
          quickButton.classList.toggle('is-active', quickButton === button);
        });
      });
    });

    if (amount) {
      amount.addEventListener('input', function () {
        quickButtons.forEach(function (button) {
          button.classList.toggle('is-active', button.dataset.amount === amount.value);
        });
      });
    }

    if (form && notice) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        notice.classList.add('is-visible');
        window.clearTimeout(noticeTimer);
        noticeTimer = window.setTimeout(function () {
          notice.classList.remove('is-visible');
        }, 2800);
      });
    }

    var fabToggle = document.getElementById('fabToggle');
    var fabScrim = document.getElementById('fabScrim');
    var fabPanel = document.getElementById('fabPanel');
    var fabClose = document.getElementById('fabClose');

    function closeFabMenu() {
      if (!fabScrim || !fabPanel) return;
      fabScrim.classList.remove('is-open');
      fabPanel.classList.remove('is-open');
      fabScrim.setAttribute('aria-hidden', 'true');
      fabPanel.setAttribute('aria-hidden', 'true');
    }

    if (fabToggle && fabScrim && fabPanel) {
      fabToggle.addEventListener('click', function (event) {
        event.preventDefault();
        fabScrim.classList.add('is-open');
        fabPanel.classList.add('is-open');
        fabScrim.setAttribute('aria-hidden', 'false');
        fabPanel.setAttribute('aria-hidden', 'false');
      });
    }

    if (fabScrim) fabScrim.addEventListener('click', closeFabMenu);
    if (fabClose) fabClose.addEventListener('click', closeFabMenu);
  })();
</script>
@endsection
