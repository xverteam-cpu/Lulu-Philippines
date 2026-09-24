@extends('layouts.app')

@section('content')
@include('partials.admin-dashboard-styles')

<div class="admin-shell">
  <nav class="admin-nav">
    <div class="admin-nav-links">
      <a class="admin-nav-btn active" href="{{ route('admin.dashboard') }}">Users</a>
      <a class="admin-nav-btn" href="{{ route('admin.withdrawals') }}">Withdrawals</a>
      <a class="admin-nav-btn" href="{{ route('admin.investments', ['status' => 'pending']) }}">Deposits</a>
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
    </div>
  </nav>

  <div class="admin-header">
    <div class="admin-header-copy">
      <h1 class="admin-title">Admin Dashboard</h1>
      <p class="admin-copy">Monitor registered users and account activity.</p>
    </div>
  </div>

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

  <section class="summary-grid">
    <div class="summary-card">
      <span class="summary-label">Total Accounts</span>
      <strong class="summary-value">{{ number_format($totalUsers) }}</strong>
    </div>
    <div class="summary-card">
      <span class="summary-label">Active Now</span>
      <strong class="summary-value">{{ number_format($onlineUsersCount) }}</strong>
    </div>
    <div class="summary-card">
      <span class="summary-label">Approved Deposits</span>
      <strong class="summary-value">₱{{ number_format($approvedDepositTotal, 2) }}</strong>
    </div>
    <div class="summary-card">
      <span class="summary-label">Pending Withdrawals</span>
      <strong class="summary-value">{{ number_format($pendingWithdrawalsCount) }}</strong>
    </div>
  </section>

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

  <!-- Content Area -->
  <div class="admin-content">
    <!-- Users Panel -->
    <div class="users-panel">
      <div class="users-panel-header">
        <div class="users-panel-copy">
          <span class="section-label">User Management</span>
          <h2 class="section-title">All registered accounts</h2>
          <p class="section-subtitle">{{ $users->total() }} matching accounts</p>
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
          <div class="toolbar-actions">
            <button class="toolbar-btn" type="button">Status ▼</button>
            <button class="toolbar-btn" type="button">Date ▼</button>
            <button class="toolbar-btn" type="button">Balance ▼</button>
            <button class="toolbar-btn toolbar-btn-secondary" type="button">Export CSV</button>
          </div>
        </div>
      </div>

      <!-- Data Table -->
      <div class="table-card">
        <div class="table-card-header">Users</div>
        <div class="table-wrap">
          <table class="users-table">
            <thead>
              <tr>
                <th>USER</th>
                <th>REFERRER</th>
                <th>BALANCE</th>
                <th>STATUS</th>
                <th>REGISTERED</th>
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
                      <span class="status-badge {{ $user->isOnline() ? 'online' : 'offline' }}">
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
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="empty-state">
                    <p class="empty-message">No users found. Try adjusting your search filters.</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        {{ $users->links() }}
      </div>
    </div>
  </div>
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
        <span class="status-badge {{ $user->isOnline() ? 'online' : 'offline' }}">
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
        <div class="modal-field">
          <span class="modal-label">IP Location</span>
          <div class="modal-value">{{ $user->region ?: $user->address ?: 'Not available' }}</div>
        </div>
        <div class="modal-field modal-full-width">
          <span class="modal-label">Last Seen</span>
          <div class="modal-value">{{ $user->last_seen_at?->format('M d, Y h:i A') ?: '—' }}</div>
        </div>
      </div>

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
</script>
@endsection
