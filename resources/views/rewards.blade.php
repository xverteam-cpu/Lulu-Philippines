<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rewards</title>
  <style>
    :root {
      color-scheme: light;
    }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: linear-gradient(180deg, #f4fbf7 0%, #ffffff 100%);
      color: #222;
    }
    .shell {
      max-width: 760px;
      margin: 0 auto;
      padding: 20px 16px 32px;
    }
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 16px;
      color: #166534;
      text-decoration: none;
      font-weight: 700;
    }
    .card {
      background: linear-gradient(135deg, #ffffff 0%, #fff8f8 100%);
      border-radius: 22px;
      box-shadow: 0 16px 40px rgba(215, 25, 32, 0.12);
      padding: 18px;
      display: flex;
      flex-direction: column;
      gap: 14px;
      border: 1px solid rgba(215, 25, 32, 0.08);
    }
    .card img {
      width: 100%;
      border-radius: 14px;
      display: block;
      object-fit: cover;
    }
    .eyebrow {
      display: inline-flex;
      width: fit-content;
      padding: 6px 10px;
      border-radius: 999px;
      background: #ffe9ec;
      color: #b0000f;
      font-size: 12px;
      font-weight: 800;
      letter-spacing: 0.04em;
      text-transform: uppercase;
    }
    .title {
      font-size: 24px;
      font-weight: 800;
      color: #111;
      margin: 0;
    }
    .subtitle {
      font-size: 14px;
      color: #666;
      line-height: 1.6;
      margin: 0;
    }
    .reward-pill {
      display: inline-flex;
      width: fit-content;
      padding: 8px 12px;
      border-radius: 999px;
      background: #fff1f2;
      color: #166534;
      font-weight: 700;
      font-size: 13px;
    }
    .claim-btn {
      border: 0;
      border-radius: 999px;
      padding: 13px 18px;
      font-size: 15px;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(135deg, #166534, #ff4f5c);
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: fit-content;
      min-width: 168px;
      box-shadow: 0 8px 18px rgba(215, 25, 32, 0.2);
    }
    .claim-actions {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }
    .claim-btn:disabled {
      opacity: 0.7;
      cursor: default;
      box-shadow: none;
    }
    .status {
      background: #f4fff7;
      border: 1px solid #7ad491;
      color: #1e7a3a;
      border-radius: 12px;
      padding: 12px 14px;
      font-size: 14px;
    }
    @media (max-width: 480px) {
      .shell {
        padding: 16px 12px 28px;
      }
      .card {
        padding: 14px;
      }
      .title {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="shell">
    <a class="back-link" href="{{ route('dashboard') }}">← Back to Dashboard</a>

    <div class="card">
      <img src="{{ asset('signup.png') }}" alt="Sign up bonus" loading="lazy" decoding="async">
      <div class="eyebrow">Welcome reward</div>
      <div class="title">$5 Sign Up Bonus</div>
      <div class="reward-pill">One-time reward • Instant credit</div>
      <p class="subtitle">Claim your one-time welcome reward and add it instantly to your available balance.</p>

      @if (session('status'))
        <div class="status">{{ session('status') }}</div>
      @endif

      <div class="claim-actions">
        @if (! empty($signupBonusClaimed))
          <button class="claim-btn" type="button" disabled>Claimed</button>
        @else
          <form method="POST" action="{{ route('rewards.claim-signup-bonus') }}">
            @csrf
            <button class="claim-btn" type="submit">Claim $5 Bonus</button>
          </form>
        @endif
      </div>
    </div>
  </div>
</body>
</html>
