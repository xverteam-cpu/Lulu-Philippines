@extends('layouts.app')

@section('content')
<style>
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Inter, Arial, Helvetica, sans-serif;
  }

  body { background: #f3f5f8; color: #071a44; overflow-x:hidden; }
  .container { max-width:none; padding:0; }

  .phone { max-width: 430px; min-height: 100vh; margin: 0 auto; background: #f3f5f8; padding: 24px 16px 110px; position: relative; }

  .topbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; }
  .brand { display:flex; align-items:center; gap:12px; }
  .logo { width:38px; height:38px; border-radius:12px; background:#166534; color:#fff; font-weight:900; display:flex; align-items:center; justify-content:center; font-size:20px; }
  .brand h1 { font-size:19px; font-weight:900; line-height:1; }
  .brand p { font-size:12px; color:#8b96a8; font-weight:700; margin-top:4px; }

  .icons { display:flex; align-items:center; gap:16px; font-size:18px; }
  .profile { width:36px; height:36px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; color:#3f247a; box-shadow:0 10px 25px rgba(0,0,0,0.08); }

  .withdraw-header { display:flex; align-items:center; gap:12px; width:100vw; min-height:80px; margin:-24px 0 22px calc(50% - 50vw); padding:14px 16px; box-sizing:border-box; color:#fff; background:#098a58; }
  .back-btn { width:42px; height:42px; border-radius:15px; background:transparent; border:none; color:#fff; font-size:32px; font-weight:800; line-height:1; display:inline-flex; align-items:center; justify-content:center; text-decoration:none; }

  .page-title h2 { font-size:25px; font-weight:900; color:#fff; }
  .page-title p { color:rgba(255,255,255,.82); font-size:13px; font-weight:600; margin-top:4px; }

  .balance-card { position:relative; aspect-ratio:4 / 1; padding:0; margin-bottom:16px; color:#fff; overflow:hidden; }
  .balance-card-art { position:absolute; inset:0; width:100%; height:100%; display:block; pointer-events:none; }
  .balance-card > * { position:relative; z-index:1; }
  .balance-label { position:absolute; top:28%; left:5%; right:5%; font-size:12px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; text-align:center; color:#fff; }
  .balance-amount { position:absolute; top:48%; left:5%; right:5%; font-size:30px; font-weight:700; line-height:1.05; text-align:center; }

  .form-card { background:transparent; border-radius:0; padding:0; box-shadow:none; }
  .label { display:block; font-size:13px; font-weight:900; margin-bottom:8px; color:#071a44; }
  .input-box { width:100%; border:1px solid #edf0f4; background:#f8fafc; border-radius:16px; padding:15px 16px; font-size:15px; font-weight:700; color:#071a44; margin-bottom:16px; outline:none; }
  .input-row { position:relative; }
  .currency { position:absolute; top:15px; left:16px; font-size:16px; font-weight:900; color:#166534; }
  .amount-input { padding-left:42px; font-size:22px; font-weight:900; }

  .quick-row { display:flex; gap:10px; margin-bottom:18px; padding-bottom:18px; border-bottom:1px solid #dfe5eb; }
  .quick-row button { flex:1; border:none; border-radius:14px; padding:12px 0; background:#f4fbf7; color:#166534; font-weight:900; font-size:13px; }

  .note { background:#f8fafc; border-radius:16px; padding:14px; font-size:12px; line-height:1.5; color:#6b7890; margin-bottom:18px; }
  .account-options { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px; }
  .account-option { border:1px solid #dfe5eb; border-radius:14px; background:#fff; color:#071a44; padding:13px; font-weight:900; cursor:pointer; }
  .account-option.is-selected { border-color:#166534; background:#e8f8ee; color:#166534; }
  .add-account-button { width:100%; border:1px solid #166534; border-radius:14px; background:#e8f8ee; color:#166534; padding:14px; font-size:14px; font-weight:900; cursor:pointer; margin-bottom:16px; }
  .saved-account-button { width:100%; border:1px solid #166534; border-radius:14px; background:#166534; color:#fff; padding:14px; font-size:14px; font-weight:900; cursor:pointer; margin-bottom:16px; text-align:left; }
  .save-account-button { width:100%; border:none; border-radius:14px; background:#166534; color:#fff; padding:14px; font-size:14px; font-weight:900; cursor:pointer; margin-bottom:16px; }
  .account-setup[hidden] { display:none; }
  .saved-account { background:#fff; border:1px solid #dfe5eb; border-radius:16px; padding:14px; margin-bottom:16px; color:#071a44; }

  .send-btn { width:100%; border:none; border-radius:18px; background:#166534; color:#fff; font-size:17px; font-weight:900; padding:17px; box-shadow:0 16px 28px rgba(237,28,36,0.28); }

  .recent { margin-top:36px; padding-top:36px; border-top:1px solid #dfe5eb; }
  .section-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
  .section-head h3 { font-size:16px; font-weight:900; }
  .section-head a { font-size:14px; color:#166534; text-decoration:none; font-weight:900; }

  .history-list { display:flex; flex-direction:column; gap:10px; }
  .history-item { background:#fff; border-radius:14px; padding:12px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 8px 22px rgba(0,0,0,0.04); }
  .history-left { font-weight:800; }
  .history-right { color:#6b7890; font-weight:900; }

  @media (max-width:380px) { .brand h1 { font-size:17px; } .balance-amount { font-size:24px; } .quick-row button { min-width: calc(50% - 5px); } }

  /* Modal styles for receipt */
  .withdrawal-modal { position:fixed; inset:0; z-index:50; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(10,10,10,.62); }
  .withdrawal-modal.is-open { display:flex; }
  .withdrawal-modal-card { width:min(100%, 480px); max-height:92vh; overflow:auto; border-radius:44px; background:#fff; padding:32px 28px; box-shadow:0 32px 80px rgba(0,0,0,.15); }
  @media (max-width:480px) {
    .withdrawal-modal-card { width:min(100%, 92vw); border-radius:18px; padding:16px; }
  }

  :root {
    --withdraw-bg: #f2f5f3;
    --withdraw-ink: #0a1f17;
    --withdraw-muted: #4b5b54;
    --withdraw-soft-muted: #8a9892;
    --withdraw-line: #e2e9e5;
    --withdraw-green: #0e8a5a;
    --withdraw-green-dark: #075a3b;
    --withdraw-mint: #e6f7ef;
  }

  html { background: #e7ece9; }
  body {
    display: flex;
    min-height: 100vh;
    align-items: flex-start;
    justify-content: center;
    overflow-x: hidden;
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20,168,109,.18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198,243,107,.14), transparent 60%),
      #e7ece9;
    color: var(--withdraw-ink);
    font-family: Inter, "Plus Jakarta Sans", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
  }

  .phone {
    width: 390px;
    max-width: 100%;
    min-height: 844px;
    margin: 40px auto;
    padding: 0 20px 28px;
    overflow: clip;
    border-radius: 54px;
    background: var(--withdraw-bg);
    box-shadow: 0 0 0 10px #0d1411, 0 0 0 11px #2a332f, 0 30px 60px -20px rgba(2,26,18,.4), 0 60px 100px -50px rgba(2,26,18,.3);
  }

  .withdraw-header {
    position: sticky;
    top: 0;
    z-index: 30;
    display: grid;
    grid-template-columns: 40px 1fr 40px;
    align-items: center;
    width: auto;
    min-height: 102px;
    margin: 0 -20px 10px;
    padding: 0 16px 4px;
    background: rgba(242,245,243,.88);
    color: var(--withdraw-ink);
    border-bottom: 1px solid rgba(10,31,23,.06);
    backdrop-filter: saturate(180%) blur(18px);
    -webkit-backdrop-filter: saturate(180%) blur(18px);
  }

  .back-btn {
    display: grid;
    width: 40px;
    height: 40px;
    place-items: center;
    border-radius: 50%;
    background: #fff;
    color: var(--withdraw-ink);
    box-shadow: 0 1px 2px rgba(6,40,28,.06), inset 0 0 0 1px rgba(10,31,23,.05);
    font-size: 0;
  }

  .back-btn::before {
    content: "";
    width: 8px;
    height: 8px;
    border-bottom: 2px solid currentColor;
    border-left: 2px solid currentColor;
    transform: rotate(45deg);
  }

  .page-title { min-width: 0; text-align: center; }
  .page-title h2 {
    color: var(--withdraw-ink);
    font: 700 17px/1.2 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    letter-spacing: -.015em;
  }
  .page-title p { display: none; }

  .balance-card {
    display: flex;
    min-height: 118px;
    aspect-ratio: auto;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 7px;
    isolation: isolate;
    margin: 10px 0 18px;
    padding: 20px 22px;
    border-radius: 24px;
    background:
      radial-gradient(120% 90% at 105% -10%, rgba(62,224,161,.55), transparent 55%),
      radial-gradient(70% 70% at -10% 110%, rgba(198,243,107,.28), transparent 60%),
      linear-gradient(160deg, #0b6a47 0%, #075c3d 50%, #0e8a5a 100%);
    box-shadow: 0 2px 4px rgba(4,30,20,.08), 0 12px 24px -8px rgba(4,30,20,.22), 0 32px 56px -24px rgba(4,30,20,.38), inset 0 1px 0 rgba(255,255,255,.22);
    text-align: center;
  }

  .balance-card::after {
    position: absolute;
    z-index: -1;
    right: -20%;
    bottom: -58px;
    left: -20%;
    height: 115px;
    background: linear-gradient(16deg, transparent 22%, rgba(198,243,107,.95) 23% 25%, rgba(62,224,161,.38) 26% 48%, transparent 49%);
    content: "";
    transform: rotate(-3deg);
  }

  .balance-card-art { display: none; }
  .balance-label,
  .balance-amount {
    position: relative;
    inset: auto;
    z-index: 1;
    color: #fff;
  }
  .balance-label {
    font-size: 11px;
    letter-spacing: .14em;
    opacity: .88;
  }
  .balance-amount {
    font: 800 34px/1 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    letter-spacing: -.04em;
    text-shadow: 0 2px 18px rgba(0,0,0,.14);
  }

  .form-card { padding: 0; }
  .form-card form > div[style*="margin-bottom"] {
    border-radius: 14px !important;
    font-size: 12.5px;
    line-height: 1.45;
  }
  .label {
    margin: 0 1px 9px;
    color: var(--withdraw-ink);
    font: 750 14px/1.2 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    letter-spacing: -.015em;
  }
  .input-row { min-width: 0; }
  .input-box {
    width: 100%;
    min-height: 54px;
    margin-bottom: 0;
    padding: 0 16px;
    border: 0;
    border-radius: 16px;
    outline: 0;
    background: rgba(255,255,255,.76);
    color: var(--withdraw-ink);
    box-shadow: inset 0 0 0 1px rgba(10,31,23,.05), 0 1px 2px rgba(6,40,28,.06);
    font: 650 15px/1.3 Inter, system-ui, sans-serif;
  }
  .input-box:focus { box-shadow: inset 0 0 0 1.5px var(--withdraw-green), 0 0 0 4px rgba(20,168,109,.12); }
  .currency { top: 17px; color: #08734b; }
  .amount-input {
    height: 60px;
    padding-left: 46px;
    font: 750 25px/1 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    letter-spacing: -.03em;
  }
  .quick-row {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
    margin: 12px 0 0;
    padding: 0 0 18px;
    border-bottom: 1px solid var(--withdraw-line);
  }
  .quick-row button {
    min-width: 0;
    min-height: 40px;
    padding: 0 4px;
    border-radius: 14px;
    background: #f7fffa;
    color: #075a3b;
    box-shadow: inset 0 0 0 1px rgba(14,138,90,.04), 0 1px 2px rgba(6,40,28,.06);
    font: 750 13px/1 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
  }
  .quick-row button:active { background: var(--withdraw-mint); }
  .error { min-height: 16px; margin-top: 7px; color: #d94b4b; }

  .form-card form > .label,
  .form-card form > .saved-account-button,
  .form-card form > .add-account-button,
  .form-card form > .account-setup,
  .form-card form > .note { margin-top: 18px; }

  .saved-account-button {
    width: 100%;
    margin-bottom: 0;
    padding: 16px;
    border: 0;
    border-radius: 18px;
    background: linear-gradient(180deg, #19723b, #0e6a35);
    color: #fff;
    box-shadow: 0 1px 2px rgba(6,40,28,.05), 0 6px 16px -6px rgba(6,40,28,.12), inset 0 1px 0 rgba(255,255,255,.13);
    text-align: left;
    font: 750 14px/1.3 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
  }
  .add-account-button {
    min-height: 48px;
    margin-bottom: 0;
    border: 1.5px solid var(--withdraw-green);
    border-radius: 16px;
    background: #effaf4;
    color: #075a3b;
    font: 750 13.5px/1 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
  }
  .account-options { gap: 10px; }
  .account-option {
    border-color: var(--withdraw-line);
    border-radius: 14px;
    color: var(--withdraw-ink);
    font-size: 13px;
  }
  .account-option.is-selected {
    border-color: var(--withdraw-green);
    background: var(--withdraw-mint);
    color: var(--withdraw-green-dark);
  }
  .account-setup .input-box { margin-bottom: 14px; }
  .save-account-button {
    min-height: 48px;
    margin-bottom: 0;
    border-radius: 16px;
    background: linear-gradient(180deg, #168449, #0e6d3a);
    font-size: 14px;
  }
  .note {
    margin-bottom: 0;
    padding: 14px;
    border-radius: 16px;
    background: rgba(255,255,255,.72);
    color: #697973;
    box-shadow: inset 0 0 0 1px rgba(10,31,23,.035);
    font-size: 12.5px;
  }
  .send-btn {
    min-height: 56px;
    padding: 0 16px;
    border-radius: 18px;
    background: linear-gradient(180deg, #168449, #0e6d3a);
    box-shadow: 0 15px 28px -12px rgba(14,109,58,.52), 0 2px 4px rgba(10,106,69,.16), inset 0 1px 0 rgba(255,255,255,.22);
    font: 750 16px/1 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
  }
  .send-btn:active { transform: scale(.985); }

  .recent {
    margin-top: 26px;
    padding-top: 20px;
    border-top: 1px solid var(--withdraw-line);
  }
  .section-head { margin-bottom: 10px; }
  .section-head h3 {
    color: var(--withdraw-ink);
    font: 750 18px/1.1 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    letter-spacing: -.025em;
  }
  .section-head a { color: #075a3b; font-size: 12.5px; }
  .history-list { gap: 9px; }
  .history-item {
    min-width: 0;
    gap: 12px;
    padding: 13px 14px;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 1px 2px rgba(6,40,28,.06), inset 0 0 0 1px rgba(10,31,23,.04);
  }
  .history-left {
    min-width: 0;
    color: var(--withdraw-ink);
    font: 750 14px/1.25 "Plus Jakarta Sans", Inter, system-ui, sans-serif;
  }
  .history-right {
    flex: 0 0 auto;
    padding: 7px 9px;
    border-radius: 999px;
    background: var(--withdraw-mint);
    color: #0c6d45;
    font: 700 11px/1 Inter, system-ui, sans-serif;
  }
  .history-right.pending { background: #fff6d8; color: #8a6412; }
  .history-right.rejected { background: #fdecec; color: #a83a3a; }
  .home {
    width: 134px;
    height: 5px;
    margin: 22px auto 0;
    border-radius: 99px;
    background: #0a1f17;
    opacity: .9;
  }
  .withdrawal-modal-card {
    max-height: min(92vh, 760px);
    border: 1px solid var(--withdraw-line);
    border-radius: 24px;
    box-shadow: 0 18px 44px -24px rgba(2,26,18,.28), 0 2px 8px rgba(6,40,28,.06);
  }
  #withdrawalReceiptReference { color: var(--withdraw-ink) !important; }
  #withdrawalReceiptBadge { background: var(--withdraw-green) !important; }
  #withdrawalReceiptAmount { color: var(--withdraw-green-dark) !important; }
  #withdrawalReceiptBank,
  #withdrawalReceiptAccount,
  #withdrawalReceiptHolder,
  #withdrawalReceiptSubmitted { color: var(--withdraw-ink) !important; }
  .withdrawal-modal-card > div[style*="linear-gradient"] {
    border-color: var(--withdraw-line) !important;
    background: #f7faf8 !important;
    box-shadow: none !important;
  }
  .withdrawal-modal-card > div[style*="border-bottom"] {
    border-bottom-color: var(--withdraw-line) !important;
  }

  @media (min-width: 521px) {
    body { align-items: center; padding: 40px 0; }
    .phone { height: min(844px, calc(100dvh - 80px)); margin: 0 auto; overflow-y: auto; scrollbar-width: none; }
    .phone::-webkit-scrollbar { display: none; }
  }
  @media (max-width: 520px) {
    body { display: block; min-height: 100dvh; background: var(--withdraw-bg); }
    .phone { width: 100%; min-height: 100vh; min-height: 100dvh; margin: 0; border-radius: 0; box-shadow: none; }
    .withdraw-header { padding-top: max(8px, env(safe-area-inset-top)); }
    .withdrawal-modal { padding: 12px; }
    .withdrawal-modal-card { width: 100%; max-height: calc(100dvh - 24px); border-radius: 22px; padding: 18px; }
  }
  @media (max-width: 360px) {
    .phone { padding-right: 16px; padding-left: 16px; }
    .withdraw-header { margin-right: -16px; margin-left: -16px; padding-right: 12px; padding-left: 12px; }
    .quick-row { gap: 7px; }
    .quick-row button { font-size: 12px; }
  }

</style>

<main class="phone">

  <header class="withdraw-header">
    <a href="{{ route('dashboard') }}" class="back-btn" aria-label="Back to dashboard">‹</a>
    <div class="page-title">
      <h2>Withdraw Funds</h2>
      <p>Request funds to your bank or linked card</p>
    </div>
    <span aria-hidden="true"></span>
  </header>

  <section class="balance-card">
    <div class="balance-label">Available balance</div>
    <div class="balance-amount">${{ number_format($availableBalance ?? 0, 2) }}</div>
  </section>

  <section class="form-card">
    @if (session('status'))
      <div style="margin-bottom:12px; padding:12px; border-radius:12px; background:#e8f8ee; color:#137547; font-weight:700;">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div style="margin-bottom:12px; padding:12px; border-radius:12px; background:#fff1f2; color:#b42318; font-weight:700;">
        <ul style="margin:0; padding-left:18px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('withdrawals.store') }}">
      @csrf

      <label class="label" for="withdrawalAmount">Amount to Withdraw</label>
      <div class="input-row">
        <span class="currency">$</span>
        <input class="input-box amount-input" id="withdrawalAmount" type="number" name="amount" min="20" max="500" step="0.01" value="{{ old('amount') }}" inputmode="decimal" placeholder="0.00" aria-describedby="withdrawalAmountError" required />
      </div>

      <div class="quick-row">
        <button type="button" data-withdrawal-amount="20">$20</button>
        <button type="button" data-withdrawal-amount="50">$50</button>
        <button type="button" data-withdrawal-amount="100">$100</button>
        <button type="button" data-withdrawal-amount="200">$200</button>
      </div>
      <div class="error" id="withdrawalAmountError" aria-live="polite">
        @error('amount'){{ $message }}@enderror
      </div>

      @php
        $savedAccount = auth()->user()->bank_name && auth()->user()->bank_account_number && auth()->user()->bank_account_holder;
        $selectedType = old('account_type', auth()->user()->withdrawal_account_type ?: 'bank');
        $accountSetupOpen = old('account_type') || $errors->has('bank_name') || $errors->has('account_number') || $errors->has('account_holder');
      @endphp
      <label class="label">Withdrawal Account</label>
      @if ($savedAccount)
        <button type="button" class="saved-account-button" aria-label="Use saved withdrawal account">
          <span style="display:block; font-size:12px; opacity:.78;">Saved {{ $selectedType === 'e_wallet' ? 'e-wallet' : 'bank account' }}</span>
          <span style="display:block; margin-top:4px;">{{ auth()->user()->bank_name }} · ••••{{ substr(auth()->user()->bank_account_number, -4) }}</span>
        </button>
        <button type="button" class="add-account-button" id="addAccountButton">+ Add another account</button>
        <input type="hidden" name="account_type" id="savedAccountType" value="{{ $selectedType }}" @if ($accountSetupOpen) disabled @endif>
        <input type="hidden" name="bank_name" id="savedProvider" value="{{ auth()->user()->bank_name }}" @if ($accountSetupOpen) disabled @endif>
        <input type="hidden" name="account_number" id="savedAccountNumber" value="{{ auth()->user()->bank_account_number }}" @if ($accountSetupOpen) disabled @endif>
        <input type="hidden" name="account_holder" id="savedAccountHolder" value="{{ auth()->user()->bank_account_holder }}" @if ($accountSetupOpen) disabled @endif>
      @else
        <button type="button" class="add-account-button" id="addAccountButton" @if ($accountSetupOpen) hidden @endif>+ Add withdrawal account</button>
      @endif
      <div class="account-setup" id="accountSetup" @if (! $accountSetupOpen) hidden @endif>
          <div class="account-options" role="group" aria-label="Withdrawal account type">
            <button type="button" class="account-option {{ $selectedType === 'e_wallet' ? 'is-selected' : '' }}" data-account-type="e_wallet">E-wallet</button>
            <button type="button" class="account-option {{ $selectedType === 'bank' ? 'is-selected' : '' }}" data-account-type="bank">Bank</button>
          </div>
          <input type="hidden" name="account_type" id="accountType" value="{{ $selectedType }}" @if (! $accountSetupOpen) disabled @endif />

          <label class="label" id="providerLabel">E-wallet provider</label>
          <select class="input-box" name="bank_name" id="provider" required @if (! $accountSetupOpen) disabled @endif>
            <option value="">Select a provider</option>
            @foreach ($withdrawalProviders['bank'] as $provider)
              <option value="{{ $provider }}" data-provider-type="bank" @selected(old('bank_name', $savedAccount ? auth()->user()->bank_name : '') === $provider)>{{ $provider }}</option>
            @endforeach
            @foreach ($withdrawalProviders['e_wallet'] as $provider)
              <option value="{{ $provider }}" data-provider-type="e_wallet" @selected(old('bank_name', $savedAccount ? auth()->user()->bank_name : '') === $provider)>{{ $provider }}</option>
            @endforeach
          </select>

          <label class="label" id="accountNumberLabel">E-wallet mobile number</label>
          <input class="input-box" id="accountNumber" type="text" name="account_number" value="{{ old('account_number', $savedAccount ? auth()->user()->bank_account_number : '') }}" placeholder="09XXXXXXXXX" inputmode="numeric" required @if (! $accountSetupOpen) disabled @endif />

          <label class="label">Account Holder Name</label>
          <input class="input-box" type="text" name="account_holder" value="{{ old('account_holder', $savedAccount ? auth()->user()->bank_account_holder : '') }}" placeholder="Enter account holder name" required @if (! $accountSetupOpen) disabled @endif />
          <button class="save-account-button" type="submit" formnovalidate formaction="{{ route('withdrawal-account.store') }}">Save Account</button>
      </div>

      <div class="note">Minimum withdrawal is $20 and maximum withdrawal is $500. Your available balance will be reduced immediately after the request is submitted.</div>

      <button class="send-btn" type="submit">Request Withdrawal</button>
    </form>
  </section>

  <section class="recent">
    <div class="section-head">
      <h3>Recent Withdrawals</h3>
      <a href="{{ route('history') }}">See All →</a>
    </div>

    <div class="history-list">
      @forelse ($recentWithdrawals as $withdrawal)
        <div class="history-item">
          <div class="history-left">
            <div>${{ number_format($withdrawal->amount, 2) }} · {{ $withdrawal->bank_name ?: ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}</div>
            <small style="display:block; margin-top:4px; color:#8a9892; font:500 11px/1.2 Inter,system-ui,sans-serif;">{{ $withdrawal->created_at?->format('M d, Y') ?: '—' }} · {{ $withdrawal->account_number ? '••••'.substr($withdrawal->account_number, -4) : 'Withdrawal request' }}</small>
          </div>
          <div class="history-right {{ strtolower($withdrawal->status) }}">{{ ucfirst($withdrawal->status) }}</div>
        </div>
      @empty
        <div class="history-item">
          <div class="history-left">No recent withdrawals yet.</div>
          <div class="history-right">—</div>
        </div>
      @endforelse
    </div>
  </section>

</main>

<script>
  (function () {
    var amountInput = document.querySelector('input[name="amount"]');
    var amountError = document.getElementById('withdrawalAmountError');
    var quickAmounts = document.querySelectorAll('[data-withdrawal-amount]');

    quickAmounts.forEach(function (button) {
      button.addEventListener('click', function () {
        amountInput.value = button.getAttribute('data-withdrawal-amount');
        amountError.textContent = '';
        amountInput.focus();
      });
    });

    amountInput.addEventListener('input', function () {
      amountError.textContent = '';
    });
  }());
</script>

<script>
  (function () {
    var typeInput = document.getElementById('accountType');
    var provider = document.getElementById('provider');
    var numberInput = document.getElementById('accountNumber');
    var addAccountButton = document.getElementById('addAccountButton');
    var accountSetup = document.getElementById('accountSetup');
    var savedFields = document.querySelectorAll('[id^="savedAccount"]');
    var options = document.querySelectorAll('[data-account-type]');
    if (addAccountButton && accountSetup) {
      addAccountButton.addEventListener('click', function () {
        accountSetup.hidden = false;
        addAccountButton.hidden = true;
        [typeInput, provider, numberInput].forEach(function (field) {
          if (field) field.disabled = false;
        });
        var holder = accountSetup.querySelector('[name="account_holder"]');
        if (holder) holder.disabled = false;
        savedFields.forEach(function (field) { field.disabled = true; });
      });
    }
    if (!typeInput || !provider) return;

    function updateProviders(type) {
      typeInput.value = type;
      options.forEach(function (option) {
        option.classList.toggle('is-selected', option.getAttribute('data-account-type') === type);
      });
      var current = provider.value;
      Array.prototype.forEach.call(provider.options, function (option) {
        option.hidden = option.value && option.getAttribute('data-provider-type') !== type;
      });
      if (!provider.querySelector('option[value="' + current + '"]') ||
          provider.querySelector('option[value="' + current + '"]').getAttribute('data-provider-type') !== type) {
        provider.value = '';
      }
      numberInput.placeholder = type === 'e_wallet' ? '09XXXXXXXXX' : 'Enter account number';
      document.getElementById('providerLabel').textContent = type === 'e_wallet' ? 'E-wallet provider' : 'Bank';
      document.getElementById('accountNumberLabel').textContent = type === 'e_wallet' ? 'E-wallet mobile number' : 'Bank account number';
      updateAccountLength();
    }

    function updateAccountLength() {
      var lengths = {
        'GCash': 13, 'Maya': 13, 'GrabPay': 13, 'ShopeePay': 13, 'Coins.ph': 13,
        'BDO Unibank': 10, 'BPI': 10, 'Metrobank': 13, 'LandBank': 10, 'UnionBank': 12
      };
      var length = lengths[provider.value] || 0;
      numberInput.maxLength = length || 255;
      numberInput.setAttribute('maxlength', String(length || 255));
    }

    options.forEach(function (option) {
      option.addEventListener('click', function () { updateProviders(option.getAttribute('data-account-type')); });
    });
    provider.addEventListener('change', updateAccountLength);
    updateProviders(typeInput.value || 'bank');
  }());
</script>

<!-- Hidden element containing receipt data for modal auto-open -->
@if (session('receipt'))
  <script id="withdrawalReceiptData" type="application/json">
    {!! json_encode(session('receipt')) !!}
  </script>
@endif

<!-- Withdrawal Receipt Modal -->
<div class="withdrawal-modal" id="withdrawalReceiptModal" aria-hidden="true">
  <div class="withdrawal-modal-card" role="dialog" aria-modal="true" aria-label="Withdrawal receipt">
    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:14px;">
      <div>
        <div style="font-size:11px; font-weight:900; letter-spacing:0.16em; text-transform:uppercase; color:#166534;">Lulu Withdrawal Receipt</div>
        <h2 style="margin:4px 0 0; font-size:26px; line-height:32px; font-weight:900; letter-spacing:-.4px; color:#071a44;" id="withdrawalReceiptReference"></h2>
      </div>
      <div style="display:flex; align-items:center; gap:8px;">
        <div id="withdrawalReceiptBadge" style="padding:8px 10px; border-radius:999px; background:#166534; color:#fff; font-size:12px; font-weight:900;">Pending</div>
        <button type="button" id="withdrawalReceiptClose" aria-label="Close receipt" style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border:0; background:#f5f5f5; border-radius:8px; cursor:pointer; font-size:18px; color:#666; transition:all 0.2s ease; flex-shrink:0;">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>
    <div style="background:linear-gradient(135deg,#fff8f8 0%,#ffffff 100%); border-radius:20px; padding:20px; margin-bottom:18px; border:1px solid #f1d0d4; box-shadow:0 10px 25px rgba(237,28,36,.08);">
      <div style="display:flex; justify-content:space-between; gap:10px; padding:12px 0; border-bottom:1px solid #f1d0d4;">
        <span style="color:#64748b; font-weight:700;">Amount</span>
        <span id="withdrawalReceiptAmount" style="font-weight:900; color:#166534;"></span>
      </div>
      <div style="display:flex; justify-content:space-between; gap:10px; padding:12px 0; border-bottom:1px solid #f1d0d4;">
        <span style="color:#64748b; font-weight:700;">Bank / E-wallet</span>
        <span id="withdrawalReceiptBank" style="font-weight:900; color:#071a44;"></span>
      </div>
      <div style="display:flex; justify-content:space-between; gap:10px; padding:12px 0; border-bottom:1px solid #f1d0d4;">
        <span style="color:#64748b; font-weight:700;">Account Number</span>
        <span id="withdrawalReceiptAccount" style="font-weight:900; color:#071a44;"></span>
      </div>
      <div style="display:flex; justify-content:space-between; gap:10px; padding:12px 0; border-bottom:1px solid #f1d0d4;">
        <span style="color:#64748b; font-weight:700;">Recipient</span>
        <span id="withdrawalReceiptHolder" style="font-weight:900; color:#071a44;"></span>
      </div>
      <div style="display:flex; justify-content:space-between; gap:10px; padding:12px 0;">
        <span style="color:#64748b; font-weight:700;">Submitted</span>
        <span id="withdrawalReceiptSubmitted" style="font-weight:900; color:#071a44;"></span>
      </div>
    </div>
    <p style="color:#666; text-align:center; margin:20px 0;">Your withdrawal request has been received. We will process it shortly.</p>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:28px;">
      <button class="send-btn" type="button" id="withdrawalReceiptDone" style="background:#f5f5f5; color:#666; border:1.5px solid #e0e0e0; grid-column:span 2;">Done</button>
    </div>
  </div>
</div>

<script>
  (function() {
    var withdrawalReceiptModal = document.getElementById('withdrawalReceiptModal');
    var withdrawalReceiptClose = document.getElementById('withdrawalReceiptClose');
    var withdrawalReceiptDone = document.getElementById('withdrawalReceiptDone');
    var withdrawalReceiptReference = document.getElementById('withdrawalReceiptReference');
    var withdrawalReceiptBadge = document.getElementById('withdrawalReceiptBadge');
    var withdrawalReceiptAmount = document.getElementById('withdrawalReceiptAmount');
    var withdrawalReceiptBank = document.getElementById('withdrawalReceiptBank');
    var withdrawalReceiptAccount = document.getElementById('withdrawalReceiptAccount');
    var withdrawalReceiptHolder = document.getElementById('withdrawalReceiptHolder');
    var withdrawalReceiptSubmitted = document.getElementById('withdrawalReceiptSubmitted');

    function closeWithdrawalReceiptModal() {
      if (!withdrawalReceiptModal) return;
      withdrawalReceiptModal.classList.remove('is-open');
      withdrawalReceiptModal.setAttribute('aria-hidden', 'true');
    }

    function openWithdrawalReceiptModal(receiptData) {
      if (!withdrawalReceiptModal) return;
      if (withdrawalReceiptReference) withdrawalReceiptReference.textContent = receiptData.reference || '';
      if (withdrawalReceiptAmount) withdrawalReceiptAmount.textContent = '$' + (receiptData.amount || '0.00');
      if (withdrawalReceiptBank) withdrawalReceiptBank.textContent = receiptData.bank_name || '';
      if (withdrawalReceiptAccount) withdrawalReceiptAccount.textContent = receiptData.account_number || '';
      if (withdrawalReceiptHolder) withdrawalReceiptHolder.textContent = receiptData.account_holder || '';
      if (withdrawalReceiptSubmitted) withdrawalReceiptSubmitted.textContent = receiptData.submitted_at || 'Just now';
      if (withdrawalReceiptBadge) {
        withdrawalReceiptBadge.textContent = receiptData.status || 'Pending';
        withdrawalReceiptBadge.style.background = receiptData.status === 'Approved' ? '#137547' : '#166534';
      }
      withdrawalReceiptModal.classList.add('is-open');
      withdrawalReceiptModal.setAttribute('aria-hidden', 'false');
      if (withdrawalReceiptDone) withdrawalReceiptDone.focus();
    }

    if (withdrawalReceiptClose) {
      withdrawalReceiptClose.addEventListener('click', closeWithdrawalReceiptModal);
    }

    if (withdrawalReceiptDone) {
      withdrawalReceiptDone.addEventListener('click', closeWithdrawalReceiptModal);
    }

    // Auto-open modal if receipt data is present in page
    var receiptDataElement = document.querySelector('script#withdrawalReceiptData[type="application/json"]');
    if (receiptDataElement && receiptDataElement.textContent.trim()) {
      try {
        var receiptData = JSON.parse(receiptDataElement.textContent.trim());
        setTimeout(function() {
          openWithdrawalReceiptModal(receiptData);
        }, 300);
      } catch (e) {
        console.error('Failed to parse receipt data:', e);
      }
    }
  })();
</script>

@endsection
