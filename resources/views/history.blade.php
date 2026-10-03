@extends('layouts.app')

@section('content')
<style>
  :root {
    --history-bg: #f2f5f3;
    --history-surface: #fff;
    --history-ink: #0a1f17;
    --history-muted: #4b5b54;
    --history-subtle: #8a9892;
    --history-line: #e2e9e5;
    --history-green: #0e8a5a;
    --history-green-dark: #075a3b;
    --history-mint: #e6f7ef;
    --history-amber: #b26b00;
    --history-amber-bg: #fff7e6;
    --history-red: #b42318;
    --history-red-bg: #fff0ed;
    --history-display: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    --history-ui: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --history-shadow-sm: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06);
    --history-shadow-md: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18);
    --history-shadow-lg: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(4, 30, 20, .35);
  }

  body {
    display: flex;
    min-height: 100vh;
    align-items: flex-start;
    justify-content: center;
    padding: 24px 0;
    color: var(--history-ink);
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9;
    font-family: var(--history-ui);
    -webkit-font-smoothing: antialiased;
  }

  .container { width: 390px; max-width: 100%; margin: 0 auto; padding: 0; }
  .history-phone { width: 100%; min-height: min(844px, calc(100vh - 48px)); overflow: hidden; border-radius: 36px; background: var(--history-bg); box-shadow: 0 0 0 8px #0d1411, 0 0 0 9px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4); }
  .history-screen { min-height: inherit; max-height: calc(100vh - 48px); overflow-y: auto; scrollbar-width: none; overscroll-behavior: contain; }
  .history-screen::-webkit-scrollbar { display: none; }
  .history-chrome { position: sticky; top: 0; z-index: 5; background: rgba(242, 245, 243, .88); backdrop-filter: saturate(180%) blur(18px); -webkit-backdrop-filter: saturate(180%) blur(18px); }
  .history-chrome.is-scrolled { box-shadow: 0 1px 0 rgba(10, 31, 23, .06); }
  .history-nav { display: grid; min-height: 60px; grid-template-columns: 40px minmax(0, 1fr) 40px; align-items: center; gap: 12px; padding: 0 16px; }
  .history-icon-button { display: grid; width: 40px; height: 40px; place-items: center; border: 0; border-radius: 50%; color: var(--history-ink); background: #fff; box-shadow: var(--history-shadow-sm), inset 0 0 0 1px rgba(10, 31, 23, .05); text-decoration: none; transition: transform .2s ease; }
  .history-icon-button:active { transform: scale(.92); }
  .history-brand { display: flex; align-items: center; justify-self: center; gap: 8px; color: var(--history-ink); text-decoration: none; white-space: nowrap; font: 800 15px/1 var(--history-display); }
  .history-brand img { width: 26px; height: 26px; border-radius: 8px; object-fit: contain; }
  .history-main { padding: 8px 20px 32px; }
  .history-hero { padding: 8px 2px 4px; }
  .history-eyebrow { color: var(--history-subtle); font: 650 10.5px/1 var(--history-ui); letter-spacing: .14em; text-transform: uppercase; }
  .history-page-title { margin: 8px 0 0; color: var(--history-ink); font: 800 30px/1.06 var(--history-display); letter-spacing: -.045em; }
  .history-intro { max-width: 330px; margin: 8px 0 0; color: var(--history-muted); font: 450 14px/1.5 var(--history-ui); }
  .history-overview { position: relative; overflow: hidden; margin-top: 18px; padding: 18px; border-radius: 24px; color: #fff; background: radial-gradient(120% 100% at 105% -10%, rgba(62, 224, 161, .5), transparent 55%), radial-gradient(70% 70% at -10% 110%, rgba(198, 243, 107, .18), transparent 60%), linear-gradient(160deg, #0b6a47 0%, #054a31 48%, #022418 100%); box-shadow: var(--history-shadow-lg), inset 0 1px 0 rgba(255, 255, 255, .2); }
  .history-overview::before { position: absolute; inset: 0; background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .06), transparent); content: ""; pointer-events: none; }
  .history-overview-label { position: relative; color: rgba(232, 255, 244, .7); font: 650 10px/1 var(--history-ui); letter-spacing: .14em; text-transform: uppercase; }
  .history-overview-value { position: relative; margin-top: 8px; font: 760 34px/1 var(--history-ui); letter-spacing: -.04em; font-variant-numeric: tabular-nums; }
  .history-overview-copy { position: relative; margin-top: 7px; color: rgba(232, 255, 244, .72); font: 500 11.5px/1.4 var(--history-ui); }
  .history-stats { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-top: 10px; }
  .history-stat { min-width: 0; padding: 15px; border-radius: 18px; background: var(--history-surface); box-shadow: var(--history-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .history-stat-icon { display: grid; width: 34px; height: 34px; place-items: center; margin-bottom: 12px; border-radius: 11px; color: var(--history-green-dark); background: var(--history-mint); }
  .history-stat-label { color: var(--history-subtle); font: 650 9.5px/1.25 var(--history-ui); letter-spacing: .11em; text-transform: uppercase; }
  .history-stat-value { margin-top: 8px; color: var(--history-ink); font: 760 24px/1 var(--history-ui); letter-spacing: -.035em; font-variant-numeric: tabular-nums; }
  .history-stat-copy { margin-top: 7px; color: var(--history-muted); font: 500 11.5px/1.35 var(--history-ui); }
  .history-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin: 22px 2px 10px; }
  .history-toolbar h2 { margin: 0; color: var(--history-ink); font: 760 18px/1.1 var(--history-display); letter-spacing: -.025em; }
  .history-filter-label { display: flex; height: 34px; align-items: center; gap: 7px; padding: 0 11px; border-radius: 11px; color: var(--history-muted); background: #fff; box-shadow: inset 0 0 0 1px var(--history-line), var(--history-shadow-sm); font: 650 11.5px/1 var(--history-ui); white-space: nowrap; }
  .history-list { display: grid; gap: 10px; }
  .history-card { overflow: hidden; border-radius: 20px; background: #fff; box-shadow: var(--history-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); transition: box-shadow .2s ease; }
  .history-card:hover { box-shadow: var(--history-shadow-lg); }
  .history-head { display: grid; width: 100%; min-height: 76px; grid-template-columns: 42px minmax(0, 1fr) 34px; align-items: center; gap: 12px; padding: 15px 15px 15px 16px; border: 0; text-align: left; background: transparent; cursor: pointer; font: inherit; }
  .history-head:hover { background: #f9fbfa; }
  .history-section-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 13px; color: var(--history-green-dark); background: var(--history-mint); box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .1); }
  .history-section-copy { min-width: 0; }
  .history-section-copy strong { display: block; color: var(--history-ink); font: 700 15px/1.2 var(--history-display); letter-spacing: -.02em; }
  .history-section-copy > span { display: block; margin-top: 4px; color: var(--history-subtle); font: 500 11.5px/1.35 var(--history-ui); }
  .history-plus { display: grid; width: 34px; height: 34px; place-items: center; border-radius: 50%; color: var(--history-muted); background: #f0f4f2; transition: transform .3s ease, background .2s ease; font: 500 20px/1 var(--history-ui); }
  .history-card.is-open .history-plus { color: var(--history-green-dark); background: var(--history-mint); transform: rotate(45deg); }
  .history-body { display: grid; grid-template-rows: 0fr; transition: grid-template-rows .36s cubic-bezier(.2, .8, .2, 1); }
  .history-card.is-open .history-body { grid-template-rows: 1fr; }
  .history-inner { min-height: 0; overflow: hidden; }
  .history-entries { padding: 0 16px 14px; }
  .history-entry { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 12px; padding: 13px 0; border-top: 1px solid #eef2f0; }
  .history-entry-name { color: var(--history-ink); font: 650 12.5px/1.25 var(--history-ui); overflow-wrap: anywhere; }
  .history-entry-date { margin-top: 4px; color: var(--history-subtle); font: 500 10.5px/1.2 var(--history-ui); }
  .history-entry-right { text-align: right; }
  .history-entry-amount { color: var(--history-ink); font: 700 12.5px/1.2 var(--history-ui); white-space: nowrap; }
  .history-tag { display: inline-flex; min-height: 21px; align-items: center; margin-top: 5px; padding: 0 7px; border-radius: 999px; font: 700 8.5px/1 var(--history-ui); letter-spacing: .08em; text-transform: uppercase; }
  .history-tag.ok { color: var(--history-green-dark); background: var(--history-mint); }
  .history-tag.pending { color: var(--history-amber); background: var(--history-amber-bg); }
  .history-tag.failed { color: var(--history-red); background: var(--history-red-bg); }
  .history-tag.neutral { color: var(--history-muted); background: #f0f4f2; }
  .history-empty { padding: 12px 0 4px; color: var(--history-subtle); text-align: center; font: 500 11.5px/1.5 var(--history-ui); }
  .history-empty strong { display: block; margin-bottom: 4px; color: var(--history-ink); font-weight: 650; }
  .history-claim { display: inline-flex; min-height: 40px; align-items: center; gap: 8px; margin: 12px 0 0; padding: 0 12px; border: 1px solid rgba(14, 138, 90, .22); border-radius: 12px; color: var(--history-green-dark); background: #fff; box-shadow: var(--history-shadow-sm); cursor: pointer; font: 650 12px/1 var(--history-ui); }
  .history-claim:disabled { opacity: .65; cursor: wait; }
  .history-footer { margin: 18px 8px 0; color: var(--history-subtle); text-align: center; font: 500 11px/1.45 var(--history-ui); }
  .history-main button:focus-visible,.history-back:focus-visible { outline: 3px solid rgba(20, 168, 109, .4); outline-offset: 3px; }

  @media (min-width: 521px) and (max-height: 940px) {
    body { padding: 16px 0; }
    .history-phone { min-height: 600px; height: calc(100vh - 32px); }
    .history-screen { max-height: calc(100vh - 32px); }
  }
  @media (max-width: 520px) {
    body { display: block; padding: 0; background: var(--history-bg); }
    .container { width: 100%; }
    .history-phone { min-height: 100vh; overflow: visible; border-radius: 0; box-shadow: none; }
    .history-screen { max-height: none; overflow: visible; }
    .history-chrome { padding-top: max(4px, env(safe-area-inset-top)); }
    .history-nav { min-height: 56px; }
    .history-main { padding-bottom: calc(28px + env(safe-area-inset-bottom)); }
  }
  @media (max-width: 350px) {
    .history-main { padding-right: 16px; padding-left: 16px; }
    .history-section-copy > span { font-size: 10.5px; }
    .history-head { grid-template-columns: 40px minmax(0, 1fr) 32px; gap: 10px; }
    .history-section-icon { width: 40px; height: 40px; }
    .history-filter-label { padding: 0 8px; font-size: 10px; }
  }
  @media (prefers-reduced-motion: reduce) {
    .history-phone *, .history-phone *::before, .history-phone *::after { transition: none !important; }
  }
</style>

@php
  $user = Auth::user();
  $rewards = $withdrawals->filter(fn ($withdrawal) => ($withdrawal->bank_name ?? '') === 'Welcome Bonus' || ($withdrawal->account_number ?? '') === 'signup-bonus');
  $signupBonusClaimed = ! empty($user->signup_bonus_claimed_at);

  $historyTagClass = static function ($status): string {
      return match (strtolower((string) $status)) {
          'approved', 'completed', 'credited' => 'ok',
          'pending', 'processing' => 'pending',
          'rejected', 'failed', 'cancelled' => 'failed',
          default => 'neutral',
      };
  };
@endphp

<div class="history-phone">
  <div class="history-screen" id="historyScreen">
    <header class="history-chrome" id="historyChrome">
      <nav class="history-nav" aria-label="Account history navigation">
        <a class="history-icon-button history-back" href="{{ route('dashboard') }}" aria-label="Back to dashboard">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
        </a>
        <a class="history-brand" href="{{ route('dashboard') }}" aria-label="LuLu Philippines dashboard">
          <img src="{{ asset('logo.png') }}" alt="">
          <span>History</span>
        </a>
        <span class="history-icon-button" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
        </span>
      </nav>
    </header>

    <main class="history-main">
      <section class="history-hero" aria-labelledby="historyTitle">
        <div class="history-eyebrow">Account activity</div>
        <h1 class="history-page-title" id="historyTitle">Account History</h1>
        <p class="history-intro">Review investments, withdrawals, daily interest, and rewards in one organized view.</p>
      </section>

      <section class="history-overview" aria-label="Interest earned to date">
        <div class="history-overview-label">Interest earned to date</div>
        <div class="history-overview-value">${{ number_format($dailyInterest, 2) }}</div>
        <div class="history-overview-copy">Total interest credited from approved investments.</div>
      </section>

      <section class="history-stats" aria-label="Activity summary">
        <article class="history-stat">
          <div class="history-stat-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg></div>
          <div class="history-stat-label">Investments</div>
          <div class="history-stat-value">{{ number_format($investments->count()) }}</div>
          <div class="history-stat-copy">Investment records on your account</div>
        </article>
        <article class="history-stat">
          <div class="history-stat-icon" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5"/><path d="M5 21h14"/></svg></div>
          <div class="history-stat-label">Withdrawals</div>
          <div class="history-stat-value">{{ number_format($withdrawals->count()) }}</div>
          <div class="history-stat-copy">Withdrawal requests submitted</div>
        </article>
      </section>

      <div class="history-toolbar">
        <h2>History details</h2>
        <span class="history-filter-label">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
          All activity
        </span>
      </div>

      <section class="history-list" aria-label="Transaction history">
        <article class="history-card is-open">
          <button class="history-head" type="button" aria-expanded="true" aria-controls="history-investments">
            <span class="history-section-icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg></span>
            <span class="history-section-copy"><strong>Investment History</strong><span>Amounts, dates, and approval status.</span></span>
            <span class="history-plus" aria-hidden="true">+</span>
          </button>
          <div class="history-body" id="history-investments"><div class="history-inner"><div class="history-entries">
            @forelse ($investments as $investment)
              @php
                $statusLabel = ucfirst(str_replace('_', ' ', (string) $investment->status));
                $statusClass = $historyTagClass($investment->status);
              @endphp
              <div class="history-entry">
                <div><div class="history-entry-name">{{ $investment->package_name }}</div><div class="history-entry-date">{{ $investment->created_at->format('F j, Y') }}</div></div>
                <div class="history-entry-right"><div class="history-entry-amount">${{ number_format((float) $investment->amount, 2) }}</div><span class="history-tag {{ $statusClass }}">{{ $statusLabel }}</span></div>
              </div>
            @empty
              <div class="history-empty"><strong>No investment transactions yet.</strong>Invest in a package to begin earning daily interest and see it listed here.</div>
            @endforelse
          </div></div></div>
        </article>

        <article class="history-card">
          <button class="history-head" type="button" aria-expanded="false" aria-controls="history-withdrawals">
            <span class="history-section-icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5"/><path d="M5 21h14"/></svg></span>
            <span class="history-section-copy"><strong>Withdrawal History</strong><span>A record of withdrawal activity.</span></span>
            <span class="history-plus" aria-hidden="true">+</span>
          </button>
          <div class="history-body" id="history-withdrawals"><div class="history-inner"><div class="history-entries">
            @forelse ($withdrawals as $withdrawal)
              @php
                $isWelcomeBonus = $withdrawal->bank_name === 'Welcome Bonus' || $withdrawal->account_number === 'signup-bonus';
                $withdrawalName = $isWelcomeBonus
                    ? '$5 Sign Up Bonus'
                    : ucwords(str_replace('_', ' ', (string) $withdrawal->payment_method)).' withdrawal';
                $withdrawalStatus = ucfirst(str_replace('_', ' ', (string) $withdrawal->status));
              @endphp
              <div class="history-entry">
                <div><div class="history-entry-name">{{ $withdrawalName }}</div>@if ($isWelcomeBonus)<div class="history-entry-date">Welcome reward</div>@else<div class="history-entry-date">{{ $withdrawal->created_at->format('F j, Y') }}</div>@endif</div>
                <div class="history-entry-right"><div class="history-entry-amount">${{ number_format((float) $withdrawal->amount, 2) }}</div><span class="history-tag {{ $historyTagClass($withdrawal->status) }}">{{ $withdrawalStatus }}</span></div>
              </div>
            @empty
              <div class="history-empty"><strong>No withdrawals yet.</strong>Request a withdrawal and the transaction will appear here once submitted.</div>
            @endforelse
          </div></div></div>
        </article>

        <article class="history-card">
          <button class="history-head" type="button" aria-expanded="false" aria-controls="history-interest">
            <span class="history-section-icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V9M10 19V5M16 19v-7M22 19V2"/></svg></span>
            <span class="history-section-copy"><strong>Daily Interest History</strong><span>Daily credits from approved investments.</span></span>
            <span class="history-plus" aria-hidden="true">+</span>
          </button>
          <div class="history-body" id="history-interest"><div class="history-inner"><div class="history-entries">
            @forelse ($dailyInterestEntries as $entry)
              <div class="history-entry">
                <div><div class="history-entry-name">{{ $entry['investment']->package_name }} interest credit</div><div class="history-entry-date">{{ $entry['date']->format('F j, Y') }}</div></div>
                <div class="history-entry-right"><div class="history-entry-amount">+${{ number_format((float) $entry['amount'], 2) }}</div><span class="history-tag ok">Credited</span></div>
              </div>
            @empty
              <div class="history-empty"><strong>No daily interest records yet.</strong>Interest starts accruing the day after an investment is approved.</div>
            @endforelse
          </div></div></div>
        </article>

        <article class="history-card">
          <button class="history-head" type="button" aria-expanded="false" aria-controls="history-rewards">
            <span class="history-section-icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12v8H4v-8M2 7h20v5H2zM12 7v13M12 7H7.5A2.5 2.5 0 1 1 10 4.5C10 7 12 7 12 7Zm0 0h4.5A2.5 2.5 0 1 0 14 4.5C14 7 12 7 12 7Z"/></svg></span>
            <span class="history-section-copy"><strong>Rewards History</strong><span>Signup bonuses and other rewards.</span></span>
            <span class="history-plus" aria-hidden="true">+</span>
          </button>
          <div class="history-body" id="history-rewards"><div class="history-inner"><div class="history-entries">
            @if (! $signupBonusClaimed)
              <button id="claim-signup-bonus" class="history-claim" type="button">Claim $5 Sign Up Bonus</button>
            @endif
            @forelse ($rewards as $reward)
              <div class="history-entry">
                <div><div class="history-entry-name">{{ $reward->bank_name === 'Welcome Bonus' ? 'Signup Bonus' : ($reward->account_holder ?: 'Account reward') }}</div><div class="history-entry-date">{{ $reward->created_at->format('F j, Y') }}</div></div>
                <div class="history-entry-right"><div class="history-entry-amount">+${{ number_format((float) $reward->amount, 2) }}</div><span class="history-tag {{ $historyTagClass($reward->status) }}">{{ ucfirst(str_replace('_', ' ', (string) $reward->status)) }}</span></div>
              </div>
            @empty
              <div class="history-empty" id="rewards-empty"><strong>No rewards yet.</strong>Any signup bonus or rewards will appear here.</div>
            @endforelse
          </div></div></div>
        </article>
      </section>
      <p class="history-footer">Your account activity is shown here from your transaction records.</p>
    </main>
  </div>
</div>

<template id="reward-entry-template">
  <div class="history-entry">
    <div><div class="history-entry-name">Signup Bonus</div><div class="history-entry-date" data-reward-date></div></div>
    <div class="history-entry-right"><div class="history-entry-amount">+$5.00</div><span class="history-tag ok">Approved</span></div>
  </div>
</template>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var screen = document.getElementById('historyScreen');
    var chrome = document.getElementById('historyChrome');

    if (screen && chrome) {
      screen.addEventListener('scroll', function () {
        chrome.classList.toggle('is-scrolled', screen.scrollTop > 4);
      }, { passive: true });
    }

    document.querySelectorAll('.history-head').forEach(function (button) {
      button.addEventListener('click', function () {
        var card = button.closest('.history-card');
        if (!card) return;
        var isOpen = card.classList.toggle('is-open');
        button.setAttribute('aria-expanded', String(isOpen));
      });
    });

    var claimButton = document.getElementById('claim-signup-bonus');
    if (!claimButton) return;

    claimButton.addEventListener('click', function () {
      if (!window.confirm('Claim the $5 signup bonus?')) return;
      claimButton.disabled = true;
      claimButton.textContent = 'Claiming...';

      fetch(@json(route('rewards.claim-signup-bonus')), {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Accept': 'text/html',
          'X-CSRF-TOKEN': @json(csrf_token()),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams()
      }).then(function (response) {
        if (!response.ok) throw new Error('Signup reward claim failed with status ' + response.status);
        return response.text();
      }).then(function () {
        var template = document.getElementById('reward-entry-template');
        var entries = document.querySelector('#history-rewards .history-entries');
        var empty = document.getElementById('rewards-empty');
        if (!template || !entries) throw new Error('Reward history could not be updated.');

        var reward = template.content.cloneNode(true);
        reward.querySelector('[data-reward-date]').textContent = new Date().toLocaleDateString(undefined, {
          month: 'long',
          day: 'numeric',
          year: 'numeric'
        });
        entries.appendChild(reward);
        if (empty) empty.remove();
        claimButton.remove();
      }).catch(function (error) {
        console.error(error);
        claimButton.disabled = false;
        claimButton.textContent = 'Claim $5 Sign Up Bonus';
        window.alert('Unable to claim signup bonus right now.');
      });
    });
  });
</script>
@endsection
