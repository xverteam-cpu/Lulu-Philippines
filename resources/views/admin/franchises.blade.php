@extends('layouts.app')

@section('content')
<style>
  .franchises-page { max-width: 1400px; margin: 0 auto; padding: 20px 0 48px; color: #172b23; }
  .franchises-header { margin: 28px 0 20px; }
  .franchises-header h1 { margin: 0; color: #14251c; font-size: 30px; }
  .franchises-header p { margin: 7px 0 0; color: #687a70; }
  .franchises-panel { overflow: hidden; border: 1px solid #e1e9e4; border-radius: 14px; background: #fff; box-shadow: 0 8px 24px rgba(17,47,31,.06); }
  .franchises-table-wrap { overflow-x: auto; }
  .franchises-table { width: 100%; border-collapse: collapse; text-align: left; }
  .franchises-table th { padding: 13px 16px; background: #f7faf8; color: #64756b; font-size: 11px; letter-spacing: .06em; text-transform: uppercase; }
  .franchises-table td { padding: 15px 16px; border-top: 1px solid #edf1ee; color: #33443a; font-size: 14px; }
  .franchises-table a { color: #166534; font-weight: 650; text-decoration: none; }
  .franchises-table a:hover { text-decoration: underline; }
  .franchises-empty { padding: 42px 20px; color: #687a70; text-align: center; }
  .franchises-pagination { padding: 16px; border-top: 1px solid #edf1ee; }
  @media (max-width: 700px) {
    .franchises-page { padding: 12px 0 32px; }
    .franchises-header h1 { font-size: 24px; }
  }
</style>

<main class="franchises-page">
  @include('partials.admin-nav', ['activeAdminPage' => 'franchises'])

  <header class="franchises-header">
    <h1>Franchise applications</h1>
    <p>{{ $applications->total() }} application{{ $applications->total() === 1 ? '' : 's' }} submitted</p>
  </header>

  <section class="franchises-panel" aria-label="Franchise applications">
    @if ($applications->isEmpty())
      <p class="franchises-empty">No franchise applications have been submitted yet.</p>
    @else
      <div class="franchises-table-wrap">
        <table class="franchises-table">
          <thead>
            <tr>
              <th>Applicant</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Package</th>
              <th>Submitted</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($applications as $application)
              <tr>
                <td><a href="{{ route('admin.franchises.show', $application) }}">{{ $application->full_name }}</a></td>
                <td>{{ $application->email }}</td>
                <td>{{ $application->phone_number }}</td>
                <td>{{ $application->preferred_package }} Pyeong</td>
                <td>{{ $application->created_at->format('M j, Y g:i A') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if ($applications->hasPages())
        <div class="franchises-pagination">{{ $applications->links() }}</div>
      @endif
    @endif
  </section>
</main>
@endsection
