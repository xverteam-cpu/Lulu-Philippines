@extends('layouts.app')

@section('content')
<style>
  .action-shell {
    max-width: 840px;
    margin: 24px auto 90px;
    padding: 18px;
  }

  .action-card {
    background: #fff;
    border-radius: 18px;
    padding: 26px;
    box-shadow: 0 16px 36px rgba(3,7,18,0.08);
    border: 1px solid rgba(3,7,18,0.04);
  }

  .action-title { font-size: 24px; font-weight: 800; margin-bottom: 10px; }
  .action-sub { color: #64748b; margin-bottom: 24px; }

  .input-group { display: grid; gap: 14px; margin-bottom: 20px; }
  .input-label { font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 0.02em; }
  .checkbox-row { display:flex; align-items:center; gap:12px; padding:14px 16px; border-radius:14px; background:#f8fafc; border:1px solid #d1d5db; }
  .checkbox-label { font-size:15px; color:#071a44; font-weight:600; }
  .primary-btn { padding: 14px 18px; border-radius: 14px; border: none; background: #166534; color: #fff; font-weight: 700; cursor: pointer; }
</style>

<main class="action-shell">
  <div class="action-card">
    <div class="action-title">Notification Settings</div>
    <div class="action-sub">Adjust how and when you receive account notifications.</div>

    <form action="#" method="post">
      @csrf
      <div class="checkbox-row">
        <input id="email_notifications" type="checkbox" checked>
        <label class="checkbox-label" for="email_notifications">Email notifications</label>
      </div>

      <div class="checkbox-row">
        <input id="account_alerts" type="checkbox" checked>
        <label class="checkbox-label" for="account_alerts">Account activity alerts</label>
      </div>

      <div class="checkbox-row">
        <input id="marketing_updates" type="checkbox">
        <label class="checkbox-label" for="marketing_updates">Marketing updates</label>
      </div>

      <button class="primary-btn" type="submit">Save preferences</button>
    </form>
  </div>
</main>
@endsection
