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

<div class="admin-shell">
  @include('partials.admin-nav', ['activeAdminPage' => 'users'])
</div>

<div class="user-shell">
  <div class="user-card">
    <div class="user-top">
      <div>
        <h1 class="user-title">{{ $managedUser->name }}</h1>
        <p style="margin:8px 0 0;color:#667085;">Registered {{ $managedUser->created_at?->format('M d, Y h:i A') ?: '-' }}</p>
      </div>
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <a class="back-link" href="{{ route('admin.dashboard') }}">Back to users</a>
        <button
          type="button"
          id="copy-user-referral-link"
          data-referral-link="{{ route('signup', ['ref' => $managedUser->referral_code]) }}"
          style="background:#0e8a5a;color:#fff;border:none;border-radius:8px;padding:10px 14px;font-weight:700;cursor:pointer;"
        >Copy referral link</button>
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
        <span class="detail-value">{{ $managedUser->phone ?: 'Not provided' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Phone/device type (detected)</span>
        <span class="detail-value">{{ $managedUser->last_device ?: 'Not recorded yet' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Phone Verification</span>
        <span class="detail-value">{{ $managedUser->phone_verified_at?->format('M d, Y h:i A') ?: 'SMS verification temporarily disabled' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Profile Region (self-reported)</span>
        <span class="detail-value">{{ $managedUser->region ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">IP Location (approximate)</span>
        <span class="detail-value">
          {{ $managedUser->ip_location ?: 'Not available' }}
          @if ($managedUser->ip_location_updated_at)
            <small>Checked {{ $managedUser->ip_location_updated_at->format('M d, Y h:i A') }}</small>
          @endif
        </span>
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
        <span class="detail-label">Last Seen (Manila time)</span>
        <span
          class="detail-value"
          data-last-seen-user-id="{{ $managedUser->id }}"
          data-time="{{ $managedUser->last_seen_at?->toIso8601String() }}"
        >{{ $managedUser->last_seen_at?->copy()->timezone('Asia/Manila')->format('M d, Y h:i A') ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Last IP Address</span>
        <span class="detail-value">{{ $managedUser->last_ip_address ?: '-' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Account Role</span>
        <span class="detail-value">{{ $managedUser->is_admin ? 'Admin' : 'Partner user' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Referral Code</span>
        <span class="detail-value">{{ $managedUser->referral_code ?: 'Not assigned' }}</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Referred By</span>
        @if ($managedUser->referrer)
          <span class="detail-value">
            <a href="{{ route('admin.users.show', $managedUser->referrer) }}">
              {{ $managedUser->referrer->name }}{{ $managedUser->referrer->username ? ' (@'.$managedUser->referrer->username.')' : '' }}
            </a>
          </span>
        @else
          <span class="detail-value">{{ $managedUser->is_admin ? 'Admin account' : 'Missing referrer' }}</span>
        @endif
      </div>
      <div class="detail-item" style="grid-column:1 / -1;">
        <span class="detail-label">Message</span>
        <span class="detail-value">{{ $managedUser->message ?: 'No message submitted' }}</span>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('copy-user-referral-link')?.addEventListener('click', async function () {
  const button = this;
  const link = button.dataset.referralLink;
  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(link);
    } else {
      const input = document.createElement('textarea');
      input.value = link;
      input.setAttribute('readonly', '');
      input.style.position = 'fixed';
      input.style.opacity = '0';
      document.body.appendChild(input);
      input.select();
      const copied = document.execCommand('copy');
      input.remove();
      if (!copied) throw new Error('Clipboard copy was rejected.');
    }
    button.textContent = 'Referral link copied';
    window.setTimeout(() => { button.textContent = 'Copy referral link'; }, 2500);
  } catch (error) {
    console.error('Could not copy referral link.', error);
    button.textContent = 'Copy failed';
    window.setTimeout(() => { button.textContent = 'Copy referral link'; }, 2500);
  }
});
</script>
<script>
  (function () {
    var lastSeen = document.querySelector('[data-last-seen-user-id]');
    if (!lastSeen) return;

    var activityUrl = @json(route('admin.user-activity'));
    var userId = lastSeen.dataset.lastSeenUserId;

    function refreshLastSeen() {
      var url = new URL(activityUrl, window.location.origin);
      url.searchParams.append('user_ids[]', userId);

      fetch(url, {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
      }).then(function (response) {
        if (!response.ok) throw new Error('Last-seen refresh failed with status ' + response.status);
        return response.json();
      }).then(function (activity) {
        var timestamp = activity.last_seen_at[userId];
        if (!timestamp) return;
        lastSeen.dataset.time = timestamp;
        lastSeen.textContent = new Intl.DateTimeFormat('en-PH', {
          timeZone: 'Asia/Manila',
          month: 'short',
          day: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        }).format(new Date(timestamp));
      }).catch(function (error) {
        console.error(error);
      });
    }

    refreshLastSeen();
    window.setInterval(refreshLastSeen, 15000);
  })();
</script>
@endsection
