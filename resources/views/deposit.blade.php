@extends('layouts.app')

@php
  $selectedMethod = request('method', 'bank');
@endphp

@section('content')
<style>
  .deposit-shell { max-width:520px; margin:28px auto; }
  .deposit-card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 18px 48px rgba(3,7,18,0.06); }
  .deposit-title { font-size:22px; font-weight:900; color:#041438; margin:0 0 8px; }
  .deposit-sub { color:#64748b; margin-bottom:18px; font-weight:700; }
  .field { margin-bottom:12px; }
  .label { display:block; font-weight:800; color:#041438; margin-bottom:6px; }
  .input, .select { width:100%; height:46px; padding:10px 12px; border-radius:8px; border:1px solid rgba(3,7,18,0.06); font-size:16px; }
  .deposit-actions { margin-top:14px; display:flex; gap:12px; }
  .btn-primary { background:#166534; color:#fff; border:0; padding:12px 16px; border-radius:10px; font-weight:900; cursor:pointer; }
  .btn-secondary { background:#fff; color:#041438; border:1px solid rgba(3,7,18,0.06); padding:12px 16px; border-radius:10px; font-weight:800; cursor:pointer; }
</style>

<main class="deposit-shell">
  <div class="deposit-card">
    <h1 class="deposit-title">Buy Shares</h1>
    <div class="deposit-sub">Deposit funds to buy shares. Choose an amount and payment method.</div>

    <form action="{{ route('unavailable') }}" method="post">
      @csrf
      <div class="field">
        <label class="label" for="amount">Amount</label>
        <input id="amount" name="amount" type="number" class="input" placeholder="Enter amount" min="1" step="0.01" required>
      </div>

      <div class="field">
        <label class="label" for="method">Payment method</label>
        <select id="method" name="method" class="select">
          <option value="gcash" {{ $selectedMethod === 'gcash' ? 'selected' : '' }}>Gcash</option>
          <option value="bank" {{ $selectedMethod === 'bank' ? 'selected' : '' }}>Bank Transfer</option>
          <option value="card" {{ $selectedMethod === 'card' ? 'selected' : '' }}>Card</option>
        </select>
      </div>

      <div class="deposit-actions">
        <button class="btn-primary" type="submit">Proceed</button>
        <a class="btn-secondary" href="{{ route('dashboard') }}">Cancel</a>
      </div>
    </form>
  </div>
</main>

@endsection
