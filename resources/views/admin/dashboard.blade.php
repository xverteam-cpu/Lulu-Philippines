@extends('layouts.app')

@section('content')
@include('partials.admin-dashboard-styles')

<style>
  .admin-page-container {
    width: 100%;
    max-width: none;
    padding: 0;
  }

  .admin-shell {
    width: 100%;
    max-width: 1680px;
    box-sizing: border-box;
    margin: 0 auto;
    padding: 20px clamp(16px, 3vw, 48px) 48px;
  }

  .admin-shell .admin-nav {
    position: sticky;
    top: 0;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
  }

  .admin-shell .admin-nav-links {
    gap: 8px;
  }

  .users-page {
    margin-top: 28px;
  }

  .users-page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
    margin-bottom: 22px;
  }

  .users-page-title {
    margin: 0;
    color: #0f172a;
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -.03em;
  }

  .users-page-subtitle {
    margin: 6px 0 0;
    color: #64748b;
    font-size: 14px;
  }

  .users-page .search-box {
    flex: 0 1 520px;
    min-width: min(320px, 100%);
  }

  .users-page .search-input {
    width: 100%;
    box-sizing: border-box;
    background: #fff;
  }

  .users-page .toolbar-actions {
    display: none;
  }

  .users-page .table-wrap {
    overflow-x: auto;
    border-top: 1px solid #dbe2ea;
    border-bottom: 1px solid #dbe2ea;
    background: #fff;
  }

  .users-page .users-table {
    min-width: 900px;
  }

  .users-page .users-table thead {
    background: #f1f5f9;
  }

  .users-page .users-table tbody tr:hover,
  .users-page .user-row:hover,
  .users-page .user-row:focus-visible {
    background: #f8fafc;
  }

  .users-page .user-details-btn {
    min-height: 34px;
    padding: 0 12px;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    background: #fff;
    color: #334155;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
  }

  .users-page .user-details-btn:hover {
    border-color: #166534;
    color: #14532d;
  }

  .users-page .pagination {
    padding: 18px 0;
    border: 0;
  }

  @media (max-width: 768px) {
    .admin-page-container {
      padding: 0;
    }

    .admin-shell {
      padding: 12px 12px 32px;
    }

    .admin-shell .admin-nav {
      padding: 10px;
    }

    .admin-shell .admin-nav-links {
      flex-wrap: nowrap;
      overflow-x: auto;
      width: 100%;
      padding-bottom: 2px;
    }

    .users-page {
      margin-top: 22px;
    }

    .users-page-header {
      align-items: stretch;
      flex-direction: column;
      gap: 16px;
    }

    .users-page-title {
      font-size: 24px;
    }

    .users-page .search-box {
      flex: auto;
      min-width: 0;
      width: 100%;
    }
  }
</style>

