@extends('layouts.app')

@section('content')
<style>
  .verification-page { --verification-green:#08764f; --verification-deep:#064a36; --verification-mint:#e9f6ef; --verification-ink:#132822; --verification-muted:#63736d; --verification-line:#dae6df; max-width:1120px; margin:0 auto; padding:28px 28px 80px; color:var(--verification-ink); font:15px/1.5 Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; }
  .verification-page *, .verification-page *::before, .verification-page *::after { box-sizing:border-box; }
  .verification-page button, .verification-page input, .verification-page select { font:inherit; }
  .verification-page button { cursor:pointer; }
  .verification-page button:disabled { cursor:not-allowed; opacity:.55; }
  .verification-page button:focus-visible, .verification-page input:focus-visible, .verification-page select:focus-visible { outline:3px solid #a4dec1; outline-offset:2px; }
  .verification-top { position:sticky; top:0; z-index:5; margin:-28px -28px 30px; background:rgba(242,245,243,.92); backdrop-filter:saturate(180%) blur(18px); -webkit-backdrop-filter:saturate(180%) blur(18px); }
  .verification-top.is-scrolled { box-shadow:0 1px 0 rgba(10,31,23,.06); }
  .verification-nav { display:grid; min-height:60px; grid-template-columns:40px minmax(0,1fr) 40px; align-items:center; gap:12px; padding:0 16px; }
  .verification-nav-button { display:grid; width:40px; height:40px; place-items:center; border:0; border-radius:50%; color:var(--verification-ink); background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.06),0 2px 6px -2px rgba(6,40,28,.12); text-decoration:none; transition:transform .2s ease; }
  .verification-nav-button:active { transform:scale(.92); }
  .verification-account-brand { display:flex; align-items:center; justify-self:center; gap:8px; color:var(--verification-ink); text-decoration:none; white-space:nowrap; font:800 15px/1 "Plus Jakarta Sans",Inter,system-ui,sans-serif; }
  .verification-account-brand img { width:26px; height:26px; border-radius:8px; object-fit:contain; }
  .verification-grid { display:grid; grid-template-columns:300px minmax(0,1fr); align-items:start; gap:30px; }
  .verification-aside { padding:25px 10px; }
  .verification-eyebrow { color:var(--verification-green); font-size:11px; font-weight:800; letter-spacing:2px; text-transform:uppercase; }
  .verification-title { margin:16px 0; font-size:37px; line-height:1.1; letter-spacing:-1.5px; }
  .verification-copy { max-width:245px; margin:0; color:var(--verification-muted); font-size:14px; }
  .verification-step-list { display:grid; gap:20px; margin-top:44px; padding:0; list-style:none; }
  .verification-step { display:flex; align-items:center; gap:13px; color:#80928a; font-size:13px; font-weight:650; }
  .verification-step-number { display:grid; width:34px; height:34px; flex:0 0 34px; place-items:center; border:1px solid #cfded5; border-radius:50%; background:#fff; }
  .verification-step.current { color:var(--verification-deep); }
  .verification-step.current .verification-step-number, .verification-step.done .verification-step-number { border-color:var(--verification-green); color:#fff; background:var(--verification-green); }
  .verification-step.done { color:var(--verification-green); }
  .verification-panel { overflow:hidden; border:1px solid #e2eae5; border-radius:28px; background:#fff; box-shadow:0 12px 55px #164f3010; }
  .verification-panel-head { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:30px 35px 22px; border-bottom:1px solid #edf2ee; }
  .verification-panel-mini { color:var(--verification-green); font-size:11px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; }
  .verification-panel-head h2 { margin:6px 0 0; font-size:25px; line-height:1.2; letter-spacing:-.8px; }
  .verification-pill { padding:8px 12px; border-radius:50px; color:var(--verification-green); background:var(--verification-mint); font-size:12px; font-weight:750; white-space:nowrap; }
  .verification-content { padding:30px 35px; }
  .verification-intro { margin:0 0 25px; color:var(--verification-muted); }
  .verification-label { display:block; margin:0 0 9px; font-weight:750; }
  .verification-select-wrap { position:relative; }
  .verification-select { width:100%; appearance:none; padding:15px 40px 15px 14px; border:1px solid var(--verification-line); border-radius:13px; color:var(--verification-ink); background:#fff; }
  .verification-select-wrap::after { position:absolute; top:8px; right:16px; color:var(--verification-muted); content:'⌄'; font-size:26px; pointer-events:none; }
  .verification-upload { margin-top:18px; padding:25px; border:1.5px dashed #a9c6b5; border-radius:19px; background:#fbfdfb; text-align:center; transition:background .2s,border-color .2s; }
  .verification-upload.drag { border-color:var(--verification-green); background:var(--verification-mint); }
  .verification-upload-icon { display:grid; width:56px; height:56px; place-items:center; margin:0 auto 13px; border-radius:17px; color:var(--verification-green); background:var(--verification-mint); font-size:28px; }
  .verification-upload h3 { margin:0 0 6px; font-size:17px; letter-spacing:-.3px; }
  .verification-upload-copy { margin:0 0 19px; color:var(--verification-muted); font-size:13px; }
  .verification-actions { display:flex; flex-wrap:wrap; justify-content:center; gap:10px; }
  .verification-button { min-height:42px; padding:11px 17px; border:1px solid var(--verification-line); border-radius:12px; color:var(--verification-deep); background:#fff; font-size:13px; font-weight:750; }
  .verification-button.primary { border-color:var(--verification-green); color:#fff; background:var(--verification-green); }
  .verification-button:hover:not(:disabled) { filter:brightness(.97); }
  .verification-file-input { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; clip-path:inset(50%); }
  .verification-preview { display:none; width:100%; max-height:245px; margin-top:17px; border-radius:12px; background:#edf4ef; object-fit:contain; }
  .verification-file-status { min-height:18px; margin:10px 0 0; color:var(--verification-muted); font-size:12px; line-height:1.5; }
  .verification-file-status.error, .verification-error { color:#aa3030; }
  .verification-hint { display:flex; gap:12px; margin-top:22px; padding:15px; border-radius:14px; color:#446755; background:#f1f8f3; font-size:12px; }
  .verification-hint strong { color:var(--verification-deep); }
  .verification-hint-icon { font-size:21px; line-height:1; }
  .verification-step-page[hidden], .verification-button[hidden] { display:none !important; }
  .verification-field-error { min-height:18px; margin-top:8px; color:#aa3030; font-size:12px; font-weight:700; }
  .verification-review { display:grid; gap:10px; }
  .verification-review-card { display:flex; align-items:center; gap:12px; margin:10px 0; padding:12px; border:1px solid var(--verification-line); border-radius:14px; }
  .verification-review-card img { width:78px; height:66px; border-radius:9px; background:#edf4ef; object-fit:cover; }
  .verification-review-card strong { display:block; font-size:13px; }
  .verification-review-card span { color:var(--verification-muted); font-size:12px; }
  .verification-consent { display:flex; align-items:flex-start; gap:11px; margin:22px 0; color:#455a51; font-size:13px; }
  .verification-consent input { width:19px; height:19px; flex:none; margin:2px 0 0; accent-color:var(--verification-green); }
  .verification-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:21px 35px; border-top:1px solid #edf2ee; }
  .verification-footnote { color:var(--verification-muted); font-size:12px; }
  .verification-status { padding:30px 35px; color:#075a3b; background:#fff; text-align:center; }
  .verification-status.pending { color:#74500b; }
  .verification-status.rejected { margin:0 35px 22px; padding:15px 16px; border-radius:14px; color:#922b22; background:#fff0ee; font-size:14px; line-height:1.5; text-align:left; }
  .verification-status-icon { color:var(--verification-green); font-size:52px; line-height:1; }
  .verification-status.pending .verification-status-icon { color:#c28a18; }
  .verification-status h2 { margin:12px 0 8px; font-size:26px; letter-spacing:-1px; }
  .verification-status p { max-width:460px; margin:0 auto 14px; color:var(--verification-muted); }
  .verification-status-meta { color:var(--verification-muted); font-size:13px; }
  .verification-privacy { margin:0; padding:0 35px 25px; color:var(--verification-muted); font-size:12px; line-height:1.6; }
  @media (max-width:820px) {
    .verification-page { padding:12px 14px 48px; }
    .verification-top { margin:-12px -14px 24px; }
    .verification-grid { display:block; }
    .verification-aside { padding:0 7px 20px; }
    .verification-title { margin:8px 0; font-size:31px; }
    .verification-copy { max-width:none; }
    .verification-step-list { display:flex; gap:7px; margin-top:21px; }
    .verification-step { flex:1; align-items:flex-start; gap:6px; font-size:11px; }
    .verification-step-number { width:28px; height:28px; flex-basis:28px; }
    .verification-panel { border-radius:22px; }
    .verification-panel-head { padding:22px 20px 19px; }
    .verification-panel-head h2 { font-size:22px; }
    .verification-content { padding:22px 20px; }
    .verification-footer { padding:18px 20px; }
    .verification-status { padding:26px 20px; }
    .verification-status.rejected { margin:0 20px 20px; }
    .verification-privacy { padding:0 20px 22px; }
    .verification-pill { padding:7px 10px; font-size:10px; }
    .verification-upload { padding:22px 12px; }
    .verification-secure { font-size:10px; }
  }
  @media (max-width:390px) {
    .verification-step { font-size:10px; }
    .verification-step-number { display:none; }
    .verification-button { padding:11px 12px; }
    .verification-panel-head h2 { font-size:20px; }
  }
  @media (prefers-reduced-motion:reduce) { .verification-page *, .verification-page *::before, .verification-page *::after { transition:none !important; } }
</style>

<main class="verification-page">
  <header class="verification-top" id="verificationTop">
    <nav class="verification-nav" aria-label="Account navigation">
      <a class="verification-nav-button" href="{{ route('dashboard') }}" aria-label="Back to dashboard">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg>
      </a>
      <a class="verification-account-brand" href="{{ route('dashboard') }}" aria-label="LuLu Philippines dashboard">
        <img src="{{ asset('logo.png') }}" alt="">
        <span>Account</span>
      </a>
      <a class="verification-nav-button" href="{{ route('notifications.index') }}" aria-label="Notifications">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
      </a>
    </nav>
  </header>
  <div class="verification-grid">
    <aside class="verification-aside">
      <div class="verification-eyebrow">Account security</div>
      <h1 class="verification-title">Let’s confirm<br>it’s really you.</h1>
      <p class="verification-copy">A quicker, simpler way to verify your account. Just your ID and a clear selfie.</p>
      <ol class="verification-step-list" aria-label="Verification progress">
        <li class="verification-step current" data-verification-nav="1"><span class="verification-step-number">1</span><span>Identity document</span></li>
        <li class="verification-step" data-verification-nav="2"><span class="verification-step-number">2</span><span>Selfie check</span></li>
        <li class="verification-step" data-verification-nav="3"><span class="verification-step-number">3</span><span>Review &amp; submit</span></li>
      </ol>
    </aside>

    <section class="verification-panel" aria-label="Identity verification">
      @if (session('status'))
        <div class="verification-content"><div class="verification-hint" role="status">{{ session('status') }}</div></div>
      @endif

      @if ($verification?->status === 'verified')
        <div class="verification-status" role="status">
          <div class="verification-status-icon" aria-hidden="true">✓</div>
          <h2>Identity verified</h2>
          <p>Your identity has been reviewed and verified{{ $verification->reviewed_at ? ' on '.$verification->reviewed_at->format('M j, Y') : '' }}. Your ID and selfie photos were deleted after review.</p>
          <div class="verification-status-meta"><strong>ID type:</strong> {{ $idTypes[$verification->id_type] ?? $verification->id_type }}</div>
        </div>
      @elseif ($verification?->status === 'pending')
        <div class="verification-status pending" role="status">
          <div class="verification-status-icon" aria-hidden="true">◷</div>
          <h2>Review in progress</h2>
          <p>Your submission is under review by our team. We’ll update your account after a decision.</p>
          <div class="verification-status-meta"><strong>ID type:</strong> {{ $idTypes[$verification->id_type] ?? $verification->id_type }}<br><strong>Submitted:</strong> {{ $verification->submitted_at?->format('M j, Y g:i A') }}</div>
        </div>
      @else
        @if ($verification?->status === 'rejected')
          <div class="verification-status rejected" role="status">
            Your previous submission was not approved. Submit a new clear photo to try again.
            @if ($verification->review_note)
              <br><strong>Review note:</strong> {{ $verification->review_note }}
            @endif
          </div>
        @endif

        <form id="identityVerificationForm" method="post" action="{{ route('profile.verification.submit') }}" enctype="multipart/form-data" data-initial-step="{{ $errors->has('selfie_image') ? 2 : ($errors->has('consent') ? 3 : 1) }}">
          @csrf
          <div class="verification-panel-head">
            <div><div class="verification-panel-mini" id="verificationStepCounter">Step 01 of 03</div><h2 id="verificationStepTitle">Upload your ID</h2></div>
            <span class="verification-pill">● Protected</span>
          </div>
          <div class="verification-content">
            <section class="verification-step-page" data-verification-page="1">
              <p class="verification-intro">Choose an accepted government-issued ID and upload clear photos of that exact document. If you select a driver’s license, submit both sides; a different document or an unreadable photo will be rejected for retake.</p>
              <label class="verification-label" for="idType">Document type</label>
              <div class="verification-select-wrap">
                <select class="verification-select" id="idType" name="id_type" required>
                  <option value="">Select an ID type</option>
                  @foreach ($idTypes as $value => $label)
                    <option value="{{ $value }}" @selected(old('id_type') === $value)>{{ $label }}</option>
                  @endforeach
                </select>
              </div>
              @error('id_type')<div class="verification-field-error">{{ $message }}</div>@enderror

              @foreach (['front' => ['Front of your ID', 'Make sure the details and photo are clearly visible.', '▤'], 'back' => ['Back of your ID (required for a driver’s license)', 'Upload the back when your ID has details there. Both sides of a driver’s license are required.', '▤']] as $side => [$label, $copy, $icon])
                <div class="verification-upload" data-zone="{{ $side }}">
                  <div class="verification-upload-icon" aria-hidden="true">{{ $icon }}</div>
                  <h3>{{ $label }}</h3>
                  <p class="verification-upload-copy">{{ $copy }}</p>
                  <input class="verification-file-input" type="file" name="{{ $side }}_image" id="{{ $side }}Image" accept="image/jpeg,image/png,image/webp" @if ($side === 'front') required @endif>
                  <div class="verification-actions">
                    <button class="verification-button primary" type="button" data-open-image="{{ $side }}" data-source="upload">Choose file</button>
                    <button class="verification-button" type="button" data-open-image="{{ $side }}" data-source="camera">Use camera</button>
                  </div>
                  <img class="verification-preview" id="{{ $side }}Preview" alt="{{ $label }} preview">
                  <p class="verification-file-status" id="{{ $side }}ImageStatus" aria-live="polite">
                    @if ($side === 'back')
                      JPG, PNG, or WebP. Large photos are optimized before upload; make sure any text on the back is readable.
                    @else
                      JPG, PNG, or WebP. Large photos are optimized before upload; make sure all text is readable and the ID fills the frame.
                    @endif
                  </p>
                  @error($side.'_image')<div class="verification-field-error">{{ $message }}</div>@enderror
                </div>
              @endforeach
              <div class="verification-hint"><span class="verification-hint-icon" aria-hidden="true">ⓘ</span><span><strong>For best results:</strong> Avoid glare and blur. Use a well-lit area and make sure all ID details are in focus.</span></div>
            </section>

            <section class="verification-step-page" data-verification-page="2" hidden>
              <p class="verification-intro">Take a clear selfie in good lighting. An administrator will compare it manually with your ID photo.</p>
              <div class="verification-upload" data-zone="selfie">
                <div class="verification-upload-icon" aria-hidden="true">◉</div>
                <h3>Take or upload your selfie</h3>
                <p class="verification-upload-copy">Face the camera directly. Keep your whole face visible, without sunglasses or filters.</p>
                <input class="verification-file-input" type="file" name="selfie_image" id="selfieImage" accept="image/jpeg,image/png,image/webp" required>
                <div class="verification-actions">
                  <button class="verification-button primary" type="button" data-open-image="selfie" data-source="camera">Open camera</button>
                  <button class="verification-button" type="button" data-open-image="selfie" data-source="upload">Upload photo</button>
                </div>
                <img class="verification-preview" id="selfiePreview" alt="Selfie preview">
                <p class="verification-file-status" id="selfieImageStatus" aria-live="polite">JPG, PNG, or WebP. Large photos are optimized before upload. Face the camera in good light, with your full face clearly visible.</p>
                @error('selfie_image')<div class="verification-field-error">{{ $message }}</div>@enderror
              </div>
              <div class="verification-hint"><span class="verification-hint-icon" aria-hidden="true">ⓘ</span><span><strong>Quick tip:</strong> Stand against a plain background and face the light. This is a manual visual review, not automated face recognition.</span></div>
            </section>

            <section class="verification-step-page" data-verification-page="3" hidden>
              <p class="verification-intro">Check your photos before sending them for manual review.</p>
              <div class="verification-review" id="verificationReviewFiles"></div>
              <label class="verification-consent">
                <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                <span>I confirm that this ID belongs to me and consent to an administrator visually comparing my selfie with my ID photo for identity verification. My ID and selfie images are visible only to administrators reviewing this submission and are deleted after the review decision. This site does not create a facial-recognition template or perform automated face matching.</span>
              </label>
              @error('consent')<div class="verification-field-error">{{ $message }}</div>@enderror
              <div class="verification-hint"><span class="verification-hint-icon" aria-hidden="true">♧</span><span>Verification is completed by an administrator. Submission does not mean your account is approved yet.</span></div>
            </section>

            @if ($errors->any())
              <div class="verification-field-error" role="alert" aria-live="polite">{{ $errors->first() }}</div>
            @endif
            <p class="verification-file-status error" id="verificationQualityError" role="alert" hidden></p>
          </div>
          <footer class="verification-footer">
            <button class="verification-button" type="button" id="verificationBack" hidden>← Back</button>
            <small class="verification-footnote" id="verificationFootnote">Usually takes 2–3 minutes</small>
            <button class="verification-button primary" type="button" id="verificationNext">Continue →</button>
            <button class="verification-button primary" id="verificationSubmit" type="submit" hidden>Submit for review →</button>
          </footer>
        </form>
        <p class="verification-privacy">Your ID and selfie are stored privately and visible only to authorized administrators. Images are deleted after the review decision. Do not send your ID or selfie by chat or email.</p>
      @endif
    </section>
  </div>
</main>

@if (! $verification || $verification->status === 'rejected')
<script>
  (() => {
    const form = document.getElementById('identityVerificationForm');
    if (!form) return;

    const pages = [...form.querySelectorAll('[data-verification-page]')];
    const navigation = [...document.querySelectorAll('[data-verification-nav]')];
    const counter = document.getElementById('verificationStepCounter');
    const title = document.getElementById('verificationStepTitle');
    const footnote = document.getElementById('verificationFootnote');
    const back = document.getElementById('verificationBack');
    const next = document.getElementById('verificationNext');
    const submit = document.getElementById('verificationSubmit');
    const error = document.getElementById('verificationQualityError');
    const reviewFiles = document.getElementById('verificationReviewFiles');
    const titles = ['Upload your ID', 'Let’s take a selfie', 'Review your details'];
    let step = Number(form.dataset.initialStep) || 1;

    function renderReview() {
      if (!reviewFiles) return;
      reviewFiles.replaceChildren();

      const idType = document.getElementById('idType');
      const selectedId = idType?.selectedOptions[0]?.textContent.trim();
      if (selectedId && idType.value) {
        const detail = document.createElement('div');
        detail.className = 'verification-review-card';
        detail.innerHTML = '<span aria-hidden="true">▤</span><div><strong>Document type</strong><span></span></div>';
        detail.querySelector('div span').textContent = selectedId;
        reviewFiles.append(detail);
      }

      [
        ['front', 'Front of ID'],
        ['back', 'Back of ID'],
        ['selfie', 'Selfie'],
      ].forEach(([side, label]) => {
        const input = document.getElementById(side + 'Image');
        const file = input?.files?.[0];
        if (!file) return;

        const item = document.createElement('div');
        item.className = 'verification-review-card';
        const preview = document.getElementById(side + 'Preview');
        if (preview?.src) {
          const image = document.createElement('img');
          image.src = preview.src;
          image.alt = label + ' preview';
          item.append(image);
        }
        const info = document.createElement('div');
        const name = document.createElement('strong');
        const filename = document.createElement('span');
        name.textContent = label;
        filename.textContent = file.name;
        info.append(name, filename);
        item.append(info);
        reviewFiles.append(item);
      });

      if (!document.getElementById('backImage')?.files?.length) {
        const optional = document.createElement('div');
        optional.className = 'verification-review-card';
        const backIsRequired = document.getElementById('idType')?.value === 'drivers_license';
        optional.innerHTML = '<div><strong>Back of ID</strong><span></span></div>';
        optional.querySelector('span').textContent = backIsRequired
          ? 'Required for a driver’s license — missing'
          : 'Not provided (if not applicable)';
        reviewFiles.append(optional);
      }
    }

    function render() {
      pages.forEach(page => {
        const current = Number(page.dataset.verificationPage) === step;
        page.hidden = !current;
        page.classList.toggle('active', current);
      });
      navigation.forEach(item => {
        const number = Number(item.dataset.verificationNav);
        item.classList.toggle('current', number === step);
        item.classList.toggle('done', number < step);
      });
      counter.textContent = `Step 0${step} of 03`;
      title.textContent = titles[step - 1];
      back.hidden = step === 1;
      next.hidden = step === 3;
      submit.hidden = step !== 3;
      footnote.textContent = step === 3 ? 'Your photos will be securely submitted for review' : 'Usually takes 2–3 minutes';
      error.hidden = true;
      if (step === 3) renderReview();
    }

    function showError(message) {
      error.textContent = message;
      error.hidden = false;
      error.scrollIntoView({ behavior:'smooth', block:'nearest' });
    }

    back.addEventListener('click', () => {
      if (step > 1) {
        step--;
        render();
      }
    });
    next.addEventListener('click', () => {
      if (step === 1) {
        if (!document.getElementById('idType').value) {
          showError('Choose your ID type to continue.');
          return;
        }
        if (!document.getElementById('frontImage').files.length) {
          showError('Add the front of your ID to continue.');
          return;
        }
        if (document.getElementById('idType').value === 'drivers_license'
          && !document.getElementById('backImage').files.length) {
          showError('A driver’s license requires clear photos of both the front and back.');
          return;
        }
      } else if (step === 2 && !document.getElementById('selfieImage').files.length) {
        showError('Add a selfie to continue.');
        return;
      }
      step = Math.min(3, step + 1);
      render();
    });
    form.querySelectorAll('.verification-file-input').forEach(input => {
      input.addEventListener('change', () => {
        if (step === 3) renderReview();
      });
    });
    render();
  })();
</script>
<script>
  (() => {
    const form = document.getElementById('identityVerificationForm');
    const qualityError = document.getElementById('verificationQualityError');
    const submit = document.getElementById('verificationSubmit');
    if (!form || !qualityError || !submit) return;
    const idType = document.getElementById('idType');
    const backInput = document.getElementById('backImage');
    const backHeading = document.querySelector('[data-zone="back"] h3');
    const backCopy = document.querySelector('[data-zone="back"] .verification-upload-copy');

    function updateBackRequirement() {
      const required = idType?.value === 'drivers_license';
      if (backInput) backInput.required = required;
      if (backHeading) {
        backHeading.textContent = required
          ? 'Back of your driver’s license (required)'
          : 'Back of your ID (if applicable)';
      }
      if (backCopy) {
        backCopy.textContent = required
          ? 'Upload the actual back of your driver’s license. Make sure all details are readable.'
          : 'Add the back if your document has important details there.';
      }
    }

    idType?.addEventListener('change', updateBackRequirement);
    updateBackRequirement();

    document.querySelectorAll('[data-open-image]').forEach(button => {
      button.addEventListener('click', () => {
        const side = button.dataset.openImage;
        const input = document.getElementById(side + 'Image');
        if (!input) return;
        if (button.dataset.source === 'camera') {
          input.setAttribute('capture', side === 'selfie' ? 'user' : 'environment');
        }
        else input.removeAttribute('capture');
        input.click();
      });
    });

    async function inspectImage(file) {
      if (typeof createImageBitmap !== 'function') {
        throw new Error('This browser cannot check photo clarity. Please use an up-to-date browser.');
      }

      const bitmap = await createImageBitmap(file);
      try {
        const width = bitmap.width;
        const height = bitmap.height;
        if (Math.min(width, height) < 350 || Math.max(width, height) < 600) {
          throw new Error('The image is too small. Take or upload a higher-resolution photo.');
        }
        const scale = Math.min(1, 360 / Math.max(width, height));
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(width * scale));
        canvas.height = Math.max(1, Math.round(height * scale));
        const context = canvas.getContext('2d', { willReadFrequently:true });
        if (!context) throw new Error('Photo quality could not be checked. Please try another image.');

        context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        const { data } = context.getImageData(0, 0, canvas.width, canvas.height);
        const gray = new Float32Array(canvas.width * canvas.height);
        for (let i = 0, pixel = 0; i < data.length; i += 4, pixel++) {
          gray[pixel] = .299 * data[i] + .587 * data[i + 1] + .114 * data[i + 2];
        }

        let sum = 0;
        let sumSquares = 0;
        let samples = 0;
        for (let y = 1; y < canvas.height - 1; y += 1) {
          for (let x = 1; x < canvas.width - 1; x += 1) {
            const index = y * canvas.width + x;
            const laplacian = gray[index - canvas.width] + gray[index - 1] - 4 * gray[index]
              + gray[index + 1] + gray[index + canvas.width];
            sum += laplacian;
            sumSquares += laplacian * laplacian;
            samples++;
          }
        }
        const variance = samples ? sumSquares / samples - Math.pow(sum / samples, 2) : 0;
        if (variance < 28) {
          throw new Error('This photo appears blurry. Hold the phone steady, focus on the ID text, and retake it in good light.');
        }
        return 'Photo looks clear.';
      } finally {
        bitmap.close();
      }
    }

    async function optimizeImage(file) {
      const bitmap = await createImageBitmap(file);
      try {
        const pixels = bitmap.width * bitmap.height;
        if (file.type === 'image/jpeg' && file.size <= 8 * 1024 * 1024 && pixels <= 12000000) {
          return { file, fileList: null, originalSize: file.size, optimized: false };
        }

        const scale = Math.min(
          1,
          4000 / Math.max(bitmap.width, bitmap.height),
          Math.sqrt(12000000 / pixels)
        );
        const canvas = document.createElement('canvas');
        canvas.width = Math.max(1, Math.round(bitmap.width * scale));
        canvas.height = Math.max(1, Math.round(bitmap.height * scale));
        const context = canvas.getContext('2d');
        if (!context) throw new Error('The photo could not be optimized. Please try another image.');
        context.fillStyle = '#fff';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);

        const blob = await new Promise((resolve, reject) => {
          canvas.toBlob(result => result
            ? resolve(result)
            : reject(new Error('The photo could not be optimized. Please try another image.')), 'image/jpeg', .92);
        });
        const optimizedFile = new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', {
          type: 'image/jpeg',
          lastModified: Date.now(),
        });
        const transfer = new DataTransfer();
        transfer.items.add(optimizedFile);
        return {
          file: optimizedFile,
          fileList: transfer.files,
          originalSize: file.size,
          optimized: true,
        };
      } finally {
        bitmap.close();
      }
    }

    document.querySelectorAll('.verification-file-input').forEach(input => {
      input.addEventListener('change', async () => {
        const side = input.id.replace('Image', '');
        const image = input.files?.[0];
        const status = document.getElementById(side + 'ImageStatus');
        const preview = document.getElementById(side + 'Preview');
        if (!status || !preview) return;
        qualityError.hidden = true;
        status.classList.remove('error');
        preview.style.display = 'none';
        if (!image) {
          status.textContent = side === 'back'
            ? idType?.value === 'drivers_license'
              ? 'The back of your driver’s license is required.'
              : 'Back photo is optional if your ID has no important back details.'
            : side === 'selfie' ? 'A clear selfie is required.' : 'A clear front photo is required.';
          return;
        }
        if (preview.src.startsWith('blob:')) URL.revokeObjectURL(preview.src);
        status.textContent = 'Checking image resolution and clarity…';
        try {
          await inspectImage(image);
          const result = await optimizeImage(image);
          if (result.optimized) input.files = result.fileList;
          await inspectImage(result.file);
          preview.src = URL.createObjectURL(result.file);
          preview.style.display = 'block';
          const sizeMessage = result.optimized
            ? ` Optimized from ${(result.originalSize / 1024 / 1024).toFixed(1)} MB to ${(result.file.size / 1024 / 1024).toFixed(1)} MB.`
            : '';
          status.textContent = `Photo looks clear and is ready to submit.${sizeMessage}`;
        } catch (error) {
          input.value = '';
          status.classList.add('error');
          status.textContent = error instanceof Error ? error.message : 'The photo could not be checked. Please try another image.';
        }
      });
    });

    form.addEventListener('submit', async event => {
      if (form.dataset.qualityChecked === 'true') return;
      event.preventDefault();
      qualityError.hidden = true;
      const front = document.getElementById('frontImage')?.files?.[0];
      const selfie = document.getElementById('selfieImage')?.files?.[0];
      const back = document.getElementById('backImage')?.files?.[0];
      if (!front || !selfie || (idType?.value === 'drivers_license' && !back)) {
        qualityError.textContent = idType?.value === 'drivers_license' && !back
          ? 'Upload clear photos of both the front and back of your driver’s license.'
          : 'Choose a clear front photo of your ID and a selfie before submitting.';
        qualityError.hidden = false;
        return;
      }

      submit.disabled = true;
      submit.textContent = 'Checking photos…';
      try {
        await inspectImage(front);
        await inspectImage(selfie);
        if (back) await inspectImage(back);
        form.dataset.qualityChecked = 'true';
        form.requestSubmit();
      } catch (error) {
        qualityError.textContent = error instanceof Error ? error.message : 'Photo quality could not be checked.';
        qualityError.hidden = false;
        submit.disabled = false;
        submit.textContent = 'Submit for review';
      }
    });
  })();
</script>
@endif
@endsection
