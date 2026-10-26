@extends('layouts.app')

@section('content')
<div style="max-width:420px;margin:48px auto;padding:24px;background:#ffffff;border-radius:18px;box-shadow:0 18px 40px rgba(0,0,0,0.08);">
  <h1 style="margin:0 0 20px;font-size:28px;color:#166534;text-align:center;">Choose a new PIN</h1>

  @if ($errors->any())
    <div role="alert" style="margin-bottom:20px;padding:14px 16px;background:#fff0f0;color:#0f3d2e;border-radius:12px;font-size:14px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form action="{{ route('pin.update') }}" method="post" style="display:grid;gap:16px;">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">

    <label for="new-pin" style="font-weight:700;color:#1f1f1f;">New four-digit PIN</label>
    <input id="new-pin" type="password" name="pin" inputmode="numeric" autocomplete="new-password" pattern="[0-9]{4}" maxlength="4" required style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:15px;">

    <label for="new-pin-confirmation" style="font-weight:700;color:#1f1f1f;">Confirm PIN</label>
    <input id="new-pin-confirmation" type="password" name="pin_confirmation" inputmode="numeric" autocomplete="new-password" pattern="[0-9]{4}" maxlength="4" required style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:15px;">

    <button type="submit" style="width:100%;padding:12px 14px;border:none;border-radius:10px;background:#166534;color:#ffffff;font-weight:700;font-size:15px;">Reset PIN</button>
  </form>
</div>
@endsection
