@extends('layouts.app')

@section('content')
<style>
  body.signup-page {
    min-height: 100vh;
    background: #f6f1df url('/investor-desktop-background.png') center center / cover fixed no-repeat;
    color: #fff;
    font-family: Arial, Helvetica, sans-serif;
  }

  .signup-page .container {
    display: flex;
    width: 100%;
    max-width: none;
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    margin: 0;
    padding: max(24px, env(safe-area-inset-top)) max(20px, env(safe-area-inset-right)) max(20px, env(safe-area-inset-bottom)) max(20px, env(safe-area-inset-left));
  }

  .signup-shell {
    width: min(390px, 100%);
  }

  .signup-card {
    position: relative;
    isolation: isolate;
    width: 100%;
    box-sizing: border-box;
    padding: 28px 22px 22px;
    border: 1px solid #ffffffad;
    border-radius: 28px;
    background: radial-gradient(ellipse at 0% 0%, #279a6b66, transparent 68%), linear-gradient(145deg, #0b8959, #06794b);
    box-shadow: 0 34px 56px -22px #103e2d80, 0 15px 25px -12px #173e2b55, 0 3px 8px #173e2b1c, inset 0 1px 0 #ffffff55;
    color: #fff;
  }

  .signup-card::before {
    position: absolute;
    z-index: -1;
    inset: 16px 12px -20px;
    border-radius: inherit;
    background: #153e2b66;
    content: "";
    filter: blur(22px);
    pointer-events: none;
  }

  .signup-eyebrow {
    display: block;
    margin: 0 8px 8px;
    color: #c1e4ce;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 2px;
  }

  .signup-title {
    margin: 0 8px 10px;
    color: #fff;
    font-size: clamp(29px, 7.6vw, 36px);
    font-weight: 700;
    letter-spacing: -1.1px;
    line-height: 1.05;
  }

  .signup-subtitle {
    margin: 0 8px 20px;
    color: #cce7d8;
    font-size: 13px;
    line-height: 1.5;
  }

  .signup-form {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .signup-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .signup-field.full {
    grid-column: 1 / -1;
  }

  .signup-label {
    padding-left: 14px;
    color: #e0eee6;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .03em;
  }

  .signup-input,
  .signup-select,
  .signup-textarea {
    width: 100%;
    box-sizing: border-box;
    min-height: 48px;
    padding: 0 18px;
    border: 1px solid #ffffff75;
    border-radius: 100px;
    background: #ffffff20;
    color: #fff;
    font: 14px Arial, Helvetica, sans-serif;
    box-shadow: inset 0 1px 0 #ffffff0a;
    outline: none;
  }

  .signup-input::placeholder,
  .signup-textarea::placeholder {
    color: #e0eee6;
    font-size: 12px;
  }

  .signup-input:focus,
  .signup-select:focus,
  .signup-textarea:focus {
    border-color: #fff;
    background: #ffffff28;
    box-shadow: 0 0 0 3px #ffffff16;
  }

  .signup-textarea {
    min-height: 96px;
    resize: vertical;
  }

  .signup-divider {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 14px 0;
    color: #cce7d8;
    font-size: 11px;
    letter-spacing: 2px;
  }

  .signup-divider::before,
  .signup-divider::after {
    height: 1px;
    flex: 1;
    background: #ffffff40;
    content: "";
  }

  .signup-actions {
    grid-column: 1 / -1;
    display: grid;
    gap: 6px;
    margin-top: 4px;
  }

  .signup-button,
  .login-link,
  .signup-google-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 50px;
    box-sizing: border-box;
    border-radius: 100px;
    font: 600 13px Arial, Helvetica, sans-serif;
    text-decoration: none;
  }

  .signup-google-button {
    gap: 9px;
    padding: 12px;
    border: 1px solid #dadce0;
    background: #fff;
    color: #3c4043;
    font-size: 11px;
    letter-spacing: 1px;
  }

  .signup-google-button:hover {
    background: #f9fafb;
  }

  .signup-google-button svg {
    width: 20px;
    height: 20px;
    flex: none;
  }

  .signup-button {
    margin-top: 4px;
    padding: 12px 18px;
    border: 1px solid #d9e5dd;
    background: #fff;
    color: #08794b;
    cursor: pointer;
  }

  .signup-button:hover {
    background: #e7f4ed;
  }

  .login-link {
    min-height: 38px;
    color: #fff;
    font-size: 11px;
    text-shadow: 0 1px 2px #075b39;
  }

  .signup-error {
    margin: 0;
    padding: 10px 14px;
    border: 1px solid #ffffff55;
    border-radius: 16px;
    background: #153e2b55;
    color: #fff0c7;
    font-size: 12px;
    line-height: 1.4;
  }

  .signup-terms {
    margin: 12px 10px 0;
    color: #e0eee6;
    font-size: 8px;
    line-height: 1.4;
    text-align: center;
  }

  .signup-terms a {
    color: inherit;
  }

  @media (max-width: 780px) {
    body.signup-page {
      background-image: url('/investor-mobile-background.png');
    }
  }

  @media (max-height: 760px) {
    .signup-page .container {
      align-items: flex-start;
    }

    .signup-card {
      padding-top: 20px;
      padding-bottom: 18px;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .signup-button {
      transition: none;
    }
  }
</style>

<div class="signup-shell">
  <div class="signup-card">
    <span class="signup-eyebrow">CREATE ACCOUNT</span>
    <h1 class="signup-title">Your everyday.<br>A little easier.</h1>
    <p class="signup-subtitle">Create your LuLu partner account to get started.</p>

    <a class="signup-google-button" href="{{ route('login.google', ! empty($referral) ? ['ref' => $referral] : []) }}" id="signup-google-link">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
        <path d="M5.84 14.09a6.6 6.6 0 0 1 0-4.18V7.07H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
      </svg>
      Continue with Google
    </a>
    <div class="signup-divider" aria-hidden="true">OR</div>

    <form class="signup-form" action="{{ route('register.partner') }}" method="post">
      @csrf
      @if ($errors->any())
        <p class="signup-error" role="alert">
          {{ $errors->first() }}
        </p>
      @endif

      <div class="signup-field">
        <label class="signup-label" for="fullname">Fullname</label>
        <input class="signup-input" id="fullname" name="fullname" type="text" value="{{ old('fullname') }}" placeholder="Enter your name" autocomplete="name" maxlength="255" required>
      </div>

      <div class="signup-field">
        <label class="signup-label" for="username">Username</label>
        <input class="signup-input" id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Choose a username" autocomplete="username" maxlength="255" required>
      </div>

      <div class="signup-field">
        <label class="signup-label" for="email">Email Address</label>
        <input class="signup-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Enter your email address" autocomplete="email" maxlength="255" required>
      </div>

      <div class="signup-field">
        <label class="signup-label" for="phone">Mobile Number</label>
        <input class="signup-input" id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+63 917 123 4567" autocomplete="tel" inputmode="tel" maxlength="40" required>
      </div>

      <div class="signup-field">
        <label class="signup-label" for="referral">Referral code <span aria-hidden="true">*</span></label>
        <input class="signup-input" id="referral" name="referral" type="text" value="{{ old('referral', $referral ?? '') }}" placeholder="Enter referral code" autocomplete="off" required>
      </div>

      <div class="signup-field">
        <label class="signup-label" for="password">Password</label>
        <input class="signup-input" id="password" name="password" type="password" placeholder="Create password" autocomplete="new-password" minlength="8" required>
      </div>

      <div class="signup-field full">
        <label class="signup-label" for="password_confirmation">Confirm password</label>
        <input class="signup-input" id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm password" autocomplete="new-password" minlength="8" required>
      </div>

      <div class="signup-actions">
        <button class="signup-button" type="submit">Create Account</button>
      </div>
    </form>
    <a class="login-link" href="{{ route('login') }}">Already have an account? Sign in</a>
    <p class="signup-terms">By continuing, you agree to our <a href="{{ route('terms-of-service') }}">Terms of Service</a>, <a href="{{ route('privacy-policy') }}">Privacy Policy</a> and Cookie Use.</p>
  </div>
</div>
<script>
document.getElementById('signup-google-link')?.addEventListener('click', function (event) {
  const referral = document.getElementById('referral');
  const code = referral?.value.trim();
  if (!code) {
    event.preventDefault();
    referral?.focus();
    referral?.reportValidity();
    return;
  }

  const url = new URL(this.href, window.location.origin);
  url.searchParams.set('ref', code);
  this.href = url.toString();
});
</script>
@endsection
