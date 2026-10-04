@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500..800&display=swap" rel="stylesheet">
<style>
  :root {
    --notify-bg: #f2f5f3;
    --notify-surface: #fff;
    --notify-ink: #0a1f17;
    --notify-muted: #4b5b54;
    --notify-subtle: #8a9892;
    --notify-line: #e2e9e5;
    --notify-green: #0e8a5a;
    --notify-green-dark: #075a3b;
    --notify-mint: #e6f7ef;
    --notify-display: "Plus Jakarta Sans", Inter, system-ui, sans-serif;
    --notify-ui: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    --notify-shadow-sm: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06);
    --notify-shadow-md: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18);
  }

  html.profile-notifications-page,
  html.profile-notifications-page body {
    min-height: 100%;
    background: #e7ece9;
  }

  html.profile-notifications-page body {
    display: flex;
    align-items: center;
    justify-content: center;
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9;
    color: var(--notify-ink);
    font-family: var(--notify-ui);
    -webkit-font-smoothing: antialiased;
  }

  html.profile-notifications-page body .container.profile-notifications-container {
    display: flex;
    width: 100%;
    max-width: none;
    justify-content: center;
    margin: 0;
    padding: 0;
  }

  .notification-phone,
  .notification-phone * {
    box-sizing: border-box;
  }

  .notification-phone {
    width: 390px;
    height: 844px;
    margin: 40px 0;
    overflow: hidden;
    border-radius: 48px;
    background: var(--notify-bg);
    box-shadow: 0 0 0 9px #0d1411, 0 0 0 10px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4);
  }

  .notification-screen {
    height: 100%;
    overflow-y: auto;
    overscroll-behavior: contain;
    scrollbar-width: none;
  }

  .notification-screen::-webkit-scrollbar {
    display: none;
  }

  .notification-chrome {
    position: sticky;
    top: 0;
    z-index: 5;
    background: rgba(242, 245, 243, .88);
    -webkit-backdrop-filter: saturate(180%) blur(18px);
    backdrop-filter: saturate(180%) blur(18px);
  }

  .notification-statusbar {
    height: calc(38px + env(safe-area-inset-top));
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: calc(4px + env(safe-area-inset-top)) 26px 0 30px;
  }

  .notification-statusbar time {
    font-size: 14px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
  }

  .notification-status-icons {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .notification-nav {
    min-height: 58px;
    display: grid;
    grid-template-columns: 40px 1fr 40px;
    align-items: center;
    padding: 0 16px 7px;
  }

  .notification-back {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #fff;
    box-shadow: var(--notify-shadow-sm), inset 0 0 0 1px rgba(10, 31, 23, .05);
    color: var(--notify-ink);
    text-decoration: none;
    transition: transform .2s ease;
  }

  .notification-back:active {
    transform: scale(.92);
  }

  .notification-nav-title {
    text-align: center;
  }

  .notification-nav-title h1 {
    font: 750 18px/1.05 var(--notify-display);
    letter-spacing: -.02em;
  }

  .notification-nav-title p {
    margin-top: 5px;
    color: var(--notify-subtle);
    font-size: 11px;
  }

  .notification-main {
    padding: 10px 20px 32px;
  }

  .notification-hero {
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

  .notification-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .notification-eyebrow {
    color: rgba(232, 255, 244, .72);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .notification-hero-icon {
    width: 40px;
    height: 40px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    background: rgba(255, 255, 255, .13);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .22);
    color: #e8fff4;
  }

  .notification-hero h2 {
    margin-top: 13px;
    font: 800 23px/1.12 var(--notify-display);
    letter-spacing: -.035em;
  }

  .notification-hero p {
    max-width: 265px;
    margin-top: 7px;
    color: rgba(232, 255, 244, .7);
    font-size: 12px;
    line-height: 1.45;
  }

  .notification-panel {
    margin-top: 18px;
    padding: 20px;
    border-radius: 24px;
    background: var(--notify-surface);
    box-shadow: var(--notify-shadow-md), inset 0 0 0 1px rgba(10, 31, 23, .04);
  }

  .notification-panel-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 14px;
  }

  .notification-panel-head h2 {
    font: 750 19px/1.15 var(--notify-display);
    letter-spacing: -.025em;
  }

  .notification-panel-head p {
    margin-top: 5px;
    color: var(--notify-muted);
    font-size: 13px;
    line-height: 1.45;
  }

  .notification-tag {
    flex: none;
    display: inline-flex;
    min-height: 26px;
    align-items: center;
    padding: 0 10px;
    border-radius: 99px;
    background: var(--notify-mint);
    box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .14);
    color: var(--notify-green);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
  }

  .notification-success {
    margin-bottom: 14px;
    padding: 12px 14px;
    border-radius: 14px;
    background: var(--notify-mint);
    color: var(--notify-green-dark);
    font-size: 13px;
    font-weight: 650;
    line-height: 1.4;
  }

  .notification-error {
    margin-bottom: 14px;
    padding: 12px 14px;
    border-radius: 14px;
    background: #fff1f0;
    color: #b42318;
    font-size: 12px;
    line-height: 1.4;
  }

  .notification-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 0;
    border-top: 1px solid #eef2f0;
    cursor: pointer;
  }

  .notification-option:first-of-type {
    border-top: 0;
  }

  .notification-option-icon {
    width: 40px;
    height: 40px;
    display: grid;
    flex: 0 0 40px;
    place-items: center;
    border-radius: 13px;
    background: var(--notify-mint);
    color: var(--notify-green);
  }

  .notification-option-copy {
    min-width: 0;
    flex: 1;
  }

  .notification-option-copy strong {
    display: block;
    color: var(--notify-ink);
    font-size: 13px;
    font-weight: 700;
    line-height: 1.3;
  }

  .notification-option-copy span {
    display: block;
    margin-top: 4px;
    color: var(--notify-subtle);
    font-size: 11px;
    line-height: 1.4;
  }

  .notification-switch {
    position: relative;
    width: 45px;
    height: 26px;
    flex: 0 0 45px;
  }

  .notification-switch input {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    clip-path: inset(50%);
  }

  .notification-switch-track {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #d8dfdb;
    box-shadow: inset 0 1px 2px rgba(10, 31, 23, .12);
    transition: background .2s ease;
  }

  .notification-switch-track::after {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(10, 31, 23, .2);
    content: "";
    transition: transform .2s ease;
  }

  .notification-switch input:checked + .notification-switch-track {
    background: var(--notify-green);
  }

  .notification-switch input:checked + .notification-switch-track::after {
    transform: translateX(19px);
  }

  .notification-switch input:focus-visible + .notification-switch-track {
    outline: 3px solid rgba(20, 168, 109, .28);
    outline-offset: 2px;
  }

  .notification-info {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 16px;
    padding: 13px;
    border-radius: 16px;
    background: #f7faf8;
    color: var(--notify-muted);
    font-size: 11px;
    line-height: 1.5;
  }

  .notification-info svg {
    flex: 0 0 auto;
    margin-top: 1px;
    color: var(--notify-green);
  }

  .notification-actions {
    display: grid;
    grid-template-columns: 1fr 1.45fr;
    gap: 10px;
    margin-top: 18px;
  }

  .notification-button {
    min-height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 0;
    border-radius: 17px;
    font: 650 14px/1 var(--notify-ui);
    text-decoration: none;
    transition: transform .2s ease, filter .2s ease;
  }

  .notification-button:active {
    transform: scale(.98);
  }

  .notification-cancel {
    background: #fff;
    box-shadow: var(--notify-shadow-sm), inset 0 0 0 1px var(--notify-line);
    color: var(--notify-muted);
  }

  .notification-save {
    background: linear-gradient(180deg, #11905e, #0a6a45);
    box-shadow: 0 10px 24px -8px rgba(10, 106, 69, .45), inset 0 1px 0 rgba(255, 255, 255, .25);
    color: #fff;
    cursor: pointer;
  }

  .notification-save:hover {
    filter: brightness(1.05);
  }

  @media (max-width: 520px) {
    html.profile-notifications-page,
    html.profile-notifications-page body {
      background: var(--notify-bg);
    }

    html.profile-notifications-page body {
      display: block;
    }

    html.profile-notifications-page body .container.profile-notifications-container {
      display: block;
    }

    .notification-phone {
      width: 100%;
      height: auto;
      min-height: 100dvh;
      margin: 0;
      overflow: visible;
      border-radius: 0;
      box-shadow: none;
    }

    .notification-screen {
      height: auto;
      min-height: 100dvh;
      overflow: visible;
    }

    .notification-main {
      padding-bottom: calc(30px + env(safe-area-inset-bottom));
    }
  }

  @media (max-width: 380px) {
    .notification-main {
      padding-right: 16px;
      padding-left: 16px;
    }

    .notification-panel {
      padding: 17px;
    }

    .notification-actions {
      grid-template-columns: 1fr;
    }
  }

  @media (max-height: 900px) and (min-width: 521px) {
    html.profile-notifications-page body {
      align-items: flex-start;
    }

    .notification-phone {
      height: calc(100vh - 40px);
      min-height: 560px;
      margin: 20px 0;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .notification-phone *,
    .notification-phone *::before {
      transition: none !important;
    }
  }
</style>

<div class="notification-phone">
  <div class="notification-screen">
    <header class="notification-chrome">
      <div class="notification-statusbar" aria-hidden="true">
        <time id="notificationLocalTime"></time>
        <div class="notification-status-icons">
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
      <nav class="notification-nav" aria-label="Notification settings navigation">
        <a class="notification-back" href="{{ route('profile') }}" aria-label="Back to profile">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
        </a>
        <div class="notification-nav-title">
          <h1>Notifications</h1>
          <p>Choose what reaches you</p>
        </div>
        <span aria-hidden="true"></span>
      </nav>
    </header>

    <main class="notification-main">
      <section class="notification-hero" aria-label="Notification preferences">
        <div class="notification-hero-top">
          <span class="notification-eyebrow">Your account</span>
          <span class="notification-hero-icon" aria-hidden="true">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
          </span>
        </div>
        <h2>Stay in the loop</h2>
        <p>Choose which account updates and messages you want to receive.</p>
      </section>

      <section class="notification-panel" aria-labelledby="notificationPanelTitle">
        <div class="notification-panel-head">
          <div>
            <h2 id="notificationPanelTitle">Notification preferences</h2>
            <p>Adjust how and when you receive updates.</p>
          </div>
          <span class="notification-tag">Settings</span>
        </div>

        @if (session('status'))
          <div class="notification-success" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
          <div class="notification-error" role="alert">We couldn't save your preferences. Please review your selections and try again.</div>
        @endif

        <form action="{{ route('profile.notifications.update') }}" method="post">
          @csrf
          <label class="notification-option" for="emailNotifications">
            <span class="notification-option-icon" aria-hidden="true">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
            </span>
            <span class="notification-option-copy">
              <strong>Email notifications</strong>
              <span>Receive service updates and account messages by email.</span>
            </span>
            <input type="hidden" name="preferences[email_notifications]" value="0">
            <span class="notification-switch">
              <input id="emailNotifications" name="preferences[email_notifications]" type="checkbox" value="1" @checked(old('preferences.email_notifications', $notificationPreferences['email_notifications']))>
              <span class="notification-switch-track"></span>
            </span>
          </label>

          <label class="notification-option" for="accountAlerts">
            <span class="notification-option-icon" aria-hidden="true">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 7v5c0 5 3.5 8.5 8 9 4.5-.5 8-4 8-9V7l-8-4Z"/><path d="m9 12 2 2 4-4"/></svg>
            </span>
            <span class="notification-option-copy">
              <strong>Account activity alerts</strong>
              <span>Get important notices about activity on your account.</span>
            </span>
            <input type="hidden" name="preferences[account_alerts]" value="0">
            <span class="notification-switch">
              <input id="accountAlerts" name="preferences[account_alerts]" type="checkbox" value="1" @checked(old('preferences.account_alerts', $notificationPreferences['account_alerts']))>
              <span class="notification-switch-track"></span>
            </span>
          </label>

          <label class="notification-option" for="marketingUpdates">
            <span class="notification-option-icon" aria-hidden="true">
              <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5-5 18-4-8-9-5Z"/><path d="m12 16 4-5"/></svg>
            </span>
            <span class="notification-option-copy">
              <strong>Marketing updates</strong>
              <span>Hear about product news, offers, and announcements.</span>
            </span>
            <input type="hidden" name="preferences[marketing_updates]" value="0">
            <span class="notification-switch">
              <input id="marketingUpdates" name="preferences[marketing_updates]" type="checkbox" value="1" @checked(old('preferences.marketing_updates', $notificationPreferences['marketing_updates']))>
              <span class="notification-switch-track"></span>
            </span>
          </label>

          <div class="notification-info" role="note">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
            <p>Your choices are saved to your account. Delivery controls are not yet connected to every notification service.</p>
          </div>

          <div class="notification-actions">
            <a class="notification-button notification-cancel" href="{{ route('profile') }}">Cancel</a>
            <button class="notification-button notification-save" type="submit">
              Save preferences
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
    var time = document.getElementById('notificationLocalTime');
    if (time) {
      time.textContent = new Intl.DateTimeFormat(undefined, {
        hour: 'numeric',
        minute: '2-digit'
      }).format(new Date());
    }
  })();
</script>
@endsection
