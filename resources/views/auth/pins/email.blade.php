@extends('layouts.app')

@section('content')
<div style="max-width:420px;margin:48px auto;padding:24px;background:#ffffff;border-radius:18px;box-shadow:0 18px 40px rgba(0,0,0,0.08);">
  <h1 style="margin:0 0 12px;font-size:28px;color:#166534;text-align:center;">Reset your PIN</h1>
  <p style="margin:0 0 20px;color:#4b5563;text-align:center;">Enter the email address on your account and we’ll send a one-time PIN reset link if it matches.</p>

  @if (session('status'))
    <div role="status" style="margin-bottom:20px;padding:14px 16px;background:#e9f5ff;color:#0b5ed7;border-radius:12px;font-size:14px;">
      {{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div role="alert" style="margin-bottom:20px;padding:14px 16px;background:#fff0f0;color:#0f3d2e;border-radius:12px;font-size:14px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form action="{{ route('pin.email') }}" method="post" style="display:grid;gap:16px;">
    @csrf
    <label for="pin-reset-email" style="font-weight:700;color:#1f1f1f;">Email address</label>
    <input id="pin-reset-email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:15px;">
    <button type="submit" style="width:100%;padding:12px 14px;border:none;border-radius:10px;background:#166534;color:#ffffff;font-weight:700;font-size:15px;">Send PIN reset link</button>
    <a href="{{ route('investors') }}" style="display:block;text-align:center;color:#166534;font-size:14px;text-decoration:none;">Back to login</a>
  </form>
</div>
@endsection
