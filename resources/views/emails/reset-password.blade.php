<table width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border-collapse:collapse; border-radius:14px; overflow:hidden;">
  <tr>
    <td align="center" style="background-color:#166534; padding:28px 24px 22px 24px;">
      <img src="{{ url('LuluLogo.svg') }}" width="180" alt="Lulu" style="display:block; border:0; outline:none; text-decoration:none; max-width:180px; height:auto;">
    </td>
  </tr>
  <tr>
    <td style="padding:34px 32px 12px 32px;">
      <h1 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:38px; font-weight:700; color:#1f1f1f;">
        Password reset request
      </h1>
      <p style="margin:12px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#5f6368;">
        You are receiving this email because we received a password reset request for your account.
      </p>
    </td>
  </tr>
  <tr>
    <td align="center" style="padding:30px 32px 8px 32px;">
      <a href="{{ $resetUrl }}" target="_blank" style="display:inline-block; background-color:#166534; color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:20px; font-weight:700; text-decoration:none; padding:14px 28px; border-radius:6px;">
        Reset Password
      </a>
    </td>
  </tr>
  <tr>
    <td style="padding:20px 32px 30px 32px;">
      <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:21px; color:#6a6a6a;">
        This link will expire in {{ $expireMinutes }} minutes. If you did not request a password reset, no further action is required.
      </p>
    </td>
  </tr>
  <tr>
    <td style="background-color:#2b2b2b; padding:26px 32px;">
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
        <tr>
          <td style="padding:0 0 16px 0;">
            <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:18px; line-height:24px; color:#ffffff; font-weight:700;">
              Lulu
            </p>
            <p style="margin:6px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:21px; color:#c9c9c9;">
              Password reset notification
            </p>
          </td>
        </tr>
        <tr>
          <td style="border-top:1px solid #4a4a4a; padding:16px 0 0 0;">
            <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:20px; color:#b8b8b8;">
              This automated email was sent to {{ $email }}. Please do not reply directly to this message. For assistance, contact Lulu Support at lotteriaphilippines@gmail.com.
            </p>
            <p style="margin:12px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:18px; color:#9f9f9f;">
              © 2026 Lulu. All rights reserved.
            </p>
            <p style="margin:14px 0 0 0; padding:0;">
              <a href="https://www.facebook.com/lotteria.ph" target="_blank" style="display:inline-block; width:28px; height:28px; line-height:28px; text-align:center; border-radius:50%; background-color:#3b5998; color:#ffffff; text-decoration:none; font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:700;">
                f
              </a>
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
