<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#087244">
  <title>Payment Options | Lulu Philippines</title>
  <style>
    :root { color-scheme:light; --green:#087244; --dark:#005b35; --ink:#14271f; --muted:#62736a; --line:#e2ebe4; --surface:#fff; --page:#f3f7f4; }
    * { box-sizing:border-box; }
    body { min-height:100vh; margin:0; background:var(--page); color:var(--ink); font:15px/1.55 Inter,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif; }
    header { padding:24px 20px; background:linear-gradient(120deg,var(--dark),var(--green)); color:#fff; }
    .header-inner, main { width:min(100% - 32px,880px); margin:0 auto; }
    .brand { color:#e2f1e7; font-size:13px; font-weight:700; letter-spacing:.08em; text-decoration:none; text-transform:uppercase; }
    h1 { margin:28px 0 6px; font-size:clamp(28px,6vw,40px); line-height:1.15; }
    header p { max-width:580px; margin:0; color:#e0eee5; }
    main { padding:28px 0 48px; }
    .notice { margin:0 0 22px; padding:14px 16px; border:1px solid #d7e7da; border-radius:14px; background:#edf6ef; color:#315542; }
    .options { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr)); gap:16px; }
    .option { display:flex; flex-direction:column; align-items:center; min-width:0; padding:20px; border:1px solid var(--line); border-radius:18px; background:var(--surface); box-shadow:0 8px 24px #143b2410; text-align:center; }
    .option h2 { margin:0 0 14px; font-size:18px; }
    .qr-frame { display:grid; width:100%; max-width:280px; aspect-ratio:1; place-items:center; padding:10px; border:1px solid var(--line); border-radius:12px; background:#fff; }
    .qr-frame img { display:block; width:100%; height:100%; object-fit:contain; }
    .download { display:inline-flex; min-height:46px; align-items:center; justify-content:center; width:100%; max-width:280px; margin-top:16px; padding:10px 16px; border-radius:12px; background:var(--green); color:#fff; font-weight:750; text-decoration:none; }
    .download:hover { background:var(--dark); }
    .download:focus-visible,.brand:focus-visible { outline:3px solid #b8cc56; outline-offset:3px; }
    footer { padding:0 16px 28px; color:var(--muted); font-size:12px; text-align:center; }
    @media(max-width:560px) { header { padding:20px 0 24px; } main { padding-top:20px; } .option { padding:16px; } }
  </style>
</head>
<body>
  <header>
    <div class="header-inner">
      <a class="brand" href="{{ route('public.home') }}">Lulu Philippines</a>
      <h1>Payment Options</h1>
      <p>Choose your payment provider to view and download its QR code.</p>
    </div>
  </header>
  <main>
    <p class="notice">Before sending a payment, confirm the amount and payment instructions with Lulu through an official channel.</p>
    <section class="options" aria-label="Payment provider QR codes">
      <article class="option">
        <h2>Palawan Pay</h2>
        <div class="qr-frame"><img src="{{ asset('PalawanPay.png') }}" alt="Palawan Pay payment QR code"></div>
        <a class="download" href="{{ asset('PalawanPay.png') }}" download="PalawanPay.png">Download QR code</a>
      </article>
      <article class="option">
        <h2>Landbank</h2>
        <div class="qr-frame"><img src="{{ asset('LandbankQR.png') }}" alt="Landbank payment QR code"></div>
        <a class="download" href="{{ asset('LandbankQR.png') }}" download="LandbankQR.png">Download QR code</a>
      </article>
      <article class="option">
        <h2>BPI</h2>
        <div class="qr-frame"><img src="{{ asset('BPIQR.png') }}" alt="BPI payment QR code"></div>
        <a class="download" href="{{ asset('BPIQR.png') }}" download="BPIQR.png">Download QR code</a>
      </article>
    </section>
  </main>
  <footer>Payment options are available without signing in.</footer>
</body>
</html>
