@extends('layouts.app')

@section('content')
<style>
  .unavailable-card {
    max-width:520px;
    margin:72px auto;
    padding:34px 26px;
    background:#ffffff;
    border-radius:18px;
    box-shadow:0 10px 28px rgba(0,0,0,0.14);
    text-align:center;
  }
  .unavailable-title {
    margin:0;
    color:#166534;
    font-size:26px;
    line-height:32px;
    font-weight:700;
  }
  .unavailable-text {
    margin:14px 0 24px;
    color:#263238;
    font-size:16px;
    line-height:24px;
  }
  .unavailable-button {
    display:inline-block;
    min-width:132px;
    padding:12px 18px;
    border-radius:24px;
    background:#166534;
    color:#ffffff;
    text-decoration:none;
    font-size:14px;
    line-height:18px;
    font-weight:700;
  }
</style>

<div class="unavailable-card">
  <h1 class="unavailable-title">Feature Unavailable</h1>
  <p class="unavailable-text">Sorry, this feature is not available in your country or region yet.</p>
  @if(auth()->check())
    <a class="unavailable-button" href="{{ route('dashboard') }}">Back to Dashboard</a>
  @else
    <a class="unavailable-button" href="{{ route('home') }}">Back Home</a>
  @endif
</div>
@endsection
