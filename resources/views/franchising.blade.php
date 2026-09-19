@extends('layouts.app')

@section('content')
<style>
  :root{--lot-red:#166534;--charcoal:#111827;--bg:#F8FAFC;--gold:#D4AF37;--text:#1F2937;--glass:rgba(255,255,255,0.90)}
  .franchise-hero{background-image:linear-gradient(rgba(17,24,39,0.68),rgba(17,24,39,0.56)),url('{{ asset('Lotteria.png') }}');background-size:cover;background-position:center;color:#fff;padding:56px 20px;border-radius:12px;margin-bottom:28px;position:relative;overflow:hidden}
  .hero-inner{max-width:1100px;margin:0 auto;display:flex;gap:28px;align-items:center;justify-content:space-between;flex-wrap:nowrap}
  .hero-content{max-width:640px}
  .hero-title{font-size:clamp(30px,5vw,52px);margin:0;font-weight:800;letter-spacing:-0.02em;color:#fff;text-shadow:0 6px 20px rgba(0,0,0,0.5);line-height:1.05}
  .hero-sub{color:rgba(255,255,255,0.92);margin:12px 0 18px;font-size:18px}
  .hero-stats{display:flex;gap:12px;margin:18px 0}
  .stat{background:rgba(255,255,255,0.06);padding:12px 14px;border-radius:10px;text-align:center}
  .stat .num{font-weight:800;font-size:18px;color:var(--gold)}
  .cta{display:inline-flex;gap:12px;align-items:center;background:var(--lot-red);color:#fff;padding:12px 18px;border-radius:12px;text-decoration:none;border:0;cursor:pointer;font-weight:700}
  .container{max-width:1100px;margin:0 auto;padding:0 18px}

  /* Packages */
  .packages{margin-top:36px}
  .cards-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:20px;align-items:stretch}
  .package-card{background:var(--glass);border-radius:24px;padding:22px;box-shadow:0 20px 50px rgba(0,0,0,.08);border:1px solid rgba(17,24,39,0.04);display:flex;flex-direction:column;position:relative}
  .package-card .media{height:160px;border-radius:14px;overflow:hidden;margin-bottom:12px;background-size:cover;background-position:center}
  .package-title{font-size:18px;margin:0;color:var(--charcoal);font-weight:800}
  .package-price{font-size:20px;font-weight:900;color:var(--lot-red);margin-top:8px}
  .package-what{color:#374151;margin:10px 0;flex:1}
  .features{list-style:none;padding:0;margin:12px 0;color:#374151}
  .features li{margin:8px 0}
  .btn-row{display:flex;gap:12px;margin-top:12px}
  .btn-select{background:var(--lot-red);color:#fff;padding:10px 14px;border-radius:10px;border:0;cursor:pointer;font-weight:700;flex:1}
  .btn-view{background:#fff;color:var(--lot-red);padding:10px 14px;border-radius:10px;border:1px solid rgba(22,101,52,0.12);cursor:pointer;flex:1}

  /* Hero media */
  .hero-media{min-width:260px;text-align:right;display:flex;align-items:center;justify-content:flex-end}
  .hero-media img{max-width:360px;width:100%;height:auto;border-radius:12px;box-shadow:0 12px 36px rgba(0,0,0,0.18);transform:translateY(2px)}

  /* Ribbon */
  .ribbon{position:absolute;left:18px;top:18px;background:var(--gold);color:var(--charcoal);padding:6px 12px;border-radius:999px;font-weight:800;font-size:12px;box-shadow:0 8px 18px rgba(0,0,0,0.08)}

  /* Comparison */
  .compare{width:100%;border-collapse:collapse;margin-top:32px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 12px 36px rgba(0,0,0,0.06)}
  .compare th,.compare td{padding:14px 18px;border-bottom:1px solid #eef2f7;text-align:left}
  .compare th{background:#f8fafc;font-weight:800;color:var(--text)}

  /* Timeline */
  .timeline{margin-top:36px;display:flex;flex-direction:column;gap:12px}
  .step{display:flex;gap:12px;align-items:flex-start}
  .step .num{width:44px;height:44px;border-radius:12px;background:var(--lot-red);color:#fff;display:grid;place-items:center;font-weight:900}
  .step .desc{color:#374151}

  /* Stats */
  .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:28px}
  .stat-card{background:#fff;padding:18px;border-radius:12px;text-align:center;box-shadow:0 10px 30px rgba(0,0,0,0.06)}
  .stat-card .big{font-size:28px;font-weight:900;color:var(--charcoal)}

  .bottom-nav {
    position: fixed !important;
    left: 12px !important;
    right: 12px !important;
    bottom: 12px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-around !important;
    gap: 18px !important;
    max-width: 640px !important;
    margin: 0 auto !important;
    padding: 0 22px !important;
    height: 86px !important;
    background: rgba(255,255,255,.95) !important;
    border-radius: 30px !important;
    border: 1px solid rgba(239,239,247,.90) !important;
    backdrop-filter: blur(18px) !important;
    box-shadow: 0 8px 24px rgba(15,23,42,.06) !important;
    z-index: 99998 !important;
    transition: transform .25s ease, opacity .2s ease !important;
  }

  .bottom-nav.hidden {
    transform: translateY(120%) !important;
    opacity: 0 !important;
    pointer-events: none !important;
  }

  .bottom-nav .nav-item {
    position: relative !important;
    z-index: 2 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 6px !important;
    color: #6b7280 !important;
    font-weight: 500 !important;
    font-size: 13px !important;
    text-decoration: none !important;
    transition: transform .2s ease, color .2s ease !important;
  }

  .bottom-nav .nav-item.active,
  .bottom-nav .nav-item:hover {
    transform: translateY(-2px) !important;
    color: #111827 !important;
  }

  .bottom-nav .nav-item img {
    width: 22px !important;
    height: 22px !important;
  }

  .nav-scan {
    z-index: 1 !important;
  }

  .bottom-nav a { text-decoration: none !important; }

  .nav-scan {
    position: relative;
    top: -24px;
    width: 64px;
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    box-shadow: none;
    transition: transform .18s ease;
  }

  .nav-scan:hover { transform: translateY(-6px); }

  .nav-scan img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
  }

  .fab-scrim {
    position: fixed;
    inset: 0;
    z-index: 120;
    background: rgba(0,0,0,0.52);
    opacity: 0;
    visibility: hidden;
    transition: opacity .28s ease;
  }

  .fab-scrim.is-open {
    opacity: 1;
    visibility: visible;
  }

  .fab-panel {
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 130;
    transform: translateY(110%);
    transition: transform .34s cubic-bezier(.22,1,.36,1);
  }

  .fab-panel.is-open { transform: translateY(0); }

  .fab-sheet {
    border-radius: 28px 28px 0 0;
    padding: 18px 18px 28px;
    background: #fff;
    box-shadow: 0 -18px 60px rgba(3,7,18,.14);
  }

  .fab-sheet-handle {
    width: 68px;
    height: 6px;
    margin: 0 auto 14px;
    border-radius: 999px;
    background: #e9e9e9;
  }

  .fab-sheet-title {
    font-size: 16px;
    font-weight: 900;
    color: #121212;
    text-align: center;
    margin-bottom: 18px;
  }

  .fab-actions { display: grid; gap: 12px; }

  .fab-action {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    border-radius: 18px;
    background: #f9fafb;
    color: #121212;
    text-decoration: none;
    font-weight: 800;
    transition: transform .2s ease, background .2s ease;
    transform: translateY(24px);
    opacity: 0;
  }

  .fab-panel.is-open .fab-action { transform: translateY(0); opacity: 1; }

  .fab-action:hover {
    background: #fff;
    transform: translateY(-2px);
  }

  .fab-action-icon {
    width: 38px;
    height: 38px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #166534, #ff6b4a);
    color: #fff;
    font-size: 18px;
  }

  .fab-action:nth-child(1) { transition-delay:.05s; }
  .fab-action:nth-child(2) { transition-delay:.10s; }
  .fab-action:nth-child(3) { transition-delay:.15s; }
  .fab-action:nth-child(4) { transition-delay:.20s; }
  .fab-action:nth-child(5) { transition-delay:.25s; }
  .fab-action:nth-child(6) { transition-delay:.30s; }

  .fab-close {
    margin-top: 16px;
    width: 100%;
    border: none;
    border-radius: 16px;
    padding: 14px 16px;
    background: #f5f5f5;
    color: #4b5563;
    font-weight: 900;
    cursor: pointer;
  }

  @media (max-width:900px){
    .cards-grid{grid-template-columns:1fr}
    .stats-grid{grid-template-columns:repeat(2,1fr)}
    .hero-inner{flex-direction:column;align-items:flex-start}
    .hero-media{display:none}
  }
</style>

<div class="franchise-hero">
  <div class="hero-inner container">
    <div class="hero-content">
      <div style="color:var(--gold);font-weight:800">LULU PHILIPPINES</div>
      <h1 class="hero-title">Become Part of a Global Restaurant Brand</h1>
      <p class="hero-sub">1,600+ Stores Worldwide · Millions of Customers Served. Own a LULU franchise today.</p>
      <div class="hero-stats">
        <div class="stat"><div class="num">1,600+</div><div style="font-size:12px;color:rgba(255,255,255,0.85)">Stores Worldwide</div></div>
        <div class="stat"><div class="num">40+</div><div style="font-size:12px;color:rgba(255,255,255,0.85)">Years Experience</div></div>
        <div class="stat"><div class="num">Millions</div><div style="font-size:12px;color:rgba(255,255,255,0.85)">Served Annually</div></div>
      </div>
      <a href="#packages" class="cta">View Franchise Packages</a>
    </div>
    <div class="hero-media">
      <img src="{{ asset('Lotteria.png') }}" alt="Lulu" loading="lazy" decoding="async">
    </div>
  </div>
</div>

<div class="container" id="packages">
  <h2 style="margin:0 0 12px;color:var(--charcoal)">Franchise Packages</h2>
  <p style="color:#6b7280;margin:0 0 18px">Premium packages designed for different site sizes and operational needs.</p>

  <div class="packages">
    <div class="cards-grid">
      <div class="package-card" data-package="franchise_40pyeong">
        <div class="media" style="background-image:url('{{ asset('Franchise.png') }}')" loading="lazy" decoding="async"></div>
        <div class="package-body">
          <div class="package-title">LULU EXPRESS — 40 PYEONG</div>
          <div class="package-price">₱23.5M</div>
          <div class="package-what">Ideal for shopping centers and business districts. Compact dine-in format with strong ROI potential.</div>
          <ul class="features">
            <li>✓ Brand Rights</li>
            <li>✓ Training & Onboarding</li>
            <li>✓ Operations Support</li>
            <li>✓ Marketing Assistance</li>
          </ul>
          <div class="btn-row">
            <button class="btn-select select-package" data-package="franchise_40pyeong" data-app-url="{{ asset('franchise_40pyeong_application.html') }}">Select Package</button>
            <button type="button" class="btn-view view-package" data-html-url="{{ asset('franchise_40pyeong.html') }}" data-presentation="{{ asset('Lotteria_Philippines_40_Pyeong_Franchise_Cost_Presentation.pptx') }}" data-image="{{ asset('Franchise.png') }}">View Details</button>
          </div>
        </div>
      </div>

      <div class="package-card" data-package="franchise_60pyeong">
        <div class="ribbon">MOST POPULAR</div>
        <div class="media" style="background-image:url('{{ asset('60pyeong.png') }}')" loading="lazy" decoding="async"></div>
        <div class="package-body">
          <div class="package-title">LULU PREMIUM — 60 PYEONG</div>
          <div class="package-price">₱45M</div>
          <div class="package-what">Premium full-service branch with extended seating and delivery-ready operations.</div>
          <ul class="features">
            <li>✓ Brand Rights</li>
            <li>✓ Comprehensive Training</li>
            <li>✓ Full Operations Support</li>
            <li>✓ National Marketing</li>
            <li>✓ Site & Construction Assistance</li>
          </ul>
          <div class="btn-row">
            <button class="btn-select select-package" data-package="franchise_60pyeong" data-app-url="{{ asset('franchise_60pyeong_application.html') }}">Select Package</button>
            <button type="button" class="btn-view view-package" data-html-url="{{ asset('franchise_60pyeong.html') }}" data-image="{{ asset('60pyeong.png') }}">View Details</button>
          </div>
        </div>
      </div>
    </div>

    <input type="hidden" id="selected_package_key" value="">
  </div>

  <!-- Comparison table -->
  <h3 style="margin-top:28px;color:var(--charcoal)">Quick Comparison</h3>
  <table class="compare" aria-label="Package comparison">
    <thead>
      <tr><th>Feature</th><th>40 Pyeong</th><th>60 Pyeong</th></tr>
    </thead>
    <tbody>
      <tr><td>Investment</td><td>₱23.5M</td><td>₱45M</td></tr>
      <tr><td>Dining Capacity</td><td>✓</td><td>✓</td></tr>
      <tr><td>Drive-Thru</td><td>Optional</td><td>Included</td></tr>
      <tr><td>Training</td><td>✓</td><td>✓</td></tr>
      <tr><td>Marketing</td><td>✓</td><td>✓</td></tr>
      <tr><td>Site Assistance</td><td>✓</td><td>✓</td></tr>
    </tbody>
  </table>

  <!-- Timeline -->
  <h3 style="margin-top:28px;color:var(--charcoal)">Franchise Journey</h3>
  <div class="timeline" role="list">
    <div class="step"><div class="num">1</div><div class="desc">Submit Inquiry</div></div>
    <div class="step"><div class="num">2</div><div class="desc">Attend Presentation</div></div>
    <div class="step"><div class="num">3</div><div class="desc">Financial Evaluation</div></div>
    <div class="step"><div class="num">4</div><div class="desc">Sign Franchise Agreement</div></div>
    <div class="step"><div class="num">5</div><div class="desc">Store Construction</div></div>
    <div class="step"><div class="num">6</div><div class="desc">Grand Opening</div></div>
  </div>

  <!-- Why LULU -->
  <h3 style="margin-top:28px;color:var(--charcoal)">Why LULU</h3>
  <div class="stats-grid">
    <div class="stat-card"><div class="big">1,600+</div><div>Stores Worldwide</div></div>
    <div class="stat-card"><div class="big">40+</div><div>Years Experience</div></div>
    <div class="stat-card"><div class="big">Millions</div><div>Served Every Year</div></div>
    <div class="stat-card"><div class="big">Global</div><div>K-Food Brand</div></div>
  </div>

</div>

<div class="fab-scrim" id="fabScrim" aria-hidden="true"></div>
<div class="fab-panel" id="fabPanel" aria-hidden="true">
  <div class="fab-sheet" role="dialog" aria-modal="true" aria-label="Quick actions menu">
    <div class="fab-sheet-handle"></div>
    <div class="fab-sheet-title">Quick actions</div>
    <div class="fab-actions">
      <a class="fab-action" href="{{ route('invest') }}">
        <span class="fab-action-icon">💰</span>
        <span>Buy shares</span>
      </a>
      <a class="fab-action" href="{{ route('send') }}">
        <span class="fab-action-icon">📤</span>
        <span>Send</span>
      </a>
      <a class="fab-action" href="{{ route('withdraw') }}">
        <span class="fab-action-icon">🏧</span>
        <span>Withdraw</span>
      </a>
      <a class="fab-action" href="{{ route('referrals') }}">
        <span class="fab-action-icon">🤝</span>
        <span>Referrals</span>
      </a>
      <a class="fab-action" href="{{ route('franchising') }}">
        <span class="fab-action-icon">🏬</span>
        <span>Franchise</span>
      </a>
      <a class="fab-action" href="{{ route('cards') }}">
        <span class="fab-action-icon">💳</span>
        <span>Cards</span>
      </a>
      <a class="fab-action" href="{{ route('loan') }}">
        <span class="fab-action-icon">🪙</span>
        <span>Loans</span>
      </a>
    </div>
    <button class="fab-close" type="button" id="fabClose">Close menu</button>
  </div>
</div>

<nav class="bottom-nav" aria-hidden="false">
  <a class="nav-item" href="{{ route('dashboard') }}">
    <img src="{{ asset('home.png') }}" alt="Home" loading="eager" decoding="async">
    <div>Home</div>
  </a>
  <a class="nav-item" href="{{ route('history') }}">
    <img src="{{ asset('history.png') }}" alt="History" loading="eager" decoding="async">
    <div>History</div>
  </a>
  <a class="nav-item" href="#" id="fabToggle">
    <div class="nav-scan">
      <img src="{{ asset('menu.png') }}" alt="Menu" loading="eager" decoding="async">
    </div>
  </a>
  <a class="nav-item" href="{{ route('unavailable') }}">
    <img src="{{ asset('reward.png') }}" alt="Rewards" loading="eager" decoding="async">
    <div>Rewards</div>
  </a>
  <a class="nav-item active" href="{{ route('franchising') }}">
    <img src="{{ asset('profile.png') }}" alt="Franchise" loading="eager" decoding="async">
    <div>Franchise</div>
  </a>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var selectButtons = document.querySelectorAll('.select-package');
  var packageCards = document.querySelectorAll('.package-card');
  var selectedInput = document.getElementById('selected_package_key');
  var viewButtons = document.querySelectorAll('.view-package');

  function normalizeUrl(url) {
    if (!url) return '';
    if (/^(https?:)?\/\//i.test(url)) return url;
    if (url.charAt(0) !== '/') url = '/' + url;
    return window.location.origin + url;
  }

  selectButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var pkg = btn.dataset.package;
      selectedInput.value = pkg;
      packageCards.forEach(function (card) { card.classList.remove('selected'); });
      var card = btn.closest('.package-card');
      if (card) card.classList.add('selected');

      var original = btn.textContent;
      btn.textContent = 'Selected';
      setTimeout(function () { btn.textContent = original; }, 1200);

      var appUrl = btn.dataset.appUrl;
      if (appUrl) {
        window.open(normalizeUrl(appUrl), '_blank');
        return;
      }
    });
  });

  viewButtons.forEach(function (button) {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      var htmlUrl = button.dataset.htmlUrl;

      if (htmlUrl) {
        window.location.href = normalizeUrl(htmlUrl);
      }
    });
  });

  var fabToggle = document.getElementById('fabToggle');
  var fabScrim = document.getElementById('fabScrim');
  var fabPanel = document.getElementById('fabPanel');
  var fabClose = document.getElementById('fabClose');

  function openFabMenu(event) {
    if (event) event.preventDefault();
    if (!fabScrim || !fabPanel) return;
    fabScrim.classList.add('is-open');
    fabPanel.classList.add('is-open');
    fabScrim.setAttribute('aria-hidden', 'false');
    fabPanel.setAttribute('aria-hidden', 'false');
  }

  function closeFabMenu() {
    if (!fabScrim || !fabPanel) return;
    fabScrim.classList.remove('is-open');
    fabPanel.classList.remove('is-open');
    fabScrim.setAttribute('aria-hidden', 'true');
    fabPanel.setAttribute('aria-hidden', 'true');
  }

  if (fabToggle) {
    fabToggle.addEventListener('click', openFabMenu);
  }

  if (fabScrim) {
    fabScrim.addEventListener('click', closeFabMenu);
  }

  if (fabClose) {
    fabClose.addEventListener('click', closeFabMenu);
  }

  var bottomNav = document.querySelector('.bottom-nav');
  var lastScroll = window.pageYOffset || document.documentElement.scrollTop;
  var touchStartY = 0;
  var touchEndY = 0;

  function updateNavVisibility(deltaY) {
    if (!bottomNav) return;
    if (deltaY > 10) {
      bottomNav.classList.add('hidden');
    } else if (deltaY < -10) {
      bottomNav.classList.remove('hidden');
    }
  }

  window.addEventListener('scroll', function () {
    var currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    updateNavVisibility(currentScroll - lastScroll);
    lastScroll = currentScroll;
  }, { passive: true });

  window.addEventListener('touchstart', function (event) {
    touchStartY = event.touches[0].clientY;
  }, { passive: true });

  window.addEventListener('touchmove', function (event) {
    touchEndY = event.touches[0].clientY;
  }, { passive: true });

  window.addEventListener('touchend', function () {
    updateNavVisibility(touchStartY - touchEndY);
    touchStartY = 0;
    touchEndY = 0;
  });

  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closeFabMenu(); } });
});
</script>

@endsection
