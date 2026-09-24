<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Your Lulu withdrawal confirmation</title>
</head>
<body style="margin:0;background:#f6f8fb;color:#1f1f1f;font-family:Arial,Helvetica,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:28px 12px;background:#f6f8fb;">
    <tr><td align="center">
      <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px;background:#fff;border-radius:14px;overflow:hidden;">
        <tr><td style="padding:28px 32px;background:#fff7f7;border-bottom:2px solid #f1d0d4;">
          <strong style="font-size:28px;color:#166534;">Lulu</strong>
        </td></tr>
        <tr><td style="padding:32px;">
          <h1 style="margin:0;font-size:28px;line-height:36px;">Withdrawal confirmation</h1>
          <p style="margin:12px 0 0;color:#5f6368;line-height:24px;">Dear {{ $clientName }}, your withdrawal request has been recorded using the account details registered on your Lulu account.</p>
        </td></tr>
        <tr><td style="padding:0 32px 20px;">
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;background:#fff8f0;border:1px solid #f0d29b;">
            <tr><td style="padding:18px 20px;">
              <p style="margin:0 0 10px;color:#9b6a12;font-size:13px;font-weight:700;text-transform:uppercase;">Withdrawal details</p>
              <p style="margin:6px 0;"><strong>Transaction ID:</strong> {{ $transactionReference }}</p>
              <p style="margin:6px 0;"><strong>Client:</strong> {{ $clientName }}</p>
              <p style="margin:6px 0;"><strong>Transaction:</strong> {{ $transactionType }}</p>
              <p style="margin:6px 0;"><strong>Account:</strong> {{ $accountProvider }} ({{ $accountNumber }})</p>
              <p style="margin:6px 0;"><strong>Date:</strong> {{ $withdrawalDate }}</p>
              <p style="margin:6px 0;"><strong>Status:</strong> {{ $status }}</p>
            </td></tr>
          </table>
        </td></tr>
        <tr><td style="padding:0 32px 24px;">
          <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr><td style="padding:9px 0;border-bottom:1px solid #edf0f2;">Withdrawal amount</td><td align="right" style="padding:9px 0;border-bottom:1px solid #edf0f2;">${{ $withdrawalAmount }}</td></tr>
            <tr><td style="padding:9px 0;border-bottom:1px solid #edf0f2;">Processing fee (5%)</td><td align="right" style="padding:9px 0;border-bottom:1px solid #edf0f2;">-${{ $processingFee }}</td></tr>
            <tr><td style="padding:14px 0 0;font-weight:700;">Total withdrawn</td><td align="right" style="padding:14px 0 0;font-weight:700;color:#166534;">${{ $totalWithdrawn }}</td></tr>
          </table>
        </td></tr>
        <tr><td style="padding:0 32px 32px;color:#5f6368;line-height:22px;">
          <p style="margin:0 0 16px;">The total withdrawn is the requested withdrawal amount less the 5% processing fee.</p>
          <a href="{{ $dashboardUrl }}" style="display:inline-block;padding:12px 18px;border-radius:8px;background:#166534;color:#fff;text-decoration:none;font-weight:700;">Open your dashboard</a>
        </td></tr>
        <tr><td style="padding:18px 32px;background:#f8fafc;color:#7a7f85;font-size:12px;line-height:18px;">Lulu Philippines · luluphilippines@gmail.com<br>© {{ date('Y') }} Lulu Philippines. All rights reserved.</td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
