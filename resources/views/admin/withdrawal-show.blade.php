@extends('layouts.app')

@section('content')
<style>
  * {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
  }

  body {
    background-color: #f5f6f8;
  }

  .admin-shell {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0;
    min-height: 100vh;
    background-color: #f5f6f8;
  }

  /* Top Navigation Bar */
  .admin-nav {
    display: flex;
    gap: 8px;
    padding: 16px 24px;
    background-color: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 100;
    flex-wrap: wrap;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }

  .admin-nav-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 36px;
    padding: 0 16px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background-color: #ffffff;
    color: #374151;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
  }

  .admin-nav-btn:hover {
    background-color: #f3f4f6;
    border-color: #14532d;
    color: #14532d;
  }

  .admin-nav-btn.active {
    background-color: #14532d;
    border-color: #14532d;
    color: #ffffff;
    box-shadow: 0 2px 4px rgba(196, 0, 0, 0.15);
  }

  /* Header Section */
  .admin-header {
    padding: 32px 24px;
    background-color: #ffffff;
    border-bottom: 1px solid #e5e7eb;
  }

  .admin-header-content {
    max-width: 1400px;
    margin: 0 auto;
  }

  .admin-title {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    letter-spacing: -0.5px;
  }

  .admin-copy {
    margin: 6px 0 0;
    font-size: 13px;
    color: #6b7280;
    font-weight: 400;
  }

  /* Content Wrapper */
  .admin-content {
    padding: 24px;
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Detail Cards */
  .detail-card {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
  }

  .detail-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .card-title {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f3f4f6;
  }

  .detail-section {
    margin-bottom: 20px;
  }

  .detail-section:last-child {
    margin-bottom: 0;
  }

  .detail-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 20px;
  }

  .detail-item {
    display: flex;
    flex-direction: column;
  }

  .detail-label {
    display: block;
    color: #6b7280;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    margin-bottom: 6px;
  }

  .detail-value {
    display: block;
    color: #1f2937;
    font-size: 15px;
    line-height: 1.5;
    font-weight: 500;
  }

  .detail-value.amount {
    font-size: 18px;
    font-weight: 700;
    color: #14532d;
  }

  /* Status Badges */
  .status-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: fit-content;
  }

  .status-pending {
    background-color: #fef08a;
    color: #713f12;
  }

  .status-approved {
    background-color: #dcfce7;
    color: #15803d;
  }

  .status-rejected {
    background-color: #fee2e2;
    color: #991b1b;
  }

  /* Buttons */
  .action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    flex-wrap: wrap;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    padding: 0 20px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    white-space: nowrap;
  }

  .btn-success {
    background-color: #10b981;
    color: #ffffff;
  }

  .btn-success:hover {
    background-color: #059669;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
  }

  .btn-danger {
    background-color: #ef4444;
    color: #ffffff;
  }

  .btn-danger:hover {
    background-color: #dc2626;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
  }

  .btn-secondary {
    background-color: #6b7280;
    color: #ffffff;
  }

  .btn-secondary:hover {
    background-color: #4b5563;
  }

  .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* Forms */
  .form-group {
    margin-bottom: 16px;
  }

  .form-label {
    display: block;
    color: #1f2937;
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 8px;
  }

  .form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    font-family: inherit;
    box-sizing: border-box;
    background-color: #ffffff;
    color: #1f2937;
    transition: all 0.2s ease;
  }

  .form-control:focus {
    outline: none;
    border-color: #14532d;
    box-shadow: 0 0 0 3px rgba(196, 0, 0, 0.1);
    background-color: #ffffff;
  }

  .form-control::placeholder {
    color: #9ca3af;
  }

  /* Alerts */
  .alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
    font-size: 13px;
  }

  .alert-success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
  }

  .alert-danger {
    background-color: #fee2e2;
    color: #7f1d1d;
    border: 1px solid #fca5a5;
  }

  .alert-info {
    background-color: #dbeafe;
    color: #0c2d6b;
    border: 1px solid #93c5fd;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #14532d;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.2s ease;
    margin-bottom: 24px;
  }

  .back-link:hover {
    gap: 10px;
  }

  @media (max-width: 1024px) {
    .detail-row {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .admin-nav {
      padding: 12px 16px;
    }

    .admin-nav-btn {
      padding: 0 12px;
      height: 32px;
      font-size: 12px;
    }

    .admin-header {
      padding: 24px 16px;
    }

    .admin-title {
      font-size: 22px;
    }

    .admin-content {
      padding: 16px;
    }

    .detail-card {
      padding: 16px;
    }

    .action-buttons {
      flex-direction: column;
    }

    .btn {
      width: 100%;
    }
  }
</style>
    background:#dcfce7;
    color:#166534;
    border:1px solid #bbf7d0;
  }
  .alert-error {
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #fecaca;
  }
  .grid-2 {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:24px;
  }
  @media (max-width:768px) {
    .admin-hero {
      flex-direction:column;
    }
    .admin-nav {
      flex-direction:column;
    }
    .admin-nav-btn {
      width:100%;
    }
    .grid-2 {
      grid-template-columns:1fr;
    }
    .action-buttons {
      flex-direction:column;
    }
    .btn {
      width:100%;
    }
  }
</style>

