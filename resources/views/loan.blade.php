@extends('layouts.app')

@section('content')
<style>
  .page-shell { max-width:520px; margin:28px auto; }
  .card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 18px 48px rgba(3,7,18,0.06); }
  .title { font-size:22px; font-weight:900; color:#041438; margin:0 0 8px; }
  .copy { color:#64748b; margin-bottom:18px; font-weight:700; }
  .btn { display:inline-block; padding:10px 14px; border-radius:10px; text-decoration:none; font-weight:800; }
  .btn-primary { background:#166534; color:#fff; }
</style>

<main class="page-shell">
  <div class="card">
    <h1 class="title">Loan</h1>
    <p class="copy">Apply for a loan or view loan offers tailored to you.</p>
    <div style="display:flex;gap:10px;">
      <a class="btn btn-primary" href="#">Apply Now</a>
      <a class="btn btn-ghost" href="{{ route('dashboard') }}">Back to Dashboard</a>
    </div>
  </div>
</main>

@endsection
