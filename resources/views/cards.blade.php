@extends('layouts.app')

@section('content')
<style>
  .page-shell { max-width:760px; margin:28px auto; }
  .card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 18px 48px rgba(3,7,18,0.06); }
  .title { font-size:22px; font-weight:900; color:#041438; margin:0 0 8px; }
  .copy { color:#64748b; margin-bottom:18px; font-weight:700; }
  .card-item { display:flex; align-items:center; justify-content:space-between; padding:12px; border-radius:12px; background:#f7f9fb; margin-bottom:10px; }
</style>

<main class="page-shell">
  <div class="card">
    <h1 class="title">Cards</h1>
    <p class="copy">Manage your saved cards and virtual cards.</p>
    <div style="margin-top:12px;">
      <div class="card-item">
        <div>Visa **** 4242</div>
        <div><a href="#" style="color:#166534;font-weight:800;text-decoration:none;">Manage</a></div>
      </div>
      <div class="card-item">
        <div>Mastercard **** 1111</div>
        <div><a href="#" style="color:#166534;font-weight:800;text-decoration:none;">Manage</a></div>
      </div>
    </div>
    <div style="margin-top:18px;"><a href="{{ route('dashboard') }}" class="btn btn-ghost">Back to Dashboard</a></div>
  </div>
</main>

@endsection
