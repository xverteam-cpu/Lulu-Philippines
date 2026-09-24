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

  /* Statistics Grid */
  .summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .summary-card {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
    position: relative;
    overflow: hidden;
    text-align: center;
  }

  .summary-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border-color: #d1d5db;
  }

  .summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #14532d, #166534);
  }

  .summary-value {
    display: block;
    font-size: 32px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
  }

  .summary-label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Tabs */
  .tabs {
    display: flex;
    gap: 0;
    margin: 24px 0 0;
    border-bottom: 2px solid #e5e7eb;
  }

  .tab {
    padding: 12px 20px;
    border: none;
    background: none;
    color: #6b7280;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s ease;
  }

  .tab:hover {
    color: #1f2937;
  }

  .tab.active {
    color: #14532d;
    border-bottom-color: #14532d;
  }

  /* Panel */
  .investments-panel {
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-top: 24px;
  }

  .panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 16px;
  }

  .search-box {
    position: relative;
    display: flex;
    gap: 8px;
    margin-left: auto;
  }

  .search-box input {
    height: 36px;
    padding: 0 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    color: #374151;
    background-color: #f9fafb;
    transition: all 0.2s ease;
    min-width: 250px;
  }

  .search-box input::placeholder {
    color: #9ca3af;
  }

  .search-box input:focus {
    outline: none;
    background-color: #ffffff;
    border-color: #14532d;
    box-shadow: 0 0 0 3px rgba(196, 0, 0, 0.1);
  }

  .search-box button {
    padding: 0 14px;
    height: 36px;
    background-color: #14532d;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
  }

  .search-box button:hover {
    background-color: #a30000;
  }

  /* Table Styles */
  .table-wrap {
    overflow-x: auto;
  }

  .investments-table {
    width: 100%;
    border-collapse: collapse;
  }

  .investments-table thead {
    background-color: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
  }

  .investments-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .investments-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 13px;
    color: #374151;
  }

  .investments-table tbody tr:hover {
    background-color: #f9fafb;
  }

  .user-name {
    font-weight: 600;
    color: #1f2937;
    display: block;
    margin-bottom: 2px;
  }

  .user-email {
    font-size: 12px;
    color: #9ca3af;
  }

  /* Status Badges */
  .status-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

  .investment-link {
    display: inline-flex;
    align-items: center;
    color: #14532d;
    text-decoration: none;
    font-weight: 600;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
    cursor: pointer;
  }

  .investment-link:hover {
    background-color: #fef2f2;
    text-decoration: underline;
  }

  /* Pagination */
  .pagination {
    display: flex;
    justify-content: center;
    gap: 4px;
    padding: 16px 24px;
    border-top: 1px solid #f3f4f6;
  }

  .pagination a,
  .pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    color: #374151;
    background-color: #ffffff;
    transition: all 0.2s ease;
  }

  .pagination a:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
  }

  .pagination .active {
    background-color: #14532d;
    color: #ffffff;
    border-color: #14532d;
  }

  .empty-state {
    text-align: center;
    padding: 40px 24px;
    color: #9ca3af;
  }

  @media (max-width: 1024px) {
    .summary-grid {
      grid-template-columns: repeat(2, 1fr);
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

    .summary-grid {
      grid-template-columns: 1fr;
    }

    .investments-table {
      font-size: 12px;
    }

    .investments-table th,
    .investments-table td {
      padding: 10px 12px;
    }

    .search-box input {
      min-width: 100%;
    }

    .panel-header {
      flex-direction: column;
    }

    .search-box {
      margin-left: 0;
      width: 100%;
    }
  }

  @media (max-width: 480px) {
    .admin-nav {
      overflow-x: auto;
      padding: 8px 12px;
    }

    .admin-nav-btn {
      flex-shrink: 0;
      padding: 0 10px;
      height: 30px;
      font-size: 11px;
    }

    .admin-header {
      padding: 16px 12px;
    }

    .admin-title {
      font-size: 18px;
    }

    .admin-content {
      padding: 12px;
    }

    .tabs {
      overflow-x: auto;
      margin: 16px -12px 0;
      padding: 0 12px;
    }

    .tab {
      padding: 10px 12px;
      font-size: 11px;
    }
  }
</style>

<div class="admin-shell">
  @include('partials.admin-nav', ['activeAdminPage' => 'deposits'])

  <!-- Header -->
  <div class="admin-header">
    <div class="admin-header-content">
      <h1 class="admin-title">Investment Management</h1>
      <p class="admin-copy">Review and process pending user investment deposits</p>
    </div>
  </div>

  <!-- Content -->
  <div class="admin-content">
    <!-- Statistics -->
    <div class="summary-grid">
      <div class="summary-card">
        <span class="summary-value">{{ $pendingCount }}</span>
        <span class="summary-label">Pending</span>
      </div>
      <div class="summary-card">
        <span class="summary-value">{{ $approvedCount }}</span>
        <span class="summary-label">Approved</span>
      </div>
      <div class="summary-card">
        <span class="summary-value">{{ $rejectedCount }}</span>
        <span class="summary-label">Rejected</span>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="tabs">
      <button class="tab {{ $status === 'pending' ? 'active' : '' }}"
              onclick="window.location.href='{{ route('admin.investments', ['status' => 'pending']) }}'">
        Pending
      </button>
      <button class="tab {{ $status === 'approved' ? 'active' : '' }}"
              onclick="window.location.href='{{ route('admin.investments', ['status' => 'approved']) }}'">
        Approved
      </button>
      <button class="tab {{ $status === 'rejected' ? 'active' : '' }}"
              onclick="window.location.href='{{ route('admin.investments', ['status' => 'rejected']) }}'">
        Rejected
      </button>
    </div>

    <!-- Investments Panel -->
    <div class="investments-panel">
      <!-- Search -->
      <div class="panel-header">
        <form method="GET" class="search-box">
          <input type="hidden" name="status" value="{{ $status }}">
          <input type="text" name="search" placeholder="Search by user, package, email..."
                 value="{{ $search }}" required>
          <button type="submit">Search</button>
        </form>
      </div>

      <!-- Table -->
      <div class="table-wrap">
        <table class="investments-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Package</th>
              <th>Amount</th>
              <th>Payment Method</th>
              <th>Status</th>
              <th>Date</th>
              <th style="width: 80px; text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($investments as $investment)
              <tr>
                <td>
                  <span class="user-name">{{ $investment->user->name }}</span>
                  <span class="user-email">{{ $investment->user->email }}</span>
                </td>
                <td>{{ $investment->package_name }}</td>
                <td style="font-weight: 600; color: #14532d;">${{ number_format($investment->amount, 2) }}</td>
                <td>{{ str_replace('_', ' ', ucfirst($investment->payment_method)) }}</td>
                <td>
                  <span class="status-badge status-{{ $investment->status }}">
                    {{ ucfirst($investment->status) }}
                  </span>
                </td>
                <td style="white-space: nowrap;">{{ $investment->created_at->format('M d, Y') }}</td>
                <td style="text-align: center;">
                  <a href="{{ route('admin.investments.show', $investment) }}" class="investment-link">
                    View
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="empty-state">
                  No investment requests found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="pagination">
        {{ $investments->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
