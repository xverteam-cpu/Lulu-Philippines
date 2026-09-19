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
    --warning: #d97706;
    --danger: #dc2626;
  }

  body {
    background: var(--bg) !important;
    color: var(--text);
  }

  .history-shell {
    max-width: 1180px;
    margin: 24px auto 80px;
    padding: 18px;
  }

  .history-panel {
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.04);
  }

  .history-topbar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
  }

  .history-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
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

  .history-title {
    margin: 0;
    font-size: 30px;
    font-weight: 700;
    line-height: 1.15;
    color: var(--text);
  }

  .history-copy {
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 15px;
    line-height: 1.6;
  }

  .summary-grid {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    margin-bottom: 24px;
  }

  .summary-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 18px 20px;
  }

  .summary-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
  }

  .summary-value {
    margin-top: 8px;
    font-size: 24px;
    font-weight: 700;
    color: var(--text);
  }

  .summary-note {
    margin-top: 6px;
    font-size: 13px;
    color: var(--muted);
    line-height: 1.5;
  }

  .accordion {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .accordion-item {
    border: 1px solid var(--border);
    border-radius: 18px;
    background: #fff;
    overflow: hidden;
  }

  .accordion-head {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 16px 20px;
    border: none;
    background: transparent;
    cursor: pointer;
    text-align: left;
    transition: background 180ms ease;
  }

  .accordion-head:hover {
    background: #f8fafc;
  }

  .accordion-head .section-title {
    font-size: 18px;
    margin: 0;
    color: var(--text);
    font-weight: 600;
  }

  .accordion-head .section-subtitle {
    margin: 4px 0 0;
    font-size: 13px;
    color: var(--muted);
  }

  .accordion-toggle {
    width: 30px;
    height: 30px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: #f3f4f6;
    color: var(--muted);
    font-size: 18px;
    font-weight: 600;
    flex-shrink: 0;
  }

  .accordion-body {
    display: none;
    padding: 0 20px 20px;
  }

  .accordion-body.open {
    display: block;
  }

  .history-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
  }

  .history-table th,
  .history-table td {
    padding: 13px 0;
    text-align: left;
    border-bottom: 1px solid var(--border);
  }

  .history-table th {
    color: var(--muted);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .history-table td {
    color: var(--text);
    font-size: 14px;
  }

  .history-table tbody tr:last-child td {
    border-bottom: none;
  }

  .badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-approved {
    background: #ecfdf3;
    color: #166534;
  }

  .badge-pending {
    background: #fffbeb;
    color: #b45309;
  }

  .badge-rejected {
    background: #fef2f2;
    color: #b91c1c;
  }

  .empty-state {
    padding: 24px;
    border-radius: 16px;
    background: #f8fafc;
    border: 1px dashed var(--border);
    text-align: center;
    color: var(--muted);
  }

  .empty-state strong {
    display: block;
    margin-bottom: 6px;
    color: var(--text);
    font-weight: 600;
  }

  .claim-btn {
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

  .claim-btn:hover {
    background: var(--accent-soft);
    border-color: rgba(200, 16, 46, 0.36);
  }

  @media (max-width: 900px) {
    .history-shell {
      padding: 12px;
    }

    .history-panel {
      padding: 20px;
    }

    .summary-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<main class="history-shell">
  <div class="history-panel">
    <div class="history-topbar">
      <div class="history-header-left">
        <a href="{{ route('dashboard') }}" class="back-link" aria-label="Go to dashboard">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="16" height="16">
            <path d="M15 6L9 12L15 18" stroke="#166534" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Dashboard</span>
        </a>
        <div>
          <h1 class="history-title">Account History</h1>
          <p class="history-copy">Review your investment, withdrawal, and reward activity in a cleaner, more structured view.</p>
        </div>
      </div>
    </div>

    <div class="summary-grid">
      <div class="summary-card">
        <div class="summary-label">Daily Interest Earned</div>
        <div class="summary-value">${{ number_format($dailyInterest, 2) }}</div>
        <p class="summary-note">Sum of daily interest from all approved investments.</p>
      </div>
      <div class="summary-card">
        <div class="summary-label">Investment History</div>
        <div class="summary-value">{{ $investments->count() }}</div>
        <p class="summary-note">Total investment records for your account.</p>
      </div>
      <div class="summary-card">
        <div class="summary-label">Withdrawal Activity</div>
        <div class="summary-value">{{ $withdrawals->count() }}</div>
        <p class="summary-note">Total withdrawal requests made from this account.</p>
      </div>
    </div>

    <div class="accordion">
      <div class="accordion-item">
        <button type="button" class="accordion-head" data-target="investments">
          <div>
            <h2 class="section-title">Investment History</h2>
            <p class="section-subtitle">All account investments, their amounts, and approval status.</p>
          </div>
          <span class="accordion-toggle">+</span>
        </button>
        <div id="investments" class="accordion-body">
          @if ($investments->isEmpty())
            <div class="empty-state">
              <strong>No investment transactions yet.</strong>
              <p>Invest in a package to begin earning daily interest and see it listed here.</p>
            </div>
          @else
            <table class="history-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Package</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Daily Interest</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($investments as $investment)
                  <tr>
                    <td>{{ $investment->created_at->format('M d, Y') }}</td>
                    <td>{{ $investment->package_name }}</td>
                    <td>${{ number_format($investment->amount, 2) }}</td>
                    <td>
                      <span class="badge badge-{{ $investment->status }}">
                        {{ ucfirst($investment->status) }}
                      </span>
                    </td>
                    <td>
                      @if ($investment->status === 'approved')
                        ${{ number_format($investment->dailyInterestAmount(), 2) }}
                      @else
                        —
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>

      <div class="accordion-item">
        <button type="button" class="accordion-head" data-target="withdrawals">
          <div>
            <h2 class="section-title">Withdrawal History</h2>
            <p class="section-subtitle">A record of all withdrawal activity from your account.</p>
          </div>
          <span class="accordion-toggle">+</span>
        </button>
        <div id="withdrawals" class="accordion-body">
          @if ($withdrawals->isEmpty())
            <div class="empty-state">
              <strong>No withdrawals yet.</strong>
              <p>Request a withdrawal and the transaction will appear here once submitted.</p>
            </div>
          @else
            <table class="history-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Description</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($withdrawals as $withdrawal)
                  <tr>
                    <td>{{ $withdrawal->created_at->format('M d, Y') }}</td>
                    <td>${{ number_format($withdrawal->amount, 2) }}</td>
                    <td>
                      @if ($withdrawal->bank_name === 'Welcome Bonus')
                        <strong>$5 Sign Up Bonus</strong><br>
                        <span style="color:#166534;font-size:12px;">Welcome reward</span>
                      @else
                        {{ ucwords(str_replace('_', ' ', $withdrawal->payment_method)) }}
                      @endif
                    </td>
                    <td>
                      <span class="badge badge-{{ $withdrawal->status }}">
                        {{ ucfirst($withdrawal->status) }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>

      <div class="accordion-item">
        <button type="button" class="accordion-head" data-target="daily-interest">
          <div>
            <h2 class="section-title">Daily Interest History</h2>
            <p class="section-subtitle">Daily interest amounts from approved investments.</p>
          </div>
          <span class="accordion-toggle">+</span>
        </button>
        <div id="daily-interest" class="accordion-body">
          @php
            $dailyList = $investments->where('status', 'approved');
          @endphp

          @if ($dailyList->isEmpty())
            <div class="empty-state">
              <strong>No daily interest records yet.</strong>
              <p>Approved investments will show daily interest here.</p>
            </div>
          @else
            <table class="history-table">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Package</th>
                  <th>Daily Interest</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($dailyList as $inv)
                  <tr>
                    <td>{{ $inv->created_at->format('M d, Y') }}</td>
                    <td>{{ $inv->package_name }}</td>
                    <td>${{ number_format($inv->dailyInterestAmount(), 2) }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>
      </div>

      <div class="accordion-item">
        <button type="button" class="accordion-head" data-target="rewards">
          <div>
            <h2 class="section-title">Rewards History</h2>
            <p class="section-subtitle">Signup bonuses and other rewards applied to your account.</p>
          </div>
          <span class="accordion-toggle">+</span>
        </button>
        <div id="rewards" class="accordion-body">
          @php
            $rewards = $withdrawals->filter(fn($w) => ($w->bank_name ?? '') === 'Welcome Bonus' || ($w->account_number ?? '') === 'signup-bonus');
            $user = Auth::user();
            $claimed = ! empty($user->signup_bonus_claimed_at);
          @endphp

          @if (! $claimed)
            <div style="padding:16px 0 0;">
              <button id="claim-signup-bonus" class="claim-btn" type="button">Claim $5 Sign Up Bonus</button>
            </div>
          @endif

          @if ($rewards->isEmpty())
            <div class="empty-state" id="rewards-empty" style="margin-top: 12px;">
              <strong>No rewards yet.</strong>
              <p>Any signup bonus or rewards will appear here.</p>
            </div>
          @endif

          <table class="history-table" id="rewards-table" style="margin-top:12px; @if($rewards->isEmpty()) display:none; @endif">
            <thead>
              <tr>
                <th>Date</th>
                <th>Amount</th>
                <th>Details</th>
              </tr>
            </thead>
            <tbody id="rewards-table-body">
              @foreach ($rewards as $r)
                <tr>
                  <td>{{ $r->created_at->format('M d, Y') }}</td>
                  <td>${{ number_format($r->amount, 2) }}</td>
                  <td>{{ $r->bank_name === 'Welcome Bonus' ? 'Signup Bonus' : ($r->account_holder ?? '') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <template id="reward-row-template">
            <tr>
              <td class="r-date"></td>
              <td class="r-amount"></td>
              <td class="r-details"></td>
            </tr>
          </template>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  (function(){
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.accordion-head').forEach(function(btn){
          btn.addEventListener('click', function(){
            var targetId = btn.getAttribute('data-target');
            var body = document.getElementById(targetId);
            if (!body) return;
            document.querySelectorAll('.accordion-body').forEach(function(b){
              if (b.id !== targetId) {
                b.classList.remove('open');
                var head = document.querySelector('.accordion-head[data-target="' + b.id + '"]');
                if (head) head.querySelector('.accordion-toggle').textContent = '+';
              }
            });

            var isOpen = body.classList.toggle('open');
            var toggle = btn.querySelector('.accordion-toggle');
            if (toggle) toggle.textContent = isOpen ? '−' : '+';
          });
        });
    });
  })();

  (function(){
    document.addEventListener('DOMContentLoaded', function(){
      var claimBtn = document.getElementById('claim-signup-bonus');
      if (!claimBtn) return;
      claimBtn.addEventListener('click', function(){
        if (!confirm('Claim the $5 signup bonus?')) return;
        claimBtn.disabled = true;
        claimBtn.textContent = 'Claiming...';

        fetch("{{ route('rewards.claim-signup-bonus') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({})
        }).then(function(res){
          if (!res.ok) throw new Error('Request failed');
          return res.text();
        }).then(function(){
          var tpl = document.getElementById('reward-row-template');
          var clone = tpl.content.cloneNode(true);
          var now = new Date();
          var dateStr = now.toLocaleString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
          clone.querySelector('.r-date').textContent = dateStr;
          clone.querySelector('.r-amount').textContent = '$5.00';
          clone.querySelector('.r-details').textContent = 'Signup Bonus';

          var tbody = document.getElementById('rewards-table-body');
          if (tbody) {
            tbody.insertBefore(clone, tbody.firstChild);
          }

          var table = document.getElementById('rewards-table');
          var empty = document.getElementById('rewards-empty');
          if (table) table.style.display = '';
          if (empty) empty.style.display = 'none';

          claimBtn.textContent = 'Claimed';
          claimBtn.disabled = true;
        }).catch(function(err){
          console.error(err);
          claimBtn.disabled = false;
          claimBtn.textContent = 'Claim $5 Sign Up Bonus';
          alert('Unable to claim signup bonus right now.');
        });
      });
    });
  })();
</script>

@endsection
