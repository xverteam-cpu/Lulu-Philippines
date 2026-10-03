@extends('layouts.app')

@section('content')
<style>
  :root {
    --ref-bg: #f2f5f3;
    --ref-surface: #fff;
    --ref-ink: #0a1f17;
    --ref-muted: #4b5b54;
    --ref-subtle: #8a9892;
    --ref-line: #e2e9e5;
    --ref-green: #0e8a5a;
    --ref-mint: #e6f7ef;
    --ref-display: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    --ref-ui: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --ref-shadow-sm: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06);
    --ref-shadow-md: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18);
  }

  body {
    display: flex;
    min-height: 100vh;
    align-items: flex-start;
    justify-content: center;
    padding: 24px 0;
    color: var(--ref-ink);
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9;
    font-family: var(--ref-ui);
    -webkit-font-smoothing: antialiased;
  }

  .container { width: 390px; max-width: 100%; margin: 0 auto; padding: 0; }
  .referral-phone { width: 100%; min-height: min(844px, calc(100vh - 48px)); overflow: hidden; border-radius: 36px; background: var(--ref-bg); box-shadow: 0 0 0 8px #0d1411, 0 0 0 9px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4); }
  .referral-screen { min-height: inherit; max-height: calc(100vh - 48px); overflow-y: auto; scrollbar-width: none; overscroll-behavior: contain; }
  .referral-screen::-webkit-scrollbar { display: none; }
  .referral-chrome { position: sticky; top: 0; z-index: 5; background: rgba(242, 245, 243, .88); backdrop-filter: saturate(180%) blur(18px); -webkit-backdrop-filter: saturate(180%) blur(18px); }
  .referral-chrome.is-scrolled { box-shadow: 0 1px 0 rgba(10, 31, 23, .06); }
  .referral-nav { display: grid; min-height: 60px; grid-template-columns: 40px minmax(0, 1fr) auto; align-items: center; gap: 12px; padding: 0 16px; }
  .referral-back { display: grid; width: 40px; height: 40px; place-items: center; border-radius: 50%; color: var(--ref-ink); background: #fff; box-shadow: var(--ref-shadow-sm), inset 0 0 0 1px rgba(10, 31, 23, .05); text-decoration: none; transition: transform .2s ease; }
  .referral-back:active { transform: scale(.92); }
  .referral-brand { display: flex; align-items: center; justify-self: center; gap: 8px; color: var(--ref-ink); text-decoration: none; white-space: nowrap; font: 800 15px/1 var(--ref-display); }
  .referral-brand img { width: 26px; height: 26px; border-radius: 8px; object-fit: contain; }
  .referral-badge { display: inline-flex; height: 28px; align-items: center; gap: 6px; padding: 0 10px; border-radius: 99px; color: #075a3b; background: var(--ref-mint); box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .18); font: 700 9.5px/1 var(--ref-ui); letter-spacing: .08em; text-transform: uppercase; }
  .referral-badge i { width: 6px; height: 6px; border-radius: 50%; background: #14a86d; }
  .referral-main { padding: 8px 20px 32px; }
  .referral-hero { padding: 8px 2px 4px; }
  .referral-eyebrow { color: var(--ref-subtle); font: 650 10.5px/1 var(--ref-ui); letter-spacing: .14em; text-transform: uppercase; }
  .referral-title { margin: 8px 0 0; color: var(--ref-ink); font: 800 30px/1.06 var(--ref-display); letter-spacing: -.045em; }
  .referral-subtitle { max-width: 330px; margin: 9px 0 0; color: var(--ref-muted); font: 450 14px/1.5 var(--ref-ui); }
  .referral-subtitle strong { color: var(--ref-ink); font-weight: 650; }
  .referral-overview { position: relative; overflow: hidden; margin-top: 18px; padding: 18px; border-radius: 24px; color: #fff; background: radial-gradient(120% 100% at 105% -10%, rgba(62, 224, 161, .5), transparent 55%), radial-gradient(70% 70% at -10% 110%, rgba(198, 243, 107, .18), transparent 60%), linear-gradient(160deg, #0b6a47 0%, #054a31 48%, #022418 100%); box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(4, 30, 20, .35), inset 0 1px 0 rgba(255, 255, 255, .2); }
  .referral-overview::before { position: absolute; inset: 0; background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .06), transparent); content: ""; pointer-events: none; }
  .referral-overview-top { position: relative; display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
  .referral-overview-label { color: rgba(232, 255, 244, .7); font: 650 10px/1 var(--ref-ui); letter-spacing: .14em; text-transform: uppercase; }
  .referral-overview-value { margin-top: 8px; font: 760 34px/1 var(--ref-ui); letter-spacing: -.04em; font-variant-numeric: tabular-nums; }
  .referral-overview-copy { margin-top: 7px; color: rgba(232, 255, 244, .72); font: 500 11.5px/1.4 var(--ref-ui); }
  .referral-overview-icon { display: grid; width: 42px; height: 42px; flex: 0 0 42px; place-items: center; border-radius: 14px; color: #e8fff4; background: rgba(255, 255, 255, .11); box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .12); }
  .referral-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-top: 10px; }
  .referral-stat { min-width: 0; padding: 15px; border-radius: 18px; background: var(--ref-surface); box-shadow: var(--ref-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .referral-stat-icon { display: grid; width: 34px; height: 34px; place-items: center; margin-bottom: 12px; border-radius: 11px; color: #075a3b; background: var(--ref-mint); }
  .referral-stat-label { color: var(--ref-subtle); font: 650 9.5px/1.25 var(--ref-ui); letter-spacing: .11em; text-transform: uppercase; }
  .referral-stat-value { margin-top: 8px; color: var(--ref-ink); font: 760 24px/1 var(--ref-ui); letter-spacing: -.035em; font-variant-numeric: tabular-nums; }
  .referral-stat-copy { margin-top: 7px; color: var(--ref-muted); font: 500 11.5px/1.35 var(--ref-ui); }
  .referral-panel { margin-top: 18px; padding: 18px; border-radius: 24px; background: var(--ref-surface); box-shadow: var(--ref-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .referral-panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
  .referral-panel-head h2,.referral-section-head h2 { margin: 0; color: var(--ref-ink); font: 760 18px/1.15 var(--ref-display); letter-spacing: -.025em; }
  .referral-panel-head p { margin: 4px 0 0; color: var(--ref-subtle); font: 500 11.5px/1.4 var(--ref-ui); }
  .referral-linkbox { display: flex; height: 52px; align-items: center; gap: 10px; margin-top: 14px; padding: 0 10px 0 14px; border-radius: 15px; background: #f7faf8; box-shadow: inset 0 0 0 1.5px var(--ref-line); }
  .referral-linkbox code { min-width: 0; flex: 1; overflow: hidden; color: var(--ref-ink); text-overflow: ellipsis; white-space: nowrap; font: 600 12.5px/1 var(--ref-ui); }
  .referral-copy-mini { display: grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border-radius: 11px; color: #075a3b; background: #fff; box-shadow: var(--ref-shadow-sm), inset 0 0 0 1px rgba(10, 31, 23, .05); transition: transform .2s ease; }
  .referral-copy-mini:active { transform: scale(.92); }
  .referral-actions { display: grid; grid-template-columns: 1fr auto; gap: 10px; margin-top: 10px; }
  .referral-copy-cta { display: flex; height: 50px; align-items: center; justify-content: center; gap: 8px; border-radius: 15px; color: #fff; background: linear-gradient(180deg, #11905e, #0a6a45); box-shadow: 0 10px 24px -8px rgba(10, 106, 69, .45), 0 2px 4px rgba(10, 106, 69, .18), inset 0 1px 0 rgba(255, 255, 255, .24); font: 700 14px/1 var(--ref-ui); transition: transform .2s ease, filter .2s ease; }
  .referral-copy-cta:hover { filter: brightness(1.05); }
  .referral-copy-cta:active { transform: scale(.98); }
  .referral-share { display: grid; width: 50px; height: 50px; place-items: center; border-radius: 15px; color: var(--ref-muted); background: #fff; box-shadow: inset 0 0 0 1px var(--ref-line), var(--ref-shadow-sm); transition: transform .2s ease; }
  .referral-share:active { transform: scale(.96); }
  .referral-tip { display: flex; gap: 10px; align-items: flex-start; margin-top: 14px; padding: 12px 13px; border-radius: 15px; color: var(--ref-muted); background: #f7faf8; box-shadow: inset 0 0 0 1px var(--ref-line); font: 500 11.5px/1.45 var(--ref-ui); }
  .referral-tip svg { flex: none; margin-top: 1px; color: var(--ref-green); }
  .referral-section-head { display: flex; align-items: end; justify-content: space-between; gap: 12px; margin: 24px 2px 10px; }
  .referral-section-amount { color: #075a3b; font: 760 18px/1 var(--ref-ui); font-variant-numeric: tabular-nums; }
  .referral-partner-list { display: grid; gap: 10px; }
  .referral-partner { display: grid; grid-template-columns: 44px minmax(0, 1fr) auto; gap: 12px; align-items: center; padding: 14px; border-radius: 20px; background: #fff; box-shadow: var(--ref-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .referral-avatar { display: grid; width: 44px; height: 44px; place-items: center; border-radius: 14px; color: #e8fff4; background: linear-gradient(145deg, #1fb97a, #054a31); font: 750 15px/1 var(--ref-display); }
  .referral-partner-main { min-width: 0; }
  .referral-partner-name { display: block; overflow: hidden; color: var(--ref-ink); text-overflow: ellipsis; white-space: nowrap; font: 700 13.5px/1.2 var(--ref-ui); }
  .referral-partner-handle { margin-left: 4px; color: var(--ref-subtle); font-weight: 600; }
  .referral-partner-email { display: block; overflow: hidden; margin-top: 4px; color: var(--ref-subtle); text-overflow: ellipsis; white-space: nowrap; font: 500 11.5px/1.2 var(--ref-ui); }
  .referral-partner-meta { text-align: right; }
  .referral-partner-meta strong { display: block; color: var(--ref-ink); font: 700 12px/1.2 var(--ref-ui); white-space: nowrap; }
  .referral-partner-meta span { display: block; margin-top: 4px; color: var(--ref-subtle); font: 500 10.5px/1.2 var(--ref-ui); white-space: nowrap; }
  .referral-empty { padding: 20px; border: 1px solid var(--ref-line); border-radius: 18px; color: var(--ref-muted); background: #fff; box-shadow: var(--ref-shadow-sm); font: 550 13px/1.5 var(--ref-ui); }
  .referral-toast { position: fixed; right: 16px; bottom: 22px; left: 16px; z-index: 50; width: fit-content; max-width: calc(100% - 32px); margin: 0 auto; padding: 11px 14px; border-radius: 13px; color: #fff; background: rgba(10, 31, 23, .94); box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22); opacity: 0; pointer-events: none; transform: translateY(12px); transition: opacity .25s ease, transform .3s ease; font: 650 12px/1 var(--ref-ui); }
  .referral-toast.is-visible { opacity: 1; transform: translateY(0); }
  .referral-main button:focus-visible,.referral-back:focus-visible { outline: 3px solid rgba(20, 168, 109, .4); outline-offset: 3px; }

  @media (min-width: 521px) and (max-height: 940px) {
    body { padding: 16px 0; }
    .referral-phone { min-height: 600px; height: calc(100vh - 32px); }
    .referral-screen { max-height: calc(100vh - 32px); }
  }

  @media (max-width: 520px) {
    body { display: block; padding: 0; background: var(--ref-bg); }
    .container { width: 100%; }
    .referral-phone { min-height: 100vh; overflow: visible; border-radius: 0; box-shadow: none; }
    .referral-screen { max-height: none; overflow: visible; }
    .referral-chrome { padding-top: max(4px, env(safe-area-inset-top)); }
    .referral-nav { min-height: 56px; }
    .referral-main { padding-bottom: calc(28px + env(safe-area-inset-bottom)); }
    .referral-toast { bottom: calc(18px + env(safe-area-inset-bottom)); }
  }

  @media (max-width: 350px) {
    .referral-main { padding-right: 16px; padding-left: 16px; }
    .referral-stats { grid-template-columns: 1fr; }
    .referral-partner { grid-template-columns: 40px minmax(0, 1fr); }
    .referral-avatar { width: 40px; height: 40px; }
    .referral-partner-meta { grid-column: 2; text-align: left; }
    .referral-badge { display: none; }
  }

  @media (prefers-reduced-motion: reduce) {
    .referral-phone *, .referral-phone *::before, .referral-phone *::after { transition: none !important; }
  }
</style>

@php
  $user = auth()->user();
  $link = $user->username ? route('signup', ['ref' => $user->username]) : route('signup');
  $totalEarned = (float) $user->referralEarnings()->sum('amount');
  $referrals = $user->referrals()
      ->withSum('investments as referred_invested_amount', 'amount')
      ->orderByDesc('created_at')
      ->get();
  $invitedCount = $referrals->count();
@endphp

<div class="referral-phone">
  <div class="referral-screen" id="referralScreen">
    <header class="referral-chrome" id="referralChrome">
      <nav class="referral-nav" aria-label="Referral navigation">
        <a class="referral-back" href="{{ route('dashboard') }}" aria-label="Back to dashboard">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
        </a>
        <a class="referral-brand" href="{{ route('dashboard') }}" aria-label="LuLu Philippines dashboard">
          <img src="{{ asset('logo.png') }}" alt="">
          <span>Referrals</span>
        </a>
        <span class="referral-badge"><i aria-hidden="true"></i>Referral</span>
      </nav>
    </header>

    <main class="referral-main">
      <section class="referral-hero" aria-labelledby="referralTitle">
        <div class="referral-eyebrow">Referral program</div>
        <h1 class="referral-title" id="referralTitle">Referrals</h1>
        <p class="referral-subtitle">Invite partners and earn rewards. Receive <strong>5% of qualifying referred investment capital</strong> when they invest.</p>
      </section>

      <section class="referral-overview" aria-label="Total referral earnings">
        <div class="referral-overview-top">
          <div>
            <div class="referral-overview-label">Total earnings</div>
            <div class="referral-overview-value">${{ number_format($totalEarned, 2) }}</div>
            <div class="referral-overview-copy">Referral rewards credited to your account.</div>
          </div>
          <div class="referral-overview-icon" aria-hidden="true">
            <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2"/><path d="M16 3.5a4 4 0 0 1 0 7M19 14a7 7 0 0 1 3 5.7V21"/></svg>
          </div>
        </div>
      </section>

      <section class="referral-stats" aria-label="Referral summary">
        <article class="referral-stat">
          <div class="referral-stat-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2M19 8v6m-3-3h6"/></svg></div>
          <div class="referral-stat-label">Invited partners</div>
          <div class="referral-stat-value">{{ number_format($invitedCount) }}</div>
          <div class="referral-stat-copy">Partners in your referral network</div>
        </article>
        <article class="referral-stat">
          <div class="referral-stat-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5 5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg></div>
          <div class="referral-stat-label">Reward rate</div>
          <div class="referral-stat-value">5%</div>
          <div class="referral-stat-copy">Of qualifying referred capital</div>
        </article>
      </section>

      <section class="referral-panel" aria-labelledby="referralLinkTitle">
        <div class="referral-panel-head">
          <div>
            <h2 id="referralLinkTitle">Your Referral Link</h2>
            <p>Share your personal link with a prospective partner.</p>
          </div>
        </div>
        <div class="referral-linkbox">
          <code id="referralLink">{{ $link }}</code>
          <button class="referral-copy-mini" id="copyReferralMini" type="button" aria-label="Copy referral link">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
          </button>
        </div>
        <div class="referral-actions">
          <button class="referral-copy-cta" id="copyReferral" type="button">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Copy Link
          </button>
          <button class="referral-share" id="shareReferral" type="button" aria-label="Share referral link">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 10.5 6.8-4m-6.8 7 6.8 4"/></svg>
          </button>
        </div>
        <div class="referral-tip">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg>
          <span>Rewards are reflected here after a referred partner makes a qualifying investment.</span>
        </div>
      </section>

      <div class="referral-section-head">
        <h2>Referred Partners</h2>
        <div class="referral-section-amount">${{ number_format($totalEarned, 2) }}</div>
      </div>
      <section class="referral-partner-list" aria-label="Referred partners">
        @forelse ($referrals as $referral)
          @php
            $initials = collect(explode(' ', trim($referral->name ?: $referral->email)))
                ->filter()
                ->take(2)
                ->map(fn ($part) => mb_substr($part, 0, 1))
                ->implode('');
          @endphp
          <article class="referral-partner">
            <div class="referral-avatar" aria-hidden="true">{{ mb_strtoupper($initials) }}</div>
            <div class="referral-partner-main">
              <span class="referral-partner-name">
                {{ $referral->name ?: $referral->email }}
                @if ($referral->username)<span class="referral-partner-handle">({{ $referral->username }})</span>@endif
              </span>
              @if ($referral->name && $referral->email)
                <span class="referral-partner-email">{{ $referral->email }}</span>
              @endif
            </div>
            <div class="referral-partner-meta">
              <strong>Invested: ${{ number_format((float) ($referral->referred_invested_amount ?? 0), 2) }}</strong>
              <span>Joined {{ $referral->created_at->diffForHumans() }}</span>
            </div>
          </article>
        @empty
          <div class="referral-empty">You haven’t invited anyone yet. Share your referral link to get started.</div>
        @endforelse
      </section>
    </main>
  </div>
</div>
<div class="referral-toast" id="referralToast" role="status" aria-live="polite">Referral link copied</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var screen = document.getElementById('referralScreen');
    var chrome = document.getElementById('referralChrome');
    var linkElement = document.getElementById('referralLink');
    var toast = document.getElementById('referralToast');
    var referralLink = linkElement ? linkElement.textContent.trim() : '';
    var toastTimer;

    if (screen && chrome) {
      screen.addEventListener('scroll', function () {
        chrome.classList.toggle('is-scrolled', screen.scrollTop > 4);
      }, { passive: true });
    }

    function showToast(message) {
      toast.textContent = message;
      toast.classList.add('is-visible');
      window.clearTimeout(toastTimer);
      toastTimer = window.setTimeout(function () {
        toast.classList.remove('is-visible');
      }, 1800);
    }

    function fallbackCopy() {
      var field = document.createElement('textarea');
      field.value = referralLink;
      field.setAttribute('readonly', '');
      field.style.position = 'fixed';
      field.style.opacity = '0';
      document.body.appendChild(field);
      field.select();
      field.setSelectionRange(0, field.value.length);
      var copied = document.execCommand('copy');
      field.remove();

      if (copied) {
        showToast('Referral link copied');
      } else {
        window.prompt('Copy your referral link:', referralLink);
      }
    }

    function copyReferralLink() {
      if (!referralLink) return;
      if (!navigator.clipboard || !navigator.clipboard.writeText) {
        fallbackCopy();
        return;
      }

      navigator.clipboard.writeText(referralLink).then(function () {
        showToast('Referral link copied');
      }).catch(fallbackCopy);
    }

    var copyButtons = document.querySelectorAll('#copyReferral, #copyReferralMini');
    copyButtons.forEach(function (button) {
      button.addEventListener('click', copyReferralLink);
    });

    var shareButton = document.getElementById('shareReferral');
    if (shareButton) {
      shareButton.addEventListener('click', function () {
        if (!navigator.share) {
          copyReferralLink();
          return;
        }

        navigator.share({
          title: 'LuLu Philippines Referral Program',
          text: 'Join LuLu Philippines using my referral link:',
          url: referralLink
        }).catch(function (error) {
          if (error.name !== 'AbortError') copyReferralLink();
        });
      });
    }
  });
</script>
@endsection
