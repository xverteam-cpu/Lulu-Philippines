@extends('layouts.app')

@section('content')
<style>
  .shell {
    max-width:920px;
    margin:32px auto;
  }
  .card {
    position:relative;
    overflow:hidden;
    padding:26px;
    border-radius:16px;
    background:#ffffff;
    box-shadow:0 10px 28px rgba(0,0,0,0.12);
  }
  .card::before {
    content:'';
    position:absolute;
    inset:0 0 auto 0;
    height:10px;
    background:linear-gradient(90deg, #14532d, #166534, #d6a84f);
  }
  .header {
    display:flex;
    justify-content:space-between;
    gap:16px;
    margin-bottom:20px;
  }
  .title {
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
    margin-bottom:24px;
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
    word-wrap:break-word;
  }
  .status-badge {
    display:inline-block;
    padding:6px 16px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
    text-transform:uppercase;
    margin-top:5px;
  }
  .status-pending {
    background:#fef08a;
    color:#7c2d12;
  }
  .status-approved {
    background:#dcfce7;
    color:#166534;
  }
  .status-rejected {
    background:#fee2e2;
    color:#991b1b;
  }
  .section-title {
    margin:24px 0 14px;
    color:#14532d;
    font-size:18px;
    font-weight:700;
  }
  .action-buttons {
    display:flex;
    gap:12px;
    margin-top:24px;
    padding-top:24px;
    border-top:1px solid #dcefe8;
  }
  .btn {
    padding:10px 20px;
    border:none;
    border-radius:8px;
    font-size:14px;
    font-weight:600;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
    transition:all 0.2s;
  }
  .btn-approve {
    background:#dcfce7;
    color:#166534;
    border:2px solid #86efac;
  }
  .btn-approve:hover {
    background:#bbf7d0;
  }
  .btn-reject {
    background:#fee2e2;
    color:#991b1b;
    border:2px solid #fca5a5;
  }
  .btn-reject:hover {
    background:#fecaca;
  }
  .rejection-form {
    background:#f4fbf7;
    border:1px solid #dcefe8;
    border-radius:10px;
    padding:14px;
    margin-top:12px;
    display:none;
  }
  .rejection-form.show {
    display:block;
  }
  .rejection-form textarea {
    width:100%;
    padding:10px;
    border:1px solid #dcefe8;
    border-radius:6px;
    font-size:14px;
    font-family:inherit;
    resize:vertical;
    min-height:100px;
  }
  .rejection-form .form-actions {
    display:flex;
    gap:8px;
    margin-top:10px;
  }
  .rejection-form button {
    padding:8px 16px;
    background:#14532d;
    color:white;
    border:none;
    border-radius:6px;
    font-size:13px;
    font-weight:600;
    cursor:pointer;
  }
  .rejection-form button:hover {
    background:#a00000;
  }
  .rejection-form .cancel-btn {
    background:#667085;
  }
  .rejection-form .cancel-btn:hover {
    background:#4b5563;
  }
  .reason-box {
    background:#fee2e2;
    border:1px solid #fca5a5;
    border-radius:8px;
    padding:12px;
    margin-top:12px;
  }
  .reason-label {
    color:#991b1b;
    font-size:12px;
    font-weight:600;
    text-transform:uppercase;
    margin-bottom:5px;
  }
  .reason-text {
    color:#7c2d12;
    font-size:14px;
  }
  @media (max-width:640px) {
    .header {
      display:block;
    }
    .detail-grid {
      grid-template-columns:1fr;
    }
    .action-buttons {
      flex-direction:column;
    }
    .btn {
      justify-content:center;
    }
  }
</style>

<div class="shell">
  <div class="card">
    <div class="header">
      <div>
        <h1 class="title">Investment Details</h1>
      </div>
      <a class="back-link" href="{{ route('admin.investments', ['status' => $investment->status]) }}">Back to {{ $investment->status }}</a>
    </div>

    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">User Name</span>
        <span class="detail-value">{{ $investment->user->name }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Email</span>
        <span class="detail-value">{{ $investment->user->email }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Phone</span>
        <span class="detail-value">{{ $investment->user->phone ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Region</span>
        <span class="detail-value">{{ $investment->user->region ?: '-' }}</span>
      </div>
    </div>

    <div class="section-title">Investment Information</div>
    <div class="detail-grid">
      <div class="detail-item">
        <span class="detail-label">Package</span>
        <span class="detail-value">{{ $investment->package_name }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Package Price</span>
        <span class="detail-value">${{ number_format($investment->package_price, 2) }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Investment Amount</span>
        <span class="detail-value">${{ number_format($investment->amount, 2) }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Payment Method</span>
        <span class="detail-value">
          <span style="text-transform:capitalize;">
            {{ str_replace('_', ' ', $investment->payment_method) }}
          </span>
        </span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Daily Interest Rate</span>
        <span class="detail-value">{{ number_format($investment->daily_interest_rate, 3) }}%</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Duration</span>
        <span class="detail-value">{{ $investment->duration_days }} days</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Status</span>
        <span class="status-badge status-{{ $investment->status }}">
          {{ ucfirst($investment->status) }}
        </span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Submitted</span>
        <span class="detail-value">{{ $investment->created_at->format('M d, Y h:i A') }}</span>
      </div>
    </div>

    @if($investment->starts_at)
      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label">Start Date</span>
          <span class="detail-value">{{ $investment->starts_at->format('M d, Y h:i A') }}</span>
        </div>
      </div>
    @endif

    @if($investment->approver)
      <div class="section-title">Approval Information</div>
      <div class="detail-grid">
        <div class="detail-item">
          <span class="detail-label">Approved By</span>
          <span class="detail-value">{{ $investment->approver->name }}</span>
        </div>
        <div class="detail-item">
          <span class="detail-label">Approved At</span>
          <span class="detail-value">{{ $investment->approved_at->format('M d, Y h:i A') }}</span>
        </div>
      </div>

      @if($investment->rejection_reason)
        <div class="reason-box">
          <div class="reason-label">Rejection Reason</div>
          <div class="reason-text">{{ $investment->rejection_reason }}</div>
        </div>
      @endif
    @endif

    @if($investment->status === 'pending')
      <div class="action-buttons">
        <form method="POST" action="{{ route('admin.investments.approve', $investment) }}" style="flex:1;">
          @csrf
          <button type="submit" class="btn btn-approve" onclick="return confirm('Are you sure you want to approve this investment?');">
            ✓ Approve Investment
          </button>
        </form>

        <button class="btn btn-reject" onclick="toggleRejectionForm();">
          ✗ Reject Investment
        </button>
      </div>

      <div class="rejection-form" id="rejectionForm">
        <form method="POST" action="{{ route('admin.investments.reject', $investment) }}">
          @csrf
          <textarea name="rejection_reason" placeholder="Provide a reason for rejection..." required></textarea>
          <div class="form-actions">
            <button type="submit">Submit Rejection</button>
            <button type="button" class="cancel-btn" onclick="toggleRejectionForm();">Cancel</button>
          </div>
        </form>
      </div>

      @if($errors->has('rejection_reason'))
        <div style="color:#14532d;font-size:14px;margin-top:12px;">
          {{ $errors->first('rejection_reason') }}
        </div>
      @endif
    @endif
  </div>
</div>

<script>
  function toggleRejectionForm() {
    const form = document.getElementById('rejectionForm');
    form.classList.toggle('show');
    if (form.classList.contains('show')) {
      form.querySelector('textarea').focus();
    }
  }
</script>
@endsection
