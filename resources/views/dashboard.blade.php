@extends('layouts.app')

@section('content')
<style>
  body { background:#ffffff !important; }
  .dashboard-shell { max-width:1120px; margin:18px auto 36px; padding:0 12px; }
  .account-hero { position:relative; overflow:hidden; border-radius:16px; background:#0f3d2e; color:#fff; box-shadow:0 10px 24px rgba(15,61,46,.2); }
  .account-hero img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; object-position:center; }
  .account-hero::after { content:''; position:absolute; inset:0; background:linear-gradient(90deg, rgba(15,61,46,.92), rgba(22,101,52,.68), rgba(245,164,0,.32)); }
  .hero-inner { position:relative; z-index:1; padding:28px 16px 20px; }
  .hero-kicker { margin:0 0 8px; color:#f5dfad; font-size:12px; line-height:16px; font-weight:800; letter-spacing:.18em; text-transform:uppercase; }
  .balance-label { color:#ffffff !important; font-size:14px; line-height:19px; font-weight:700; text-shadow:0 1px 0 rgba(0,0,0,.25); }
  .balance-value { margin-top:4px; color:#fff; font-size:32px; line-height:38px; font-weight:800; }
  .hero-actions { display:flex; flex-wrap:wrap; gap:6px; margin-top:12px; }
  .hero-action { display:inline-flex; align-items:center; justify-content:center; min-height:32px; padding:0 11px; border-radius:7px; border:1px solid rgba(255,255,255,.35); background:transparent; color:#fff; font-size:10px; line-height:13px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; text-decoration:none; cursor:pointer; }
  .hero-action.primary { border-color:#fff; background:#fff; color:#14532d; }
  .notice { margin-top:14px; max-width:560px; border-radius:10px; background:rgba(255,255,255,.12); padding:12px 14px; color:#fff; font-size:13px; line-height:19px; }
  .summary-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:12px; margin:14px 0; }
  .swipe-invest {
    position:relative;
    overflow:hidden;
    display:flex;
    align-items:center;
    gap:12px;
    width:100%;
    min-height:50px;
    margin:14px auto 0;
    padding:7px;
    border:0;
    border-radius:999px;
    background:linear-gradient(90deg, #14532d, #166534);
    color:#fff;
    text-decoration:none;
    box-shadow:0 10px 24px rgba(20,83,45,.22);
    touch-action:none;
    user-select:none;
    cursor:grab;
  }
  .swipe-invest::before {
    content:'';
    position:absolute;
    inset:0;
    background:linear-gradient(90deg, rgba(20,83,45,0) 0%, rgba(255,255,255,.18) 42%, rgba(255,255,255,.72) 74%, #ffffff 100%);
    pointer-events:none;
  }
  .swipe-knob {
    position:relative;
    z-index:1;
    width:50px;
    height:50px;
    flex:0 0 auto;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#fff;
    color:#14532d;
    font-size:26px;
    line-height:1;
    font-weight:900;
    box-shadow:0 6px 16px rgba(0,0,0,.18);
    transition:transform .18s ease;
    cursor:grab;
    touch-action:none;
  }
  .swipe-invest.is-dragging .swipe-knob {
    transition:none;
    cursor:grabbing;
  }
  .swipe-invest.is-dragging {
    cursor:grabbing;
  }
  .swipe-invest.is-complete .swipe-knob {
    transition:transform .18s ease;
  }
  .swipe-copy {
    position:relative;
    z-index:1;
    flex:1;
    min-width:0;
    text-align:left;
  }
  .swipe-title {
    display:block;
    font-size:13px;
    line-height:17px;
    font-weight:900;
    letter-spacing:.08em;
    text-transform:uppercase;
  }
  .swipe-hint {
    display:block;
    margin-top:2px;
    color:#f5dfad;
    font-size:11px;
    line-height:15px;
    font-weight:700;
  }
  .summary-card { padding:16px; border-radius:16px; background:#fff; box-shadow:0 10px 24px rgba(15,23,42,.08); border:1px solid #dcefe8; }
  .summary-card.featured { background:linear-gradient(135deg, #14532d, #166534 62%, #d6a84f); border-color:#166534; color:#fff; }
  .summary-label { color:#14532d; font-size:11px; line-height:15px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
  .summary-card.featured .summary-label { color:#f5dfad; }
  .summary-value { margin-top:10px; color:#001a33; font-size:25px; line-height:31px; font-weight:900; }
  .summary-card.featured .summary-value { color:#fff; }
  .summary-help { margin-top:10px; color:#64748b; font-size:12px; line-height:17px; }
  .summary-card.featured .summary-help { color:#fffaf0; }
  .status-message { margin:14px 0 0; border-radius:14px; background:#fffaf0; border:1px solid #d6a84f; padding:12px 14px; color:#14532d; font-size:13px; line-height:18px; font-weight:800; }
  .activity-card { border-radius:18px; background:#fff; padding:16px; box-shadow:0 10px 24px rgba(15,23,42,.08); border:1px solid #dcefe8; }
  .activity-head { display:block; gap:12px; }
  .activity-title { color:#14532d; font-size:15px; line-height:20px; font-weight:800; }
  .activity-item { display:block; gap:14px; margin-top:10px; border-radius:14px; background:#f4fbf7; padding:14px 16px; color:#475569; font-size:14px; line-height:20px; }
  .activity-item strong { display:block; color:#14532d; }
  .activity-time { color:#64748b; font-size:12px; }
  .logout-form { display:inline; }
  @media (min-width:761px) {
    .dashboard-shell { margin:28px auto 48px; padding:0 18px; }
    .account-hero { border-radius:18px; }
    .hero-inner { padding:42px 22px 22px; }
    .balance-label { font-size:16px; line-height:22px; }
    .balance-value { font-size:40px; line-height:46px; }
    .hero-action { min-height:34px; padding:0 15px; }
    .swipe-invest { margin-top:18px; }
    .summary-grid { gap:16px; margin:18px 0 22px; }
    .summary-card { padding:20px; }
    .activity-card { padding:24px; }
    .activity-head, .activity-item { display:flex; align-items:center; justify-content:space-between; }
  }
  /* Mobile tweaks */
  @media (max-width: 480px) {
    .dashboard-shell { max-width:100%; margin:12px 10px 20px; padding:0 10px; }
    .account-hero { border-radius:14px; }
    .hero-inner { padding:18px 12px 16px; }
    .balance-value { font-size:28px; line-height:34px; }
    .balance-label { font-size:13px; }
    .hero-actions { gap:8px; }
    .hero-action { font-size:11px; padding:0 10px; min-height:30px; }
    .swipe-invest { padding:6px; min-height:44px; border-radius:12px; }
    .swipe-knob { width:44px; height:44px; font-size:22px; }
    .summary-grid { grid-template-columns:1fr; gap:12px; }
    .summary-card { padding:10px; border-radius:12px; min-height:unset; }
    .summary-card > div:first-child { display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .summary-card.featured { padding:8px 10px; }
    .summary-card.featured > div:first-child { align-items:center; }
    .summary-card.featured .summary-value { font-size:22px; margin:0; line-height:1; }
    .summary-value { font-size:20px; margin-top:0; }
    .summary-label { font-size:11px; }
    .summary-help { margin-top:6px; font-size:12px; }
    .notice { max-width:100%; }
    .activity-card { padding:14px; }
    .activity-item { padding:12px; }
    body, html { -webkit-text-size-adjust:100%; }
  }
</style>

@php
  $user = auth()->user();
  $investments = $user->investments()->latest()->get();
  $activeCapital = $investments->sum(fn ($investment) => (float) $investment->amount);
  $dailyInterest = $investments->sum(fn ($investment) => $investment->dailyInterestAmount());
  $earnedIncome = $investments->sum(fn ($investment) => $investment->earnedInterest());
  $availableBalance = (float) $user->balance;
  $recentInvestments = $investments->take(3);
@endphp

<main class="dashboard-shell">
  @if (session('status'))
    <div class="status-message">{{ session('status') }}</div>
  @endif

  <section class="account-hero">
    <img src="/MGames%20Festival.png" alt="">
    <div class="hero-inner">
      <div class="balance-label">Available balance</div>
      <div class="balance-value">$0.00</div>
      <div class="hero-actions">
        <a class="hero-action" href="{{ route('unavailable') }}">Withdraw</a>
        <a class="hero-action" href="{{ route('unavailable') }}">Transfer</a>
        <a class="hero-action" href="{{ route('unavailable') }}">Statements</a>
        @if ($user->is_admin)
          <a class="hero-action" href="{{ route('admin.dashboard') }}">Admin</a>
        @endif
        <form class="logout-form" action="{{ route('logout') }}" method="post">
          @csrf
          <button class="hero-action" type="submit">Logout</button>
        </form>
      </div>
    </div>
  </section>

  <div class="swipe-invest" role="link" tabindex="0" data-swipe-url="{{ route('invest') }}" aria-label="Swipe to invest">
    <span class="swipe-knob">›</span>
    <span class="swipe-copy">
      <span class="swipe-title">Swipe to Invest</span>
      <span class="swipe-hint">Start your partner investment</span>
    </span>
  </div>

  <section class="summary-grid" aria-label="Account summary">
    <div class="summary-card featured">
      <div>
        <div class="summary-label">Investment</div>
        <div class="summary-value">${{ number_format($activeCapital, 2) }}</div>
      </div>
      <div class="summary-help">Active partner capital.</div>
    </div>
    <div class="summary-card">
      <div>
        <div class="summary-label">Income</div>
        <div class="summary-value">${{ number_format($earnedIncome, 2) }}</div>
      </div>
      <div class="summary-help">Total earned income.</div>
    </div>
    <div class="summary-card">
      <div>
        <div class="summary-label">Referral</div>
        <div class="summary-value">$0.00</div>
      </div>
      <div class="summary-help">Referral rewards.</div>
    </div>
    <div class="summary-card">
      <div>
        <div class="summary-label">Daily Interest</div>
        <div class="summary-value">${{ number_format($dailyInterest, 2) }}</div>
      </div>
      <div class="summary-help">Estimated daily earnings.</div>
    </div>
  </section>

  <section class="activity-card">
    <div class="activity-head">
      <div class="activity-title">Recent activity</div>
      <a href="{{ route('unavailable') }}" style="color:#14532d;text-decoration:none;font-size:13px;font-weight:800;">View all</a>
    </div>
    @foreach ($recentInvestments as $investment)
      <div class="activity-item">
        <div>
          <strong>{{ $investment->package_name }} availed</strong>
          <span class="activity-time">{{ $investment->created_at?->format('h:i A') ?: 'Now' }} - investment submitted</span>
        </div>
        <div style="font-weight:800;color:#14532d;">${{ number_format((float) $investment->amount, 2) }}</div>
      </div>
    @endforeach
    <div class="activity-item"><div><strong>Partner account created</strong><span class="activity-time">{{ $user->created_at?->format('h:i A') ?: 'Now' }} - submitted</span></div><div style="font-weight:800;color:#14532d;">{{ $user->region ?: 'N/A' }}</div></div>
    <div class="activity-item"><div><strong>Application profile saved</strong><span class="activity-time">{{ $user->last_seen_at?->format('h:i A') ?: 'Now' }} - active</span></div><div style="font-weight:800;color:#14532d;">{{ $user->phone ?: 'N/A' }}</div></div>
  </section>
</main>
<script>
  (function () {
    var swipe = document.querySelector('.swipe-invest');
    if (!swipe) return;

    var knob = swipe.querySelector('.swipe-knob');
    var dragStartX = 0;
    var initialX = 0;
    var currentX = 0;
    var maxX = 0;
    var dragging = false;

    function setKnob(x) {
      currentX = Math.max(0, Math.min(x, maxX));
      knob.style.transform = 'translateX(' + currentX + 'px)';
    }

    function resetKnob() {
      dragging = false;
      swipe.classList.remove('is-dragging');
      setKnob(0);
    }

    function completeSwipe() {
      swipe.classList.remove('is-dragging');
      swipe.classList.add('is-complete');
      setKnob(maxX);
      window.setTimeout(function () {
        window.location.href = swipe.dataset.swipeUrl;
      }, 160);
    }

    function start(clientX, event) {
      dragging = true;
      dragStartX = clientX;
      initialX = currentX;
      maxX = Math.max(0, swipe.clientWidth - knob.offsetWidth - 14);
      swipe.classList.add('is-dragging');
      if (event) event.preventDefault();
    }

    function move(clientX, event) {
      if (!dragging) return;
      setKnob(initialX + clientX - dragStartX);
      if (event) event.preventDefault();
    }

    function end() {
      if (!dragging) return;
      dragging = false;
      if (currentX >= maxX * 0.5) {
        completeSwipe();
        return;
      }
      resetKnob();
    }

    setKnob(0);

    if (window.PointerEvent) {
      swipe.addEventListener('pointerdown', function (event) {
        if (event.button !== undefined && event.button !== 0) return;
        swipe.setPointerCapture(event.pointerId);
        start(event.clientX, event);
      });
      swipe.addEventListener('pointermove', function (event) {
        move(event.clientX, event);
      });
      swipe.addEventListener('pointerup', end);
      swipe.addEventListener('pointercancel', resetKnob);
    } else {
      swipe.addEventListener('mousedown', function (event) {
        start(event.clientX, event);
      });
      swipe.addEventListener('touchstart', function (event) {
        start(event.touches[0].clientX, event);
      }, { passive:false });
      document.addEventListener('mousemove', function (event) {
        move(event.clientX, event);
      });
      document.addEventListener('touchmove', function (event) {
        move(event.touches[0].clientX, event);
      }, { passive:false });
      document.addEventListener('mouseup', end);
      document.addEventListener('touchend', end);
    }
    swipe.addEventListener('keydown', function (event) {
      if (event.key === 'Enter' || event.key === ' ') {
        completeSwipe();
        event.preventDefault();
      }
    });
  })();
</script>
@endsection
