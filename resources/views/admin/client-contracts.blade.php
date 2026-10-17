@extends('layouts.app')

@section('content')
<style>
  .client-contracts { max-width:1100px; margin:28px auto; padding:0 16px 40px; color:#14251c; font-family:Inter,system-ui,sans-serif; }
  .client-contracts h1 { margin:0; font-size:28px; }
  .client-contracts-intro { margin:7px 0 20px; color:#687a70; }
  .client-contract-list { overflow:hidden; border:1px solid #e0e8e3; border-radius:14px; background:#fff; }
  .client-contract-row { display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:16px; padding:16px 18px; border-bottom:1px solid #edf1ee; color:inherit; }
  .client-contract-row:last-child { border-bottom:0; }
  .client-contract-row:hover { background:#f7faf8; }
  .client-contract-open { display:grid; min-width:0; grid-template-columns:1.2fr 1fr 1fr auto; align-items:center; gap:16px; color:inherit; text-decoration:none; }
  .client-contract-open:focus-visible,.client-contract-print:focus-visible { outline:3px solid #4f9f70; outline-offset:3px; }
  .client-contract-name { display:block; font-weight:750; }
  .client-contract-email,.client-contract-muted { display:block; margin-top:4px; color:#687a70; font-size:13px; }
  .client-contract-status { justify-self:start; padding:5px 9px; border-radius:999px; background:#eaf5ed; color:#17623b; font-size:12px; font-weight:750; text-transform:capitalize; }
  .client-contract-print { display:inline-flex; min-height:38px; align-items:center; justify-content:center; gap:7px; padding:0 13px; border:1px solid #cbdacf; border-radius:9px; background:#fff; color:#17623b; font-size:13px; font-weight:750; text-decoration:none; white-space:nowrap; }
  .client-contract-print:hover { border-color:#17623b; background:#f0f8f2; }
  .client-contract-empty { padding:30px 18px; color:#687a70; text-align:center; }
  .client-contract-pagination { margin-top:18px; }
  @media(max-width:700px) { .client-contract-row { grid-template-columns:1fr auto; gap:8px 12px; } .client-contract-open { grid-template-columns:1fr auto; gap:8px 12px; } .client-contract-open > :nth-child(2) { grid-column:1; } .client-contract-open > :nth-child(3) { grid-column:2; grid-row:1 / span 2; } .client-contract-open > :nth-child(4) { grid-column:1; } }
</style>

<div class="admin-shell">
  @include('partials.admin-nav', ['activeAdminPage' => 'contracts'])
</div>

<main class="client-contracts">
  <h1>Client Contracts</h1>
  <p class="client-contracts-intro">Signed bond purchase agreements submitted by clients.</p>

  <section class="client-contract-list" aria-label="Submitted client contracts">
    @forelse ($contracts as $investment)
      <div class="client-contract-row">
        <a class="client-contract-open" href="{{ route('admin.contracts.show', $investment) }}">
          <span>
            <span class="client-contract-name">{{ $investment->agreement_details['full_name'] }}</span>
            <span class="client-contract-email">{{ $investment->user->email }}</span>
          </span>
          <span>
            <span class="client-contract-name">{{ $investment->package_name }}</span>
            <span class="client-contract-muted">${{ number_format((float) $investment->amount, 2) }} · {{ $investment->duration_days }} days</span>
          </span>
          <span class="client-contract-status">{{ $investment->status }}</span>
          <span class="client-contract-muted">{{ $investment->agreement_signed_at?->format('M j, Y g:i A') ?? 'Date unavailable' }}</span>
        </a>
        <a class="client-contract-print" href="{{ route('admin.contracts.show', ['investment' => $investment, 'print' => 1]) }}" target="_blank" rel="noopener">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v7H6z"/><path d="M18 12h.01"/></svg>
          Print
        </a>
      </div>
    @empty
      <p class="client-contract-empty">No client contracts have been submitted yet.</p>
    @endforelse
  </section>

  <div class="client-contract-pagination">{{ $contracts->links() }}</div>
</main>
@endsection
