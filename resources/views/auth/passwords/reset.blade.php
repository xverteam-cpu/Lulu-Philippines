@extends('layouts.app')

@section('content')
<div style="max-width:420px;margin:48px auto;padding:24px;background:#ffffff;border-radius:18px;box-shadow:0 18px 40px rgba(0,0,0,0.08);">
  <h1 style="margin:0 0 20px;font-size:28px;color:#166534;text-align:center;">Set a new password</h1>

  @if ($errors->any())
    <div style="margin-bottom:20px;padding:14px 16px;background:#fff0f0;color:#0f3d2e;border-radius:12px;font-size:14px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form action="{{ route('password.update') }}" method="post" style="display:grid;gap:16px;">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <label style="font-weight:700;color:#1f1f1f;">New password</label>
    <input type="password" name="password" required style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:15px;">

    <label style="font-weight:700;color:#1f1f1f;">Confirm password</label>
    <input type="password" name="password_confirmation" required style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:15px;">

    <button type="submit" style="width:100%;padding:12px 14px;border:none;border-radius:10px;background:#166534;color:#ffffff;font-weight:700;font-size:15px;">Reset password</button>
  </form>
</div>
@endsection
