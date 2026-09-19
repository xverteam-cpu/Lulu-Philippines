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
  .login-link {
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
    .login-link {
      width: 100%;
    }
  }
</style>

<div class="signup-shell">
  <div class="signup-card">
    <h1 class="signup-title">Sign Up</h1>
    <p class="signup-subtitle">Create your partner account to get started.</p>

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
