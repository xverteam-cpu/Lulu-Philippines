@include('emails.partials.header')

<p>Hello {{ $recipientName }},</p>

<p>You have been invited to set up your Lulu account. Use the private link below to choose your password and 4-digit PIN:</p>

<p><a href="{{ $setupLink }}">Set up your Lulu account</a></p>

<p>This one-time link expires in 24 hours. If you were not expecting this invitation, you can ignore this email.</p>
