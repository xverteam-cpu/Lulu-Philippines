@php
  $activeAdminPage = $activeAdminPage ?? null;
  $onAdminDashboard = request()->routeIs('admin.dashboard');
@endphp

<style>
  .admin-nav-links { display:flex; align-items:center; flex-wrap:wrap; gap:12px; width:100%; }
  .admin-nav form { margin:0; }
  .admin-nav .header-btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:44px; padding:0 16px; border:1px solid #D1D5DB; border-radius:12px; background:#166534; color:#fff; font-size:14px; font-weight:600; cursor:pointer; text-decoration:none; white-space:nowrap; }
  .admin-nav .header-btn-secondary { background:#fff; color:#0F172A; }
  .admin-nav .header-btn:hover { filter:brightness(.96); }
  @media (max-width:768px) {
    .admin-nav .header-btn { min-height:32px; padding:0 10px; font-size:12px; }
  }
</style>

<nav class="admin-nav">
  <div class="admin-nav-links">
    <a class="admin-nav-btn {{ $activeAdminPage === 'users' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
      <span>👥</span>
      <span>Users</span>
    </a>
    <a class="admin-nav-btn {{ $activeAdminPage === 'withdrawals' ? 'active' : '' }}" href="{{ route('admin.withdrawals') }}">
      <span>💸</span>
      <span>Withdrawals</span>
    </a>
    <a class="admin-nav-btn {{ $activeAdminPage === 'deposits' ? 'active' : '' }}" href="{{ route('admin.investments', ['status' => 'pending']) }}">
      <span>💰</span>
      <span>Deposits</span>
    </a>

    <form method="POST" action="{{ route('admin.backup') }}" style="display:inline-flex;">
      @csrf
      <button class="header-btn admin-backup-btn" type="submit">
        <span>💾</span>
        <span>Backup</span>
      </button>
    </form>

    <form method="POST" action="{{ route('admin.send-promotional-email') }}" style="display:inline-flex;">
      @csrf
      <button class="header-btn header-btn-secondary" type="submit">
        <span>📧</span>
        <span>Send Promotional Email</span>
      </button>
    </form>

    @if ($onAdminDashboard)
      <button class="header-btn header-btn-secondary" type="button" onclick="toggleModal('sendPackageModal', true)">
        <span>📦</span>
        <span>Send Package</span>
      </button>
      <button class="header-btn header-btn-secondary" type="button" onclick="toggleModal('manageSlotsModal', true)">
        Manage Slots
      </button>
      <button class="header-btn header-btn-secondary" type="button" onclick="toggleModal('sendFundsModal', true)">
        Send Funds
      </button>
    @else
      <a class="header-btn header-btn-secondary" href="{{ route('admin.dashboard') }}#sendPackageModal">
        <span>📦</span>
        <span>Send Package</span>
      </a>
      <a class="header-btn header-btn-secondary" href="{{ route('admin.dashboard') }}#manageSlotsModal">
        Manage Slots
      </a>
      <a class="header-btn header-btn-secondary" href="{{ route('admin.dashboard') }}#sendFundsModal">
        Send Funds
      </a>
    @endif

    <form action="{{ route('logout') }}" method="post" style="display:inline-flex;">
      @csrf
      <button class="header-btn header-btn-secondary" type="submit">
        <span>🚪</span>
        <span>Logout</span>
      </button>
    </form>
  </div>
</nav>
