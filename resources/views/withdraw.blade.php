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

</style>

<main class="phone">

  <header class="withdraw-header">
    <a href="{{ route('dashboard') }}" class="back-btn">‹</a>
    <div class="page-title">
      <h2>Withdraw Money</h2>
      <p>Request funds to your bank or linked card</p>
    </div>
  </header>

  <section class="balance-card">
    <svg class="balance-card-art" viewBox="0 0 1200 300" role="img"
         aria-label="Lulu Retail green card background" preserveAspectRatio="none"
         xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="withdrawHeroBase" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#006f51"/>
          <stop offset=".48" stop-color="#008f65"/>
          <stop offset="1" stop-color="#006c50"/>
        </linearGradient>
        <linearGradient id="withdrawHeroLime" x1="0" y1="1" x2="1" y2="0">
          <stop offset="0" stop-color="#67c936" stop-opacity=".10"/>
          <stop offset=".55" stop-color="#79d83e" stop-opacity=".72"/>
          <stop offset="1" stop-color="#25ae5e" stop-opacity=".30"/>
        </linearGradient>
        <linearGradient id="withdrawHeroGlow" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0" stop-color="#f9dd3c" stop-opacity="0"/>
          <stop offset=".55" stop-color="#ffe45c" stop-opacity=".95"/>
          <stop offset="1" stop-color="#fff29a" stop-opacity=".76"/>
        </linearGradient>
        <radialGradient id="withdrawHeroCornerGlow" cx="0" cy="0" r="1">
          <stop offset="0" stop-color="#9de052" stop-opacity=".72"/>
          <stop offset="1" stop-color="#9de052" stop-opacity="0"/>
        </radialGradient>
        <clipPath id="withdrawHeroCardClip">
          <rect width="1200" height="300" rx="32"/>
        </clipPath>
      </defs>
      <g clip-path="url(#withdrawHeroCardClip)">
        <rect width="1200" height="300" fill="url(#withdrawHeroBase)"/>
        <ellipse cx="195" cy="-35" rx="300" ry="185" fill="url(#withdrawHeroCornerGlow)"/>
        <path d="M-40 264 C170 72 285 30 470 -16 L250 -25 C133 48 52 111 -40 206Z" fill="#1fac67" opacity=".22"/>
        <path d="M580 330 C785 300 886 150 1240 58 L1240 330Z" fill="#1bb966" opacity=".42"/>
        <path d="M705 330 C884 286 1003 193 1240 132 L1240 330Z" fill="url(#withdrawHeroLime)"/>
        <path d="M760 330 C922 274 1058 207 1240 169" fill="none" stroke="url(#withdrawHeroGlow)" stroke-width="9" stroke-linecap="round"/>
        <path d="M-55 110 C70 92 132 35 190 -18" fill="none" stroke="#f6d63b" stroke-width="3" opacity=".9"/>
        <path d="M845 330 C1000 270 1118 248 1240 278 L1240 330Z" fill="#70cf3a" opacity=".28"/>
        <rect width="1200" height="300" rx="32" fill="none" stroke="#ffffff" stroke-opacity=".08" stroke-width="2"/>
      </g>
    </svg>
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

      <label class="label">Amount to Withdraw</label>
      <div class="input-row">
        <span class="currency">$</span>
        <input class="input-box amount-input" type="number" name="amount" min="20" max="500" step="0.01" placeholder="0.00" required />
      </div>

      <div class="quick-row">
        <button type="button" onclick="document.querySelector('input[name=amount]').value='10'">$10</button>
        <button type="button" onclick="document.querySelector('input[name=amount]').value='25'">$25</button>
        <button type="button" onclick="document.querySelector('input[name=amount]').value='50'">$50</button>
        <button type="button" onclick="document.querySelector('input[name=amount]').value='100'">$100</button>
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
          <span style="display:block; margin-top:4px;">{{ auth()->user()->bank_name }} · {{ auth()->user()->bank_account_number }}</span>
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
      <a href="#">See All →</a>
    </div>

    <div class="history-list">
      @forelse ($recentWithdrawals as $withdrawal)
        <div class="history-item">
          <div class="history-left">
            ${{ number_format($withdrawal->amount, 2) }} • {{ ucfirst(str_replace('_', ' ', $withdrawal->payment_method)) }}
          </div>
          <div class="history-right">{{ ucfirst($withdrawal->status) }}</div>
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
