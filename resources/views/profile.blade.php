@extends('layouts.app')

@section('content')
<style>
  :root {
    --bg: #f8fafc;
    --panel: #ffffff;
    --border: #e5e7eb;
    --text: #111827;
    --muted: #6b7280;
    --accent: #166534;
    --accent-soft: #fde8ec;
    --success: #16a34a;
  }

  body {
    background: var(--bg);
  }

  .profile-shell {
    max-width: 1180px;
    margin: 24px auto 80px;
    padding: 18px;
  }

  .profile-panel {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.04);
  }

  .profile-topbar {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 24px;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 999px;
    border: 1px solid var(--border);
    text-decoration: none;
    color: var(--text);
    font-weight: 600;
    background: #fff;
    transition: background 180ms ease, border-color 180ms ease;
  }

  .back-link:hover {
    background: #f8fafc;
    border-color: #d1d5db;
  }

  .page-title {
    font-size: 30px;
    font-weight: 700;
    line-height: 1.15;
    color: var(--text);
  }

  .page-sub {
    margin-top: 6px;
    font-size: 15px;
    color: var(--muted);
  }

  .profile-grid {
    display: grid;
    grid-template-columns: minmax(270px, 0.32fr) minmax(0, 0.68fr);
    gap: 24px;
    align-items: start;
  }

  .sidebar {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .panel-card {
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 20px;
    background: #fff;
  }

  .profile-summary {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .profile-header {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .profile-avatar {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent) 0%, #f43f5e 100%);
    color: #fff;
    display: grid;
    place-items: center;
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 0.02em;
  }

  .profile-name {
    font-size: 19px;
    font-weight: 700;
    color: var(--text);
  }

  .profile-role,
  .profile-email {
    font-size: 14px;
    color: var(--muted);
    margin-top: 3px;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    background: #f0fdf4;
    color: #166534;
    font-size: 13px;
    font-weight: 600;
  }

  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--success);
    box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.12);
  }

  .section-stack {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }

  .section-block {
    padding-top: 18px;
    border-top: 1px solid var(--border);
  }

  .section-block:first-child {
    padding-top: 0;
    border-top: none;
  }

  .section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 12px;
  }

  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    font-size: 15px;
  }

  .info-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .info-label {
    color: var(--muted);
    font-weight: 500;
    min-width: 110px;
  }

  .info-value {
    color: var(--text);
    font-weight: 600;
    text-align: right;
  }

  .action-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    color: var(--text);
    text-decoration: none;
    transition: background 180ms ease, transform 180ms ease;
    cursor: pointer;
  }

  .action-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .action-row:hover {
    background: #f8fafc;
    transform: translateY(-1px);
  }

  .action-content {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .action-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    color: var(--accent);
    background: var(--accent-soft);
  }

  .action-label {
    font-weight: 600;
    font-size: 15px;
  }

  .action-chevron {
    color: var(--muted);
    font-size: 18px;
    line-height: 1;
  }

  .logout-form {
    margin-top: 8px;
  }

  .logout-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border-radius: 999px;
    border: 1px solid rgba(200, 16, 46, 0.24);
    background: transparent;
    color: var(--accent);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: background 180ms ease, border-color 180ms ease;
  }

  .logout-btn:hover {
    background: var(--accent-soft);
    border-color: rgba(200, 16, 46, 0.36);
  }

  .muted-copy {
    color: var(--muted);
    font-size: 14px;
    line-height: 1.6;
  }

  @media (max-width: 900px) {
    .profile-grid {
      grid-template-columns: 1fr;
    }

    .profile-shell {
      padding: 12px;
    }

    .profile-panel {
      padding: 20px;
    }
  }
</style>

<main class="profile-shell">
  <div class="profile-panel">
    <div class="profile-topbar">
      <a href="{{ route('dashboard') }}" class="back-link">
        <span aria-hidden="true">←</span>
        <span>Dashboard</span>
      </a>
      <div>
        <div class="page-title">My Account</div>
        <div class="page-sub">Manage your account settings and security.</div>
      </div>
    </div>

    @php $user = auth()->user(); @endphp

    <div class="profile-grid">
      <aside class="sidebar">
        <div class="panel-card profile-summary">
          <div class="profile-header">
            <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div>
              <div class="profile-name">{{ $user->name }}</div>
              <div class="profile-role">{{ $user->account_type ?? 'Corporate Account' }}</div>
              <div class="profile-email">{{ $user->email }}</div>
              <div class="status-badge">
                <span class="status-dot"></span>
                {{ $user->isOnline() ? 'Online' : 'Offline' }}
              </div>
            </div>
          </div>

          <form class="logout-form" action="{{ route('logout') }}" method="post">
            @csrf
            <button class="logout-btn" type="submit">Log out</button>
          </form>
        </div>
      </aside>

      <section class="section-stack">
        <div class="panel-card">
          <div class="section-title">Account Information</div>
          <div class="info-row">
            <span class="info-label">Username</span>
            <span class="info-value">{{ $user->username ?: ($user->name) }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Referral</span>
            <span class="info-value">{{ $user->referred_by ? ($user->referrer?->username ?? 'Linked') : '—' }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Region</span>
            <span class="info-value">{{ $user->region ?: 'Not set' }}</span>
          </div>
        </div>

        <div class="panel-card">
          <div class="section-title">Security</div>
          <div class="info-row">
            <span class="info-label">Password</span>
            <span class="info-value">••••••••</span>
          </div>
          <div class="info-row">
            <span class="info-label">Last Login</span>
            <span class="info-value">{{ $user->last_seen_at?->diffForHumans() ?? '—' }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Member Since</span>
            <span class="info-value">{{ $user->created_at?->format('F Y') }}</span>
          </div>
        </div>

        <div class="panel-card">
          <div class="section-title">Preferences</div>
          <div class="section-block">
            <div class="info-row">
              <span class="info-label">Notifications</span>
              <span class="info-value">Enabled</span>
            </div>
            <div class="info-row">
              <span class="info-label">Language</span>
              <span class="info-value">English</span>
            </div>
            <div class="info-row">
              <span class="info-label">Theme</span>
              <span class="info-value">System</span>
            </div>
          </div>
        </div>

        <div class="panel-card">
          <div class="section-title">Quick Actions</div>
          <div class="section-block">
            <a href="{{ route('profile.edit') }}" class="action-row">
              <span class="action-content">
                <span class="action-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                  </svg>
                </span>
                <span class="action-label">Edit Profile</span>
              </span>
              <span class="action-chevron">›</span>
            </a>
            <a href="{{ route('profile.password') }}" class="action-row">
              <span class="action-content">
                <span class="action-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                  </svg>
                </span>
                <span class="action-label">Change Password</span>
              </span>
              <span class="action-chevron">›</span>
            </a>
            <a href="{{ route('profile.notifications') }}" class="action-row">
              <span class="action-content">
                <span class="action-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5"></path>
                    <path d="M9 17a3 3 0 0 0 6 0"></path>
                  </svg>
                </span>
                <span class="action-label">Notification Settings</span>
              </span>
              <span class="action-chevron">›</span>
            </a>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>
@endsection
