@extends('layouts.app')

@section('content')
<main style="max-width:480px;margin:48px auto;padding:28px;background:#fff;border-radius:18px;box-shadow:0 18px 40px rgba(0,0,0,.08);">
  <h1 style="margin:0 0 8px;color:#166534;text-align:center;">Add your mobile number</h1>
  <p style="margin:0 0 22px;color:#4b5563;text-align:center;">Please add a mobile number to continue using your Lulu account.</p>

  @if ($errors->any())
    <div role="alert" style="margin-bottom:18px;padding:12px 14px;background:#fff0f0;color:#7f1d1d;border-radius:10px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form action="{{ route('phone.store') }}" method="post" style="display:grid;gap:12px;">
    @csrf
    <label for="phone" style="font-weight:700;color:#1f2937;">Mobile number</label>
    <input
      id="phone"
      name="phone"
      type="tel"
      value="{{ old('phone', auth()->user()->phone) }}"
      autocomplete="tel"
      inputmode="tel"
      maxlength="40"
      required
      autofocus
      placeholder="+63 917 123 4567"
      style="padding:12px;border:1px solid #d1d5db;border-radius:10px;"
    >
    <button type="submit" style="margin-top:8px;padding:13px;border:0;border-radius:10px;background:#166534;color:#fff;font-weight:700;cursor:pointer;">Save mobile number</button>
  </form>
</main>
@endsection