<div class="admin-shell">
  <nav class="admin-nav">
    <a class="admin-nav-btn" href="{{ route('admin.dashboard') }}">📊 All Users</a>
    <a class="admin-nav-btn active" href="{{ route('admin.withdrawals') }}">💸 Pending Withdrawals</a>
    <a class="admin-nav-btn" href="{{ route('admin.investments', ['status' => 'pending']) }}">💰 Pending Deposits</a>
    <form action="{{ route('logout') }}" method="post" style="display:inline;">
      @csrf
      <button class="admin-nav-btn" type="submit">🚪 Logout</button>
    </form>
  </nav>

  <div class="admin-hero">
    <div>
      <h1 class="admin-title">Withdrawal Details</h1>
      <p class="admin-copy">Review and approve or reject this withdrawal request</p>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success">
      {{ session('status') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-error">
      <ul style="margin:0;padding:0;list-style:none;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="grid-2">
    <div class="detail-card">
      <div class="detail-section">
        <span class="detail-label">Withdrawal Status</span>
        <span class="status-badge status-{{ $withdrawal->status }}">
          {{ ucfirst($withdrawal->status) }}
        </span>
      </div>

      <div class="detail-section">
        <span class="detail-label">User Information</span>
        <div style="padding:12px;background:#f9fafb;border-radius:6px;">
          <div style="margin-bottom:8px;">
            <strong style="display:block;color:#001a33;">{{ $withdrawal->user->name }}</strong>
            <span style="color:#667085;font-size:13px;">{{ $withdrawal->user->email }}</span>
          </div>
          <div>
            <strong>Phone:</strong> {{ $withdrawal->user->phone ?: '-' }}<br>
            <strong>Region:</strong> {{ $withdrawal->user->region ?: '-' }}
          </div>
        </div>
      </div>

      <div class="detail-section">
        <span class="detail-label">Withdrawal Amount</span>
        <span class="detail-value" style="color:#14532d;font-size:28px;font-weight:700;">
          ${{ number_format($withdrawal->amount, 2) }}
        </span>
      </div>

      <div class="detail-section">
        <span class="detail-label">Payment Method</span>
        <span class="detail-value">{{ str_replace('_', ' ', ucfirst($withdrawal->payment_method)) }}</span>
      </div>

      <div class="detail-section">
        <span class="detail-label">Requested Date</span>
        <span class="detail-value">{{ $withdrawal->created_at->format('M d, Y h:i A') }}</span>
      </div>
    </div>

    <div class="detail-card">
      <div class="detail-section">
        <span class="detail-label">Bank Details</span>
        <div style="padding:12px;background:#f9fafb;border-radius:6px;">
          <div style="margin-bottom:8px;">
            <strong>Bank Name:</strong> {{ $withdrawal->bank_name ?: 'Not provided' }}
          </div>
          <div style="margin-bottom:8px;">
            <strong>Account Number:</strong> {{ $withdrawal->account_number ?: 'Not provided' }}
          </div>
          <div>
            <strong>Account Holder:</strong> {{ $withdrawal->account_holder ?: 'Not provided' }}
          </div>
        </div>
      </div>

      @if ($withdrawal->approver)
        <div class="detail-section">
          <span class="detail-label">Approved By</span>
          <div style="padding:12px;background:#f9fafb;border-radius:6px;">
            <strong style="display:block;color:#001a33;">{{ $withdrawal->approver->name }}</strong>
            <span style="color:#667085;font-size:13px;">{{ $withdrawal->approver->email }}</span><br>
            <span style="color:#667085;font-size:12px;">{{ $withdrawal->approved_at?->format('M d, Y h:i A') ?: '-' }}</span>
          </div>
        </div>
      @endif

      @if ($withdrawal->rejection_reason)
        <div class="detail-section">
          <span class="detail-label">Rejection Reason</span>
          <div style="padding:12px;background:#fee2e2;border-radius:6px;color:#991b1b;">
            {{ $withdrawal->rejection_reason }}
          </div>
        </div>
      @endif
    </div>
  </div>

  @if ($withdrawal->status === 'pending')
    <div class="detail-card">
      <div style="border-bottom:1px solid #dcefe8;padding-bottom:16px;margin-bottom:16px;">
        <h3 style="margin:0;color:#001a33;font-size:16px;">Take Action</h3>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
        <form method="POST" action="{{ route('admin.withdrawals.approve', $withdrawal) }}">
          @csrf
          <button type="submit" class="btn btn-primary" style="width:100%;">✓ Approve Withdrawal</button>
        </form>

        <button class="btn btn-danger" style="width:100%;" onclick="document.getElementById('rejectForm').style.display='block';">
          ✗ Reject Withdrawal
        </button>
      </div>

      <form id="rejectForm" method="POST" action="{{ route('admin.withdrawals.reject', $withdrawal) }}"
            style="display:none;border-top:1px solid #dcefe8;padding-top:16px;margin-top:16px;">
        @csrf
        <div class="form-group">
          <label class="form-label">Rejection Reason</label>
          <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Enter the reason for rejection..."
                    required></textarea>
          @error('rejection_reason')
            <span style="color:#dc3545;font-size:12px;margin-top:4px;display:block;">{{ $message }}</span>
          @enderror
        </div>
        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-danger">Confirm Rejection</button>
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('rejectForm').style.display='none';">
            Cancel
          </button>
        </div>
      </form>
    </div>
  @endif

  <div class="detail-card" style="text-align:center;">
    <a href="{{ route('admin.withdrawals') }}" class="btn btn-secondary">← Back to Withdrawals</a>
  </div>
</div>
@endsection
