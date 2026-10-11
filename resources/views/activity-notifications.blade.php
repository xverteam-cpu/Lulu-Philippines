@extends('layouts.app')

@section('content')
<style>
  .activity-page { --activity-ink:#132822; --activity-muted:#63736d; --activity-line:#dae6df; --activity-green:#08764f; max-width:760px; margin:0 auto; padding:28px 28px 64px; color:var(--activity-ink); font:15px/1.5 Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
  .activity-page *, .activity-page *::before, .activity-page *::after { box-sizing:border-box; }
  .activity-header { position:sticky; top:0; z-index:5; display:grid; min-height:60px; grid-template-columns:40px minmax(0,1fr) 40px; align-items:center; gap:12px; margin:-28px -28px 24px; padding:0 16px; background:rgba(242,245,243,.94); backdrop-filter:saturate(180%) blur(18px); -webkit-backdrop-filter:saturate(180%) blur(18px); }
  .activity-header-link { position:relative; display:grid; width:40px; height:40px; place-items:center; border:0; border-radius:50%; color:var(--activity-ink); background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.12); text-decoration:none; }
  .activity-header-link:focus-visible, .activity-mark-read:focus-visible { outline:3px solid #a4dec1; outline-offset:2px; }
  .activity-brand { display:flex; align-items:center; justify-self:center; gap:8px; color:var(--activity-ink); text-decoration:none; white-space:nowrap; font:800 15px/1 "Plus Jakarta Sans",Inter,system-ui,sans-serif; }
  .activity-brand img { width:26px; height:26px; border-radius:8px; object-fit:contain; }
  .activity-title-row { display:flex; align-items:center; justify-content:space-between; gap:16px; margin:0 0 20px; }
  .activity-title { margin:0; font:800 28px/1.1 "Plus Jakarta Sans",Inter,system-ui,sans-serif; letter-spacing:-.04em; }
  .activity-subtitle { margin:7px 0 0; color:var(--activity-muted); font-size:14px; }
  .activity-mark-read { padding:9px 12px; border:1px solid var(--activity-line); border-radius:11px; color:#075a3b; background:#fff; font-size:12px; font-weight:750; cursor:pointer; white-space:nowrap; }
  .activity-flash { margin-bottom:16px; padding:12px 14px; border-radius:12px; color:#075a3b; background:#e6f7ef; font-size:13px; }
  .activity-list { display:grid; gap:10px; }
  .activity-item { display:flex; align-items:flex-start; gap:13px; padding:16px; border:1px solid var(--activity-line); border-radius:17px; color:inherit; background:#fff; box-shadow:0 5px 18px rgba(11,57,37,.04); text-decoration:none; }
  .activity-item.unread { border-color:#acd9c0; background:#f8fcf9; }
  .activity-dot { width:9px; height:9px; flex:0 0 9px; margin-top:6px; border-radius:50%; background:#d5dfd9; }
  .activity-item.unread .activity-dot { background:#0e8a5a; box-shadow:0 0 0 4px rgba(14,138,90,.1); }
  .activity-copy { min-width:0; flex:1; }
  .activity-item-title { display:block; font-size:14px; font-weight:800; }
  .activity-description { display:block; margin-top:4px; color:var(--activity-muted); font-size:13px; line-height:1.5; }
  .activity-meta { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:6px 12px; margin-top:9px; color:#809089; font-size:11px; }
  .activity-amount { color:#075a3b; font-size:12px; font-weight:800; }
  .activity-empty { padding:34px 20px; border:1px dashed #cbd9d1; border-radius:18px; color:var(--activity-muted); background:#fff; text-align:center; }
  .activity-badge { position:absolute; top:-5px; right:-5px; display:grid; min-width:19px; height:19px; place-items:center; padding:0 5px; border:2px solid #f2f5f3; border-radius:999px; color:#fff; background:#c94335; font:800 10px/1 Inter,system-ui,sans-serif; }
  @media (max-width:520px) {
    .activity-page { padding:12px 14px 44px; }
    .activity-header { margin:-12px -14px 22px; }
    .activity-title { font-size:25px; }
    .activity-item { padding:14px; }
  }
</style>

<main class="activity-page">
  <header class="activity-header">
    <a class="activity-header-link" href="{{ route('dashboard') }}" aria-label="Back to dashboard">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
    </a>
    <a class="activity-brand" href="{{ route('dashboard') }}" aria-label="LuLu Philippines dashboard">
      <img src="{{ asset('logo.png') }}" alt=""><span>Account</span>
    </a>
    <a class="activity-header-link" href="{{ route('notifications.index') }}" aria-label="Notifications{{ $unreadCount ? ', '.$unreadCount.' unread' : '' }}">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
      @if ($unreadCount > 0)<span class="activity-badge" aria-hidden="true">{{ min($unreadCount, 99) }}</span>@endif
    </a>
  </header>

  <div class="activity-title-row">
    <div>
      <h1 class="activity-title">Notifications</h1>
      <p class="activity-subtitle">Your account activity, transactions, and verification updates.</p>
    </div>
    @if ($unreadCount > 0)
      <form method="POST" action="{{ route('notifications.read_all') }}">
        @csrf
        @foreach ($activities as $activity)
          @if (! in_array($activity['id'], $readIds, true))
            <input type="hidden" name="ids[]" value="{{ $activity['id'] }}">
          @endif
        @endforeach
        <button class="activity-mark-read" type="submit">Mark all read</button>
      </form>
    @endif
  </div>

  @if (session('status'))
    <div class="activity-flash" role="status">{{ session('status') }}</div>
  @endif

  <section class="activity-list" aria-label="Recent activity">
    @forelse ($activities as $activity)
      @php($isUnread = ! in_array($activity['id'], $readIds, true))
      <a class="activity-item{{ $isUnread ? ' unread' : '' }}" href="{{ $activity['href'] }}">
        <span class="activity-dot" aria-hidden="true"></span>
        <span class="activity-copy">
          <span class="activity-item-title">{{ $activity['title'] }}</span>
          <span class="activity-description">{{ $activity['description'] }}</span>
          <span class="activity-meta">
            <time datetime="{{ $activity['occurred_at']?->toIso8601String() }}">{{ $activity['occurred_at']?->format('M j, Y · g:i A') }}</time>
            @if ($activity['amount'] !== null)
              <span class="activity-amount">{{ $activity['amount_prefix'] }}{{ number_format((float) $activity['amount'], 2) }}</span>
            @endif
          </span>
        </span>
      </a>
    @empty
      <div class="activity-empty">You don’t have any account notifications yet. New transactions and account updates will appear here.</div>
    @endforelse
  </section>
</main>
@endsection
