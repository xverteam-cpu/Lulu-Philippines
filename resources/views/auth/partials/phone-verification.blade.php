<div
  data-phone-verification
  data-firebase-config="{{ json_encode(config('services.firebase')) }}"
  style="display:grid;gap:10px;"
>
  <input type="hidden" name="phone_verification_token" data-phone-token>
  <button type="button" data-send-phone-code style="padding:11px 14px;border:1px solid #166534;border-radius:8px;background:#fff;color:#166534;font-weight:700;cursor:pointer;">
    Send verification code
  </button>
  <div data-phone-recaptcha></div>
  <div data-phone-code-step hidden style="display:grid;gap:10px;">
    <label for="phone-verification-code" style="font-weight:700;color:#1f2937;">SMS verification code</label>
    <input id="phone-verification-code" type="text" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" data-phone-code placeholder="Enter the 6-digit code" style="padding:12px;border:1px solid #d1d5db;border-radius:10px;">
    <button type="button" data-confirm-phone-code style="padding:11px 14px;border:0;border-radius:8px;background:#166534;color:#fff;font-weight:700;cursor:pointer;">
      Verify code
    </button>
  </div>
  <p data-phone-verification-status role="status" aria-live="polite" style="margin:0;color:#4b5563;font-size:13px;line-height:1.5;">
    Enter your number in international format (for example, +63 917 123 4567) and verify it by SMS. Standard carrier messaging rates may apply.
  </p>
</div>
