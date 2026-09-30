@extends('layouts.app')

@section('content')
<style>
  .franchise-detail-page { max-width: 1100px; margin: 0 auto; padding: 20px 0 48px; color: #172b23; }
  .franchise-detail-header { margin: 28px 0 20px; }
  .franchise-detail-header h1 { margin: 0; color: #14251c; font-size: 30px; }
  .franchise-detail-header p { margin: 7px 0 0; color: #687a70; }
  .franchise-detail-card { padding: clamp(20px, 4vw, 32px); border: 1px solid #e1e9e4; border-radius: 14px; background: #fff; box-shadow: 0 8px 24px rgba(17,47,31,.06); }
  .franchise-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 22px; }
  .franchise-detail-item.full { grid-column: 1 / -1; }
  .franchise-detail-label { display: block; margin-bottom: 6px; color: #687a70; font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; }
  .franchise-detail-value { color: #172b23; font-size: 15px; line-height: 1.6; white-space: pre-wrap; overflow-wrap: anywhere; }
  .franchise-detail-actions { margin-top: 24px; }
  .franchise-detail-actions a { display: inline-flex; align-items: center; min-height: 42px; padding: 0 15px; border: 1px solid #d9e2dc; border-radius: 10px; color: #166534; font-weight: 650; text-decoration: none; }
  .franchise-detail-actions a:hover { background: #f7faf8; }
  @media (max-width: 640px) {
    .franchise-detail-page { padding: 12px 0 32px; }
    .franchise-detail-header h1 { font-size: 24px; }
    .franchise-detail-grid { grid-template-columns: 1fr; }
    .franchise-detail-item.full { grid-column: auto; }
  }
</style>

<main class="franchise-detail-page">
  @include('partials.admin-nav', ['activeAdminPage' => 'franchises'])

  <header class="franchise-detail-header">
    <h1>{{ $application->full_name }}</h1>
    <p>Franchise application submitted {{ $application->created_at->format('M j, Y g:i A') }}</p>
  </header>

  <section class="franchise-detail-card" aria-label="Submitted application details">
    <div class="franchise-detail-grid">
      <div class="franchise-detail-item">
        <span class="franchise-detail-label">Full name</span>
        <div class="franchise-detail-value">{{ $application->full_name }}</div>
      </div>
      <div class="franchise-detail-item">
        <span class="franchise-detail-label">Email address</span>
        <div class="franchise-detail-value">{{ $application->email }}</div>
      </div>
      <div class="franchise-detail-item">
        <span class="franchise-detail-label">Phone number</span>
        <div class="franchise-detail-value">{{ $application->phone_number }}</div>
      </div>
      <div class="franchise-detail-item">
        <span class="franchise-detail-label">Preferred package</span>
        <div class="franchise-detail-value">{{ $application->preferred_package ? $application->preferred_package.' Pyeong' : 'Not specified' }}</div>
      </div>
      <div class="franchise-detail-item full">
        <span class="franchise-detail-label">Location / proposed site</span>
        <div class="franchise-detail-value">{{ $application->location }}</div>
      </div>
      <div class="franchise-detail-item full">
        <span class="franchise-detail-label">Business background</span>
        <div class="franchise-detail-value">{{ $application->business_background ?: 'Not provided' }}</div>
      </div>
      <div class="franchise-detail-item full">
        <span class="franchise-detail-label">Estimated investment capacity</span>
        <div class="franchise-detail-value">{{ $application->investment_capacity ?: 'Not provided' }}</div>
      </div>
      <div class="franchise-detail-item full">
        <span class="franchise-detail-label">Additional notes</span>
        <div class="franchise-detail-value">{{ $application->additional_notes ?: 'Not provided' }}</div>
      </div>
      @if ($application->user)
        <div class="franchise-detail-item full">
          <span class="franchise-detail-label">Linked account</span>
          <div class="franchise-detail-value">{{ $application->user->name }} ({{ $application->user->email }})</div>
        </div>
      @endif
    </div>

    <div class="franchise-detail-actions">
      <a href="{{ route('admin.franchises') }}">Back to Franchise applications</a>
    </div>
  </section>
</main>
@endsection
