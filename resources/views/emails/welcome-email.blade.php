<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Lulu</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f2eb; font-family:Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; background-color:#f6f2eb; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:640px; width:100%; background-color:#ffffff; border-collapse:collapse; border-radius:16px; overflow:hidden;">
                    <tr>
                        <td align="center" style="padding:0; background-color:#ffffff;">
                            <img src="{{ asset('Welcome.png') }}" alt="Welcome to Lulu" width="640" style="display:block; width:100%; max-width:640px; height:auto; border:0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 32px 18px 32px;">
                            <h1 style="margin:0 0 12px 0; font-size:28px; line-height:36px; color:#1f1f1f; font-weight:700;">Welcome, {{ $user_name }}!</h1>
                            <p style="margin:0; font-size:16px; line-height:26px; color:#5a5a5a;">Thank you for joining Lulu. Your account is now ready, and you can begin exploring your investor dashboard right away.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 24px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="background-color:#fff7f0; border:1px solid #f1d8bb; border-radius:10px; width:100%;">
                                <tr>
                                    <td style="padding:18px 20px;">
                                        <p style="margin:0 0 8px 0; font-size:14px; line-height:22px; color:#8a5a15; font-weight:700; text-transform:uppercase; letter-spacing:0.4px;">What happens next?</p>
                                        <p style="margin:0; font-size:15px; line-height:24px; color:#4f4f4f;">Log in to your dashboard, complete your setup, and discover the investment opportunities available to you.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 32px 32px;">
                            <a href="{{ $login_link }}" style="display:inline-block; background-color:#166534; color:#ffffff; text-decoration:none; font-size:15px; line-height:20px; font-weight:700; padding:14px 24px; border-radius:6px;">Go to Login</a>
                            <a href="{{ $dashboard_link }}" style="display:inline-block; margin-left:10px; color:#166534; text-decoration:none; font-size:15px; line-height:20px; font-weight:700;">Open Dashboard</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 32px 32px 32px; background-color:#2f2f2f;">
                            <p style="margin:0 0 6px 0; font-size:18px; line-height:24px; color:#ffffff; font-weight:700;">Lulu</p>
                            <p style="margin:0; font-size:13px; line-height:20px; color:#cfcfcf;">Welcome aboard and thank you for choosing Lulu.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
