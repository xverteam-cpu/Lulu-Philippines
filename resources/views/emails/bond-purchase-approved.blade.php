<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your bond purchase has been approved</title>
</head>
<body style="margin:0;padding:0;background-color:#f2f5f3;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#f2f5f3;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:640px;width:100%;background-color:#ffffff;border-collapse:collapse;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="padding:32px 32px 12px;">
                            <p style="margin:0 0 8px;font-size:13px;line-height:20px;color:#166534;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Lulu</p>
                            <h1 style="margin:0;font-size:28px;line-height:36px;color:#1f1f1f;font-weight:700;">Your bond purchase has been approved</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 18px;">
                            <p style="margin:0;font-size:16px;line-height:26px;color:#5a5a5a;">Hello {{ $recipient_name }},</p>
                            <p style="margin:12px 0 0;font-size:16px;line-height:26px;color:#5a5a5a;">Your purchase of the <strong>{{ $package_name }}</strong> bond for <strong>${{ $package_amount }}</strong> has been approved. The bond is now active and available in your Lulu account.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 28px;">
                            <a href="{{ $dashboard_link }}" style="display:inline-block;background-color:#166534;color:#ffffff;text-decoration:none;font-size:15px;line-height:20px;font-weight:700;padding:14px 24px;border-radius:6px;">View Your Dashboard</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px 32px;background-color:#2f2f2f;">
                            <p style="margin:0;font-size:13px;line-height:20px;color:#cfcfcf;">Thank you for being part of the Lulu community.</p>
                            <p style="margin:12px 0 0;font-size:12px;line-height:20px;color:#b8b8b8;">For assistance, contact Lulu Support at official@luluphilippines.com.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
