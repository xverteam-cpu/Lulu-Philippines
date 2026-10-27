@extends('layouts.app')

@section('content')
<main style="max-width:440px;margin:48px auto;padding:28px;background:#fff;border-radius:18px;box-shadow:0 18px 40px rgba(0,0,0,.08);">
  <h1 style="margin:0 0 8px;color:#166534;text-align:center;">Set up your Lulu account</h1>
  <p style="margin:0 0 22px;color:#4b5563;text-align:center;">Welcome, {{ $invitation->name }}. Add your mobile number, choose a password, and set a 4-digit PIN.</p>

  @if ($errors->any())
    <div role="alert" style="margin-bottom:18px;padding:12px 14px;background:#fff0f0;color:#7f1d1d;border-radius:10px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form action="{{ route('account-setup.store', ['token' => $token]) }}" method="post" style="display:grid;gap:12px;">
    @csrf
    <label for="phone">Mobile number</label>
    <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" maxlength="40" required placeholder="+63 917 123 4567" style="padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    @include('auth.partials.phone-verification')
    <label for="password">Password</label>
    <input id="password" type="password" name="password" autocomplete="new-password" minlength="8" required style="padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required style="padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    <label for="pin">4-digit PIN</label>
    <input id="pin" type="password" name="pin" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" autocomplete="off" required style="padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    <label for="pin_confirmation">Confirm 4-digit PIN</label>
    <input id="pin_confirmation" type="password" name="pin_confirmation" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" autocomplete="off" required style="padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    <button type="submit" style="margin-top:8px;padding:13px;border:0;border-radius:10px;background:#166534;color:#fff;font-weight:700;">Create account</button>
  </form>
</main>
@vite('resources/js/phone-verification.js')
@endsection
