@extends('layouts.app')

@section('content')
<style>
  .franchise-shell {
    width: min(100%, 1040px);
    margin: 0 auto;
    padding: 0 0 32px;
  }

  .franchise-apply {
    display: flex;
    justify-content: center;
    margin: 24px 0 16px;
  }

  .franchise-apply a,
  .franchise-form button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 0 22px;
    border: 0;
    border-radius: 12px;
    background: #166534;
    color: #fff;
    font: inherit;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
  }

  .franchise-form-card {
    margin: 24px auto 0;
    padding: clamp(20px, 4vw, 36px);
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
  }

  .franchise-form-card h1 {
    margin: 0;
    color: #111827;
    font-size: clamp(24px, 4vw, 32px);
  }

  .franchise-form-card > p {
    margin: 8px 0 22px;
    color: #64748b;
  }

  .franchise-form {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
  }

  .franchise-form-field {
    min-width: 0;
  }

  .franchise-form-field.full {
    grid-column: 1 / -1;
  }

  .franchise-form label {
    display: block;
    margin-bottom: 7px;
    color: #1f2937;
    font-size: 14px;
    font-weight: 650;
  }

  .franchise-form input,
  .franchise-form select,
  .franchise-form textarea {
    box-sizing: border-box;
    width: 100%;
    min-height: 46px;
    padding: 11px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 9px;
    background: #fff;
    color: #111827;
    font: inherit;
  }

  .franchise-form textarea {
    min-height: 112px;
    resize: vertical;
  }

  .franchise-form input:focus,
  .franchise-form select:focus,
  .franchise-form textarea:focus {
    border-color: #166534;
    outline: 3px solid rgba(22, 101, 52, .14);
  }

  .franchise-form .input-error {
    margin: 6px 0 0;
    color: #b91c1c;
    font-size: 13px;
  }

  .franchise-form .form-submit {
    grid-column: 1 / -1;
  }

  .franchise-form .form-submit button {
    width: 100%;
  }

  .franchise-alert {
    margin: 0 0 18px;
    padding: 13px 16px;
    border-radius: 10px;
    background: #dcfce7;
    color: #166534;
  }

  .franchise-errors {
    margin: 0 0 18px;
    padding: 13px 16px;
    border-radius: 10px;
    background: #fef2f2;
    color: #991b1b;
  }

  .franchise-header-embed {
    position: relative;
    width: 100vw;
    aspect-ratio: 100 / 54.612;
    margin-top: -18px;
    margin-left: calc(50% - 50vw);
    overflow: hidden;
    border-radius: 0;
    background: #fff;
    isolation: isolate;
  }

  .franchise-header-embed iframe {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
  }

  .franchise-header-mask {
    position: absolute;
    z-index: 1;
    top: 0;
    right: 0;
    bottom: 0;
    width: clamp(100px, 18vw, 160px);
    background: linear-gradient(165deg, #087a48, #14532d 78%);
    border-radius: 55% 0 0 0 / 18% 0 0 0;
    box-shadow: -10px 0 28px rgba(15, 23, 42, .13);
    pointer-events: auto;
    transform: scaleY(0);
    transform-origin: center bottom;
    transition: transform .62s cubic-bezier(.22, 1, .36, 1), border-radius .62s ease;
  }

  .franchise-header-embed:hover .franchise-header-mask,
  .franchise-header-embed:focus-within .franchise-header-mask,
  .franchise-header-embed.is-raised .franchise-header-mask {
    border-radius: 12% 0 0 0 / 6% 0 0 0;
    transform: scaleY(1);
  }

  @media (prefers-reduced-motion: reduce) {
    .franchise-header-mask {
      transition: none;
    }
  }

  @media (max-width: 640px) {
    .franchise-shell {
      padding: 0 0 24px;
    }

    .franchise-form {
      grid-template-columns: 1fr;
    }

    .franchise-form-field.full,
    .franchise-form .form-submit {
      grid-column: auto;
    }
  }
</style>

<div class="franchise-shell">
  <div class="franchise-header-embed">
    <iframe
      src="https://www.canva.com/design/DAHWplRk3gs/uzYiq8rFZP0ax_GEVfbMiA/view?embed"
      title="Get Started franchise introduction"
      loading="lazy"
      allowfullscreen></iframe>
    <div class="franchise-header-mask" aria-hidden="true"></div>
  </div>
  <div class="franchise-apply">
    <a href="#franchise-application">Apply for a Franchise</a>
  </div>

  <section class="franchise-form-card" id="franchise-application" aria-labelledby="franchise-form-title">
    <h1 id="franchise-form-title">Franchise Application</h1>
    <p>Complete the form and our franchise team will review your application.</p>

    @if (session('status'))
      <div class="franchise-alert" role="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="franchise-errors" role="alert">
        Please review the highlighted fields and submit the form again.
      </div>
    @endif

    <form class="franchise-form" method="POST" action="{{ route('franchise-applications.store') }}">
      @csrf

      <div class="franchise-form-field">
        <label for="full_name">Full Name <span aria-hidden="true">*</span></label>
        <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" autocomplete="name" maxlength="255" required>
        @error('full_name') <p class="input-error">{{ $message }}</p> @enderror
      </div>

      <div class="franchise-form-field">
        <label for="email">Email Address <span aria-hidden="true">*</span></label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>
        @error('email') <p class="input-error">{{ $message }}</p> @enderror
      </div>

      <div class="franchise-form-field">
        <label for="phone_number">Phone Number <span aria-hidden="true">*</span></label>
        <input id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}" autocomplete="tel" maxlength="40" required>
        @error('phone_number') <p class="input-error">{{ $message }}</p> @enderror
      </div>

      @if (in_array(request('package'), ['40', '60'], true))
        <input type="hidden" name="preferred_package" value="{{ request('package') }}">
      @endif

      <div class="franchise-form-field full">
        <label for="location">Location / Proposed Site <span aria-hidden="true">*</span></label>
        <input id="location" name="location" type="text" value="{{ old('location') }}" maxlength="255" required>
        @error('location') <p class="input-error">{{ $message }}</p> @enderror
      </div>

      <div class="franchise-form-field full">
        <label for="business_background">Business Background</label>
        <textarea id="business_background" name="business_background" maxlength="5000">{{ old('business_background') }}</textarea>
        @error('business_background') <p class="input-error">{{ $message }}</p> @enderror
      </div>

      <div class="franchise-form-field full">
        <label for="investment_capacity">Estimated Investment Capacity</label>
        <input id="investment_capacity" name="investment_capacity" type="text" value="{{ old('investment_capacity') }}" maxlength="100">
        @error('investment_capacity') <p class="input-error">{{ $message }}</p> @enderror
      </div>

      <div class="franchise-form-field full">
        <label for="additional_notes">Additional Notes</label>
        <textarea id="additional_notes" name="additional_notes" maxlength="5000">{{ old('additional_notes') }}</textarea>
        @error('additional_notes') <p class="input-error">{{ $message }}</p> @enderror
      </div>

      <div class="form-submit">
        <button type="submit">Submit Application</button>
      </div>
    </form>
  </section>
</div>

@if ($errors->any())
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var formSection = document.getElementById('franchise-application');
      if (formSection) formSection.scrollIntoView();
    });
  </script>
@endif
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var franchiseHeader = document.querySelector('.franchise-header-embed');
    if (!franchiseHeader) return;

    function raiseHeaderMask() {
      franchiseHeader.classList.add('is-raised');
    }

    franchiseHeader.addEventListener('pointerenter', raiseHeaderMask, { once: true });
    franchiseHeader.addEventListener('focusin', raiseHeaderMask, { once: true });
    franchiseHeader.addEventListener('touchstart', raiseHeaderMask, { once: true, passive: true });
  });
</script>
@endsection
