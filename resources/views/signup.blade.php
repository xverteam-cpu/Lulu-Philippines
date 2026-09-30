@extends('layouts.app')

@section('content')
<style>
  .signup-shell {
    min-height: calc(100vh - 36px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 28px 16px;
    background: linear-gradient(135deg, #f4fbf7 0%, #ffe8e8 100%);
  }
  .signup-card {
    width: 100%;
    max-width: 620px;
    padding: 28px 24px 24px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 28px rgba(0,0,0,0.14);
  }
  .signup-title {
    margin: 0 0 8px;
    color: #166534;
    font-size: 28px;
    font-weight: 700;
    text-align: center;
  }
  .signup-subtitle {
    margin: 0 0 20px;
    color: #667085;
    font-size: 15px;
    text-align: center;
  }
  .signup-form {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }
  .signup-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  .signup-field.full {
    grid-column: 1 / -1;
  }
  .signup-label {
    color: #001a33;
    font-size: 15px;
    font-weight: 700;
  }
  .signup-input,
  .signup-select,
  .signup-textarea {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #ff4f62;
    border-radius: 6px;
    padding: 10px 12px;
    color: #001a33;
    font-size: 15px;
    outline: none;
  }
  .signup-textarea {
    min-height: 96px;
    resize: vertical;
  }
  .signup-actions {
    grid-column: 1 / -1;
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 8px;
  }
  .signup-button,
  .login-link,
  .signup-google-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 160px;
    height: 44px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
  }
  .signup-google-button {
    grid-column: 1 / -1;
    gap: 10px;
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #d0d5dd;
    background: #ffffff;
    color: #344054;
  }
  .signup-google-button:hover {
    background: #f9fafb;
  }
  .signup-google-button svg {
    width: 18px;
    height: 18px;
  }
  .signup-button {
    border: 0;
    background: #166534;
    color: #ffffff;
    cursor: pointer;
  }
  .login-link {
    border: 1px solid #166534;
    color: #166534;
    background: #ffffff;
  }
  @media (max-width: 640px) {
    .signup-form {
      grid-template-columns: 1fr;
    }
    .signup-actions {
      flex-direction: column;
    }
    .signup-button,
    .login-link,
    .signup-google-button {
      width: 100%;
    }
  }
</style>

<div class="signup-shell">
  <div class="signup-card">
    <h1 class="signup-title">Sign Up</h1>
    <p class="signup-subtitle">Create your partner account to get started.</p>

    <a class="signup-google-button" href="{{ route('login.google', ! empty($referral) ? ['ref' => $referral] : []) }}">
      <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
        <path d="M5.84 14.09a6.6 6.6 0 0 1 0-4.18V7.07H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
      </svg>
      Continue with Google
    </a>

    <form class="signup-form" action="{{ route('register.partner') }}" method="post">
      @csrf
      @if ($errors->any())
        <div class="signup-field full" style="padding:10px 12px;border-radius:6px;background:#fff0f2;color:#0f3d2e;font-size:14px;line-height:20px;">
          {{ $errors->first() }}
        </div>
      @endif

      <div class="signup-field">
        <label class="signup-label" for="fullname">Fullname</label>
        <input class="signup-input" id="fullname" name="fullname" type="text" value="{{ old('fullname') }}" placeholder="Enter your name">
      </div>

      <div class="signup-field">
        <label class="signup-label" for="username">Username</label>
        <input class="signup-input" id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Choose a username">
      </div>

      <div class="signup-field">
        <label class="signup-label" for="email">Email Address</label>
        <input class="signup-input" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
      </div>

      <div class="signup-field">
        <label class="signup-label" for="referral">Referral</label>
        <input class="signup-input" id="referral" name="referral" type="text" value="{{ old('referral', $referral ?? '') }}" placeholder="Enter referral code or username">
      </div>

      <div class="signup-field">
        <label class="signup-label" for="password">Password</label>
        <input class="signup-input" id="password" name="password" type="password" placeholder="Create password">
      </div>

      <div class="signup-field">
        <label class="signup-label" for="password_confirmation">Confirm Password</label>
        <input class="signup-input" id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm password">
      </div>

      <div class="signup-actions">
        <button class="signup-button" type="submit">Create Account</button>
        <a class="login-link" href="{{ route('login') }}">Back to Login</a>
      </div>
    </form>
  </div>
</div>
@endsection
