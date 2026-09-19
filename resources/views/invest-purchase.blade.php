@extends('layouts.app')

@section('content')
<style>
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

<main class="purchase-page">
  <header class="purchase-header">
    <a class="purchase-back" href="{{ route('invest') }}" aria-label="Back to packages">&lsaquo;</a>
    <h1>Purchase {{ $package['name'] }} Bond</h1>
  </header>

  <div class="purchase-shell">
    <form class="purchase-card" method="post" action="{{ route('investments.store') }}" id="purchaseForm">
      @csrf
      <input type="hidden" name="package" value="{{ $packageKey }}">
      <input type="hidden" name="currency" id="purchaseCurrency" value="USD">
      <input type="hidden" name="payment_method" id="purchasePaymentMethod" value="">
      @if ($errors->any())
        <div class="form-error">{{ $errors->first() }}</div>
      @endif
      <h2 class="purchase-title">{{ $package['name'] }}</h2>
      <p class="purchase-copy">Investment range: ${{ number_format($package['min_amount'], 2) }} to ${{ number_format($package['max_amount'], 2) }} with {{ number_format($package['daily_interest_rate'], 2) }}% daily interest for {{ $package['duration_days'] }} days.</p>

      <label class="purchase-label" for="purchaseAmount">Amount in USD</label>
      <input class="purchase-input" id="purchaseAmount" type="number" name="amount" min="{{ $package['min_amount'] }}" max="{{ $package['max_amount'] }}" step="0.01" value="{{ old('amount', $package['price']) }}" required>
      <div class="purchase-range"><span>Min: ${{ number_format($package['min_amount'], 2) }}</span><span>Max: ${{ number_format($package['max_amount'], 2) }}</span></div>

      <div class="currency-toggle" aria-label="Currency">
        <button class="currency-button is-active" type="button" data-currency="USD">USD</button>
        <button class="currency-button" type="button" data-currency="PHP">PHP</button>
      </div>

      <div class="estimate-grid">
        <div class="estimate-card"><div class="estimate-label">Daily</div><div class="estimate-value" id="purchaseDaily">$0.00</div></div>
        <div class="estimate-card"><div class="estimate-label">Weekly</div><div class="estimate-value" id="purchaseWeekly">$0.00</div></div>
        <div class="estimate-card"><div class="estimate-label">Total</div><div class="estimate-value" id="purchaseTotal">$0.00</div></div>
      </div>
      <p class="estimate-note" id="purchaseNote"></p>

      <button class="purchase-submit" type="button" id="showPayment">Confirm</button>

    </form>
  </div>
</main>

<div class="payment-modal" id="paymentModal" aria-hidden="true">
  <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle">
    <h2 class="payment-modal-title" id="paymentModalTitle">Mode of payment</h2>
    <p class="payment-modal-copy">Choose how you want to pay for this investment.</p>
    <div class="payment-options">
      <button class="payment-choice" type="button" data-payment="bank_transfer">Bank transfer<span>Pay through bank deposit</span></button>
      <button class="payment-choice" type="button" data-payment="e_wallet">E-wallet<span>Pay using GCash, Maya, GrabPay, or ShopeePay</span></button>
      <button class="payment-choice" type="button" data-payment="account_balance">Account balance<span>Use available account funds</span></button>
      <button class="payment-choice" type="button" data-payment="crypto">Crypto<span>Pay using cryptocurrency</span></button>
    </div>

    <div class="payment-modal-actions">
      <button type="button" id="paymentModalCancel">Cancel</button>
      <button class="payment-modal-submit" type="button" id="submitPurchase" disabled>Continue</button>
    </div>
  </div>

  <div class="payment-modal" id="walletModal" aria-hidden="true">
    <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="walletModalTitle">
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
</div>

