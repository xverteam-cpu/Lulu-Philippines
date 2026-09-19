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
  .input-field { width: 100%; padding: 14px 16px; border-radius: 14px; border: 1px solid #d1d5db; background: #f8fafc; font-size: 15px; }
  .primary-btn { padding: 14px 18px; border-radius: 14px; border: none; background: #166534; color: #fff; font-weight: 700; cursor: pointer; }
</style>

<main class="action-shell">
  <div class="action-card">
    <div class="action-title">Change Password</div>
    <div class="action-sub">Protect your account by updating your password regularly.</div>

    <form action="#" method="post">
      @csrf
      <div class="input-group">
        <label class="input-label" for="current_password">Current password</label>
        <input class="input-field" id="current_password" name="current_password" type="password">
      </div>

      <div class="input-group">
        <label class="input-label" for="password">New password</label>
        <input class="input-field" id="password" name="password" type="password">
      </div>

      <div class="input-group">
        <label class="input-label" for="password_confirmation">Confirm new password</label>
        <input class="input-field" id="password_confirmation" name="password_confirmation" type="password">
      </div>

      <button class="primary-btn" type="submit">Update password</button>
    </form>
  </div>
</main>
@endsection
