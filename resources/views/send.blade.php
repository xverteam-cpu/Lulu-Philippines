@extends('layouts.app')

@section('content')
<style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Inter, Arial, Helvetica, sans-serif;
  }

  body {
    background: #f3f5f8;
    color: #071a44;
  }

  .phone {
    max-width: 430px;
    min-height: 100vh;
    margin: 0 auto;
    background: #f3f5f8;
    padding: 24px 16px 110px;
    position: relative;
  }

  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
  }

  .brand { display:flex; align-items:center; gap:12px; }

  .logo {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #166534;
    color: #fff;
    font-weight: 900;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .brand h1 { font-size: 19px; font-weight: 900; line-height: 1; }
  .brand p { font-size: 12px; color: #8b96a8; font-weight: 700; margin-top:4px; }

  .icons { display:flex; align-items:center; gap:16px; font-size:18px; }

  .profile {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fff;
    display:flex;
    align-items:center;
    justify-content:center;
    color: #3f247a;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  }

  .back-row { display:flex; align-items:center; gap:12px; margin-bottom:18px; }

  .back-btn {
    width: 42px;
    height: 42px;
    border-radius: 15px;
    background: #fff;
    border: none;
    color: #166534;
    font-size: 24px;
    font-weight: 800;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
  }

  .page-title h2 { font-size:25px; font-weight:900; }
  .page-title p { color:#71809a; font-size:13px; font-weight:600; margin-top:4px; }

  .balance-card {
    background: #166534;
    border-radius: 22px;
    padding: 22px;
    color: #fff;
    margin-bottom: 16px;
    box-shadow: 0 18px 35px rgba(237,28,36,0.22);
  }

  .balance-card span { font-size:12px; font-weight:900; letter-spacing:.5px; opacity:.9; }
  .balance-card h3 { font-size:34px; margin-top:12px; font-weight:900; }

  .form-card { background:#fff; border-radius:22px; padding:18px; box-shadow:0 12px 32px rgba(0,0,0,0.06); }

  .label { display:block; font-size:13px; font-weight:900; margin-bottom:8px; color:#071a44; }

  .input-box {
    width:100%;
    border:1px solid #edf0f4;
    background:#f8fafc;
    border-radius:16px;
    padding:15px 16px;
    font-size:15px;
    font-weight:700;
    color:#071a44;
    margin-bottom:16px;
    outline:none;
  }

  .input-row { position:relative; }
  .currency { position:absolute; top:15px; left:16px; font-size:16px; font-weight:900; color:#166534; }
  .amount-input { padding-left:42px; font-size:22px; font-weight:900; }

  .quick-row { display:flex; gap:10px; margin-bottom:18px; }
  .quick-row button { flex:1; border:none; border-radius:14px; padding:12px 0; background:#f4fbf7; color:#166534; font-weight:900; font-size:13px; }

  .note { background:#f8fafc; border-radius:16px; padding:14px; font-size:12px; line-height:1.5; color:#6b7890; margin-bottom:18px; }

  .send-btn { width:100%; border:none; border-radius:18px; background:#166534; color:#fff; font-size:17px; font-weight:900; padding:17px; box-shadow:0 16px 28px rgba(237,28,36,0.28); }

  .recent { margin-top:20px; }
  .section-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
  .section-head h3 { font-size:16px; font-weight:900; }
  .section-head a { font-size:14px; color:#166534; text-decoration:none; font-weight:900; }

  .recipient-list { display:flex; gap:12px; overflow-x:auto; padding-bottom:4px; }
  .recipient { min-width:76px; background:#fff; border-radius:18px; padding:12px 8px; text-align:center; box-shadow:0 8px 22px rgba(0,0,0,0.05); }
  .avatar { width:42px; height:42px; border-radius:50%; background:#fff0f1; color:#166534; display:flex; align-items:center; justify-content:center; margin:0 auto 8px; font-weight:900; }
  .recipient p { font-size:12px; font-weight:800; }

  .bottom-nav {
    position: fixed;
    left: 12px;
    right: 12px;
    bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 18px;
    max-width: 640px;
    margin: 0 auto;
    padding: 0 22px;
    height: 86px;
    background: rgba(255,255,255,.95);
    border-radius: 30px;
    border: 1px solid rgba(239,239,247,.90);
    backdrop-filter: blur(18px);
    box-shadow: 0 8px 24px rgba(15,23,42,.06);
    z-index: 150;
  }

  .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    color: #64748b;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    transition: transform .2s ease, color .2s ease;
  }

  .nav-item:hover {
    transform: translateY(-2px);
    color: #071a44;
  }

  .nav-item img {
    width: 22px;
    height: 22px;
  }

  .bottom-nav a {
    text-decoration: none;
  }

  .nav-scan {
    position: relative;
    top: -24px;
    width: 74px;
    height: 74px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    box-shadow: none;
    transition: transform .18s ease;
  }

  .nav-scan:hover {
    transform: translateY(-6px);
  }

  .nav-scan img {
    width: 74px;
    height: 74px;
    object-fit: contain;
    display: block;
    border-radius: 0;
    background: transparent;
    padding: 0;
  }

  .fab-scrim {
    position: fixed;
    inset: 0;
    z-index: 120;
    background: rgba(0,0,0,.52);
    opacity: 0;
    visibility: hidden;
    transition: opacity .28s ease;
  }

  .fab-scrim.is-open {
    opacity: 1;
    visibility: visible;
  }

  .fab-panel {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 130;
    transform: translateY(110%);
    transition: transform .34s cubic-bezier(.22,1,.36,1);
  }

  .fab-panel.is-open {
    transform: translateY(0);
  }

  .fab-sheet {
    border-radius: 28px 28px 0 0;
    padding: 18px 18px 28px;
    background: #fff;
    box-shadow: 0 -18px 60px rgba(3,7,18,.14);
  }

  .fab-sheet-handle {
    width: 68px;
    height: 6px;
    margin: 0 auto 14px;
    border-radius: 999px;
    background: #e9e9e9;
  }

  .fab-sheet-title {
    font-size: 16px;
    font-weight: 900;
    color: #121212;
    text-align: center;
    margin-bottom: 18px;
  }

  .fab-actions {
    display: grid;
    gap: 12px;
  }

  .fab-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 6px;
    border-radius: 0;
    background: transparent;
    color: #121212;
    text-decoration: none;
    font-weight: 800;
    transition: transform .2s ease, background .2s ease;
    transform: translateY(24px);
    opacity: 0;
  }

  .fab-panel.is-open .fab-action {
    transform: translateY(0);
    opacity: 1;
  }

  .fab-action:hover {
    background: transparent;
    transform: translateY(-2px);
  }

  .fab-action-icon {
    width: 72px;
    height: 72px;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    box-shadow: none;
    overflow: hidden;
    flex-shrink: 0;
    padding: 0;
  }

  .fab-action-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    padding: 0;
    border-radius: 0;
  }

  .fab-action:nth-child(1) { transition-delay:.05s; }
  .fab-action:nth-child(2) { transition-delay:.10s; }
  .fab-action:nth-child(3) { transition-delay:.15s; }
  .fab-action:nth-child(4) { transition-delay:.20s; }
  .fab-action:nth-child(5) { transition-delay:.25s; }
  .fab-action:nth-child(6) { transition-delay:.30s; }
  .fab-action:nth-child(7) { transition-delay:.35s; }

  @media (max-width: 380px) {
    .brand h1 { font-size:17px; }
    .balance-card h3 { font-size:30px; }
    .quick-row { flex-wrap:wrap; }
    .quick-row button { min-width: calc(50% - 5px); }
  }
</style>

<main class="phone">

  <div class="back-row">
    <a href="{{ route('dashboard') }}" class="back-btn">‹</a>
    <div class="page-title">
      <h2>Send Money</h2>
      <p>Transfer funds securely to another wallet</p>
    </div>
  </div>

  <section class="balance-card">
    <span>AVAILABLE BALANCE</span>
    <h3>$0.00</h3>
  </section>

  <section class="form-card">
    <label class="label">Recipient Wallet ID</label>
    <input class="input-box" type="text" placeholder="Enter wallet ID or mobile number" />

    <label class="label">Amount to Send</label>
    <div class="input-row">
      <span class="currency">$</span>
      <input class="input-box amount-input" type="number" placeholder="0.00" />
    </div>

    <div class="quick-row">
      <button>$10</button>
      <button>$25</button>
      <button>$50</button>
      <button>$100</button>
    </div>

    <label class="label">Message</label>
    <input class="input-box" type="text" placeholder="Add a short note optional" />

    <div class="note">
      Transfers are processed through the Lulu Wallet system. Please review recipient details before confirming your transaction.
    </div>

    <button class="send-btn">Continue to Send</button>
  </section>
</main>

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

<nav class="bottom-nav" aria-hidden="false">
  <a class="nav-item" href="{{ route('dashboard') }}">
    <img src="{{ asset('home.png') }}" alt="Home" loading="eager" decoding="async">
    <div>Home</div>
  </a>
  <a class="nav-item" href="{{ route('history') }}">
    <img src="{{ asset('history.png') }}" alt="History" loading="eager" decoding="async">
    <div>History</div>
  </a>
  <a class="nav-item" href="#" id="fabToggle">
    <div class="nav-scan">
      <img src="{{ asset('menu.png') }}" alt="Menu" loading="eager" decoding="async">
    </div>
  </a>
  <a class="nav-item" href="{{ route('rewards') }}">
    <img src="{{ asset('reward.png') }}" alt="Rewards" loading="eager" decoding="async">
    <div>Rewards</div>
  </a>
  <a class="nav-item" href="{{ route('profile') }}">
    <img src="{{ asset('profile.png') }}" alt="Profile" loading="eager" decoding="async">
    <div>Profile</div>
  </a>
</nav>

<script>
  (function () {
    var fabToggle = document.getElementById('fabToggle');
    var fabScrim = document.getElementById('fabScrim');
    var fabPanel = document.getElementById('fabPanel');
    var fabClose = document.getElementById('fabClose');

    function openFabMenu(event) {
      if (event) event.preventDefault();
      if (!fabScrim || !fabPanel) return;
      fabScrim.classList.add('is-open');
      fabPanel.classList.add('is-open');
      fabScrim.setAttribute('aria-hidden', 'false');
      fabPanel.setAttribute('aria-hidden', 'false');
    }

    function closeFabMenu() {
      if (!fabScrim || !fabPanel) return;
      fabScrim.classList.remove('is-open');
      fabPanel.classList.remove('is-open');
      fabScrim.setAttribute('aria-hidden', 'true');
      fabPanel.setAttribute('aria-hidden', 'true');
    }

    if (fabToggle) {
      fabToggle.addEventListener('click', openFabMenu);
    }

    if (fabScrim) {
      fabScrim.addEventListener('click', closeFabMenu);
    }

    if (fabClose) {
      fabClose.addEventListener('click', closeFabMenu);
    }
  })();
</script>

@endsection