<div class="admin-shell">
  @include('partials.admin-nav', ['activeAdminPage' => 'users'])

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="error-list">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Send Package Modal -->
  <div id="sendPackageModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card">
      <div class="modal-header">
        <div>
          <h2 class="modal-title">Send Package to User</h2>
          <p class="modal-subtitle">Choose a user and package to gift instantly.</p>
        </div>
        <button type="button" class="modal-close" onclick="toggleModal('sendPackageModal', false)" aria-label="Close send package modal">&times;</button>
      </div>

      <form method="POST" action="{{ route('admin.send-package') }}" class="modal-form">
        @csrf

        <div class="modal-field">
          <label class="modal-label" for="send-package-user">Select User</label>
          <select id="send-package-user" name="user_id" required class="modal-select">
            <option value="">-- Choose a user --</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
          </select>
        </div>

        <div class="modal-field">
          <label class="modal-label" for="adminPackageSelect">Select Package</label>
          <select id="adminPackageSelect" name="package" required class="modal-select">
            <option value="">-- Choose a package --</option>
            @foreach($packages as $packageKey => $package)
              <option value="{{ $packageKey }}">{{ $package['name'] }} — ${{ number_format($package['price'], 2, '.', ',') }} — {{ number_format($package['daily_interest_rate'], 2) }}% daily</option>
            @endforeach
          </select>
        </div>

        <div class="package-quick-row">
          @foreach($packages as $packageKey => $package)
            <button type="button" class="package-quick-btn" data-package-key="{{ $packageKey }}">
              {{ $package['name'] }}
            </button>
          @endforeach
        </div>

        <div class="modal-actions">
          <button type="submit" class="modal-action modal-action-primary">✓ Send Package</button>
          <button type="button" class="modal-action modal-action-secondary" onclick="toggleModal('sendPackageModal', false)">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Manage Package Slots Modal -->
  <div id="manageSlotsModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card">
      <div class="modal-header">
        <div>
          <h2 class="modal-title">Manage Package Slot Counts</h2>
          <p class="modal-subtitle">Update remaining slots for active packages.</p>
        </div>
        <button type="button" class="modal-close" onclick="toggleModal('manageSlotsModal', false)" aria-label="Close manage slots modal">&times;</button>
      </div>

      <form method="POST" action="{{ route('admin.package-slots.update') }}" class="modal-form">
        @csrf

        <div class="modal-grid">
          @foreach ($packages as $packageKey => $package)
            <div class="modal-field">
              <label class="modal-label" for="slots-{{ $packageKey }}">{{ $package['name'] }} Remaining Slots</label>
              <input
                id="slots-{{ $packageKey }}"
                type="number"
                name="slots[{{ $packageKey }}]"
                min="0"
                value="{{ $packageSlots[$packageKey] ?? 0 }}"
                required
                class="modal-input"
              >
            </div>
          @endforeach
        </div>

        <div class="modal-actions">
          <button type="submit" class="modal-action modal-action-primary">✓ Update Slots</button>
          <button type="button" class="modal-action modal-action-secondary" onclick="toggleModal('manageSlotsModal', false)">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Send Funds Modal -->
  <div id="sendFundsModal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card">
      <div class="modal-header">
        <div>
          <h2 class="modal-title">Send Funds to User</h2>
          <p class="modal-subtitle">Credit a user's account quickly and safely.</p>
        </div>
        <button type="button" class="modal-close" onclick="toggleModal('sendFundsModal', false)" aria-label="Close send funds modal">&times;</button>
      </div>

      <form method="POST" action="{{ route('admin.send-funds') }}" class="modal-form">
        @csrf

        <div class="modal-field">
          <label class="modal-label" for="send-funds-user">Select User</label>
          <select id="send-funds-user" name="user_id" required class="modal-select">
            <option value="">-- Choose a user --</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
          </select>
        </div>

        <div class="modal-field">
          <label class="modal-label" for="send-funds-amount">Amount (USD)</label>
          <input id="send-funds-amount" type="number" name="amount" step="0.01" min="0.01" required placeholder="Enter amount" class="modal-input">
        </div>

        <div class="modal-field modal-full-width">
          <label class="modal-label" for="send-funds-reason">Reason/Note</label>
          <textarea id="send-funds-reason" name="reason" placeholder="Enter reason for sending funds" rows="3" class="modal-textarea"></textarea>
        </div>

        <div class="modal-actions">
          <button type="submit" class="modal-action modal-action-primary">✓ Send Funds</button>
          <button type="button" class="modal-action modal-action-secondary" onclick="toggleModal('sendFundsModal', false)">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <section class="users-page" aria-labelledby="users-page-title">
    <header class="users-page-header">
      <div>
        <h1 class="users-page-title" id="users-page-title">User management</h1>
        <p class="users-page-subtitle">{{ $users->total() }} matching accounts</p>
      </div>
      <div class="search-box">
          <form class="search-form" method="get" action="{{ route('admin.dashboard') }}">
            <input
              class="search-input"
              name="search"
              value="{{ $search }}"
              type="search"
              placeholder="Search by name, email, phone..."
              aria-label="Search users"
            >
          </form>
      </div>
    </header>

    <div class="table-wrap">
      <table class="users-table">
        <thead>
          <tr>
            <th>USER</th>
            <th>REFERRER</th>
            <th>BALANCE</th>
            <th>STATUS</th>
            <th>REGISTERED</th>
            <th>ACTIONS</th>
          </tr>
        </thead>
        <tbody>
              @forelse ($users as $user)
                <tr
                  class="user-row"
                  tabindex="0"
                  role="button"
                  aria-label="View details for {{ $user->name }}"
                  onclick="openUserModal('userModal-{{ $user->id }}')"
                  onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openUserModal('userModal-{{ $user->id }}'); }"
                >
                  <!-- User Information -->
                  <td>
                    <div class="user-info">
                      <span class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                      <div>
                        <span class="user-cell-name">{{ $user->name }}</span>
                        <span class="user-cell-email">{{ $user->email }}</span>
                      </div>
                    </div>
                  </td>
                  <!-- Referred By -->
                  <td>
                    @php
                      $referrer = $user->referrer;
                    @endphp
                    {{ $referrer ? ($referrer->name ?: $referrer->email) : '—' }}
                  </td>
                  <!-- Available Balance -->
                  <td class="text-nowrap">
                    {{ $user->balance != null ? '$' . number_format((float) $user->balance, 2) : '$0.00' }}
                  </td>
                  <!-- Status Badges -->
                  <td>
                    <div class="status-badges">
                      <span class="status-badge {{ $user->isOnline() ? 'online' : 'offline' }}" data-user-online-status data-user-id="{{ $user->id }}">
                        {{ $user->isOnline() ? 'Online' : 'Offline' }}
                      </span>
                      @if ($user->created_at && $user->created_at->greaterThan(now()->subDay()))
                        <span class="status-badge new">New</span>
                      @endif
                      @if ($user->is_admin)
                        <span class="status-badge admin">Admin</span>
                      @endif
                    </div>
                  </td>
                  <!-- Registration Date -->
                  <td class="text-nowrap">
                    {{ $user->created_at?->format('M d, Y') ?: '—' }}
                  </td>
                  <td>
                    <button
                      type="button"
                      class="user-details-btn"
                      onclick="event.stopPropagation(); openUserModal('userModal-{{ $user->id }}')"
                    >View details</button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="empty-state">
                    <p class="empty-message">No users found. Try adjusting your search filters.</p>
                  </td>
                </tr>
              @endforelse
        </tbody>
      </table>
    </div>

    <div class="pagination">
      {{ $users->links() }}
    </div>
  </section>
