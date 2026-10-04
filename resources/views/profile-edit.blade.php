@extends('layouts.app')

@section('content')
@php
  $user = auth()->user();
  $initials = collect(preg_split('/\s+/', trim($user->name)))
      ->filter()
      ->take(2)
      ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
      ->implode('');
@endphp
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500..800&display=swap" rel="stylesheet">
<style>
  :root {
    --profile-bg: #f2f5f3;
    --profile-surface: #fff;
    --profile-ink: #0a1f17;
    --profile-muted: #4b5b54;
    --profile-subtle: #8a9892;
    --profile-line: #e2e9e5;
    --profile-green: #0e8a5a;
    --profile-green-dark: #075a3b;
    --profile-mint: #e6f7ef;
    --profile-danger: #b42318;
    --profile-display: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    --profile-ui: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --profile-shadow-sm: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06);
    --profile-shadow-md: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18);
  }

  html.profile-edit-page,
  html.profile-edit-page body {
    min-height: 100%;
    background: #e7ece9;
  }

  html.profile-edit-page body {
    display: flex;
    align-items: center;
    justify-content: center;
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9;
    color: var(--profile-ink);
    font-family: var(--profile-ui);
    -webkit-font-smoothing: antialiased;
  }

  html.profile-edit-page body .container.profile-edit-container {
    display: flex;
    width: 100%;
    max-width: none;
    justify-content: center;
    margin: 0;
    padding: 0;
  }

  .profile-edit-phone,
  .profile-edit-phone * {
    box-sizing: border-box;
  }

  .profile-edit-phone {
    width: 390px;
    height: 844px;
    margin: 40px 0;
    overflow: hidden;
    border-radius: 48px;
    background: var(--profile-bg);
    box-shadow: 0 0 0 9px #0d1411, 0 0 0 10px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4);
  }

  .profile-edit-screen {
    height: 100%;
    overflow-y: auto;
    overscroll-behavior: contain;
    scrollbar-width: none;
  }

  .profile-edit-screen::-webkit-scrollbar {
    display: none;
  }

  .profile-edit-chrome {
    position: sticky;
    top: 0;
    z-index: 5;
    background: rgba(242, 245, 243, .88);
    -webkit-backdrop-filter: saturate(180%) blur(18px);
    backdrop-filter: saturate(180%) blur(18px);
  }

  .profile-edit-statusbar {
    height: calc(38px + env(safe-area-inset-top));
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: calc(4px + env(safe-area-inset-top)) 26px 0 30px;
  }

  .profile-edit-statusbar time {
    color: var(--profile-ink);
    font-size: 14px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
  }

  .profile-edit-status-icons {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .profile-edit-nav {
    min-height: 58px;
    display: grid;
    grid-template-columns: 40px 1fr 40px;
    align-items: center;
    padding: 0 16px 7px;
  }

  .profile-edit-back {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #fff;
    box-shadow: var(--profile-shadow-sm), inset 0 0 0 1px rgba(10, 31, 23, .05);
    color: var(--profile-ink);
    text-decoration: none;
    transition: transform .2s ease;
  }

  .profile-edit-back:active {
    transform: scale(.92);
  }

  .profile-edit-nav-title {
    text-align: center;
  }

  .profile-edit-nav-title h1 {
    font: 750 18px/1.05 var(--profile-display);
    letter-spacing: -.02em;
  }

  .profile-edit-nav-title p {
    margin-top: 5px;
    color: var(--profile-subtle);
    font-size: 11px;
  }

  .profile-edit-main {
    padding: 10px 20px 32px;
  }

  .profile-edit-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: 22px;
    border-radius: 28px;
    background:
      radial-gradient(120% 90% at 105% -10%, rgba(62, 224, 161, .52), transparent 55%),
      radial-gradient(70% 70% at -10% 110%, rgba(198, 243, 107, .28), transparent 60%),
      radial-gradient(60% 50% at 40% 40%, rgba(20, 168, 109, .35), transparent 70%),
      linear-gradient(160deg, #0b6a47 0%, #054a31 42%, #022418 100%);
    box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(2, 26, 18, .38), inset 0 1px 0 rgba(255, 255, 255, .22);
    color: #fff;
  }

  .profile-edit-hero::before {
    position: absolute;
    z-index: -1;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 .5 0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    content: "";
    opacity: .08;
    pointer-events: none;
  }

  .profile-edit-identity {
    position: relative;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .profile-edit-avatar {
    width: 64px;
    height: 64px;
    display: grid;
    flex: 0 0 64px;
    place-items: center;
    border-radius: 20px;
    background: linear-gradient(180deg, rgba(255, 255, 255, .25), rgba(255, 255, 255, .08));
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .35), inset 0 0 0 1px rgba(255, 255, 255, .12);
    color: #fff;
    font: 800 23px/1 var(--profile-display);
    -webkit-backdrop-filter: blur(8px);
    backdrop-filter: blur(8px);
  }

  .profile-edit-copy {
    min-width: 0;
    flex: 1;
  }

  .profile-edit-copy .eyebrow {
    color: rgba(232, 255, 244, .72);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .profile-edit-copy h2 {
    overflow: hidden;
    margin-top: 7px;
    color: #fff;
    font: 800 20px/1.15 var(--profile-display);
    letter-spacing: -.03em;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .profile-edit-copy p {
    overflow: hidden;
    margin-top: 5px;
    color: rgba(232, 255, 244, .68);
    font-size: 12px;
    line-height: 1.35;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .profile-edit-badge {
    display: inline-flex;
    min-height: 26px;
    align-items: center;
    gap: 7px;
    margin-top: 9px;
    padding: 0 9px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .1);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .12);
    color: #daffe9;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .07em;
    text-transform: uppercase;
  }

  .profile-edit-badge::before {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #3ee0a1;
    content: "";
  }

  .profile-edit-panel {
    margin-top: 18px;
    padding: 20px;
    border-radius: 24px;
    background: var(--profile-surface);
    box-shadow: var(--profile-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04);
  }

  .profile-edit-panel-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 18px;
  }

  .profile-edit-panel-head h2 {
    font: 750 19px/1.15 var(--profile-display);
    letter-spacing: -.025em;
  }

  .profile-edit-panel-head p {
    margin-top: 5px;
    color: var(--profile-muted);
    font-size: 13px;
    line-height: 1.45;
  }

  .profile-edit-tag {
    flex: none;
    display: inline-flex;
    min-height: 26px;
    align-items: center;
    padding: 0 10px;
    border-radius: 99px;
    background: var(--profile-mint);
    box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .14);
    color: var(--profile-green);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
  }

  .profile-edit-field + .profile-edit-field {
    margin-top: 16px;
  }

  .profile-edit-field label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin: 0 2px 8px;
    color: var(--profile-muted);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .profile-edit-field input {
    width: 100%;
    height: 56px;
    padding: 0 16px;
    border: 0;
    border-radius: 16px;
    outline: 0;
    background: #f8faf9;
    box-shadow: inset 0 0 0 1px var(--profile-line);
    color: var(--profile-ink);
    font: 600 15px/1 var(--profile-ui);
    transition: box-shadow .2s ease, background .2s ease;
  }

  .profile-edit-field input:focus {
    background: #fff;
    box-shadow: inset 0 0 0 1.5px #14a86d, 0 0 0 4px rgba(20, 168, 109, .1);
  }

  .profile-edit-hint {
    margin: 7px 2px 0;
    color: var(--profile-subtle);
    font-size: 11px;
    line-height: 1.4;
  }

  .profile-edit-error {
    margin: 7px 2px 0;
    color: var(--profile-danger);
    font-size: 12px;
    line-height: 1.4;
  }

  .profile-edit-notice {
    margin-bottom: 16px;
    padding: 12px 14px;
    border-radius: 14px;
    background: var(--profile-mint);
    color: var(--profile-green-dark);
    font-size: 13px;
    font-weight: 650;
    line-height: 1.4;
  }

  .profile-edit-info {
    margin-top: 18px;
    padding: 14px;
    border-radius: 18px;
    background: linear-gradient(180deg, #f8fbf9, #f3f8f5);
    box-shadow: inset 0 0 0 1px rgba(10, 31, 23, .035);
  }

  .profile-edit-info-row {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .profile-edit-info-row + .profile-edit-info-row {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #eaf0ed;
  }

  .profile-edit-info-icon {
    width: 38px;
    height: 38px;
    display: grid;
    flex: 0 0 38px;
    place-items: center;
    border-radius: 12px;
    background: var(--profile-mint);
    color: var(--profile-green);
  }

  .profile-edit-info-copy {
    min-width: 0;
    flex: 1;
  }

  .profile-edit-info-copy span {
    display: block;
    color: var(--profile-subtle);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .09em;
    text-transform: uppercase;
  }

  .profile-edit-info-copy strong {
    display: block;
    overflow: hidden;
    margin-top: 5px;
    color: var(--profile-ink);
    font-size: 13px;
    font-weight: 650;
    line-height: 1.25;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .profile-edit-security {
    margin-top: 18px;
    padding: 16px;
    border-radius: 20px;
    background: #fff;
    box-shadow: var(--profile-shadow-sm), inset 0 0 0 1px var(--profile-line);
  }

  .profile-edit-security-head {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .profile-edit-security-icon {
    width: 42px;
    height: 42px;
    display: grid;
    flex: 0 0 42px;
    place-items: center;
    border-radius: 14px;
    background: var(--profile-mint);
    color: var(--profile-green);
  }

  .profile-edit-security h3 {
    font-size: 14px;
    font-weight: 700;
    line-height: 1.2;
  }

  .profile-edit-security p {
    margin-top: 4px;
    color: var(--profile-muted);
    font-size: 12px;
    line-height: 1.4;
  }

  .profile-edit-security-link {
    width: 100%;
    min-height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    margin-top: 13px;
    border-radius: 13px;
    background: #f7faf8;
    box-shadow: inset 0 0 0 1px var(--profile-line);
    color: var(--profile-green-dark);
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
  }

  .profile-edit-actions {
    display: grid;
    grid-template-columns: 1fr 1.45fr;
    gap: 10px;
    margin-top: 18px;
  }

  .profile-edit-button {
    min-height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    border: 0;
    border-radius: 17px;
    font: 650 14px/1 var(--profile-ui);
    text-decoration: none;
    transition: transform .2s ease, filter .2s ease;
  }

  .profile-edit-button:active {
    transform: scale(.98);
  }

  .profile-edit-cancel {
    background: #fff;
    box-shadow: var(--profile-shadow-sm), inset 0 0 0 1px var(--profile-line);
    color: var(--profile-muted);
  }

  .profile-edit-submit {
    position: relative;
    overflow: hidden;
    background: linear-gradient(180deg, #11905e 0%, #0a6a45 100%);
    box-shadow: 0 10px 24px -8px rgba(10, 106, 69, .45), inset 0 1px 0 rgba(255, 255, 255, .25);
    color: #fff;
    cursor: pointer;
  }

  .profile-edit-submit:hover {
    filter: brightness(1.05);
  }

  @media (max-width: 520px) {
    html.profile-edit-page,
    html.profile-edit-page body {
      background: var(--profile-bg);
    }

    html.profile-edit-page body {
      display: block;
    }

    html.profile-edit-page body .container.profile-edit-container {
      display: block;
    }

    .profile-edit-phone {
      width: 100%;
      height: auto;
      min-height: 100dvh;
      margin: 0;
      overflow: visible;
      border-radius: 0;
      box-shadow: none;
    }

    .profile-edit-screen {
      height: auto;
      min-height: 100dvh;
      overflow: visible;
    }

    .profile-edit-statusbar {
      padding-top: calc(4px + env(safe-area-inset-top));
    }

    .profile-edit-main {
      padding-bottom: calc(28px + env(safe-area-inset-bottom));
    }
  }

  @media (max-height: 900px) and (min-width: 521px) {
    html.profile-edit-page body {
      align-items: flex-start;
    }

    .profile-edit-phone {
      height: calc(100vh - 40px);
      min-height: 560px;
      margin: 20px 0;
    }
  }

  @media (max-width: 380px) {
    .profile-edit-main {
      padding-right: 16px;
      padding-left: 16px;
    }

    .profile-edit-actions {
      grid-template-columns: 1fr;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .profile-edit-phone *,
    .profile-edit-phone *::before {
      transition: none !important;
    }
  }
</style>

<div class="profile-edit-phone">
  <div class="profile-edit-screen">
    <header class="profile-edit-chrome">
      <div class="profile-edit-statusbar" aria-hidden="true">
        <time id="profileEditLocalTime"></time>
        <div class="profile-edit-status-icons">
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
      <nav class="profile-edit-nav" aria-label="Profile editing navigation">
        <a class="profile-edit-back" href="{{ route('profile') }}" aria-label="Back to profile">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <div class="profile-edit-nav-title">
          <h1>Edit Profile</h1>
          <p>Manage your personal details</p>
        </div>
        <span aria-hidden="true"></span>
      </nav>
    </header>

    <main class="profile-edit-main">
      <section class="profile-edit-hero" aria-label="Account profile">
        <div class="profile-edit-identity">
          <div class="profile-edit-avatar" id="profileEditAvatar">{{ $initials ?: 'U' }}</div>
          <div class="profile-edit-copy">
            <div class="eyebrow">Account profile</div>
            <h2 id="profileEditName">{{ $user->name }}</h2>
            <p>{{ $user->email }}</p>
            <span class="profile-edit-badge">{{ $user->region ?: 'Region not set' }}</span>
          </div>
        </div>
      </section>

      <section class="profile-edit-panel" aria-labelledby="profileEditPanelTitle">
        <div class="profile-edit-panel-head">
          <div>
            <h2 id="profileEditPanelTitle">Profile information</h2>
            <p>Update the information shown on your account.</p>
          </div>
          <span class="profile-edit-tag">Editable</span>
        </div>

        @if (session('status'))
          <div class="profile-edit-notice" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="profile-edit-notice" role="alert" style="background:#fff1f0;color:var(--profile-danger)">
            Please review the highlighted fields and try again.
          </div>
        @endif

        <form id="profileEditForm" action="{{ route('profile.update') }}" method="post">
          @csrf
          <div class="profile-edit-field">
            <label for="profileEditNameInput">Full name</label>
            <input id="profileEditNameInput" name="name" type="text" value="{{ old('name', $user->name) }}" autocomplete="name" maxlength="255" required @if ($errors->has('name')) aria-describedby="profileEditNameError" @endif>
            @error('name')<p class="profile-edit-error" id="profileEditNameError" role="alert">{{ $message }}</p>@enderror
          </div>

          <div class="profile-edit-field">
            <label for="profileEditEmail">Email address</label>
            <input id="profileEditEmail" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" maxlength="255" required aria-describedby="profileEditEmailHint{{ $errors->has('email') ? ' profileEditEmailError' : '' }}">
            <p class="profile-edit-hint" id="profileEditEmailHint">Account alerts and important notices will be sent here.</p>
            @error('email')<p class="profile-edit-error" id="profileEditEmailError" role="alert">{{ $message }}</p>@enderror
          </div>

          <div class="profile-edit-field">
            <label for="profileEditRegion">Region <span style="color:var(--profile-subtle);font-weight:600;letter-spacing:0;text-transform:none">Optional</span></label>
            <input id="profileEditRegion" name="region" type="text" value="{{ old('region', $user->region ?: '') }}" autocomplete="address-level1" maxlength="255" @if ($errors->has('region')) aria-describedby="profileEditRegionError" @endif>
            @error('region')<p class="profile-edit-error" id="profileEditRegionError" role="alert">{{ $message }}</p>@enderror
          </div>

          <div class="profile-edit-info" aria-label="Account details">
            <div class="profile-edit-info-row">
              <span class="profile-edit-info-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
              </span>
              <span class="profile-edit-info-copy">
                <span>Account email</span>
                <strong>{{ $user->email }}</strong>
              </span>
            </div>
            <div class="profile-edit-info-row">
              <span class="profile-edit-info-icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
              </span>
              <span class="profile-edit-info-copy">
                <span>Member since</span>
                <strong>{{ $user->created_at?->format('F Y') ?? 'Date unavailable' }}</strong>
              </span>
            </div>
          </div>

          <section class="profile-edit-security" aria-labelledby="profileEditSecurityTitle">
            <div class="profile-edit-security-head">
              <span class="profile-edit-security-icon" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
              </span>
              <span>
                <h3 id="profileEditSecurityTitle">Security settings</h3>
                <p>Manage your password and account protection separately.</p>
              </span>
            </div>
            <a class="profile-edit-security-link" href="{{ route('profile.password') }}">
              Open security settings
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
          </section>

          <div class="profile-edit-actions">
            <a class="profile-edit-button profile-edit-cancel" href="{{ route('profile') }}">Cancel</a>
            <button class="profile-edit-button profile-edit-submit" type="submit">
              Save changes
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
            </button>
          </div>
        </form>
      </section>
    </main>
  </div>
</div>

<script>
  (function () {
    var time = document.getElementById('profileEditLocalTime');
    if (time) {
      time.textContent = new Intl.DateTimeFormat(undefined, {
        hour: 'numeric',
        minute: '2-digit'
      }).format(new Date());
    }

    var nameInput = document.getElementById('profileEditNameInput');
    var heroName = document.getElementById('profileEditName');
    var avatar = document.getElementById('profileEditAvatar');

    if (nameInput && heroName && avatar) {
      nameInput.addEventListener('input', function () {
        var parts = nameInput.value.trim().split(/\s+/).filter(Boolean);
        var initials = parts.length
          ? (parts[0].charAt(0) + (parts.length > 1 ? parts[parts.length - 1].charAt(0) : '')).toLocaleUpperCase()
          : 'U';

        heroName.textContent = nameInput.value.trim() || 'Your name';
        avatar.textContent = initials;
      });
    }
  })();
</script>
@endsection
