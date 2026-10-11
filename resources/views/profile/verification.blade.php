@extends('layouts.app')

@section('content')
<style>
  .verification-page { width:min(100% - 32px, 680px); margin:28px auto 48px; color:#0a1f17; }
  .verification-back { display:inline-flex; align-items:center; gap:8px; margin-bottom:18px; color:#075a3b; font-size:14px; font-weight:650; text-decoration:none; }
  .verification-card { padding:clamp(20px, 5vw, 32px); border-radius:24px; background:#fff; box-shadow:0 1px 2px rgba(6,40,28,.05),0 6px 16px -6px rgba(6,40,28,.12),0 18px 36px -18px rgba(6,40,28,.18); }
  .verification-eyebrow { color:#0e8a5a; font-size:11px; font-weight:750; letter-spacing:.14em; text-transform:uppercase; }
  .verification-title { margin:8px 0; font-size:clamp(24px, 6vw, 32px); line-height:1.1; letter-spacing:-.04em; }
  .verification-copy { margin:0; color:#52635b; font-size:14px; line-height:1.6; }
  .verification-status { margin-top:22px; padding:15px 16px; border-radius:14px; color:#075a3b; background:#e6f7ef; font-size:14px; line-height:1.5; }
  .verification-status.pending { color:#74500b; background:#fff6dc; }
  .verification-status.rejected { color:#922b22; background:#fff0ee; }
  .verification-form { display:grid; gap:18px; margin-top:24px; }
  .verification-label { display:block; margin-bottom:8px; font-size:13px; font-weight:700; }
  .verification-select { width:100%; min-height:48px; padding:0 13px; border:1px solid #d8e3dc; border-radius:12px; color:#183329; background:#fff; font:inherit; }
  .verification-file-input { position:absolute; width:1px; height:1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; clip-path:inset(50%); }
  .verification-file-actions { display:flex; flex-wrap:wrap; gap:8px; }
  .verification-file-button { display:inline-flex; min-height:42px; align-items:center; justify-content:center; padding:0 14px; border:1px solid #d8e3dc; border-radius:12px; color:#075a3b; background:#fff; font:650 13px/1 Inter,system-ui,sans-serif; cursor:pointer; }
  .verification-file-button:hover { background:#eff8f3; }
  .verification-preview { display:none; width:min(100%, 360px); max-height:250px; margin-top:12px; border:1px solid #dfe8e2; border-radius:12px; object-fit:contain; background:#f4f8f5; }
  .verification-file-status { min-height:18px; margin:8px 0 0; color:#697a71; font-size:12px; line-height:1.5; }
  .verification-file-status.error { color:#b42318; }
  .verification-consent { display:flex; align-items:flex-start; gap:10px; color:#52635b; font-size:12px; line-height:1.5; }
  .verification-consent input { flex:none; margin-top:2px; accent-color:#0e8a5a; }
  .verification-submit { min-height:48px; border:0; border-radius:14px; color:#fff; background:linear-gradient(180deg,#11905e,#0a6a45); box-shadow:0 8px 18px -10px rgba(10,106,69,.7); font:700 14px/1 Inter,system-ui,sans-serif; cursor:pointer; }
  .verification-submit:disabled { opacity:.55; cursor:wait; }
  .verification-error { margin:0; color:#b42318; font-size:13px; }
  .verification-privacy { margin:20px 0 0; color:#738179; font-size:12px; line-height:1.6; }
  .verification-details { display:grid; gap:10px; margin-top:18px; color:#52635b; font-size:13px; line-height:1.55; }
  .verification-details p { margin:0; }
</style>

<main class="verification-page">
  <a class="verification-back" href="{{ route('profile') }}">← Back to My Account</a>
  <section class="verification-card" aria-labelledby="verificationTitle">
    <div class="verification-eyebrow">Identity check</div>
    <h1 class="verification-title" id="verificationTitle">Get verified</h1>
    <p class="verification-copy">Submit a clear photo of a valid government ID and a selfie. An administrator will compare your face with the ID photo before your account is marked verified.</p>

    @if (session('status'))
      <div class="verification-status" role="status">{{ session('status') }}</div>
    @endif

    @if ($verification?->status === 'verified')
      <div class="verification-status" role="status">Verified{{ $verification->reviewed_at ? ' on '.$verification->reviewed_at->format('M j, Y') : '' }}. Your ID and selfie photos were deleted after review.</div>
    @elseif ($verification?->status === 'pending')
      <div class="verification-status pending" role="status">Your submission is under review. You can return here to check its status.</div>
      <div class="verification-details">
        <p><strong>ID type:</strong> {{ $idTypes[$verification->id_type] ?? $verification->id_type }}</p>
        <p><strong>Submitted:</strong> {{ $verification->submitted_at?->format('M j, Y g:i A') }}</p>
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

      <form class="verification-form" id="identityVerificationForm" method="post" action="{{ route('profile.verification.submit') }}" enctype="multipart/form-data">
        @csrf
        <div>
          <label class="verification-label" for="idType">Choose your ID</label>
          <select class="verification-select" id="idType" name="id_type" required>
            <option value="">Select a valid ID</option>
            @foreach ($idTypes as $value => $label)
              <option value="{{ $value }}" @selected(old('id_type') === $value)>{{ $label }}</option>
            @endforeach
          </select>
          @error('id_type')<p class="verification-error">{{ $message }}</p>@enderror
        </div>

        @foreach (['front' => 'Front of ID', 'back' => 'Back of ID (optional)', 'selfie' => 'Selfie (required)'] as $side => $label)
          <div class="verification-file-field" data-side="{{ $side }}">
            <span class="verification-label">{{ $label }}</span>
            <input class="verification-file-input" type="file" name="{{ $side }}_image" id="{{ $side }}Image" accept="image/jpeg,image/png,image/webp" @if ($side !== 'back') required @endif>
            <div class="verification-file-actions">
              <button class="verification-file-button" type="button" data-open-image="{{ $side }}" data-source="upload">Upload from device</button>
              <button class="verification-file-button" type="button" data-open-image="{{ $side }}" data-source="camera">Take a photo</button>
            </div>
            <img class="verification-preview" id="{{ $side }}Preview" alt="{{ $label }} preview">
            <p class="verification-file-status" id="{{ $side }}ImageStatus" aria-live="polite">
              @if ($side === 'selfie')
                JPG, PNG, or WebP; maximum 10 MB. Face the camera in good light, with your full face clearly visible.
              @elseif ($side === 'back')
                JPG, PNG, or WebP; maximum 10 MB. Make sure any text on the back is readable.
              @else
                JPG, PNG, or WebP; maximum 10 MB. Make sure all text is readable and the ID fills the frame.
              @endif
            </p>
            @error($side.'_image')<p class="verification-error">{{ $message }}</p>@enderror
          </div>
        @endforeach

        <label class="verification-consent">
          <input type="checkbox" name="consent" value="1" required>
          <span>I confirm that this ID belongs to me and consent to an administrator visually comparing my selfie with my ID photo for identity verification. My ID and selfie images are visible only to administrators reviewing this submission and are deleted after the review decision. This site does not create a facial-recognition template or perform automated face matching.</span>
        </label>

        <p class="verification-file-status error" id="verificationQualityError" role="alert" hidden></p>
        <button class="verification-submit" id="verificationSubmit" type="submit">Submit for review</button>
      </form>
      <p class="verification-privacy">For your safety, do not send your ID or selfie by chat or email. Images are stored privately, not in the public website folder, and are deleted once an administrator approves or rejects the submission.</p>
    @endif
  </section>
</main>

@if (! $verification || $verification->status === 'rejected')
<script>
  (() => {
    const form = document.getElementById('identityVerificationForm');
    const qualityError = document.getElementById('verificationQualityError');
    const submit = document.getElementById('verificationSubmit');
    if (!form || !qualityError || !submit) return;

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
        if (width * height > 12000000) {
          throw new Error('The image resolution is too large. Resize it and upload it again.');
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
        return 'Photo looks clear. You can submit it.';
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
            ? 'Back photo is optional.'
            : side === 'selfie' ? 'A clear selfie is required.' : 'A clear front photo is required.';
          return;
        }
        if (preview.src.startsWith('blob:')) URL.revokeObjectURL(preview.src);
        if (image.size > 10 * 1024 * 1024) {
          input.value = '';
          status.classList.add('error');
          status.textContent = 'Image is larger than 10 MB. Choose a smaller image.';
          return;
        }
        status.textContent = 'Checking image resolution and clarity…';
        try {
          const message = await inspectImage(image);
          preview.src = URL.createObjectURL(image);
          preview.style.display = 'block';
          status.textContent = message;
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
      if (!front || !selfie) {
      qualityError.textContent = 'Choose a clear front photo of your ID and a selfie before submitting.';
        qualityError.hidden = false;
        return;
      }

      submit.disabled = true;
      submit.textContent = 'Checking photos…';
      try {
        await inspectImage(front);
        await inspectImage(selfie);
        const back = document.getElementById('backImage')?.files?.[0];
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
