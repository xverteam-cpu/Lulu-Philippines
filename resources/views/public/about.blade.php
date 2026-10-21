<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Get to know Lulu Philippines: discover public information, investor resources, and franchise opportunities.">
  <meta name="theme-color" content="#075b3d">
  <title>About Lulu Philippines</title>
  <link rel="icon" type="image/png" href="{{ asset('icons/lulu-192.png') }}">
  <link rel="stylesheet" href="{{ asset('lulu-public.css') }}">
  <style>
    :root {
      color-scheme: light;
      --about-ink: #102a20;
      --about-muted: #64736b;
      --about-green: #08734b;
      --about-deep: #06432f;
      --about-paper: #f3f7f4;
      --about-line: #dfe9e2;
      --about-gold: #e7c878;
    }

    * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body { margin: 0; background: var(--about-paper); color: var(--about-ink); font-family: Inter, "Plus Jakarta Sans", system-ui, sans-serif; }
    a { color: inherit; }
    .about-site { min-height: 100vh; overflow: hidden; }
    .about-header { position: relative; z-index: 2; background: #fff; border-bottom: 1px solid var(--about-line); }
    .about-nav { width: min(1180px, calc(100% - 48px)); min-height: 78px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .about-brand { display: inline-flex; align-items: center; gap: 12px; text-decoration: none; font-weight: 800; letter-spacing: -.03em; }
    .about-brand img { width: 48px; height: 48px; object-fit: contain; }
    .about-nav-links { display: flex; align-items: center; gap: clamp(16px, 3vw, 34px); }
    .about-nav-links a { color: #46584f; text-decoration: none; font-size: 14px; font-weight: 650; }
    .about-nav-links a:hover { color: var(--about-green); }
    .about-nav-links .about-nav-cta { padding: 12px 17px; border-radius: 999px; color: #fff; background: var(--about-green); }
    .about-hero { position: relative; isolation: isolate; overflow: hidden; padding: clamp(76px, 10vw, 140px) 24px 70px; color: #fff; background: linear-gradient(125deg, #073e2d 4%, #08734b 58%, #0a8a5a 100%); }
    .about-hero::before, .about-hero::after { position: absolute; z-index: -1; content: ""; border: 1px solid rgba(255,255,255,.13); border-radius: 50%; }
    .about-hero::before { width: 620px; height: 620px; top: -290px; right: -85px; }
    .about-hero::after { width: 450px; height: 450px; top: -205px; right: 0; }
    .about-container { width: min(1120px, 100%); margin: 0 auto; }
    .about-eyebrow { display: inline-flex; align-items: center; gap: 10px; color: #d9e9df; text-transform: uppercase; letter-spacing: .16em; font-size: 11px; font-weight: 800; }
    .about-eyebrow::before { width: 26px; height: 1px; content: ""; background: var(--about-gold); }
    .about-hero h1 { max-width: 780px; margin: 24px 0 18px; font-size: clamp(44px, 7vw, 88px); line-height: .98; letter-spacing: -.065em; }
    .about-hero h1 em { color: #d6efad; font-style: normal; }
    .about-hero-copy { max-width: 610px; margin: 0; color: #e1eee7; font-size: clamp(16px, 2vw, 19px); line-height: 1.75; }
    .about-hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 32px; }
    .about-button { display: inline-flex; min-height: 48px; align-items: center; justify-content: center; gap: 11px; padding: 0 20px; border: 1px solid transparent; border-radius: 999px; background: #fff; color: var(--about-deep); text-decoration: none; font-size: 14px; font-weight: 800; transition: transform .18s ease, background .18s ease; }
    .about-button:hover { transform: translateY(-2px); }
    .about-button-secondary { border-color: rgba(255,255,255,.45); background: transparent; color: #fff; }
    .about-intro { padding: 76px 24px 30px; }
    .about-intro-grid { display: grid; grid-template-columns: .85fr 1.15fr; align-items: start; gap: clamp(32px, 8vw, 100px); }
    .about-section-label { color: var(--about-green); text-transform: uppercase; letter-spacing: .14em; font-size: 11px; font-weight: 850; }
    .about-intro h2, .about-resources h2, .about-legalities h2 { margin: 15px 0; font-size: clamp(30px, 4vw, 48px); line-height: 1.08; letter-spacing: -.045em; }
    .about-intro-copy { margin: 0; color: var(--about-muted); font-size: 17px; line-height: 1.85; }
    .about-note { display: flex; align-items: flex-start; gap: 14px; margin-top: 25px; padding: 18px; border: 1px solid var(--about-line); border-radius: 16px; background: #fff; }
    .about-note-icon { display: grid; flex: 0 0 38px; height: 38px; place-items: center; border-radius: 12px; background: #e5f3e9; color: var(--about-green); font-weight: 900; }
    .about-note p { margin: 0; color: #52645a; font-size: 14px; line-height: 1.65; }
    .about-resources { padding: 58px 24px 92px; }
    .about-resources-head { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 26px; }
    .about-resources h2 { margin-bottom: 0; }
    .about-resources-intro { max-width: 420px; margin: 0 0 4px; color: var(--about-muted); line-height: 1.7; }
    .about-resource-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    .about-resource { position: relative; min-height: 245px; display: flex; flex-direction: column; align-items: flex-start; padding: 24px; border: 1px solid var(--about-line); border-radius: 20px; background: #fff; text-decoration: none; box-shadow: 0 12px 30px rgba(11, 57, 37, .035); transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
    .about-resource:hover { transform: translateY(-4px); border-color: #b5d6c2; box-shadow: 0 18px 36px rgba(11, 57, 37, .09); }
    .about-resource-icon { display: grid; width: 46px; height: 46px; place-items: center; border-radius: 15px; background: #e8f4ec; color: var(--about-green); font-size: 20px; font-weight: 850; }
    .about-resource h3 { margin: 28px 0 8px; font-size: 19px; letter-spacing: -.025em; }
    .about-resource p { margin: 0; color: var(--about-muted); font-size: 14px; line-height: 1.65; }
    .about-resource-arrow { position: absolute; right: 22px; bottom: 20px; color: var(--about-green); font-size: 20px; }
    .about-legalities { padding: 0 24px 92px; }
    .about-legalities-head { display: flex; align-items: end; justify-content: space-between; gap: 24px; margin-bottom: 26px; }
    .about-legalities h2 { margin-bottom: 0; }
    .about-legalities-intro { max-width: 480px; margin: 0 0 4px; color: var(--about-muted); line-height: 1.7; }
    .about-legalities-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .about-legal-document { margin: 0; overflow: hidden; border: 1px solid var(--about-line); border-radius: 20px; background: #fff; box-shadow: 0 12px 30px rgba(11, 57, 37, .04); }
    .about-legal-document a { display: grid; min-height: 280px; place-items: center; padding: 16px; background: #f7faf8; }
    .about-legal-document img { display: block; width: 100%; height: 420px; object-fit: contain; }
    .about-legal-document figcaption { padding: 17px 20px; font-size: 15px; font-weight: 750; }
    .about-footer { padding: 24px; background: #092e21; color: #d7e4dc; }
    .about-footer-inner { width: min(1120px, 100%); margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
    .about-footer p { margin: 0; font-size: 13px; }
    .about-footer a { color: #fff; text-decoration: none; font-weight: 700; }
    @media (max-width: 760px) {
      .about-nav { width: min(100% - 32px, 1180px); min-height: 68px; }
      .about-brand img { width: 42px; height: 42px; }
      .about-nav-links { gap: 14px; }
      .about-nav-links a { font-size: 12px; }
      .about-nav-links a:not(.about-nav-cta) { display: none; }
      .about-nav-links .about-nav-cta { padding: 11px 14px; }
      .about-intro-grid { grid-template-columns: 1fr; gap: 12px; }
      .about-intro { padding-top: 55px; }
      .about-resources-head { display: block; }
      .about-resources-intro { margin-top: 14px; }
      .about-resource-grid { grid-template-columns: 1fr; }
      .about-resource { min-height: 205px; }
      .about-legalities-head { display: block; }
      .about-legalities-intro { margin-top: 14px; }
      .about-legalities-grid { grid-template-columns: 1fr; }
      .about-legal-document img { height: 340px; }
      .about-footer-inner { align-items: flex-start; flex-direction: column; }
    }
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
    }
  </style>
</head>
<body class="about-site">
  <header class="about-header">
    <div class="about-nav">
      <a class="about-brand" href="{{ route('public.home') }}" aria-label="Lulu Philippines home">
        <img src="{{ asset('icons/lulu-512.png') }}" alt="">
        <span>Lulu Philippines</span>
      </a>
      <nav class="about-nav-links" aria-label="Main navigation">
        <a href="#our-story">About</a>
        <a href="#explore">Explore</a>
        <a href="#legalities">Legalities</a>
        <a href="{{ route('investors') }}">Investor information</a>
        <a class="about-nav-cta" href="{{ route('public.home') }}">Home <span aria-hidden="true">↗</span></a>
      </nav>
    </div>
  </header>

  <main>
    <section class="about-hero">
      <div class="about-container">
        <span class="about-eyebrow">Get to know us</span>
        <h1>Everything Lulu.<br><em>Closer to you.</em></h1>
        <p class="about-hero-copy">Welcome to Lulu Philippines. Explore our public information and find the right next step for you—all on one page, with no account required to browse.</p>
        <div class="about-hero-actions">
          <a class="about-button" href="#explore">Explore Lulu <span aria-hidden="true">↓</span></a>
          <a class="about-button about-button-secondary" href="{{ route('public.home') }}">Visit homepage <span aria-hidden="true">↗</span></a>
        </div>
      </div>
    </section>

    <section class="about-intro" id="our-story">
      <div class="about-container about-intro-grid">
        <div>
          <span class="about-section-label">About Lulu Philippines</span>
          <h2>A simpler way to find your way around Lulu.</h2>
        </div>
        <div>
          <p class="about-intro-copy">This public hub brings together the main areas of the Lulu Philippines website. Learn more about the business, review investor information, or explore franchise opportunities using the links below.</p>
          <div class="about-note">
            <span class="about-note-icon" aria-hidden="true">i</span>
            <p>You can read this page and explore its public links without creating an account. Account creation is only needed for services that specifically require sign-in.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="about-resources" id="explore">
      <div class="about-container">
        <div class="about-resources-head">
          <div>
            <span class="about-section-label">Explore Lulu</span>
            <h2>A place for your next step.</h2>
          </div>
          <p class="about-resources-intro">Choose an area to learn more. These pages are publicly accessible; any account requirements are explained where relevant.</p>
        </div>
        <div class="about-resource-grid">
          <a class="about-resource" href="{{ route('investors') }}#overview">
            <span class="about-resource-icon" aria-hidden="true">L</span>
            <h3>Our business</h3>
            <p>Visit the homepage to explore Lulu’s business information and company highlights.</p>
            <span class="about-resource-arrow" aria-hidden="true">↗</span>
          </a>
          <a class="about-resource" href="{{ route('investors') }}">
            <span class="about-resource-icon" aria-hidden="true">↗</span>
            <h3>Investor information</h3>
            <p>Browse the public investor information and resources available on the website.</p>
            <span class="about-resource-arrow" aria-hidden="true">↗</span>
          </a>
          <a class="about-resource" href="{{ route('franchising') }}">
            <span class="about-resource-icon" aria-hidden="true">+</span>
            <h3>Franchise opportunities</h3>
            <p>Learn about franchise opportunities and review the application information.</p>
            <span class="about-resource-arrow" aria-hidden="true">↗</span>
          </a>
        </div>
      </div>
    </section>

    <section class="about-legalities" id="legalities">
      <div class="about-container">
        <div class="about-legalities-head">
          <div>
            <span class="about-section-label">Company documents</span>
            <h2>Legalities</h2>
          </div>
          <p class="about-legalities-intro">View Lulu Philippines’ business and registration documents. Select any document to open the full-size image.</p>
        </div>
        <div class="about-legalities-grid">
          <figure class="about-legal-document">
            <a href="{{ asset('BusinessPermit.png') }}" target="_blank" rel="noopener noreferrer">
              <img src="{{ asset('BusinessPermit.png') }}" alt="Lulu Philippines business permit" loading="lazy">
            </a>
            <figcaption>Business Permit</figcaption>
          </figure>
          <figure class="about-legal-document">
            <a href="{{ asset('BIR Final.png') }}" target="_blank" rel="noopener noreferrer">
              <img src="{{ asset('BIR Final.png') }}" alt="Lulu Philippines Bureau of Internal Revenue registration certificate" loading="lazy">
            </a>
            <figcaption>BIR Registration Certificate</figcaption>
          </figure>
          <figure class="about-legal-document">
            <a href="{{ asset('BarangayCert.png') }}" target="_blank" rel="noopener noreferrer">
              <img src="{{ asset('BarangayCert.png') }}" alt="Lulu Philippines barangay business clearance" loading="lazy">
            </a>
            <figcaption>Barangay Business Clearance</figcaption>
          </figure>
          <figure class="about-legal-document">
            <a href="{{ asset('Lulucerttosell.png') }}" target="_blank" rel="noopener noreferrer">
              <img src="{{ asset('Lulucerttosell.png') }}" alt="Lulu Philippines certificate of permit to offer securities for sale" loading="lazy">
            </a>
            <figcaption>Permit to Offer Securities for Sale</figcaption>
          </figure>
        </div>
      </div>
    </section>
  </main>

  <footer class="about-footer">
    <div class="about-footer-inner">
      <p>© {{ date('Y') }} Lulu Philippines</p>
      <a href="{{ route('privacy-policy') }}">Privacy policy</a>
      <a href="{{ route('terms-of-service') }}">Terms of service</a>
      <a href="{{ route('public.home') }}">Back to homepage <span aria-hidden="true">↑</span></a>
    </div>
  </footer>
</body>
</html>
