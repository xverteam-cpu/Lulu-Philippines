@php
  $activeAdminPage = $activeAdminPage ?? null;
  $onAdminDashboard = request()->routeIs('admin.dashboard');
@endphp

<style>
  .admin-nav-links { display:flex; align-items:center; flex-wrap:wrap; gap:10px; width:100%; }
  .admin-nav form { margin:0; }
  .admin-nav .admin-nav-btn,
  .admin-nav .header-btn {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:9px;
    min-height:44px;
    padding:0 16px;
    border:1px solid #d9e2dc;
    border-radius:11px;
    background:#fff;
    color:#334155;
    font-size:13px;
    font-weight:600;
    letter-spacing:.005em;
    cursor:pointer;
    text-decoration:none;
    white-space:nowrap;
    box-shadow:0 1px 2px rgba(15,23,42,.04);
    transition:border-color .18s ease, background-color .18s ease, color .18s ease, box-shadow .18s ease, transform .18s ease;
  }
  .admin-nav .admin-nav-btn:hover,
  .admin-nav .header-btn:hover {
    border-color:#9db9a6;
    background:#f8fbf9;
    color:#14532d;
    box-shadow:0 4px 12px rgba(20,83,45,.08);
    transform:translateY(-1px);
  }
  .admin-nav .admin-nav-btn.active,
  .admin-nav .header-btn:not(.header-btn-secondary) {
    border-color:#166534;
    background:#166534;
    color:#fff;
    box-shadow:0 4px 10px rgba(22,101,52,.15);
  }
  .admin-nav .admin-nav-btn.active:hover,
  .admin-nav .header-btn:not(.header-btn-secondary):hover {
    border-color:#14532d;
    background:#14532d;
    color:#fff;
  }
  .admin-nav .header-btn-secondary { background:#fff; color:#334155; }
  .admin-nav .nav-icon { display:inline-flex; width:16px; height:16px; flex:0 0 16px; color:#166534; }
  .admin-nav .active .nav-icon,
  .admin-nav .header-btn:not(.header-btn-secondary) .nav-icon { color:#d7f0df; }
  .admin-nav .nav-icon svg { display:block; width:16px; height:16px; }
  .admin-nav .header-btn-secondary:hover .nav-icon,
  .admin-nav .admin-nav-btn:not(.active):hover .nav-icon { color:#14532d; }
  @media (max-width:768px) {
    .admin-nav .admin-nav-btn,
    .admin-nav .header-btn { min-height:38px; padding:0 12px; font-size:12px; }
  }
</style>

<nav class="admin-nav">
  <div class="admin-nav-links">
    <a class="admin-nav-btn {{ $activeAdminPage === 'users' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
      <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20v-1.5a4.5 4.5 0 0 0-4.5-4.5h-4A4.5 4.5 0 0 0 3 18.5V20"/><circle cx="9.5" cy="7" r="4"/><path d="M16 3.3a4 4 0 0 1 0 7.4M21 20v-1.5a4.5 4.5 0 0 0-3.5-4.4"/></svg></span>
      <span>Users</span>
    </a>
    <a class="admin-nav-btn {{ $activeAdminPage === 'withdrawals' ? 'active' : '' }}" href="{{ route('admin.withdrawals') }}">
      <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M4 17v3h16v-3"/></svg></span>
      <span>Withdrawals</span>
    </a>
    <a class="admin-nav-btn {{ $activeAdminPage === 'deposits' ? 'active' : '' }}" href="{{ route('admin.investments', ['status' => 'pending']) }}">
      <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 9h18"/><path d="M7 15h3"/></svg></span>
      <span>Deposits</span>
    </a>

    <form method="POST" action="{{ route('admin.backup') }}" style="display:inline-flex;">
      @csrf
      <button class="header-btn admin-backup-btn" type="submit">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h13l3 3v13H4z"/><path d="M8 4v6h8V4"/><path d="M8 20v-6h8v6"/></svg></span>
        <span>Backup</span>
      </button>
    </form>

    <form method="POST" action="{{ route('admin.send-promotional-email') }}" style="display:inline-flex;">
      @csrf
      <button class="header-btn header-btn-secondary" type="submit">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
        <span>Send Promotional Email</span>
      </button>
    </form>

    @if ($onAdminDashboard)
      <button class="header-btn header-btn-secondary" type="button" onclick="toggleModal('sendPackageModal', true)">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 8 9 5 9-5"/><path d="M3 8v9l9 5 9-5V8"/><path d="M12 13v9"/></svg></span>
        <span>Send Package</span>
      </button>
      <button class="header-btn header-btn-secondary" type="button" onclick="toggleModal('manageSlotsModal', true)">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg></span>
        Manage Slots
      </button>
      <button class="header-btn header-btn-secondary" type="button" onclick="toggleModal('sendFundsModal', true)">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h13l-3-3"/><path d="m20 7-3 3"/><path d="M17 17H4l3 3"/><path d="m4 17 3-3"/></svg></span>
        Send Funds
      </button>
    @else
      <a class="header-btn header-btn-secondary" href="{{ route('admin.dashboard') }}#sendPackageModal">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 8 9 5 9-5"/><path d="M3 8v9l9 5 9-5V8"/><path d="M12 13v9"/></svg></span>
        <span>Send Package</span>
      </a>
      <a class="header-btn header-btn-secondary" href="{{ route('admin.dashboard') }}#manageSlotsModal">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/></svg></span>
        Manage Slots
      </a>
      <a class="header-btn header-btn-secondary" href="{{ route('admin.dashboard') }}#sendFundsModal">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h13l-3-3"/><path d="m20 7-3 3"/><path d="M17 17H4l3 3"/><path d="m4 17 3-3"/></svg></span>
        Send Funds
      </a>
    @endif

    <form action="{{ route('logout') }}" method="post" style="display:inline-flex;">
      @csrf
      <button class="header-btn header-btn-secondary" type="submit">
        <span class="nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/></svg></span>
        <span>Logout</span>
      </button>
    </form>
  </div>
</nav>
