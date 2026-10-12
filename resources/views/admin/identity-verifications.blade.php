@extends('layouts.app')

@section('content')
<style>
  .verification-admin-shell { max-width:980px; margin:28px auto 48px; padding:0 16px; }
  .verification-admin-heading { margin:0 0 18px; color:#14532d; font-size:26px; }
  .verification-admin-copy { margin:-8px 0 22px; color:#667085; line-height:1.5; }
  .verification-admin-status { margin-bottom:16px; padding:12px 14px; border-radius:10px; color:#075a3b; background:#e6f7ef; }
  .verification-admin-empty { padding:30px; border-radius:16px; color:#52635b; background:#fff; text-align:center; box-shadow:0 6px 24px rgba(0,0,0,.06); }
  .verification-admin-list { display:grid; gap:18px; }
  .verification-admin-card { padding:20px; border:1px solid #e2e9e5; border-radius:16px; background:#fff; box-shadow:0 6px 24px rgba(0,0,0,.06); }
  .verification-admin-card h2 { margin:0; color:#0a1f17; font-size:19px; }
  .verification-admin-meta { display:flex; flex-wrap:wrap; gap:8px 18px; margin:8px 0 16px; color:#52635b; font-size:13px; }
  .verification-admin-images { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; }
  .verification-admin-image img { display:block; width:100%; max-height:300px; object-fit:contain; border:1px solid #dfe8e2; border-radius:10px; background:#f4f8f5; }
  .verification-admin-image figcaption { margin-bottom:7px; color:#52635b; font-size:12px; font-weight:700; }
  .verification-admin-signature { margin-top:16px; }
  .verification-admin-signature img { display:block; max-width:100%; max-height:180px; padding:8px; border:1px solid #dfe8e2; border-radius:10px; background:#fff; }
  .verification-admin-signature p { margin:6px 0 0; color:#52635b; font-size:13px; }
  .verification-admin-actions { display:flex; flex-wrap:wrap; align-items:flex-end; gap:12px; margin-top:18px; }
  .verification-admin-actions form { margin:0; }
  .verification-admin-actions label { display:block; margin-bottom:6px; color:#52635b; font-size:12px; font-weight:650; }
  .verification-admin-actions textarea { display:block; width:min(400px,80vw); min-height:68px; padding:9px 10px; border:1px solid #d8e3dc; border-radius:9px; font:inherit; }
  .verification-admin-actions button { min-height:40px; padding:0 14px; border:0; border-radius:9px; color:#fff; background:#0a6a45; font-weight:700; cursor:pointer; }
  .verification-admin-actions .reject-button { background:#a92d24; }
  .verification-admin-pagination { margin-top:18px; }
  @media (max-width:520px) { .verification-admin-card { padding:15px; } .verification-admin-actions { align-items:stretch; flex-direction:column; } .verification-admin-actions textarea { width:100%; } }
</style>

<div class="admin-shell">
  @include('partials.admin-nav', ['activeAdminPage' => 'identity-verifications'])
</div>
<main class="verification-admin-shell">
  <h1 class="verification-admin-heading">Identity verifications</h1>
  <p class="verification-admin-copy">Compare each selfie with the face on the submitted ID. Approving or rejecting a request deletes the ID and selfie photos immediately.</p>

  @if (session('status'))
    <div class="verification-admin-status" role="status">{{ session('status') }}</div>
  @endif

  @forelse ($verifications as $verification)
    <article class="verification-admin-card">
      <h2>{{ $verification->user->name }}</h2>
      <div class="verification-admin-meta">
        <span>{{ $verification->user->email }}</span>
        <span><strong>ID:</strong> {{ $idTypes[$verification->id_type] ?? $verification->id_type }}</span>
        <span><strong>Submitted:</strong> {{ $verification->submitted_at?->format('M j, Y g:i A') }}</span>
      </div>
      <div class="verification-admin-images">
        <figure class="verification-admin-image">
          <figcaption>Front of ID</figcaption>
          <a href="{{ route('admin.identity-verifications.document', [$verification, 'front']) }}" target="_blank" rel="noopener noreferrer">
            <img src="{{ route('admin.identity-verifications.document', [$verification, 'front']) }}" alt="Private front image of {{ $verification->user->name }}'s identity document" referrerpolicy="no-referrer">
          </a>
        </figure>
        @if ($verification->back_image_path)
          <figure class="verification-admin-image">
            <figcaption>Back of ID</figcaption>
            <a href="{{ route('admin.identity-verifications.document', [$verification, 'back']) }}" target="_blank" rel="noopener noreferrer">
              <img src="{{ route('admin.identity-verifications.document', [$verification, 'back']) }}" alt="Private back image of {{ $verification->user->name }}'s identity document" referrerpolicy="no-referrer">
            </a>
          </figure>
        @endif
        <figure class="verification-admin-image">
          <figcaption>Selfie — compare with ID</figcaption>
          <a href="{{ route('admin.identity-verifications.document', [$verification, 'selfie']) }}" target="_blank" rel="noopener noreferrer">
            <img src="{{ route('admin.identity-verifications.document', [$verification, 'selfie']) }}" alt="Private verification selfie of {{ $verification->user->name }}" referrerpolicy="no-referrer">
          </a>
        </figure>
      </div>
      @if ($verification->user->latestSignedInvestment)
        <div class="verification-admin-signature">
          <strong>Latest signed investment agreement</strong>
          <p>{{ $verification->user->latestSignedInvestment->package_name }} · {{ $verification->user->latestSignedInvestment->agreement_signature_name }} · {{ $verification->user->latestSignedInvestment->agreement_signed_at?->format('M j, Y g:i A') }}</p>
          <img src="{{ $verification->user->latestSignedInvestment->agreement_signature_data }}" alt="Signature for {{ $verification->user->latestSignedInvestment->package_name }} investment agreement">
        </div>
      @endif
      <div class="verification-admin-actions">
        <form method="post" action="{{ route('admin.identity-verifications.approve', $verification) }}" onsubmit="return confirm('Approve this identity after comparing the selfie to the ID? Uploaded photos will be deleted immediately.');">
          @csrf
          <button type="submit">Approve</button>
        </form>
        <form method="post" action="{{ route('admin.identity-verifications.reject', $verification) }}" onsubmit="return confirm('Reject this identity? Uploaded photos will be deleted immediately.');">
          @csrf
          <label for="review-note-{{ $verification->id }}">Reason (shown to user for resubmission)</label>
          <textarea id="review-note-{{ $verification->id }}" name="review_note" maxlength="500" required></textarea>
          <button class="reject-button" type="submit">Reject</button>
        </form>
      </div>
    </article>
  @empty
    <p class="verification-admin-empty">There are no identity submissions waiting for review.</p>
  @endforelse

  <div class="verification-admin-pagination">{{ $verifications->links() }}</div>
</main>
@endsection
