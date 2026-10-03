@extends('layouts.app')

@section('content')
<style>
  :root {
    --account-bg: #f2f5f3;
    --account-surface: #fff;
    --account-ink: #0a1f17;
    --account-muted: #4b5b54;
    --account-subtle: #8a9892;
    --account-line: #e2e9e5;
    --account-green: #0e8a5a;
    --account-green-dark: #075a3b;
    --account-mint: #e6f7ef;
    --account-danger: #b42318;
    --account-display: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    --account-ui: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --account-shadow-sm: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06);
    --account-shadow-md: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18);
  }

  body {
    display: flex;
    min-height: 100vh;
    align-items: flex-start;
    justify-content: center;
    padding: 24px 0;
    color: var(--account-ink);
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9;
    font-family: var(--account-ui);
    -webkit-font-smoothing: antialiased;
  }

  .container { width: 390px; max-width: 100%; margin: 0 auto; padding: 0; }
  .account-phone { width: 100%; min-height: min(844px, calc(100vh - 48px)); overflow: hidden; border-radius: 36px; background: var(--account-bg); box-shadow: 0 0 0 8px #0d1411, 0 0 0 9px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4); }
  .account-screen { min-height: inherit; max-height: calc(100vh - 48px); overflow-y: auto; scrollbar-width: none; overscroll-behavior: contain; }
  .account-screen::-webkit-scrollbar { display: none; }
  .account-chrome { position: sticky; top: 0; z-index: 5; background: rgba(242, 245, 243, .88); backdrop-filter: saturate(180%) blur(18px); -webkit-backdrop-filter: saturate(180%) blur(18px); }
  .account-chrome.is-scrolled { box-shadow: 0 1px 0 rgba(10, 31, 23, .06); }
  .account-nav { display: grid; min-height: 60px; grid-template-columns: 40px minmax(0, 1fr) 40px; align-items: center; gap: 12px; padding: 0 16px; }
  .account-icon-button { display: grid; width: 40px; height: 40px; place-items: center; border: 0; border-radius: 50%; color: var(--account-ink); background: #fff; box-shadow: var(--account-shadow-sm), inset 0 0 0 1px rgba(10, 31, 23, .05); text-decoration: none; transition: transform .2s ease; }
  .account-icon-button:active { transform: scale(.92); }
  .account-brand { display: flex; align-items: center; justify-self: center; gap: 8px; color: var(--account-ink); text-decoration: none; white-space: nowrap; font: 800 15px/1 var(--account-display); }
  .account-brand img { width: 26px; height: 26px; border-radius: 8px; object-fit: contain; }
  .account-main { padding: 8px 20px 32px; }
  .account-hero { padding: 8px 2px 4px; }
  .account-eyebrow { color: var(--account-subtle); font: 650 10.5px/1 var(--account-ui); letter-spacing: .14em; text-transform: uppercase; }
  .account-page-title { margin: 8px 0 0; color: var(--account-ink); font: 800 30px/1.06 var(--account-display); letter-spacing: -.045em; }
  .account-intro { max-width: 320px; margin: 8px 0 0; color: var(--account-muted); font: 450 14px/1.5 var(--account-ui); }
  .account-profile-card { position: relative; overflow: hidden; margin-top: 18px; padding: 18px; border-radius: 24px; background: var(--account-surface); box-shadow: var(--account-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .account-profile-card::before { position: absolute; top: -80px; right: -70px; width: 180px; height: 180px; border-radius: 50%; background: radial-gradient(circle, rgba(62, 224, 161, .18), rgba(62, 224, 161, 0) 70%); content: ""; pointer-events: none; }
  .account-profile-main { position: relative; display: flex; align-items: center; gap: 14px; }
  .account-avatar { position: relative; display: grid; width: 64px; height: 64px; flex: 0 0 64px; place-items: center; border-radius: 20px; color: #fff; background: linear-gradient(145deg, #0a6a45 0%, #d83d59 100%); box-shadow: 0 12px 24px -14px rgba(7, 90, 59, .65), inset 0 1px 0 rgba(255, 255, 255, .28); font: 800 24px/1 var(--account-display); }
  .account-avatar::after { position: absolute; right: -3px; bottom: -3px; width: 17px; height: 17px; border: 3px solid #fff; border-radius: 50%; background: #16a76a; box-shadow: 0 0 0 1px rgba(10, 31, 23, .06); content: ""; }
  .account-identity { min-width: 0; flex: 1; }
  .account-identity h2 { overflow: hidden; margin: 0; color: var(--account-ink); text-overflow: ellipsis; white-space: nowrap; font: 780 20px/1.15 var(--account-display); letter-spacing: -.03em; }
  .account-role { margin-top: 5px; color: var(--account-muted); font: 520 13px/1.3 var(--account-ui); }
  .account-email { overflow: hidden; margin-top: 3px; color: var(--account-subtle); text-overflow: ellipsis; white-space: nowrap; font: 520 13px/1.3 var(--account-ui); }
  .account-status { display: inline-flex; height: 28px; align-items: center; gap: 7px; margin-top: 10px; padding: 0 11px; border-radius: 999px; color: var(--account-green-dark); background: var(--account-mint); box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .13); font: 650 11.5px/1 var(--account-ui); }
  .account-status i { width: 8px; height: 8px; border-radius: 50%; background: #16a76a; box-shadow: 0 0 0 4px rgba(22, 167, 106, .12); }
  .account-profile-actions { position: relative; display: flex; flex-wrap: wrap; gap: 8px; margin-top: 16px; }
  .account-button { display: inline-flex; min-height: 42px; align-items: center; justify-content: center; gap: 8px; padding: 0 15px; border: 0; border-radius: 13px; cursor: pointer; font: 650 13px/1 var(--account-ui); transition: transform .2s ease, box-shadow .2s ease, background .2s ease; }
  .account-button:active { transform: scale(.97); }
  .account-button-primary { color: #fff; background: linear-gradient(180deg, #11905e, #0a6a45); box-shadow: 0 8px 18px -10px rgba(10, 106, 69, .7), inset 0 1px 0 rgba(255, 255, 255, .25); text-decoration: none; }
  .account-button-ghost { color: var(--account-green-dark); background: #fff; box-shadow: inset 0 0 0 1px var(--account-line), var(--account-shadow-sm); }
  .account-section { margin-top: 16px; padding: 18px; border-radius: 24px; background: var(--account-surface); box-shadow: var(--account-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04); }
  .account-section-head { display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
  .account-section h3 { margin: 0; color: var(--account-ink); font: 760 16px/1.2 var(--account-display); letter-spacing: -.025em; }
  .account-section-badge { flex: none; padding: 7px 9px; border-radius: 999px; color: var(--account-green-dark); background: var(--account-mint); font: 700 9.5px/1 var(--account-ui); letter-spacing: .12em; text-transform: uppercase; }
  .account-rows { overflow: hidden; }
  .account-row { display: flex; min-height: 52px; align-items: center; justify-content: space-between; gap: 18px; padding: 12px 0; }
  .account-row + .account-row { border-top: 1px solid #eef2f0; }
  .account-row-label { color: var(--account-muted); font: 500 13.5px/1.3 var(--account-ui); }
  .account-row-value { min-width: 0; overflow: hidden; color: var(--account-ink); text-align: right; text-overflow: ellipsis; white-space: nowrap; font: 650 13.5px/1.3 var(--account-ui); }
  .account-row-value.muted { color: var(--account-subtle); }
  .account-row-value.mono { letter-spacing: .16em; }
  .account-actions { overflow: hidden; margin-top: 2px; }
  .account-action { display: grid; width: 100%; min-height: 62px; grid-template-columns: 42px minmax(0, 1fr) 20px; align-items: center; gap: 12px; border: 0; color: var(--account-ink); text-align: left; text-decoration: none; background: transparent; cursor: pointer; font: inherit; transition: background .2s ease; }
  .account-action + .account-action { border-top: 1px solid #eef2f0; }
  .account-action:hover { background: #fafcfb; }
  .account-action:active { background: #f4f8f6; }
  .account-action-icon { display: grid; width: 40px; height: 40px; place-items: center; border-radius: 13px; color: var(--account-green-dark); background: var(--account-mint); box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .1); }
  .account-action-text strong { display: block; font: 650 14px/1.2 var(--account-ui); }
  .account-action-text span { display: block; margin-top: 4px; color: var(--account-subtle); font: 500 11.5px/1.3 var(--account-ui); }
  .account-chevron { color: #99a49f; }
  .account-logout { display: flex; width: 100%; min-height: 48px; align-items: center; justify-content: center; gap: 8px; margin-top: 16px; border: 0; border-radius: 15px; color: var(--account-danger); background: #fff; box-shadow: inset 0 0 0 1px #f1d4d6, var(--account-shadow-sm); cursor: pointer; font: 650 14px/1 var(--account-ui); }
  .account-footer { margin: 18px 8px 0; color: var(--account-subtle); text-align: center; font: 500 11px/1.45 var(--account-ui); }
  .account-toast { position: fixed; right: 16px; bottom: 22px; left: 16px; z-index: 50; width: fit-content; max-width: calc(100% - 32px); margin: 0 auto; padding: 12px 15px; border-radius: 14px; color: #fff; background: rgba(10, 31, 23, .94); box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22); opacity: 0; pointer-events: none; transform: translateY(12px); transition: opacity .25s ease, transform .3s ease; font: 600 12.5px/1 var(--account-ui); }
  .account-toast.is-visible { opacity: 1; transform: translateY(0); }
  .account-main button:focus-visible,.account-back:focus-visible,.account-action:focus-visible { outline: 3px solid rgba(20, 168, 109, .4); outline-offset: 3px; }

  @media (min-width: 521px) and (max-height: 940px) {
    body { padding: 16px 0; }
    .account-phone { min-height: 600px; height: calc(100vh - 32px); }
    .account-screen { max-height: calc(100vh - 32px); }
  }
  @media (max-width: 520px) {
    body { display: block; padding: 0; background: var(--account-bg); }
    .container { width: 100%; }
    .account-phone { min-height: 100vh; overflow: visible; border-radius: 0; box-shadow: none; }
    .account-screen { max-height: none; overflow: visible; }
    .account-chrome { padding-top: max(4px, env(safe-area-inset-top)); }
    .account-nav { min-height: 56px; }
    .account-main { padding-bottom: calc(28px + env(safe-area-inset-bottom)); }
    .account-toast { bottom: calc(18px + env(safe-area-inset-bottom)); }
  }
  @media (max-width: 360px) {
    .account-main { padding-right: 16px; padding-left: 16px; }
    .account-identity h2 { font-size: 18px; }
    .account-page-title { font-size: 28px; }
    .account-row { gap: 10px; }
    .account-row-label,.account-row-value { font-size: 12.5px; }
  }
  @media (prefers-reduced-motion: reduce) {
    .account-phone *, .account-phone *::before, .account-phone *::after { transition: none !important; }
  }
</style>

@php
  $user = auth()->user();
  $displayName = trim((string) $user->name) ?: (string) $user->email;
  $avatarInitial = mb_strtoupper(mb_substr($displayName, 0, 1));
  $username = $user->username ?: $user->name;
  $isOnline = $user->isOnline();
@endphp

<div class="account-phone">
  <div class="account-screen" id="accountScreen">
    <header class="account-chrome" id="accountChrome">
      <nav class="account-nav" aria-label="Account navigation">
        <a class="account-icon-button account-back" href="{{ route('dashboard') }}" aria-label="Back to dashboard">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
        </a>
        <a class="account-brand" href="{{ route('dashboard') }}" aria-label="LuLu Philippines dashboard">
          <img src="{{ asset('logo.png') }}" alt="">
          <span>Account</span>
        </a>
        <a class="account-icon-button" href="{{ route('profile.notifications') }}" aria-label="Notification settings">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
        </a>
      </nav>
    </header>

    <main class="account-main">
      <section class="account-hero" aria-labelledby="accountTitle">
        <div class="account-eyebrow">Account settings</div>
        <h1 class="account-page-title" id="accountTitle">My Account</h1>
        <p class="account-intro">Manage your profile, security preferences, and account access in one place.</p>
      </section>

      <section class="account-profile-card" aria-label="Your profile">
        <div class="account-profile-main">
          <div class="account-avatar" aria-hidden="true">{{ $avatarInitial }}</div>
          <div class="account-identity">
            <h2>{{ $displayName }}</h2>
            <div class="account-role">{{ $user->account_type ?? 'Corporate Account' }}</div>
            <div class="account-email">{{ $user->email }}</div>
            <span class="account-status"><i aria-hidden="true"></i>{{ $isOnline ? 'Online' : 'Offline' }}</span>
          </div>
        </div>
        <div class="account-profile-actions">
          <a class="account-button account-button-primary" href="{{ route('profile.edit') }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
            Edit profile
          </a>
          <button class="account-button account-button-ghost" id="copyUsername" type="button" data-username="{{ $username }}">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M15 9V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h3"/></svg>
            Copy username
          </button>
        </div>
      </section>

      <section class="account-section">
        <div class="account-section-head"><h3>Account Information</h3><span class="account-section-badge">Profile</span></div>
        <div class="account-rows">
          <div class="account-row"><span class="account-row-label">Username</span><span class="account-row-value">{{ $username }}</span></div>
          <div class="account-row"><span class="account-row-label">Referral</span><span class="account-row-value {{ $user->referred_by ? '' : 'muted' }}">{{ $user->referred_by ? ($user->referrer?->username ?? 'Linked') : 'Not set' }}</span></div>
          <div class="account-row"><span class="account-row-label">Region</span><span class="account-row-value {{ $user->region ? '' : 'muted' }}">{{ $user->region ?: 'Not set' }}</span></div>
        </div>
      </section>

      <section class="account-section">
        <div class="account-section-head"><h3>Security</h3><span class="account-section-badge">Protected</span></div>
        <div class="account-rows">
          <div class="account-row"><span class="account-row-label">Password</span><span class="account-row-value mono">••••••••</span></div>
          <div class="account-row"><span class="account-row-label">Last active</span><span class="account-row-value">{{ $user->last_seen_at?->diffForHumans() ?? '—' }}</span></div>
          <div class="account-row"><span class="account-row-label">Member since</span><span class="account-row-value">{{ $user->created_at?->format('F Y') ?? '—' }}</span></div>
        </div>
      </section>

      <section class="account-section">
        <div class="account-section-head"><h3>Preferences</h3><span class="account-section-badge">Personal</span></div>
        <div class="account-rows">
          <div class="account-row"><span class="account-row-label">Notifications</span><span class="account-row-value">Enabled</span></div>
          <div class="account-row"><span class="account-row-label">Language</span><span class="account-row-value">English</span></div>
          <div class="account-row"><span class="account-row-label">Theme</span><span class="account-row-value">System</span></div>
        </div>
      </section>

      <section class="account-section">
        <div class="account-section-head"><h3>Quick Actions</h3></div>
        <div class="account-actions">
          <a class="account-action" href="{{ route('profile.edit') }}">
            <span class="account-action-icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg></span>
            <span class="account-action-text"><strong>Edit Profile</strong><span>Update account details</span></span>
            <svg class="account-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
          </a>
          <a class="account-action" href="{{ route('profile.password') }}">
            <span class="account-action-icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></span>
            <span class="account-action-text"><strong>Change Password</strong><span>Keep your account secure</span></span>
            <svg class="account-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
          </a>
          <a class="account-action" href="{{ route('profile.notifications') }}">
            <span class="account-action-icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg></span>
            <span class="account-action-text"><strong>Notification Settings</strong><span>Choose what you want to receive</span></span>
            <svg class="account-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
          </a>
        </div>
      </section>

      <form action="{{ route('logout') }}" method="post">
        @csrf
        <button class="account-logout" type="submit">
          <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>
          Log out
        </button>
      </form>
      <p class="account-footer">Account activity and security information are shown for the signed-in session.</p>
    </main>
  </div>
</div>
<div class="account-toast" id="accountToast" role="status" aria-live="polite"></div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var screen = document.getElementById('accountScreen');
    var chrome = document.getElementById('accountChrome');
    var copyButton = document.getElementById('copyUsername');
    var toast = document.getElementById('accountToast');
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

    if (copyButton) {
      copyButton.addEventListener('click', function () {
        var username = copyButton.getAttribute('data-username') || '';
        if (!username) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(username).then(function () {
            showToast('Username copied');
          }).catch(function (error) {
            console.error(error);
            window.prompt('Copy your username:', username);
          });
          return;
        }

        window.prompt('Copy your username:', username);
      });
    }
  });
</script>
@endsection
