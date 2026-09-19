@extends('layouts.app')

@section('content')
<style>
  .user-shell {
    max-width:920px;
    margin:32px auto;
  }
  .user-card {
    position:relative;
    overflow:hidden;
    padding:26px;
    border-radius:16px;
    background:#ffffff;
    box-shadow:0 10px 28px rgba(0,0,0,0.12);
  }
  .user-card::before {
    content:'';
    position:absolute;
    inset:0 0 auto 0;
    height:10px;
    background:linear-gradient(90deg, #14532d, #166534, #d6a84f);
  }
  .user-top {
    display:flex;
    justify-content:space-between;
    gap:16px;
    margin-bottom:20px;
  }
  .user-title {
    margin:0;
    color:#14532d;
    font-size:28px;
    line-height:34px;
  }
  .back-link {
    color:#14532d;
    font-weight:700;
    text-decoration:none;
  }
  .detail-grid {
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:14px;
  }
  .detail-item {
    padding:14px;
    border:1px solid #dcefe8;
    border-radius:10px;
    background:#f4fbf7;
  }
  .detail-label {
    display:block;
    margin-bottom:5px;
    color:#14532d;
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
  }
  .detail-value {
    color:#001a33;
    font-size:15px;
    line-height:22px;
    overflow-wrap:anywhere;
  }
  @media (max-width:640px) {
    .user-top,
    .detail-grid {
      display:block;
    }
    .detail-item {
      margin-top:12px;
    }
  }
</style>

<div class="user-shell">
  <div class="user-card">
    <div class="user-top">
      <div>
        <h1 class="user-title">{{ $managedUser->name }}</h1>
        <p style="margin:8px 0 0;color:#667085;">Registered {{ $managedUser->created_at?->format('M d, Y h:i A') ?: '-' }}</p>
      </div>
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a class="back-link" href="{{ route('admin.dashboard') }}">Back to users</a>
        @if(! $managedUser->is_admin)
          <form action="{{ route('admin.users.restrict', $managedUser) }}" method="POST" style="display:inline;" onsubmit="return confirm('Restrict this user from accessing the website?');">
            @csrf
            <button type="submit" style="background:#111827; color:#fff; border:none; border-radius:8px; padding:10px 14px; font-weight:700; cursor:pointer;">
              {{ $managedUser->is_restricted ? 'Already restricted' : 'Restrict IP' }}
            </button>
          </form>
          <form action="{{ route('admin.users.destroy', $managedUser) }}" method="POST" onsubmit="return confirm('Delete this user account? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" style="background:#14532d; color:#fff; border:none; border-radius:8px; padding:10px 14px; font-weight:700; cursor:pointer;">
              Delete account
            </button>
          </form>
        @endif
      </div>
    </div>

    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Email</span>
        <span class="detail-value">{{ $managedUser->email }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Phone</span>
        <span class="detail-value">{{ $managedUser->phone ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Region</span>
        <span class="detail-value">{{ $managedUser->region ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Address</span>
        <span class="detail-value">{{ $managedUser->address ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Online Status</span>
        <span class="detail-value">{{ $managedUser->isOnline() ? 'Online' : 'Offline' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Last Seen</span>
        <span class="detail-value">{{ $managedUser->last_seen_at?->format('M d, Y h:i A') ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Last IP Address</span>
        <span class="detail-value">{{ $managedUser->last_ip_address ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Account Role</span>
        <span class="detail-value">{{ $managedUser->is_admin ? 'Admin' : 'Partner user' }}</span>
      </div>
      <div class="detail-item" style="grid-column:1 / -1;">
        <span class="detail-label">Message</span>
        <span class="detail-value">{{ $managedUser->message ?: 'No message submitted' }}</span>
      </div>
    </div>
  </div>
</div>
@endsection
