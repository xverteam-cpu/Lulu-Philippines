@extends('layouts.app')

@section('content')
<style>
  .temporary-password-card {
    box-sizing: border-box;
    width: 100%;
    max-width: 440px;
    margin: 48px auto;
    padding: 28px;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
  }

  .temporary-password-card *,
  .temporary-password-card *::before,
  .temporary-password-card *::after {
    box-sizing: border-box;
  }
</style>

<main class="temporary-password-card">
  <h1 style="margin:0 0 8px;color:#166534;text-align:center;">Choose a new password</h1>
  <p style="margin:0 0 22px;color:#4b5563;text-align:center;">Your temporary password has been used. Set a new password to finish signing in.</p>

  @if ($errors->any())
    <div role="alert" style="margin-bottom:18px;padding:12px 14px;background:#fff0f0;color:#7f1d1d;border-radius:10px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form action="{{ route('temporary-password.update') }}" method="post" style="display:grid;gap:12px;">
    @csrf
    <label for="temporary-password-new">New password</label>
    <input id="temporary-password-new" type="password" name="password" autocomplete="new-password" minlength="8" required style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    <label for="temporary-password-confirmation">Confirm new password</label>
    <input id="temporary-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" required style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    <button type="submit" style="margin-top:8px;padding:13px;border:0;border-radius:10px;background:#166534;color:#fff;font-weight:700;">Set password and sign in</button>
  </form>
</main>
@endsection
