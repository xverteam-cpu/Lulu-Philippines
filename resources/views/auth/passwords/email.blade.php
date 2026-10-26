@extends('layouts.app')

@section('content')
<style>
  .password-recovery-page body {
    min-height: 100vh;
    background: radial-gradient(ellipse at top left, #e6f3eb 0%, #f5f8f5 68%);
    color: #123d2c;
  }

  .password-recovery-page .container.password-recovery-container {
    box-sizing: border-box;
    display: flex;
    width: 100%;
    max-width: none;
    min-height: 100vh;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin: 0;
    padding: 26px 20px;
  }

  .password-recovery-content {
    width: 100%;
    max-width: 452px;
  }

  .password-recovery-brand {
    margin-bottom: 27px;
    color: #087749;
    text-align: center;
  }

  .password-recovery-brand strong {
    display: block;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -.06em;
    line-height: 1;
  }

  .password-recovery-brand span {
    display: block;
    margin-top: 8px;
    font-size: 8px;
    letter-spacing: .48em;
  }

  .password-recovery-card {
    box-sizing: border-box;
    width: 100%;
    padding: 36px 32px 28px;
    background: #fff;
    border: 1px solid rgba(223, 234, 226, .9);
    border-radius: 28px;
    box-shadow: 0 22px 55px rgba(17, 64, 41, .1);
  }

  .password-recovery-card *,
  .password-recovery-card *::before,
  .password-recovery-card *::after {
    box-sizing: border-box;
  }

  .password-recovery-card form {
    min-width: 0;
  }

  .password-recovery-lock {
    display: grid;
    width: 60px;
    height: 60px;
    place-items: center;
    margin-bottom: 25px;
    border-radius: 19px;
    background: #edf6ef;
    color: #087749;
  }

  .password-recovery-eyebrow {
    margin: 0 0 13px;
    color: #277451;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .2em;
    text-transform: uppercase;
  }

  .password-recovery-card h1 {
    margin: 0;
    color: #123d2c;
    font-size: clamp(27px, 6vw, 32px);
    font-weight: 850;
    letter-spacing: -.045em;
    line-height: 1.15;
  }

  .password-recovery-intro {
    margin: 14px 0 27px;
    color: #65756d;
    font-size: 14px;
    line-height: 1.7;
  }

  .password-recovery-card form {
    gap: 10px !important;
  }

  .password-recovery-card label {
    margin-bottom: 0;
    color: #183b2c !important;
    font-size: 12px;
  }

  .password-recovery-input {
    display: flex;
    width: 100%;
    min-width: 0;
    height: 56px;
    align-items: center;
    gap: 12px;
    padding: 0 14px;
    border: 1px solid #d6e3da;
    border-radius: 14px;
    background: #fbfdfb;
    color: #789186;
  }

  .password-recovery-card input[type="email"] {
    display: block;
    flex: 1;
    width: 0;
    min-width: 0;
    height: 100%;
    padding: 0;
    border: 0;
    outline: 0;
    background: transparent;
    color: #183b2c;
    font: inherit;
    font-size: 14px;
  }

  .password-recovery-card input[type="email"]::placeholder {
    color: #9aa9a1;
  }

  .password-recovery-hint {
    margin: 0 0 12px;
    color: #819087;
    font-size: 11px;
  }

  .password-recovery-submit {
    display: flex;
    width: 100%;
    min-height: 54px;
    align-items: center;
    justify-content: center;
    gap: 12px;
    margin-top: 4px;
    border: 0;
    border-radius: 14px;
    background: #087344;
    box-shadow: 0 9px 18px rgba(8, 115, 68, .15);
    color: #fff;
    font: inherit;
    font-size: 13px;
    font-weight: 750;
    cursor: pointer;
  }

  .password-recovery-submit:hover {
    background: #06663d;
  }

  .password-recovery-back {
    display: block;
    margin-top: 16px;
    color: #087749;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    text-decoration: none;
  }

  .password-recovery-pin {
    margin: 25px 0 0;
    padding-top: 22px;
    border-top: 1px solid #e7eee9;
    color: #829087;
    font-size: 12px;
    line-height: 1.7;
    text-align: center;
  }

  .password-recovery-pin a {
    display: block;
    color: #087749;
    font-weight: 650;
    text-decoration: none;
  }

  .password-recovery-security {
    color: #839188;
    font-size: 11px;
    text-align: center;
  }

  .password-recovery-alert {
    margin: 0 0 18px;
    padding: 12px 14px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.5;
    overflow-wrap: anywhere;
  }

  .password-recovery-alert--status {
    background: #eaf5ee;
    color: #17613b;
  }

  .password-recovery-alert--error {
    background: #fff0f0;
    color: #8a2727;
  }

  @media (max-width: 480px) {
    .password-recovery-page .container.password-recovery-container {
      justify-content: flex-start;
      padding: max(28px, env(safe-area-inset-top)) 16px 24px;
    }

    .password-recovery-brand {
      margin-bottom: 24px;
    }

    .password-recovery-card {
      padding: 28px 24px 24px;
      border-radius: 24px;
    }

    .password-recovery-security {
      padding-bottom: env(safe-area-inset-bottom);
    }
  }
</style>

<main class="password-recovery-content">
  <div class="password-recovery-brand" aria-label="LuLu Philippines">
    <strong>LuLu</strong>
    <span>PHILIPPINES</span>
  </div>

  <section class="password-recovery-card" aria-labelledby="password-recovery-title">
    <div class="password-recovery-lock" aria-hidden="true">
      <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <rect x="4.5" y="10" width="15" height="11" rx="2.5"/>
        <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
        <path d="M12 14v3"/>
      </svg>
    </div>
    <p class="password-recovery-eyebrow">Account recovery</p>
    <h1 id="password-recovery-title">Reset your password</h1>
    <p class="password-recovery-intro">No worries. Enter your registered email and we’ll help you get back into your account.</p>

    @if (session('status'))
      <div class="password-recovery-alert password-recovery-alert--status" role="status">
        {{ session('status') }}
      </div>
    @endif

    @if ($errors->any())
      <div class="password-recovery-alert password-recovery-alert--error" role="alert">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('password.email') }}" method="post">
      @csrf

      <label for="password-reset-email">Email address</label>
      <div class="password-recovery-input">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <rect x="3" y="5" width="18" height="14" rx="2"/>
          <path d="m4 7 8 6 8-6"/>
        </svg>
        <input id="password-reset-email" type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" autocomplete="email" required>
      </div>
      <p class="password-recovery-hint">Use the email linked to your Lulu account.</p>

      <button class="password-recovery-submit" type="submit">
        Send reset link
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
      </button>
      <a class="password-recovery-back" href="{{ route('investors') }}">← &nbsp; Back to login</a>
    </form>

    <p class="password-recovery-pin">
      Need help with your investor PIN?
      <a href="{{ route('pin.request') }}">Recover your PIN</a>
    </p>
  </section>
</main>
<footer class="password-recovery-security">
  <span aria-hidden="true">♧</span> &nbsp; Your account security matters.
</footer>
@endsection
