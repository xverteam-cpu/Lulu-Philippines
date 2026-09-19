@extends('layouts.app')

@section('content')
<style>
  .page { min-height: 100vh; padding: 32px 18px; }
  .referral-card { max-width: 920px; margin: 0 auto; background: transparent; border-radius: 0; padding: 0; box-shadow: none; border: 0; }
  .top-bar { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:28px; }
  .back-button { display:inline-flex; align-items:center; gap:8px; padding:11px 16px; background:#f4f7fb; color:#0b1f3a; border:1px solid #d8e0ea; border-radius:10px; font-size:14px; font-weight:700; text-decoration:none; }
  .badge { padding:9px 14px; background:#ecfdf3; color:#16703a; border:1px solid #c8f0d5; border-radius:999px; font-size:13px; font-weight:700; }
  h1 { margin:0 0 10px; font-size:32px; line-height:1.2; color:#061b3a; letter-spacing:-0.5px; }
  .subtitle { max-width:720px; margin:0 0 30px; font-size:16px; line-height:1.6; color:#5b6b82; font-weight:600; }
  .stats-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px; }
  .stat-box { padding:20px; background:#f8fafc; border:1px solid #e4eaf1; border-radius:14px; }
  .stat-label { margin:0 0 8px; font-size:13px; color:#64748b; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; }
  .stat-value { margin:0; font-size:24px; font-weight:800; color:#061b3a; }
  .referral-panel { padding:22px; background:#fbfcfe; border:1px solid #e3e9f1; border-radius:16px; margin-bottom:26px; }
  .panel-label { margin:0 0 12px; font-size:14px; font-weight:800; color:#0b1f3a; }

  /* Fix: allow the input to shrink inside flex, prevent overflow, and align copy button */
  .copy-row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
  .referral-input { flex: 1 1 auto; min-width: 0; padding:16px 18px; border:1px solid #d9e2ec; border-radius:12px; background:#ffffff; color:#0b1f3a; font-size:15px; font-weight:700; outline:none; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .copy-button { flex: 0 0 140px; border:none; border-radius:12px; background:#098a58; color:#ffffff; font-size:15px; font-weight:800; cursor:pointer; box-shadow:0 8px 18px rgba(9,138,88,0.22); }
  .copy-button:hover { background:#08764c; }

  .earnings-row { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:18px; }
  .earnings-title { margin:0; font-size:18px; font-weight:800; color:#071c36; }
  .earnings-amount { margin:0; font-size:22px; font-weight:900; color:#061b3a; }
  .empty-state { padding:20px; border-radius:14px; background:#fff7f7; border:1px solid #fde2e2; color:#63718a; font-size:15px; font-weight:700; margin-bottom:24px; }
  .footer-actions { display:flex; justify-content:flex-start; }
  .dashboard-link { color:#e5192a; font-size:15px; font-weight:800; text-decoration:none; }
  .dashboard-link:hover { text-decoration:underline; }
  .ref-list { display:flex; flex-direction:column; gap:12px; }
  .ref-item { display:flex; justify-content:space-between; align-items:center; padding:14px; border-radius:12px; border:1px solid #eef3f8; background:#ffffff; }
  .ref-left { font-weight:800; }
  .ref-meta { color:#6b7890; font-size:13px; }

  @media (max-width:700px) {
    .stats-grid { grid-template-columns:1fr; }
    .copy-row { flex-direction:column; }
    .copy-button { width:100%; min-height:52px; flex: none; }
    .referral-input { white-space:normal; overflow:auto; }
  }
</style>

@php
  $user = auth()->user();
  $link = $user && $user->username ? route('signup', ['ref' => $user->username]) : route('signup');
  $totalEarned = $user ? (float) $user->referralEarnings()->sum('amount') : 0;
  $referrals = $user ? $user->referrals()->orderByDesc('created_at')->get() : collect();
  $invitedCount = $referrals->count();
@endphp

<main class="page">
  <section class="referral-card">

    <div class="top-bar">
      <a href="{{ route('dashboard') }}" class="back-button">← Back</a>
      <div class="badge">Referral Program</div>
    </div>

    <h1>Referrals</h1>

    <p class="subtitle">Invite partners and earn rewards. Share your referral link below and receive 5% of your downline’s capital when they buy shares.</p>

    <div class="stats-grid">
      <div class="stat-box">
        <p class="stat-label">Total Earnings</p>
        <p class="stat-value">${{ number_format($totalEarned, 2) }}</p>
      </div>

      <div class="stat-box">
        <p class="stat-label">Invited Partners</p>
        <p class="stat-value">{{ $invitedCount }}</p>
      </div>

      <div class="stat-box">
        <p class="stat-label">Reward Rate</p>
        <p class="stat-value">5%</p>
      </div>
    </div>

    <div class="referral-panel">
      <p class="panel-label">Your Referral Link</p>
      <div class="copy-row">
        <input id="referralLink" class="referral-input" type="text" value="{{ $link }}" readonly>
        <button id="copyReferral" class="copy-button">Copy Link</button>
      </div>
    </div>

    <div class="earnings-row">
      <p class="earnings-title">Total Referral Earnings</p>
      <p class="earnings-amount">${{ number_format($totalEarned, 2) }}</p>
    </div>

    @if ($referrals->isEmpty())
      <div class="empty-state">You haven’t invited anyone yet.</div>
    @else
      <div class="ref-list">
        @foreach ($referrals as $r)
          <div class="ref-item">
            <div class="ref-left">
              <div>{{ $r->name }} @if($r->username) <small class="ref-meta">({{ $r->username }})</small>@endif</div>
              <div class="ref-meta">{{ $r->email }}</div>
            </div>
            <div style="text-align:right">
              <div class="ref-meta">Invested: ${{ number_format($r->investments()->sum('amount'), 2) }}</div>
              <div class="ref-meta">Joined {{ $r->created_at->diffForHumans() }}</div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var copyReferral = document.getElementById('copyReferral');
    var referralLink = document.getElementById('referralLink');
    if (!copyReferral || !referralLink) return;

    copyReferral.addEventListener('click', function () {
      referralLink.select();
      referralLink.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(referralLink.value).then(function () {
        var prev = copyReferral.textContent;
        copyReferral.textContent = 'Copied';
        setTimeout(function () { copyReferral.textContent = prev; }, 1800);
      }).catch(function () {
        // fallback
        alert('Copy your link: ' + referralLink.value);
      });
    });
  });
</script>

@endsection