</div>

@foreach ($users as $user)
  <template id="userModal-{{ $user->id }}">
    <div class="modal-card">
      <button class="modal-close" type="button" onclick="closeUserModal()" aria-label="Close user details">&times;</button>
      <div class="modal-header">
        <div>
          <h2 class="modal-title">{{ $user->name }}</h2>
          <p class="modal-subtitle">Registered {{ $user->created_at?->format('M d, Y h:i A') ?: '—' }}</p>
        </div>
        <span class="status-badge {{ $user->isOnline() ? 'online' : 'offline' }}" data-user-online-status data-user-id="{{ $user->id }}">
          {{ $user->isOnline() ? 'Online' : 'Offline' }}
        </span>
      </div>

      <div class="modal-grid">
        <div class="modal-field">
          <span class="modal-label">Email</span>
          <div class="modal-value">{{ $user->email }}</div>
        </div>
        <div class="modal-field">
          <span class="modal-label">Phone</span>
          <div class="modal-value">{{ $user->phone ?: '—' }}</div>
        </div>
        <div class="modal-field">
          <span class="modal-label">Referred By</span>
          <div class="modal-value">
            @php $referrer = $user->referrer; @endphp
            {{ $referrer ? ($referrer->name ?: $referrer->email) : '—' }}
          </div>
        </div>
        @php
          $approvedInvestments = $user->investments()->where('status', 'approved')->get();
          $investmentIncomeRecords = $approvedInvestments;
          $totalCreditedInterest = $approvedInvestments->sum(fn($investment) => $investment->creditedInterest());
          $displayBalance = (float) ($user->balance ?? 0);
        @endphp

        <div class="modal-field">
          <span class="modal-label">Available Balance</span>
          <div class="modal-value">${{ number_format($displayBalance, 2) }}</div>
        </div>
        <div class="modal-field">
          <span class="modal-label">Balance Breakdown</span>
          <div class="modal-value">Base: ${{ number_format($displayBalance - $totalCreditedInterest, 2) }} | Credited Interest: ${{ number_format($totalCreditedInterest, 2) }}</div>
        </div>
        <div class="modal-field">
          <span class="modal-label">Region</span>
          <div class="modal-value">{{ $user->region ?: '—' }}</div>
        </div>
        <div class="modal-field">
          <span class="modal-label">Address</span>
          <div class="modal-value">{{ $user->address ?: '—' }}</div>
        </div>
        <div class="modal-field">
          <span class="modal-label">IP Address</span>
          <div class="modal-value">{{ $user->last_ip_address ?: '—' }}</div>
        </div>
        @php $blockedIp = $user->last_ip_address ? $blockedIps->get($user->last_ip_address) : null; @endphp
        <div class="modal-field">
          <span class="modal-label">IP Access</span>
          @if ($blockedIp)
            <div class="modal-value" style="color:#b91c1c;font-weight:700;">Blocked</div>
          @elseif ($user->last_ip_address)
            <div class="modal-value" style="color:#166534;font-weight:700;">Allowed</div>
          @else
            <div class="modal-value">No IP recorded</div>
          @endif
        </div>
        <div class="modal-field">
          <span class="modal-label">IP Location</span>
          <div class="modal-value">{{ $user->region ?: $user->address ?: 'Not available' }}</div>
        </div>
        <div class="modal-field modal-full-width">
          <span class="modal-label">Last Seen</span>
          <div class="modal-value">{{ $user->last_seen_at?->format('M d, Y h:i A') ?: '—' }}</div>
        </div>
      </div>

      @if ($blockedIp)
        <form action="{{ route('admin.blocked-ips.destroy', $blockedIp) }}" method="POST" style="margin:20px 0;">
          @csrf
          @method('DELETE')
          <button type="submit" class="admin-nav-btn" onclick="return confirm('Unblock IP address {{ $blockedIp->ip_address }}?')">Unblock IP address</button>
        </form>
      @elseif ($user->last_ip_address && ! $user->is_admin)
        <form action="{{ route('admin.users.block-ip', $user) }}" method="POST" style="margin:20px 0;">
          @csrf
          <button type="submit" class="admin-nav-btn" style="border-color:#b91c1c;color:#b91c1c;" onclick="return confirm('Block IP address {{ $user->last_ip_address }}? All requests from this IP will be denied, including access to the site before login.')">Block IP address</button>
        </form>
      @else
        <p class="empty-message" style="margin:20px 0;">An IP address must be recorded before it can be blocked.</p>
      @endif

      <details class="user-history-section">
        <summary class="user-history-title">Deposit / Investment History</summary>
        @if($user->investments()->latest()->get()->isNotEmpty())
          <div class="user-history-list">
            @foreach($user->investments()->latest()->get() as $investment)
              <div class="user-history-item">
                <strong>{{ $investment->package_name ?: 'Investment' }}</strong>
                <div class="user-history-meta">
                  <span>{{ $investment->status }}</span>
                  <span>${{ number_format((float) $investment->amount, 2) }}</span>
                  <span>{{ $investment->created_at?->format('M d, Y') ?: '—' }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="user-history-list">
            <div class="user-history-item">
              <p class="empty-message">No investment history.</p>
            </div>
          </div>
        @endif
      </details>

      <details class="user-history-section">
        <summary class="user-history-title">Withdraw History</summary>
        @if($user->withdrawals()->latest()->get()->isNotEmpty())
          <div class="user-history-list">
            @foreach($user->withdrawals()->latest()->get() as $withdrawal)
              <div class="user-history-item">
                <strong>${{ number_format((float) $withdrawal->amount, 2) }}</strong>
                <div class="user-history-meta">
                  <span>{{ $withdrawal->status }}</span>
                  <span>{{ $withdrawal->payment_method }}</span>
                  <span>{{ $withdrawal->created_at?->format('M d, Y') ?: '—' }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="user-history-list">
            <div class="user-history-item">
              <p class="empty-message">No withdrawal history.</p>
            </div>
          </div>
        @endif
      </details>

      <details class="user-history-section">
        <summary class="user-history-title">Income History</summary>
        @if($investmentIncomeRecords->isNotEmpty() || $user->referralEarnings()->latest()->get()->isNotEmpty())
          <div class="user-history-list">
            @foreach($investmentIncomeRecords as $investment)
              <div class="user-history-item">
                <strong>${{ number_format($investment->earnedInterest(), 2) }}</strong>
                <div class="user-history-meta">
                  <span>Interest earned for {{ $investment->package_name ?: 'investment' }}</span>
                  <span>{{ $investment->starts_at?->format('M d, Y') ?: '—' }}</span>
                </div>
              </div>
            @endforeach

            @foreach($user->referralEarnings()->latest()->get() as $earning)
              <div class="user-history-item">
                <strong>${{ number_format((float) $earning->amount, 2) }}</strong>
                <div class="user-history-meta">
                  <span>Referral commission</span>
                  <span>{{ $earning->created_at?->format('M d, Y') ?: '—' }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="user-history-list">
            <div class="user-history-item">
              <p class="empty-message">No income history.</p>
            </div>
          </div>
        @endif
      </details>
    </div>
  </template>
@endforeach

<script>
  (function () {
    var activityUrl = @json(route('admin.user-activity'));
    var countElement = document.querySelector('[data-online-users-count]');

    function refreshOnlineStatus() {
      fetch(activityUrl, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
      }).then(function (response) {
        if (!response.ok) throw new Error('User activity refresh failed with status ' + response.status);
        return response.json();
      }).then(function (activity) {
        var onlineIds = new Set(activity.online_user_ids.map(String));

        function updateBadges(root) {
          root.querySelectorAll('[data-user-online-status]').forEach(function (badge) {
            var online = onlineIds.has(badge.dataset.userId);
            badge.textContent = online ? 'Online' : 'Offline';
            badge.classList.toggle('online', online);
            badge.classList.toggle('offline', !online);
          });
        }

        updateBadges(document);
        document.querySelectorAll('template').forEach(function (template) {
          updateBadges(template.content);
        });

        if (countElement) {
          countElement.textContent = Number(activity.online_users_count).toLocaleString();
        }
      }).catch(function (error) {
        console.error(error);
      });
    }

    refreshOnlineStatus();
    window.setInterval(refreshOnlineStatus, 30000);
  })();
</script>

<div id="userDetailsModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="userDetailsTitle">
  <div id="userDetailsModalBody" class="modal-card"></div>
</div>

<script>
  function toggleModal(modalId, open) {
    var modal = document.getElementById(modalId);
    if (!modal) {
      return;
    }

    if (open) {
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    } else {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }
  }

  function openUserModal(templateId) {
    var template = document.getElementById(templateId);
    var body = document.getElementById('userDetailsModalBody');
    var modal = document.getElementById('userDetailsModal');

    if (!template || !body || !modal) {
      return;
    }

    body.innerHTML = template.innerHTML;
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }

  function closeUserModal() {
    var modal = document.getElementById('userDetailsModal');
    if (modal) {
      modal.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  }

  document.addEventListener('click', function (event) {
    var modal = document.getElementById('userDetailsModal');
    if (!modal || !modal.classList.contains('is-open')) {
      return;
    }

    if (event.target === modal) {
      closeUserModal();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeUserModal();
    }
  });

  if (window.location.hash) {
    var actionModal = window.location.hash.substring(1);
    if (['sendPackageModal', 'manageSlotsModal', 'sendFundsModal'].indexOf(actionModal) !== -1) {
      toggleModal(actionModal, true);
    }
  }
</script>
@endsection