<div class="payment-modal" id="bankModal" aria-hidden="true">
  <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="bankModalTitle">
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
    var currency = 'USD';
    var amount = document.getElementById('purchaseAmount');
    var currencyInput = document.getElementById('purchaseCurrency');
    var paymentInput = document.getElementById('purchasePaymentMethod');
    var daily = document.getElementById('purchaseDaily');
    var weekly = document.getElementById('purchaseWeekly');
    var total = document.getElementById('purchaseTotal');
    var note = document.getElementById('purchaseNote');
    var paymentModal = document.getElementById('paymentModal');
    var paymentModalCancel = document.getElementById('paymentModalCancel');
    var submitPurchase = document.getElementById('submitPurchase');
    var bankModal = document.getElementById('bankModal');
    var walletModal = document.getElementById('walletModal');
    var qrModal = document.getElementById('qrModal');
    var selectedBank = null;
    function money(value) {
      var converted = currency === 'PHP' ? value * phpRate : value;
      return (currency === 'PHP' ? '₱' : '$') + converted.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
    }
    function update() {
      var base = Number(amount.value || 0);
      var dailyValue = base * rate / 100;
      daily.textContent = money(dailyValue);
      weekly.textContent = money(dailyValue * 7);
      total.textContent = money(base + dailyValue * days);
      note.textContent = money(base) + ' x ' + rate.toFixed(2) + '% = ' + money(dailyValue) + ' daily. Estimated total balance after ' + days + ' days is ' + money(base + dailyValue * days) + '.';
      currencyInput.value = currency;
      document.querySelectorAll('[data-currency]').forEach(function (button) { button.classList.toggle('is-active', button.dataset.currency === currency); });
    }
    amount.addEventListener('input', update);
    document.querySelectorAll('[data-currency]').forEach(function (button) {
      button.addEventListener('click', function () { currency = button.dataset.currency; update(); });
    });
    document.getElementById('showPayment').addEventListener('click', function () {
      paymentModal.classList.add('is-open');
      paymentModal.setAttribute('aria-hidden', 'false');
    });
    document.querySelectorAll('#paymentModal [data-payment]').forEach(function (button) {
      button.addEventListener('click', function () {
        paymentInput.value = button.dataset.payment;
        if (button.dataset.payment === 'bank_transfer') {
          paymentModal.classList.remove('is-open');
          paymentModal.setAttribute('aria-hidden', 'true');
          bankModal.classList.add('is-open');
          bankModal.setAttribute('aria-hidden', 'false');
          return;
        }
        if (button.dataset.payment === 'e_wallet') {
          paymentModal.classList.remove('is-open');
          paymentModal.setAttribute('aria-hidden', 'true');
          walletModal.classList.add('is-open');
          walletModal.setAttribute('aria-hidden', 'false');
          return;
        }
        document.querySelectorAll('#paymentModal [data-payment]').forEach(function (item) { item.classList.remove('is-selected'); });
        button.classList.add('is-selected');
        submitPurchase.disabled = false;
      });
    });
    paymentModalCancel.addEventListener('click', function () {
      paymentModal.classList.remove('is-open');
      paymentModal.setAttribute('aria-hidden', 'true');
    });
    document.getElementById('bankModalBack').addEventListener('click', function () {
      bankModal.classList.remove('is-open');
      bankModal.setAttribute('aria-hidden', 'true');
      paymentModal.classList.add('is-open');
      paymentModal.setAttribute('aria-hidden', 'false');
    });
    document.getElementById('walletModalBack').addEventListener('click', function () {
      walletModal.classList.remove('is-open');
      walletModal.setAttribute('aria-hidden', 'true');
      paymentModal.classList.add('is-open');
      paymentModal.setAttribute('aria-hidden', 'false');
    });
    document.querySelectorAll('[data-bank-name]').forEach(function (button) {
      button.addEventListener('click', function () {
        selectedBank = button.dataset.bankName;
        var providerKey = selectedBank.toLowerCase().replace(/\s+/g, '');
        var paymentUrl = new URL('{{ url('/invest/payment') }}/' + providerKey, window.location.origin);
        paymentUrl.searchParams.set('package', '{{ $packageKey }}');
        paymentUrl.searchParams.set('amount', amount.value);
        paymentUrl.searchParams.set('currency', currency);
        window.location.href = paymentUrl.toString();
      });
      document.querySelectorAll('[data-wallet-name]').forEach(function (button) {
        button.addEventListener('click', function () {
          var providerKey = button.dataset.walletName.toLowerCase().replace(/\s+/g, '');
          var paymentUrl = new URL('{{ url('/invest/payment') }}/' + providerKey, window.location.origin);
          paymentUrl.searchParams.set('package', '{{ $packageKey }}');
          paymentUrl.searchParams.set('amount', amount.value);
          paymentUrl.searchParams.set('currency', currency);
          window.location.href = paymentUrl.toString();
        });
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
      document.getElementById('purchaseForm').submit();
    });
    submitPurchase.addEventListener('click', function () {
      if (paymentInput.value) document.getElementById('purchaseForm').submit();
    });
    update();
  })();
</script>
@endsection
