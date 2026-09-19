<table width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border-collapse:collapse; border-radius:14px; overflow:hidden;">

  <tr>
    <td align="center" style="background:linear-gradient(90deg,#ffffff 0%,#fff7f7 100%); padding:28px 24px 24px 24px; border-bottom:2px solid #f1d0d4;">
      <img src="{{ url('LuluLogo.svg') }}" width="180" alt="Lulu" style="display:block; border:0; outline:none; text-decoration:none; max-width:180px; height:auto;">
    </td>
  </tr>

  <tr>
    <td style="padding:34px 32px 12px 32px;">
      <h1 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:30px; line-height:38px; font-weight:700; color:#1f1f1f;">
        Thank you for your package purchase.
      </h1>
      <p style="margin:12px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:24px; color:#5f6368;">
        We have received your package purchase request. Your invoice summary is shown below for your reference.
      </p>
    </td>
  </tr>

  <tr>
    <td style="padding:22px 32px 0 32px;">
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; background-color:#fff8f0; border:1px solid #f0d29b; border-radius:12px;">
        <tr>
          <td style="padding:18px 20px;">
            <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:20px; color:#9b6a12; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
              Invoice Status
            </p>
            <p style="margin:6px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:20px; line-height:28px; color:#166534; font-weight:700;">
              {{ $status_text }}
            </p>
            <p style="margin:6px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:22px; color:#5f6368;">
              Your account will be updated once the payment has been verified by our finance team.
            </p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:26px 32px 0 32px;">
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; background-color:#f8f8f8; border:1px solid #e3e3e3;">
        <tr>
          <td style="padding:20px 22px; border-bottom:1px solid #e3e3e3;">
            <p style="margin:0 0 5px 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:18px; color:#7a7a7a;">
              Item
            </p>
            <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:18px; line-height:24px; color:#1f1f1f; font-weight:700;">
              Lulu Joint Venture Package
            </p>
          </td>
        </tr>

        <tr>
          <td style="padding:0;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
              <tr>
                <td width="50%" style="padding:18px 22px; border-bottom:1px solid #e3e3e3; border-right:1px solid #e3e3e3;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Package Name</p>
                  <p style="margin:0; font-size:16px; line-height:22px; color:#1f1f1f; font-weight:700;">{{ $package_name }}</p>
                </td>
                <td width="50%" style="padding:18px 22px; border-bottom:1px solid #e3e3e3;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Package Amount</p>
                  <p style="margin:0; font-size:16px; line-height:22px; color:#1f1f1f; font-weight:700;">₱{{ $package_amount }}</p>
                </td>
              </tr>

              <tr>
                <td width="50%" style="padding:18px 22px; border-bottom:1px solid #e3e3e3; border-right:1px solid #e3e3e3;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Purchase Date</p>
                  <p style="margin:0; font-size:16px; line-height:22px; color:#1f1f1f; font-weight:700;">{{ $purchase_date }}</p>
                </td>
                <td width="50%" style="padding:18px 22px; border-bottom:1px solid #e3e3e3;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Payment Method</p>
                  <p style="margin:0; font-size:16px; line-height:22px; color:#1f1f1f; font-weight:700;">{{ $payment_method }}</p>
                </td>
              </tr>

              <tr>
                <td width="50%" style="padding:18px 22px; border-bottom:1px solid #e3e3e3; border-right:1px solid #e3e3e3;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Invoice ID</p>
                  <p style="margin:0; font-size:15px; line-height:22px; color:#1f1f1f; font-weight:700; word-break:break-all;">{{ $invoice_id }}</p>
                </td>
                <td width="50%" style="padding:18px 22px; border-bottom:1px solid #e3e3e3;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Reference No.</p>
                  <p style="margin:0; font-size:15px; line-height:22px; color:#1f1f1f; font-weight:700; word-break:break-all;">{{ $reference_number }}</p>
                </td>
              </tr>

              <tr>
                <td width="50%" style="padding:18px 22px; border-right:1px solid #e3e3e3;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Member Name</p>
                  <p style="margin:0; font-size:16px; line-height:22px; color:#1f1f1f; font-weight:700;">{{ $member_name }}</p>
                </td>
                <td width="50%" style="padding:18px 22px;">
                  <p style="margin:0 0 5px 0; font-size:13px; line-height:18px; color:#7a7a7a;">Member ID</p>
                  <p style="margin:0; font-size:16px; line-height:22px; color:#1f1f1f; font-weight:700;">{{ $member_id }}</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td style="padding:26px 32px 0 32px;">
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
        <tr>
          <td style="padding:0 0 12px 0;">
            <h2 style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:20px; line-height:28px; color:#1f1f1f; font-weight:700;">
              Payment Summary
            </h2>
          </td>
        </tr>
      </table>

      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; border:1px solid #e3e3e3;">
        <tr>
          <td style="padding:14px 18px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; color:#5f6368; border-bottom:1px solid #e3e3e3;">
            Package Amount
          </td>
          <td align="right" style="padding:14px 18px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; color:#1f1f1f; font-weight:700; border-bottom:1px solid #e3e3e3;">
            ₱{{ $package_amount }}
          </td>
        </tr>
        <tr>
          <td style="padding:14px 18px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; color:#5f6368; border-bottom:1px solid #e3e3e3;">
            Processing Fee
          </td>
          <td align="right" style="padding:14px 18px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:20px; color:#1f1f1f; font-weight:700; border-bottom:1px solid #e3e3e3;">
            ₱0.00
          </td>
        </tr>
        <tr>
          <td style="padding:18px; font-family:Arial, Helvetica, sans-serif; font-size:16px; line-height:22px; color:#1f1f1f; font-weight:700; background-color:#f8f8f8;">
            Total Paid
          </td>
          <td align="right" style="padding:18px; font-family:Arial, Helvetica, sans-serif; font-size:18px; line-height:24px; color:#166534; font-weight:700; background-color:#f8f8f8;">
            ₱{{ $package_amount }}
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <tr>
    <td align="center" style="padding:30px 32px 8px 32px;">
      <a href="{{ $dashboard_link }}" target="_blank" style="display:inline-block; background-color:#166534; color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:20px; font-weight:700; text-decoration:none; padding:14px 28px; border-radius:6px;">
        View Package Details
      </a>
    </td>
  </tr>

  <tr>
    <td style="padding:20px 32px 30px 32px;">
      <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:13px; line-height:21px; color:#6a6a6a;">
        This invoice confirms that a package purchase request has been generated under your Lulu member account. Activation, documentation release, and account status updates are subject to successful payment verification and internal compliance review.
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
              Official Joint Venture Program Notification
            </p>
          </td>
        </tr>

        <tr>
          <td style="border-top:1px solid #4a4a4a; padding:16px 0 0 0;">
            <p style="margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:20px; color:#b8b8b8;">
              This automated email was sent to {{ $member_email }}. Please do not reply directly to this message. For assistance, contact Lulu Support at lotteriaphilippines@gmail.com.
            </p>
            <p style="margin:12px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:18px; color:#9f9f9f;">
              Disclaimer: This email and its contents are intended only for the registered recipient. Package confirmation, account activation, and related documentation remain subject to verification, applicable terms, and official company procedures.
            </p>
            <p style="margin:14px 0 0 0; padding:0; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:18px; color:#9f9f9f;">
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
