@extends('layouts.app')

@section('content')
<style>
  :root {
    --fr-bg: #f2f5f3;
    --fr-ink: #0a1f17;
    --fr-muted: #64756d;
    --fr-line: #e2e9e5;
    --fr-green: #0e8a5a;
    --fr-deep: #04321f;
    --fr-mint: #3ee0a1;
  }

  body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 24px 0;
    color: var(--fr-ink);
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9;
  }

  .container {
    width: 390px;
    max-width: 100%;
    margin: 0 auto;
    padding: 0;
  }

  .franchise-phone {
    position: relative;
    width: 100%;
    min-height: min(844px, calc(100vh - 48px));
    overflow: hidden;
    border-radius: 36px;
    background: var(--fr-bg);
    box-shadow: 0 0 0 8px #0d1411, 0 0 0 9px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4);
  }

  .franchise-screen {
    min-height: inherit;
    max-height: calc(100vh - 48px);
    overflow-y: auto;
    scrollbar-width: none;
    overscroll-behavior: contain;
  }

  .franchise-screen::-webkit-scrollbar { display: none; }

  .franchise-chrome {
    position: sticky;
    top: 0;
    z-index: 5;
    padding: 10px 16px;
    border-bottom: 1px solid rgba(10, 31, 23, .06);
    background: rgba(242, 245, 243, .94);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
  }

  .franchise-nav {
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
  }

  .franchise-back,
  .franchise-brand {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
  }

  .franchise-back {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 2px rgba(6, 40, 28, .08), inset 0 0 0 1px rgba(10, 31, 23, .05);
  }

  .franchise-brand {
    gap: 8px;
    justify-self: center;
    color: var(--fr-ink);
    white-space: nowrap;
    font: 800 15px/1 "Plus Jakarta Sans", Inter, sans-serif;
  }

  .franchise-brand-mark {
    width: 26px;
    height: 26px;
    border-radius: 8px;
    object-fit: contain;
  }

  .franchise-page-label {
    color: #64756d;
    font: 700 11px/1 Inter, sans-serif;
    white-space: nowrap;
  }

  .franchise-main { padding: 6px 20px 0; }
  .franchise-eyebrow {
    color: #8a9892;
    font: 650 10.5px/1 Inter, sans-serif;
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .franchise-title {
    margin: 8px 0 0;
    color: var(--fr-ink);
    font: 800 28px/1.08 "Plus Jakarta Sans", Inter, sans-serif;
    letter-spacing: -.035em;
  }

  .franchise-lede {
    margin: 10px 0 0;
    color: #4b5b54;
    font: 450 14px/1.5 Inter, sans-serif;
  }

  .franchise-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    margin-top: 18px;
    border-radius: 28px;
    color: #fff;
    background:
      radial-gradient(120% 80% at 100% 0%, rgba(62, 224, 161, .5), transparent 55%),
      radial-gradient(80% 60% at 0% 100%, rgba(198, 243, 107, .22), transparent 60%),
      linear-gradient(160deg, #0b6a47 0%, #054a31 45%, #022418 100%);
    box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(4, 30, 20, .38), inset 0 1px 0 rgba(255, 255, 255, .22);
  }

  .franchise-hero::before {
    position: absolute;
    z-index: -1;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.12'/%3E%3C/svg%3E");
    content: "";
    opacity: .35;
    pointer-events: none;
  }

  .franchise-store-art { display: block; width: 100%; height: auto; padding: 12px 20px 0; }
  .franchise-hero-copy { padding: 4px 20px 20px; }
  .franchise-hero h2 {
    margin: 0;
    color: #fff;
    font: 800 24px/1.1 "Plus Jakarta Sans", Inter, sans-serif;
    letter-spacing: -.03em;
  }

  .franchise-hero p {
    margin: 8px 0 0;
    color: rgba(232, 255, 244, .78);
    font: 450 13.5px/1.5 Inter, sans-serif;
  }

  .franchise-highlights { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-top: 16px; }
  .franchise-highlight {
    display: flex;
    min-height: 74px;
    flex-direction: column;
    align-items: flex-start;
    gap: 9px;
    padding: 11px 10px 12px;
    border-radius: 16px;
    color: #f0fff8;
    background: linear-gradient(180deg, rgba(255, 255, 255, .16), rgba(255, 255, 255, .06));
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25), inset 0 0 0 1px rgba(255, 255, 255, .1);
    font: 600 11.5px/1.25 Inter, sans-serif;
  }

  .franchise-highlight svg {
    box-sizing: content-box;
    padding: 6px;
    border-radius: 9px;
    color: #7bf2c2;
    background: rgba(62, 224, 161, .16);
  }

  .franchise-section-head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin: 30px 2px 12px;
  }

  .franchise-section-head h2 {
    margin: 0;
    color: var(--fr-ink);
    font: 750 19px/1.1 "Plus Jakarta Sans", Inter, sans-serif;
    letter-spacing: -.025em;
  }

  .franchise-section-meta { color: #8a9892; font: 600 11.5px/1 Inter, sans-serif; }
  .franchise-steps,
  .franchise-form {
    margin: 0;
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18), inset 0 0 0 1px rgba(10, 31, 23, .04);
  }

  .franchise-steps { padding: 8px 18px; list-style: none; }
  .franchise-steps li { position: relative; display: flex; gap: 14px; padding: 12px 0; }
  .franchise-steps li:not(:last-child)::after {
    position: absolute;
    bottom: -10px;
    left: 15px;
    width: 2px;
    background: repeating-linear-gradient(180deg, #d5e0da 0 4px, transparent 4px 8px);
    content: "";
    top: 46px;
  }

  .franchise-step-dot {
    z-index: 1;
    display: grid;
    width: 32px;
    height: 32px;
    flex: none;
    place-items: center;
    border-radius: 11px;
    color: #8a9892;
    background: #f1f5f3;
    box-shadow: inset 0 0 0 1px #dce5e0;
    font: 700 13px/1 Inter, sans-serif;
  }

  .franchise-steps .is-current .franchise-step-dot {
    color: #fff;
    background: linear-gradient(180deg, #14a86d, #0a6a45);
    box-shadow: 0 6px 14px -4px rgba(14, 138, 90, .55), inset 0 1px 0 rgba(255, 255, 255, .3);
  }

  .franchise-steps h3 { margin: 1px 0 0; font: 650 15px/1.25 Inter, sans-serif; }
  .franchise-steps p { margin: 3px 0 0; color: #4b5b54; font: 450 12.5px/1.45 Inter, sans-serif; }
  .franchise-current-tag {
    display: inline-flex;
    height: 20px;
    align-items: center;
    padding: 0 8px;
    border-radius: 99px;
    color: #0b6b45;
    background: #e6f7ef;
    font: 700 9px/1 Inter, sans-serif;
    letter-spacing: .12em;
    text-transform: uppercase;
  }

  .franchise-form { padding: 18px; }
  .franchise-form-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
  .franchise-form-head span:first-child,
  .franchise-group-label {
    color: #8a9892;
    font: 650 10.5px/1 Inter, sans-serif;
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .franchise-required { color: #8a9892; font: 550 11.5px/1 Inter, sans-serif; }
  .franchise-required b { color: #c2410c; }
  .franchise-group-label { display: block; margin: 22px 2px 10px; }
  .franchise-field { position: relative; min-width: 0; margin-top: 12px; }
  .franchise-control {
    display: block;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    min-height: 58px;
    padding: 24px 16px 8px;
    border: 0;
    border-radius: 16px;
    outline: 0;
    color: var(--fr-ink);
    background: #f6f9f7;
    box-shadow: inset 0 0 0 1.5px var(--fr-line);
    font: 500 15px/1.3 Inter, sans-serif;
    transition: box-shadow .2s ease, background .2s ease;
  }

  .franchise-control::placeholder { color: transparent; }
  .franchise-control:focus { background: #fff; box-shadow: inset 0 0 0 2px #14a86d, 0 0 0 4px rgba(20, 168, 109, .14); }
  .franchise-field label {
    position: absolute;
    top: 20px;
    left: 16px;
    color: #8a9892;
    pointer-events: none;
    transform-origin: left top;
    transition: transform .22s ease, color .2s ease;
    font: 500 15px/1 Inter, sans-serif;
  }

  .franchise-control:focus ~ label,
  .franchise-control:not(:placeholder-shown) ~ label,
  .franchise-field.has-value label { color: #4b5b54; transform: translateY(-11px) scale(.76); }
  .franchise-control:focus ~ label { color: #0e8a5a; }
  .franchise-control.is-invalid { background: #fff8f7; box-shadow: inset 0 0 0 2px #d14343; }
  .franchise-control.is-invalid ~ label { color: #b42318; }
  .franchise-field textarea.franchise-control { min-height: 112px; padding-top: 27px; resize: vertical; }
  .franchise-field .franchise-error { margin: 6px 4px 0; color: #b42318; font: 550 12px/1.35 Inter, sans-serif; }

  .franchise-notice,
  .franchise-error-summary {
    margin: 0 0 14px;
    padding: 12px 14px;
    border-radius: 14px;
    font: 550 13px/1.45 Inter, sans-serif;
  }

  .franchise-notice { color: #075a3b; background: #e6f7ef; }
  .franchise-error-summary { color: #991b1b; background: #fef2f2; }
  .franchise-note {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-top: 16px;
    padding: 14px 16px;
    border-radius: 18px;
    color: #5a4a2e;
    background: linear-gradient(180deg, #fffdf7, #fff9ec);
    box-shadow: inset 0 0 0 1px rgba(178, 107, 0, .16);
    font: 500 12.5px/1.45 Inter, sans-serif;
  }

  .franchise-submit-wrap {
    position: sticky;
    bottom: 0;
    z-index: 4;
    margin: 0 -18px -18px;
    padding: 28px 18px 20px;
    background: linear-gradient(180deg, rgba(242, 245, 243, 0), var(--fr-bg) 24px, var(--fr-bg) 100%);
  }

  .franchise-submit {
    display: flex;
    width: 100%;
    min-height: 56px;
    align-items: center;
    justify-content: center;
    gap: 9px;
    border: 0;
    border-radius: 18px;
    color: #fff;
    background: linear-gradient(180deg, #11905e, #0a6a45);
    box-shadow: 0 10px 24px -8px rgba(10, 106, 69, .55), 0 2px 4px rgba(10, 106, 69, .2), inset 0 1px 0 rgba(255, 255, 255, .25);
    cursor: pointer;
    font: 650 16px/1 Inter, sans-serif;
  }

  .franchise-submit:hover { filter: brightness(1.05); }
  .franchise-footer-space { height: 16px; }

  @media (max-width: 520px) {
    body { display: block; padding: 0; background: var(--fr-bg); }
    .container { width: 100%; }
    .franchise-phone { min-height: 100vh; overflow: visible; border-radius: 0; box-shadow: none; }
    .franchise-screen { max-height: none; overflow: visible; }
    .franchise-submit-wrap {
      position: static;
      margin: 18px 0 0;
      padding: 0;
      background: none;
    }
    .franchise-footer-space { height: 24px; }
  }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
  }
</style>

<div class="franchise-phone">
  <div class="franchise-screen" id="franchise-screen">
    <header class="franchise-chrome">
      <nav class="franchise-nav" aria-label="Main navigation">
        <a class="franchise-back" href="{{ auth()->check() ? route('dashboard') : url('/') }}" aria-label="{{ auth()->check() ? 'Back to dashboard' : 'Back to LuLu' }}">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/><path d="M9 12h11"/></svg>
        </a>
        <a class="franchise-brand" href="{{ url('/') }}" aria-label="LuLu home">
          <img class="franchise-brand-mark" src="{{ asset('logo.png') }}" alt="">
          <span>LuLu Philippines</span>
        </a>
        <span class="franchise-page-label">Franchise</span>
      </nav>
    </header>

    <main class="franchise-main">
      <p class="franchise-eyebrow">LuLu franchise opportunities</p>
      <h1 class="franchise-title">Open a store in your community</h1>
      <p class="franchise-lede">Share a little more LuLu with your neighbourhood. Tell us about yourself and the location you have in mind.</p>

      <section class="franchise-hero" aria-labelledby="franchise-hero-title">
        <svg class="franchise-store-art" viewBox="0 0 350 190" role="img" aria-label="Illustration of a LuLu storefront">
          <defs>
            <linearGradient id="fr-wall" x1="0" x2="1" y1="0" y2="1"><stop stop-color="#f6fff9"/><stop offset="1" stop-color="#bfe8d2"/></linearGradient>
            <linearGradient id="fr-awning" x1="0" x2="1"><stop stop-color="#c6f36b"/><stop offset="1" stop-color="#3ee0a1"/></linearGradient>
            <linearGradient id="fr-glass" x1="0" x2="1" y1="0" y2="1"><stop stop-color="#96d9c0"/><stop offset="1" stop-color="#e5fff2"/></linearGradient>
          </defs>
          <ellipse cx="175" cy="167" rx="132" ry="12" fill="#001d13" opacity=".24"/>
          <circle cx="283" cy="36" r="22" fill="#c6f36b" opacity=".35"/>
          <path d="M65 72h220v90H65z" rx="10" fill="url(#fr-wall)"/>
          <path d="m52 74 20-36h206l20 36z" fill="#fff" opacity=".9"/>
          <path d="M75 74h200v23H75z" fill="url(#fr-awning)"/>
          <path d="M75 74h200v23c-17 14-33 14-50 0-17 14-33 14-50 0-17 14-33 14-50 0-17 14-33 14-50 0z" fill="url(#fr-awning)"/>
          <rect x="91" y="109" width="58" height="49" rx="5" fill="url(#fr-glass)" stroke="#8bc4a7"/>
          <rect x="201" y="109" width="58" height="49" rx="5" fill="url(#fr-glass)" stroke="#8bc4a7"/>
          <path d="M96 123h48m-48 14h48m-48 14h48m-43-27v27m18-27v27m19-27v27M206 123h48m-48 14h48m-48 14h48m-43-27v27m18-27v27m19-27v27" stroke="#0e8a5a" stroke-opacity=".34"/>
          <rect x="158" y="105" width="34" height="57" rx="4" fill="#075a3b"/>
          <rect x="163" y="112" width="24" height="12" rx="3" fill="#fff"/>
          <text x="175" y="121" text-anchor="middle" fill="#075a3b" font-family="Inter, sans-serif" font-size="7" font-weight="800">LuLu</text>
          <circle cx="185" cy="137" r="2" fill="#c6f36b"/>
          <path d="M49 162h252" stroke="#c6f36b" stroke-width="4" stroke-linecap="round"/>
          <path d="M42 53 46 43l4 10 10 4-10 4-4 10-4-10-10-4zm265 16 3-7 3 7 7 3-7 3-3 7-3-7-7-3z" fill="#c6f36b"/>
        </svg>
        <div class="franchise-hero-copy">
          <h2 id="franchise-hero-title">Grow with LuLu</h2>
          <p>Our franchise team will guide you through the next steps and help explore the right opportunity for your location.</p>
          <div class="franchise-highlights">
            <div class="franchise-highlight"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="M5 11v6l7 4 7-4v-6"/></svg><span>Training &amp;<br>onboarding</span></div>
            <div class="franchise-highlight"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9h18l-2-5H5L3 9Z"/><path d="M4 9v11h16V9M9 20v-6h6v6"/></svg><span>Store design<br>support</span></div>
            <div class="franchise-highlight"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 11v2a1 1 0 0 0 1 1h2l5 4V6l-5 4H5a1 1 0 0 0-1 1Z"/><path d="M16 9a5 5 0 0 1 0 6m3-9a9 9 0 0 1 0 12"/></svg><span>Marketing<br>toolkit</span></div>
          </div>
        </div>
      </section>

      <div class="franchise-section-head"><h2>How it works</h2><span class="franchise-section-meta">4 steps</span></div>
      <ol class="franchise-steps">
        <li class="is-current"><span class="franchise-step-dot">1</span><div><h3>Apply <span class="franchise-current-tag">You're here</span></h3><p>Tell us about yourself and where you'd like a store.</p></div></li>
        <li><span class="franchise-step-dot">2</span><div><h3>Review call</h3><p>Our franchise team will connect with you to talk through the opportunity.</p></div></li>
        <li><span class="franchise-step-dot">3</span><div><h3>Site visit</h3><p>We can review your proposed location together.</p></div></li>
        <li><span class="franchise-step-dot">4</span><div><h3>Agreement</h3><p>Review the franchise agreement before making a decision.</p></div></li>
      </ol>

      <div class="franchise-section-head" id="franchise-application"><h2>Your application</h2></div>
      @if (session('status'))
        <div class="franchise-notice" role="status">{{ session('status') }}</div>
      @endif

      @if ($errors->any())
        <div class="franchise-error-summary" role="alert">Please review the highlighted fields and submit the form again.</div>
      @endif

      <form class="franchise-form" id="franchise-application-form" method="POST" action="{{ route('franchise-applications.store') }}" autocomplete="on">
        @csrf
        <div class="franchise-form-head"><span>Contact details</span><span class="franchise-required"><b>*</b> Required</span></div>

        <div class="franchise-field">
          <input class="franchise-control @error('full_name') is-invalid @enderror" id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" autocomplete="name" maxlength="255" placeholder="Full name" required>
          <label for="full_name">Full name <span aria-hidden="true">*</span></label>
          @error('full_name') <p class="franchise-error">{{ $message }}</p> @enderror
        </div>

        <div class="franchise-field">
          <input class="franchise-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" placeholder="Email address" required>
          <label for="email">Email address <span aria-hidden="true">*</span></label>
          @error('email') <p class="franchise-error">{{ $message }}</p> @enderror
        </div>

        <div class="franchise-field">
          <input class="franchise-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number') }}" autocomplete="tel" maxlength="40" placeholder="+63 917 000 0000" required>
          <label for="phone_number">Mobile number <span aria-hidden="true">*</span></label>
          @error('phone_number') <p class="franchise-error">{{ $message }}</p> @enderror
        </div>

        <span class="franchise-group-label">Store details</span>

        @if (in_array(request('package'), ['40', '60'], true))
          <input type="hidden" name="preferred_package" value="{{ request('package') }}">
        @endif

        <div class="franchise-field">
          <input class="franchise-control @error('location') is-invalid @enderror" id="location" name="location" type="text" value="{{ old('location') }}" autocomplete="address-line1" maxlength="255" placeholder="Location / proposed site" required>
          <label for="location">Location / proposed site <span aria-hidden="true">*</span></label>
          @error('location') <p class="franchise-error">{{ $message }}</p> @enderror
        </div>

        <div class="franchise-field">
          <input class="franchise-control @error('investment_capacity') is-invalid @enderror" id="investment_capacity" name="investment_capacity" type="text" value="{{ old('investment_capacity') }}" maxlength="100" placeholder="Estimated investment capacity">
          <label for="investment_capacity">Estimated investment capacity</label>
          @error('investment_capacity') <p class="franchise-error">{{ $message }}</p> @enderror
        </div>

        <span class="franchise-group-label">A little about you</span>

        <div class="franchise-field">
          <textarea class="franchise-control @error('business_background') is-invalid @enderror" id="business_background" name="business_background" maxlength="5000" placeholder="Business background">{{ old('business_background') }}</textarea>
          <label for="business_background">Business background</label>
          @error('business_background') <p class="franchise-error">{{ $message }}</p> @enderror
        </div>

        <div class="franchise-field">
          <textarea class="franchise-control @error('additional_notes') is-invalid @enderror" id="additional_notes" name="additional_notes" maxlength="5000" placeholder="Additional notes">{{ old('additional_notes') }}</textarea>
          <label for="additional_notes">Additional notes</label>
          @error('additional_notes') <p class="franchise-error">{{ $message }}</p> @enderror
        </div>

        <div class="franchise-note">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 11v5m0-8h.01"/></svg>
          <span>Submitting this form is an expression of interest. Our team will contact you to discuss the details.</span>
        </div>

        <div class="franchise-submit-wrap">
          <button class="franchise-submit" type="submit">
            Submit application
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
          </button>
        </div>
      </form>
      <div class="franchise-footer-space" aria-hidden="true"></div>
    </main>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var fields = document.querySelectorAll('.franchise-field');
    fields.forEach(function (field) {
      var control = field.querySelector('.franchise-control');
      if (!control) return;
      var updateValueState = function () {
        field.classList.toggle('has-value', control.value.trim() !== '');
      };
      updateValueState();
      control.addEventListener('input', updateValueState);
      control.addEventListener('change', updateValueState);
    });

    var screen = document.getElementById('franchise-screen');
    var formAnchor = document.getElementById('franchise-application');
    if (screen && formAnchor && (document.querySelector('.franchise-error-summary') || document.querySelector('.franchise-notice'))) {
      formAnchor.scrollIntoView({ block: 'start' });
    }
  });
</script>
@endsection
