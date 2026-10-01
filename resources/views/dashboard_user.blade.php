@extends('layouts.app')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap');

  :root {
    --color-primary: #166534;
    --color-primary-soft: #70f556;
    --color-navy: #0f172a;
    --color-title: #1e293b;
    --color-body: #475569;
    --color-muted: #94a3b8;
    --bg: #f4f6f9;
    --card: #ffffff;
    --border: #eef2f7;
    --shadow-soft: 0px 2px 8px rgba(0, 0, 0, 0.02);
    --shadow-card: 0px 10px 30px rgba(0, 0, 0, 0.04);
    --shadow-lulu: 0px 12px 24px rgba(70, 164, 45, 0.22);
    --radius: 22px;
    --radius-sm: 18px;
    --radius-lg: 30px;
  }

  body {
    background: var(--bg) !important;
    color: var(--color-body);
    font-family: Inter, 'Helvetica Neue', Helvetica, Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  .container {
    max-width: none;
    padding: 0;
  }

  .wallet-shell {
    max-width: 430px;
    margin: 0 auto 140px;
    padding: 0 16px;
  }

  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    width: 100vw;
    margin: 0 0 18px calc(50% - 50vw);
    padding: 14px 16px;
    box-sizing: border-box;
    color: #fff;
    background: #098a58;
    border-radius: 0;
    box-shadow: none;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .brand-mark {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #098a58;
    font-weight: 900;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
    overflow: hidden;
  }

  .brand-mark img {
    width: 145%;
    height: 145%;
    object-fit: contain;
    display: block;
  }

  .account-profile-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 11px;
    border: 1px solid rgba(255,255,255,.34);
    border-radius: 999px;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: background .18s ease, transform .18s ease;
  }

  .account-profile-link:hover {
    background: rgba(255,255,255,.14);
    transform: translateY(-1px);
  }

  .account-profile-link svg {
    width: 17px;
    height: 17px;
  }

  .hero {
    position: relative;
    aspect-ratio: 2.5 / 1;
    min-height: 180px;
    box-sizing: border-box;
    padding: 28px 22px 22px;
    border-radius: 30px;
    color: #fff;
    overflow: hidden;
    background: linear-gradient(135deg, #00512f 0%, #087c49 58%, #035c38 100%);
    box-shadow: 0 20px 40px rgba(0, 86, 49, .18);
  }

  .hero::before,
  .hero::after {
    position: absolute;
    content: "";
    border-radius: 50%;
    pointer-events: none;
  }

  .hero::before {
    width: 210px;
    height: 210px;
    top: -65px;
    right: -85px;
    background: rgba(85, 255, 145, .09);
  }

  .hero::after {
    width: 160px;
    height: 160px;
    right: -30px;
    bottom: -90px;
    background: rgba(103, 255, 165, .08);
  }

  .hero > * {
    position: relative;
    z-index: 1;
  }

  .hero .hero-cta.mail,
  .hero .hero-cta.buy {
    position: relative;
    padding: 14px 18px;
    justify-content: center;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    z-index: 5;
  }

  .hero .hero-cta.mail::before,
  .hero .hero-cta.buy::before {
    display: none;
  }

  .hero .hero-cta.mail {
    width: auto;
    min-width: auto;
    cursor: pointer;
  }

  .hero .hero-cta.mail .view-details-label {
    font-size: 14px;
    font-weight: 700;
  }

  .hero-top-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
  }

  .notification-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    min-width: 20px;
    height: 20px;
    line-height: 20px;
    border-radius: 999px;
    background: #166534;
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,.16);
    pointer-events: none;
  }

  .notification-panel {
    position: fixed;
    left: 12px;
    right: 12px;
    top: 84px;
    max-width: 640px;
    margin: 0 auto;
    background: #fff;
    border-radius: 24px;
    box-shadow: var(--shadow-soft), var(--shadow-card);
    overflow: hidden;
    z-index: 40;
    transform: translateY(-20px);
    opacity: 0;
    visibility: hidden;
    transition: opacity .22s ease, transform .22s ease, visibility .22s ease;
  }

  .notification-panel.is-open {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
  }

  .notification-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 18px 22px;
    background: #f8fafc;
    border-bottom: 1px solid #edf2f7;
  }

  .notification-panel-header strong {
    font-size: 16px;
    font-weight: 900;
    color: #111827;
  }

  .notification-panel-header button {
    background: transparent;
    border: none;
    color: #6b7280;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
  }

  .notification-list {
    display: grid;
    gap: 0;
  }

  .notification-item {
    padding: 16px 22px;
    border-bottom: 1px solid #f1f5f9;
  }

  .notification-item:last-child {
    border-bottom: none;
  }

  .notification-title {
    font-weight: 800;
    color: #111827;
    margin-bottom: 6px;
  }

  .notification-text {
    color: #4b5563;
    font-size: 14px;
    margin-bottom: 8px;
  }

  .notification-time {
    color: #9ca3af;
    font-size: 12px;
  }

  .notification-empty {
    padding: 20px 22px;
    color: #6b7280;
    font-size: 14px;
    text-align: center;
  }

  .sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
  }

  .hero-top {
    display: flex;
    align-items: center;
    justify-content: flex-start;
  }

  .hero-kicker {
    margin: 0;
    color: #c8ffe1;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
  }

  .hero-balance {
    margin-top: 12px;
  }

  .hero .balance-value {
    color: #fff;
    font-size: 40px;
    font-weight: 800;
    letter-spacing: -.03em;
    line-height: 1.1;
  }

  .hero-stats {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    margin-top: 20px;
  }

  .hero-stat {
    min-width: 0;
    padding: 12px 15px;
    border: 1px solid rgba(255, 255, 255, .22);
    border-radius: 20px;
    background: rgba(255, 255, 255, .1);
  }

  .hero-stat-label {
    color: #d8f5e6;
    font-size: 13px;
    line-height: 18px;
  }

  .hero-stat-value {
    margin-top: 2px;
    font-size: 19px;
    font-weight: 700;
    line-height: 24px;
  }

  .hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    color: var(--color-primary);
    border-radius: 999px;
    padding: 14px 20px;
    font-weight: 700;
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(22,101,52,.15);
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .hero-cta:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 22px rgba(22,101,52,.18);
  }

  .card {
    margin-top: 12px;
    background: var(--card);
    border-radius: var(--radius);
    padding: 14px;
    box-shadow: var(--shadow-soft), var(--shadow-card);
    border: 1px solid var(--border);
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .card:hover {
    transform: translateY(-3px);
    box-shadow: 0px 4px 18px rgba(0,0,0,0.06), 0px 14px 48px rgba(0,0,0,0.04);
  }

  .package-section {
    margin: 18px 0;
  }

  .package-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .dashboard-card-link {
    position: relative;
    display: block;
    width: 100%;
    aspect-ratio: 1.86;
    overflow: hidden;
    border-radius: 18px;
    background: transparent;
  }

  .dashboard-card-link img {
    position: absolute;
    top: -1.65%;
    left: -21.8%;
    display: block;
    width: 143.6%;
    max-width: none;
    height: 103.4%;
    object-fit: fill;
    pointer-events: none;
  }

  .franchise-card-link {
    margin-top: 14px;
  }

  .dashboard-card-link:focus-visible {
    outline: 3px solid #087a48;
    outline-offset: 3px;
  }

  .dashboard-card-link.is-stomping {
    animation: dashboard-card-stomp .46s cubic-bezier(.2,.8,.2,1);
  }

  @keyframes dashboard-card-stomp {
    0%, 100% { transform: translateY(0) scale(1); }
    28% { transform: translateY(5px) scale(.97, .95); }
    58% { transform: translateY(-7px) scale(1.02, 1.02); }
    82% { transform: translateY(1px) scale(.99, .99); }
  }

  #Cards101 {
    display: none !important;
  }

  .package-card {
    position: relative;
    aspect-ratio: 1.58;
    min-width: 0;
    overflow: hidden;
    box-sizing: border-box;
    padding: clamp(12px, 3vw, 18px);
    border-radius: 22px;
    color: #fff;
    box-shadow: 0 12px 25px rgba(0, 45, 36, .14);
    isolation: isolate;
  }

  .package-card--empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    background: rgba(255, 255, 255, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.52);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(2px);
    color: #0d5d3c;
  }

  .package-card.crunch {
    background: #006839 url("{{ asset('images/wallet-card-background.svg') }}") center / cover no-repeat;
  }

  .package-card.loaded {
    background: #d6f6dc url("{{ asset('images/savings-card-background.svg') }}") center / cover no-repeat;
    color: #0b5438;
  }

  .package-card.supreme {
    background: #153d3f url("{{ asset('images/credit-card-background.svg') }}") center / cover no-repeat;
  }

  .package-card.crunch .package-name,
  .package-card.crunch .package-caption,
  .package-card.crunch .package-label,
  .package-card.crunch .package-value {
    color: #c0c0c0;
  }

  .package-card.loaded .package-name,
  .package-card.loaded .package-caption,
  .package-card.loaded .package-label,
  .package-card.loaded .package-value {
    color: #d4af37;
  }

  .package-card.supreme .package-name,
  .package-card.supreme .package-caption,
  .package-card.supreme .package-label,
  .package-card.supreme .package-value {
    color: #e5e4e2;
  }

  .package-brand {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-right: 24px;
  }

  .package-name {
    overflow: hidden;
    font-size: clamp(15px, 3vw, 22px);
    font-weight: 800;
    line-height: 1.15;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .package-caption {
    margin-top: 3px;
    font-size: 7px;
    letter-spacing: 1.2px;
    opacity: .76;
  }

  .package-balance {
    position: absolute;
    right: 12px;
    bottom: 12px;
    left: clamp(12px, 3vw, 18px);
  }

  .package-label {
    font-size: clamp(9px, 1.6vw, 12px);
    font-weight: 700;
    letter-spacing: 1.1px;
    opacity: .82;
  }

  .package-value {
    margin-top: 2px;
    font-size: clamp(18px, 3.5vw, 28px);
    font-weight: 800;
    line-height: 1.1;
  }

  .package-menu {
    display: none;
  }

  .package-card-actions {
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    z-index: 2;
  }

  .package-plus-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: clamp(22px, 4vw, 30px);
    height: clamp(22px, 4vw, 30px);
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.86);
    color: #0b5d3b;
    text-decoration: none;
    font-size: clamp(18px, 3vw, 22px);
    line-height: 1;
    font-weight: 700;
    box-shadow: 0 10px 20px rgba(15, 118, 85, 0.18);
  }

  .package-plus-action:hover {
    transform: translateY(-1px);
  }

  .package-empty-card-copy {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 2px;
    color: #0f5c3b;
  }

  .package-empty-name {
    font-size: clamp(12px, 2.4vw, 18px);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: .04em;
    text-transform: uppercase;
  }

  .package-empty-caption {
    font-size: clamp(8px, 1.6vw, 11px);
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    opacity: 0.7;
  }

  .package-empty-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: clamp(38px, 8vw, 52px);
    height: clamp(38px, 8vw, 52px);
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.72);
    color: #0d5d3c;
    text-decoration: none;
    box-shadow: 0 10px 20px rgba(15, 118, 85, 0.12);
    font-size: clamp(24px, 5vw, 30px);
    line-height: 1;
    font-weight: 600;
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .package-empty-action:hover {
    transform: scale(1.04);
    box-shadow: 0 12px 24px rgba(15, 118, 85, 0.18);
  }

  .balance-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
  }

  .balance-meta {
    text-align: right;
  }

  .balance-label {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .12em;
    text-transform: uppercase;
    opacity: .95;
    color: #64748B;
  }

  .balance-card .balance-value {
    margin-top: 6px;
    font-size: 26px;
    font-weight: 700;
    color: var(--color-title);
  }

  .small-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,.18);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .06em;
  }

  .status-copy {
    margin-top: 8px;
    color: rgba(255,255,255,.88);
    font-size: 12px;
  }

  .actions-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 16px;
    justify-items: center;
  }

  .action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 7px;
    text-align: center;
    color: var(--color-title);
    font-weight: 500;
    font-size: 13px;
    text-decoration: none;
    width: 100%;
    max-width: 160px;
    min-width: 0;
  }

  .action .icon {
    width: 36px;
    height: 36px;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    box-shadow: none;
    border: none;
    transition: transform .18s ease;
  }

  .action:hover .icon {
    transform: translateY(-3px);
  }

  .action svg {
    width: 36px;
    height: 36px;
    object-fit: contain;
    color: var(--color-primary);
  }

  .section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  .section-title {
    font-size: 18px;
    font-weight: 600;
    color: #64748B;
  }

  .section-link {
    color: var(--color-primary);
    font-weight: 700;
    text-decoration: none;
  }

  .discover {
    display: flex;
    gap: 16px;
    overflow-x: auto;
    padding-bottom: 6px;
  }

  .discover-wrapper {
    margin-top: 24px;
  }

  .discover-item {
    min-width: 98px;
    height: 82px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    gap: 10px;
    padding: 20px;
    border-radius: var(--radius-sm);
    background: var(--card);
    box-shadow: 0 2px 6px rgba(15,23,42,.04), 0 10px 35px rgba(15,23,42,.05);
    text-align: left;
    color: var(--color-title);
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
  }

  .discover-item svg {
    width: 22px;
    height: 22px;
  }

  .promo {
    margin-top: 18px;
    border-radius: var(--radius);
    padding: 14px 16px;
    min-height: 72px;
    background: linear-gradient(180deg, #ffffff, #fcfcfd);
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid var(--border);
    box-shadow: 0 2px 6px rgba(15,23,42,.04), 0 10px 35px rgba(15,23,42,.05);
  }

  .discover-banner {
    margin-top: 18px;
  }

  .banner-carousel {
    position: relative;
    display: block;
    border-radius: var(--radius);
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 2px 6px rgba(15,23,42,.04), 0 10px 35px rgba(15,23,42,.05);
    aspect-ratio: 18 / 7;
    min-height: 92px;
    cursor: pointer;
    background: transparent;
    padding: 0;
  }

  .banner-carousel:hover {
    transform: translateY(-1px);
  }

  .banner-slide {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity .36s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 12px;
  }

  .banner-slide.is-active {
    opacity: 1;
    z-index: 1;
  }

  .banner-card {
    width: min(100%, 420px);
    height: 100%;
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid rgba(224,226,232,.9);
    box-shadow: var(--shadow-soft), var(--shadow-card);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .banner-card img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
  }

  .banner-carousel-indicators {
    position: absolute;
    left: 50%;
    bottom: 12px;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    z-index: 2;
  }

  .banner-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255,255,255,.7);
    border: none;
    cursor: pointer;
    transition: transform .18s ease, background .18s ease;
  }

  .banner-indicator.is-active {
    background: #166534;
    transform: scale(1.2);
  }

  .promo-copy {
    flex: 1;
  }

  .promo-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--color-title);
  }

  .promo-subtitle {
    margin-top: 4px;
    font-size: 12px;
    line-height: 1.4;
    color: #64748b;
  }

  .promo .cta {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    padding: 0 18px;
    border-radius: 999px;
    background: var(--color-primary);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    box-shadow: 0 8px 18px rgba(22,101,52,.15);
    transition: transform .18s ease, box-shadow .18s ease;
    min-width: 130px;
  }

  .promo .cta:hover {
    transform: scale(1.02);
    box-shadow: 0 10px 22px rgba(22,101,52,.18);
  }

  .bottom-nav {
    position: fixed !important;
    left: 50%;
    right: auto;
    bottom: calc(12px + env(safe-area-inset-bottom)) !important;
    width: min(440px, calc(100vw - 32px));
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0;
    max-width: 440px;
    margin: 0;
    padding: 0 12px;
    height: 68px;
    background: rgba(255,255,255,.95);
    border-radius: 22px;
    border: 1px solid rgba(239,239,247,.90);
    backdrop-filter: blur(18px);
    box-shadow: 0 10px 30px rgba(15,23,42,.12);
    z-index: 140;
    visibility: visible;
    opacity: 1;
  }

  .nav-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 20%;
    min-width: 0;
    color: #66747b;
    font-weight: 500;
    font-size: 9px;
    white-space: nowrap;
    text-decoration: none;
    transition: transform .18s cubic-bezier(.2,.8,.2,1);
    border: 0;
    background: transparent;
    cursor: pointer;
    padding: 0;
    font-family: inherit;
    z-index: 0;
    isolation: isolate;
    touch-action: manipulation;
  }

  .nav-item::before {
    position: absolute;
    top: 0;
    left: 50%;
    width: 34px;
    aspect-ratio: 1;
    border-radius: 50%;
    background: rgba(8, 122, 72, .14);
    box-shadow: 0 5px 14px rgba(8, 122, 72, .16);
    content: "";
    opacity: 0;
    pointer-events: none;
    transform: translate(-50%, -50%) scale(.45);
    transition: opacity .16s ease, transform .2s cubic-bezier(.2,.8,.2,1);
    z-index: 0;
  }

  @media (hover: hover) {
    .nav-item:hover {
      transform: translateY(-2px);
      color: var(--color-title);
    }

    .nav-item:hover::before {
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }
  }

  .nav-item:active {
    transform: translateY(2px) scale(.92, .88);
  }

  .nav-item:focus-visible::before {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }

  .nav-item.is-stomping {
    animation: nav-stomp-target .46s cubic-bezier(.2,.8,.2,1);
  }

  .nav-item.is-stomping svg,
  .nav-item.is-stomping .nav-scan {
    animation: nav-stomp-icon .46s cubic-bezier(.2,.8,.2,1);
  }

  .nav-item.is-stomping::before {
    animation: nav-stomp-pulse .46s ease-out;
  }

  @keyframes nav-stomp-target {
    0%, 100% { transform: translateY(0) scale(1); }
    28% { transform: translateY(2px) scale(.9, .82); }
    58% { transform: translateY(-5px) scale(1.08, 1.1); }
    82% { transform: translateY(1px) scale(.97, .96); }
  }

  @keyframes nav-stomp-pulse {
    0% { opacity: 0; transform: translate(-50%, -50%) scale(.4); }
    24% { opacity: 1; transform: translate(-50%, -50%) scale(1.12); }
    58% { opacity: .8; transform: translate(-50%, -50%) scale(.88); }
    100% { opacity: 0; transform: translate(-50%, -50%) scale(1.28); }
  }

  @keyframes nav-stomp-icon {
    0%, 100% { transform: translateY(0) scale(1); }
    28% { transform: translateY(3px) scale(.78, .72); }
    58% { transform: translateY(-4px) scale(1.12, 1.16); }
    82% { transform: translateY(1px) scale(.96, .94); }
  }

  @media (prefers-reduced-motion: reduce) {
    .nav-item,
    .nav-item::before,
    .dashboard-card-link {
      animation: none !important;
      transition: none !important;
    }
  }

  .nav-item.active {
    color: #087a48;
    font-weight: 800;
  }

  .nav-item--scan {
    position: relative;
    z-index: 2;
  }

  .nav-scan {
    position: relative;
    top: -18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: rgba(255,255,255,0.95);
    box-shadow: 0 10px 22px rgba(15,23,42,0.12);
    border: 1px solid rgba(15,118,85,0.08);
    color: #0f7a4e;
    font-size: 32px;
    font-weight: 700;
    line-height: 1;
    text-decoration: none;
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .nav-scan:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 28px rgba(15,23,42,0.14);
  }

  .nav-item:focus-visible {
    outline: 3px solid #087a48;
    outline-offset: 2px;
    border-radius: 14px;
  }

  .nav-item img,
  .nav-item svg {
    width: 18px;
    height: 18px;
    color: currentColor;
  }

  .bottom-nav a {
    text-decoration: none;
  }

  .discover-item,
  .section-link {
    text-decoration: none;
  }

  .fab-scrim {
    position: fixed;
    inset: 0;
    z-index: 120;
    background: rgba(15,23,42,0.16);
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
    left: 50%;
    bottom: calc(92px + env(safe-area-inset-bottom));
    width: min(340px, calc(100vw - 32px));
    z-index: 130;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transform: translate(-50%, 18px) scale(.88);
    transform-origin: center bottom;
    transition: opacity .22s ease, transform .28s cubic-bezier(.2,.8,.2,1), visibility .22s ease;
  }

  .fab-panel.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transform: translate(-50%, 0) scale(1);
  }

  .fab-sheet {
    border: 1px solid rgba(226,232,240,.9);
    border-radius: 22px;
    padding: 18px;
    background: #fff;
    box-shadow: 0 16px 42px rgba(3,7,18,0.18);
    transform: scale(.96);
    transform-origin: center bottom;
    transition: transform .32s cubic-bezier(.2,.8,.2,1), border-radius .32s ease, box-shadow .32s ease;
  }

  .fab-panel.is-open .fab-sheet {
    transform: scale(1);
    border-radius: 24px;
    box-shadow: 0 22px 54px rgba(3,7,18,0.2);
  }

  .fab-sheet-handle {
    display: none;
  }

  .fab-sheet-title {
    font-size: 15px;
    font-weight: 900;
    color: #1e293b;
    margin: 0 0 10px;
  }

  .fab-actions {
    display: grid;
    gap: 4px;
  }

  .fab-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px;
    border-radius: 14px;
    background: #fff;
    color: #1e293b;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    transition: background .18s ease, transform .18s ease;
  }

  .fab-action:hover {
    background: #f0fdf4;
    transform: translateX(2px);
  }

  .fab-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e9f7ef;
    box-shadow: none;
    overflow: hidden;
    flex-shrink: 0;
    padding: 8px;
  }

  .fab-action-icon img,
  .fab-action-icon svg {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    color: var(--color-primary);
  }

  .fab-close {
    margin-top: 10px;
    width: 100%;
    border: none;
    border-radius: 12px;
    padding: 10px 12px;
    background: transparent;
    color: #64748b;
    font-weight: 700;
    cursor: pointer;
  }

  .payment-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(15, 23, 42, 0.48);
    z-index: 400;
    padding: 20px;
  }

  .payment-modal.is-open {
    display: flex;
  }

  .payment-modal-card {
    position: relative;
    width: min(100%, 440px);
    background: #fff;
    border-radius: 22px;
    padding: 22px 18px 18px;
    box-shadow: 0 30px 60px rgba(15, 23, 42, 0.18);
  }

  .payment-modal-card h3 {
    margin: 0 0 16px;
    font-size: 24px;
    line-height: 1.2;
    color: #0b1e20;
  }

  .payment-method-options {
    display: grid;
    gap: 10px;
  }

  .payment-method-option {
    width: 100%;
    border: 1px solid #dfe7e2;
    background: #f8faf8;
    border-radius: 14px;
    padding: 14px 12px;
    text-align: left;
    cursor: pointer;
    transition: border-color .18s ease, background .18s ease, transform .18s ease;
  }

  .payment-method-option.is-selected {
    border-color: #1f8a5d;
    background: rgba(31, 138, 93, 0.08);
  }

  .payment-method-label {
    display: block;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
  }

  .payment-method-copy {
    display: block;
    color: #475569;
    font-size: 13px;
  }

  .payment-modal-close {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 34px;
    height: 34px;
    border: 0;
    border-radius: 50%;
    background: #edf2ee;
    color: #0f172a;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
  }

  .payment-modal-continue {
    width: 100%;
    margin-top: 18px;
    border: 0;
    border-radius: 12px;
    background: #166534;
    color: #fff;
    font-weight: 800;
    padding: 12px 16px;
    cursor: pointer;
  }

  @media (max-width: 760px) {
    .wallet-shell {
      margin: 0 auto 110px;
      padding: 0 12px;
    }
    .hero {
      aspect-ratio: auto;
      width: 100%;
      min-height: 190px;
      padding: 20px;
    }
    .hero .balance-value {
      font-size: 34px;
    }
    .balance-card {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }
    .balance-card .balance-value {
      font-size: 24px;
    }
    .actions-grid {
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 14px;
    }
    .discover {
      gap: 12px;
    }
    .discover-item {
      min-width: 140px;
    }
    .promo {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      position: relative;
      overflow: hidden;
      padding: 12px 14px;
      gap: 12px;
      min-height: 66px;
    }
    .promo-copy {
      min-width: 0;
      flex: 1;
    }
    .promo .cta {
      width: auto;
      margin-top: 0;
      align-self: center;
      max-width: 140px;
      padding: 0 14px;
      height: 36px;
      font-size: 13px;
    }
    .bottom-nav {
      width: calc(100vw - 32px);
      padding: 0 12px;
      gap: 0;
      bottom: calc(12px + env(safe-area-inset-bottom)) !important;
    }
    .nav-scan {
      top: -15px;
    }
    .fab-panel {
      bottom: calc(82px + env(safe-area-inset-bottom));
    }
    .notification-panel {
      left: 8px;
      right: 8px;
      top: 74px;
    }
  }

  .dashboard-shell {
    max-width: 430px;
    min-height: calc(100vh - 24px);
    margin: 12px auto 120px;
    padding: 0 16px 24px;
    overflow: hidden;
    border: 1px solid #e7ede7;
    border-radius: 30px;
    background: #f7f9f6;
    box-shadow: 0 18px 55px rgba(10, 31, 23, .1);
  }

  .dashboard-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0 -16px 18px;
    padding: 14px 18px;
    background: rgba(255, 255, 255, .96);
    border-bottom: 1px solid #e9eee8;
  }

  .dashboard-logo {
    position: relative;
    display: grid;
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    place-items: center;
    overflow: hidden;
    border: 1px solid #e3eee4;
    border-radius: 50%;
    background: #fff;
  }

  .dashboard-logo img {
    position: relative;
    z-index: 1;
    display: block;
    width: 78%;
    height: 78%;
    object-fit: contain;
  }

  .dashboard-profile {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 13px 8px 8px;
    border: 1px solid #e3eae3;
    border-radius: 999px;
    background: #fff;
    color: #244a39;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
  }

  .dashboard-profile-icon {
    display: grid;
    width: 30px;
    height: 30px;
    place-items: center;
    border-radius: 50%;
    background: #eaf5e9;
  }

  .dashboard-profile svg {
    width: 17px;
    height: 17px;
  }

  .dashboard-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    padding: 23px 21px 18px;
    border: 1px solid rgba(255, 255, 255, .14);
    border-radius: 27px;
    background: linear-gradient(128deg, #092b20 0%, #0c563a 57%, #14804f 100%);
    color: #fff;
    box-shadow: 0 16px 35px rgba(8, 78, 49, .2);
  }

  .dashboard-hero::before,
  .dashboard-hero::after {
    position: absolute;
    z-index: -1;
    width: 220px;
    height: 220px;
    border: 1px solid rgba(192, 241, 125, .15);
    border-radius: 50%;
    content: "";
    pointer-events: none;
  }

  .dashboard-hero::before {
    top: -148px;
    right: -48px;
    box-shadow: 0 0 0 18px rgba(192, 241, 125, .035), 0 0 0 38px rgba(192, 241, 125, .025);
  }

  .dashboard-hero::after {
    right: -112px;
    bottom: -166px;
    width: 270px;
    height: 270px;
    border-color: rgba(87, 232, 170, .22);
  }

  .dashboard-hero-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
  }

  .dashboard-hero-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #d5f4df;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .02em;
  }

  .dashboard-hero-label::before {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #bff16d;
    box-shadow: 0 0 12px rgba(191, 241, 109, .7);
    content: "";
  }

  .dashboard-balance-toggle {
    display: grid;
    width: 36px;
    height: 36px;
    place-items: center;
    border: 1px solid rgba(255, 255, 255, .22);
    border-radius: 50%;
    background: rgba(255, 255, 255, .1);
    color: #fff;
    cursor: pointer;
  }

  .dashboard-balance-toggle svg {
    width: 18px;
    height: 18px;
  }

  .dashboard-balance {
    margin-top: 13px;
    color: #fff;
    font-size: clamp(34px, 9vw, 42px);
    font-weight: 800;
    letter-spacing: -.045em;
    line-height: 1.12;
    font-variant-numeric: tabular-nums;
  }

  .balance-masked {
    display: none;
    letter-spacing: .12em;
  }

  .dashboard-hero.is-hidden .balance-readable,
  .dashboard-hero.is-hidden .asset-readable {
    display: none;
  }

  .dashboard-hero.is-hidden .balance-masked,
  .dashboard-hero.is-hidden .asset-masked {
    display: inline;
  }

  .dashboard-assets {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-top: 22px;
    padding-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, .2);
  }

  .dashboard-assets-icon {
    display: grid;
    width: 38px;
    height: 38px;
    flex: 0 0 38px;
    place-items: center;
    border: 1px solid rgba(255, 255, 255, .2);
    border-radius: 13px;
    background: rgba(255, 255, 255, .1);
  }

  .dashboard-assets-icon svg {
    width: 19px;
    height: 19px;
  }

  .dashboard-assets-label {
    color: #c4e5d1;
    font-size: 11px;
    font-weight: 600;
  }

  .dashboard-assets-value {
    margin-top: 2px;
    color: #fff;
    font-size: 16px;
    font-weight: 750;
    font-variant-numeric: tabular-nums;
  }

  .dashboard-promos {
    display: grid;
    gap: 14px;
    margin-top: 20px;
  }

  .dashboard-promo {
    position: relative;
    display: block;
    overflow: hidden;
    aspect-ratio: 2.24 / 1;
    border-radius: 23px;
    background: #e8eee8;
    box-shadow: 0 9px 20px rgba(15, 39, 26, .1);
    transition: transform .2s ease, box-shadow .2s ease;
  }

  .dashboard-promo img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .dashboard-promo:focus-visible {
    outline: 3px solid #13814d;
    outline-offset: 3px;
  }

  @media (hover: hover) {
    .dashboard-promo:hover {
      transform: translateY(-2px);
      box-shadow: 0 13px 26px rgba(15, 39, 26, .14);
    }
  }

  .bottom-nav {
    width: min(410px, calc(100vw - 28px));
    height: 66px;
    padding: 0 12px;
    border: 1px solid rgba(227, 235, 227, .95);
    border-radius: 23px;
    background: rgba(255, 255, 255, .96);
    box-shadow: 0 12px 32px rgba(14, 45, 29, .16);
  }

  .nav-item.active {
    color: #11804e;
  }

  .nav-scan {
    background: #12804f;
    border: 4px solid #f7f9f6;
    color: #fff;
    box-shadow: 0 8px 20px rgba(14, 110, 66, .28);
  }

  @media (max-width: 600px) {
    .dashboard-shell {
      min-height: 100vh;
      margin: 0 auto 110px;
      padding: 0 14px 20px;
      overflow: visible;
      border: 0;
      border-radius: 0;
      box-shadow: none;
    }

    .dashboard-topbar {
      margin: 0 -14px 16px;
      padding: 12px 16px;
    }

    .dashboard-hero {
      padding: 18px 18px 15px;
      border-radius: 25px;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .dashboard-promo {
      transition: none;
    }
  }

  :root {
    --dashboard-bg: #f2f5f3;
    --dashboard-ink: #0a1f17;
    --dashboard-muted: #8a9892;
    --dashboard-green: #0e8a5a;
    --dashboard-mint: #3ee0a1;
    --dashboard-lime: #c6f36b;
  }

  body {
    display: flex;
    min-height: 100vh;
    align-items: flex-start;
    justify-content: center;
    background:
      radial-gradient(900px 600px at 15% 10%, rgba(20, 168, 109, .18), transparent 60%),
      radial-gradient(700px 500px at 90% 90%, rgba(198, 243, 107, .14), transparent 60%),
      #e7ece9 !important;
    color: var(--dashboard-ink);
    font-family: Inter, system-ui, -apple-system, sans-serif;
  }

  .container {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
  }

  .dashboard-shell {
    position: relative;
    width: 390px;
    max-width: 100%;
    height: 844px;
    min-height: 844px;
    margin: 40px auto;
    padding: 0 0 8px;
    overflow-x: hidden;
    overflow-y: auto;
    border: 0;
    border-radius: 54px;
    background: var(--dashboard-bg);
    box-shadow: 0 0 0 10px #0d1411, 0 0 0 11px #2a332f, 0 30px 60px -20px rgba(2, 26, 18, .4), 0 60px 100px -50px rgba(2, 26, 18, .3);
    scrollbar-width: none;
  }

  .dashboard-shell::-webkit-scrollbar {
    display: none;
  }

  .dashboard-topbar {
    position: sticky;
    top: 0;
    z-index: 30;
    display: block;
    margin: 0;
    padding: 0;
    border: 0;
    background: rgba(242, 245, 243, .78);
    backdrop-filter: saturate(180%) blur(20px);
    -webkit-backdrop-filter: saturate(180%) blur(20px);
  }

  .dashboard-dash-nav {
    display: flex;
    height: 72px;
    align-items: center;
    justify-content: space-between;
    padding: 8px 20px 6px;
  }

  .dashboard-logo {
    position: relative;
    display: grid;
    width: 50px;
    height: 50px;
    flex: 0 0 50px;
    place-items: center;
    overflow: hidden;
    border-radius: 50%;
    background: conic-gradient(from 210deg, #3ee0a1, #0e8a5a, #054a31, #c6f36b, #3ee0a1);
    box-shadow: 0 6px 16px -6px rgba(4, 30, 20, .45), 0 1px 2px rgba(4, 30, 20, .12);
  }

  .dashboard-logo::before {
    position: absolute;
    inset: 2.5px;
    border-radius: 50%;
    background: #fff;
    content: "";
  }

  .dashboard-logo img {
    position: relative;
    z-index: 1;
    display: block;
    width: 86%;
    height: 86%;
    border-radius: 50%;
    background: #fff;
    object-fit: contain;
  }

  .dashboard-profile {
    height: 42px;
    gap: 9px;
    padding: 0 15px 0 5px;
    color: var(--dashboard-ink);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: -.005em;
    background: rgba(255, 255, 255, .9);
    box-shadow: 0 1px 2px rgba(6, 40, 28, .06), 0 2px 6px -2px rgba(6, 40, 28, .06), inset 0 0 0 1px rgba(10, 31, 23, .06);
  }

  .dashboard-profile-icon {
    width: 32px;
    height: 32px;
    color: #075a3b;
    background: linear-gradient(180deg, #edfaf3, #d7f1e4);
    box-shadow: inset 0 0 0 1px rgba(14, 138, 90, .16);
  }

  .dashboard-content {
    padding: 0 20px 24px;
  }

  .dashboard-hero {
    display: flex;
    flex-direction: column;
    height: auto;
    min-height: 0;
    aspect-ratio: auto;
    padding: 18px 18px 15px;
    border: 0;
    border-radius: 28px;
    background:
      radial-gradient(120% 90% at 105% -10%, rgba(62, 224, 161, .55), transparent 55%),
      radial-gradient(70% 70% at -10% 110%, rgba(198, 243, 107, .28), transparent 60%),
      radial-gradient(60% 50% at 40% 40%, rgba(20, 168, 109, .35), transparent 70%),
      linear-gradient(160deg, #0b6a47 0%, #054a31 42%, #022418 100%);
    box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(4, 30, 20, .38), inset 0 1px 0 rgba(255, 255, 255, .22), inset 0 0 0 1px rgba(255, 255, 255, .07);
  }

  .dashboard-hero .hero__waves {
    position: absolute;
    inset: 0;
    z-index: 0;
    display: block;
    width: 100%;
    height: 100%;
    pointer-events: none;
  }

  .dashboard-hero > *:not(.hero__waves) {
    position: relative;
    z-index: 1;
  }

  .dashboard-hero::before {
    inset: 0;
    z-index: -1;
    width: auto;
    height: auto;
    border: 0;
    border-radius: inherit;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='180' height='180'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 1 0 0 0 0 1 0 0 0 0 1 0 0 0 .5 0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    opacity: .09;
    mix-blend-mode: overlay;
  }

  .dashboard-hero::after {
    inset: 0 0 55%;
    z-index: -1;
    width: auto;
    height: auto;
    border: 0;
    border-radius: 28px 28px 0 0;
    background: linear-gradient(180deg, rgba(255, 255, 255, .12), rgba(255, 255, 255, 0));
  }

  .dashboard-hero-top {
    justify-content: space-between;
  }

  .dashboard-hero-label {
    color: rgba(232, 255, 244, .72);
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .dashboard-hero-label::before {
    width: 6px;
    height: 6px;
    background: var(--dashboard-mint);
    box-shadow: 0 0 0 3px rgba(62, 224, 161, .22), 0 0 10px var(--dashboard-mint);
  }

  .dashboard-balance-toggle {
    width: 36px;
    height: 36px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(180deg, rgba(255, 255, 255, .18), rgba(255, 255, 255, .06));
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .3), inset 0 0 0 1px rgba(255, 255, 255, .1);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }

  .dashboard-balance-toggle .off {
    display: none;
  }

  .dashboard-hero.is-hidden .dashboard-balance-toggle .on {
    display: none;
  }

  .dashboard-hero.is-hidden .dashboard-balance-toggle .off {
    display: block;
  }

  .dashboard-balance {
    display: flex;
    align-items: flex-start;
    margin-top: 8px;
    color: #fff;
    font-size: clamp(34px, 10vw, 42px);
    font-weight: 700;
    letter-spacing: -.04em;
    line-height: 1;
    text-shadow: 0 2px 18px rgba(0, 0, 0, .18);
  }

  .dashboard-balance .currency {
    margin: 5px 3px 0 0;
    color: rgba(255, 255, 255, .8);
    font-size: 26px;
    font-weight: 600;
    letter-spacing: 0;
  }

  .dashboard-balance .decimal {
    color: rgba(255, 255, 255, .55);
  }

  .dashboard-balance .balance-mask,
  .dashboard-assets-value .balance-mask {
    display: none;
    letter-spacing: .04em;
  }

  .dashboard-hero.is-hidden .balance-readable,
  .dashboard-hero.is-hidden .asset-readable {
    display: none;
  }

  .dashboard-hero.is-hidden .balance-mask,
  .dashboard-hero.is-hidden .asset-mask {
    display: inline;
  }

  .dashboard-hero.is-hidden .balance-mask {
    position: relative;
    top: -6px;
    font-size: 40px;
    line-height: 48px;
  }

  .dashboard-assets {
    margin-top: 12px;
    padding: 8px 11px;
    border: 0;
    border-radius: 18px;
    background: linear-gradient(180deg, rgba(255, 255, 255, .13), rgba(255, 255, 255, .05));
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .2), inset 0 0 0 1px rgba(255, 255, 255, .08);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
  }

  .dashboard-assets-icon {
    width: 38px;
    height: 38px;
    flex-basis: 38px;
    border: 0;
    border-radius: 12px;
    background: rgba(62, 224, 161, .16);
    box-shadow: inset 0 0 0 1px rgba(62, 224, 161, .28);
    color: #7bf2c2;
  }

  .dashboard-assets-label {
    color: rgba(232, 255, 244, .62);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .14em;
    text-transform: uppercase;
  }

  .dashboard-assets-value {
    margin-top: 7px;
    color: #fff;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: -.025em;
    line-height: 1;
  }

  .dashboard-assets-value .decimal {
    color: rgba(255, 255, 255, .6);
    font-size: 15px;
  }

  .dashboard-promos {
    gap: 16px;
    margin-top: 24px;
  }

  .dashboard-promo {
    isolation: isolate;
    height: 212px;
    aspect-ratio: auto;
    border-radius: 26px;
    color: #fff;
    box-shadow: 0 1px 2px rgba(6, 40, 28, .05), 0 6px 16px -6px rgba(6, 40, 28, .12), 0 18px 36px -18px rgba(6, 40, 28, .18);
    transform: perspective(900px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) translateY(var(--lift, 0px));
    transition: transform .5s cubic-bezier(.2, .8, .2, 1), box-shadow .4s cubic-bezier(.2, .8, .2, 1);
  }

  .dashboard-promo img {
    position: absolute;
    inset: 0;
    z-index: -3;
    object-fit: cover;
    transition: transform 1.2s cubic-bezier(.2, .8, .2, 1);
  }

  .dashboard-promo:first-child img {
    object-position: 30% 50%;
  }

  .dashboard-promo:last-child img {
    object-position: 42% 50%;
  }

  .dashboard-promo:focus-visible {
    box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(4, 30, 20, .38), 0 0 0 3px var(--dashboard-bg), 0 0 0 5px var(--dashboard-green);
    outline: none;
  }

  .dashboard-promo:hover {
    --lift: -3px;
    box-shadow: 0 2px 4px rgba(4, 30, 20, .08), 0 12px 24px -8px rgba(4, 30, 20, .22), 0 32px 56px -24px rgba(4, 30, 20, .38);
  }

  .dashboard-promo:hover img {
    transform: scale(1.05);
  }

  .dashboard-promo:active {
    transform: perspective(900px) rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg)) scale(.985);
  }

  .dashboard-promo-scrim {
    position: absolute;
    inset: 0;
    z-index: -2;
    background: linear-gradient(180deg, rgba(2, 26, 18, 0) 28%, rgba(2, 26, 18, .42) 58%, rgba(2, 26, 18, .9) 100%), linear-gradient(90deg, rgba(4, 50, 31, .55), transparent 62%);
    pointer-events: none;
  }

  .dashboard-promo-content {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: flex-end;
    padding: 16px 18px 16px 20px;
    pointer-events: none;
  }

  .dashboard-promo h2 {
    color: #fff;
    font-family: "Plus Jakarta Sans", Inter, sans-serif;
    font-size: 23px;
    font-weight: 800;
    letter-spacing: -.03em;
    line-height: 1.05;
    text-shadow: 0 2px 14px rgba(0, 0, 0, .35);
  }

  .dashboard-promo-cta {
    display: inline-flex;
    height: 34px;
    align-items: center;
    gap: 7px;
    margin-top: 10px;
    padding: 0 13px 0 14px;
    border-radius: 99px;
    background: linear-gradient(180deg, rgba(255, 255, 255, .24), rgba(255, 255, 255, .1));
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .35), inset 0 0 0 1px rgba(255, 255, 255, .18), 0 6px 16px -6px rgba(0, 0, 0, .4);
    color: #fff;
    font-size: 12.5px;
    font-weight: 600;
    letter-spacing: -.005em;
    backdrop-filter: blur(14px) saturate(160%);
    -webkit-backdrop-filter: blur(14px) saturate(160%);
  }

  .dashboard-promo-edge {
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .3), inset 0 0 0 1px rgba(255, 255, 255, .1);
    pointer-events: none;
  }

  .dashboard-tabbar-wrap {
    position: sticky;
    bottom: 0;
    z-index: 35;
    padding: 26px 16px 10px;
    background: linear-gradient(180deg, rgba(242, 245, 243, 0), rgba(242, 245, 243, .85) 45%, var(--dashboard-bg));
  }

  .bottom-nav {
    position: relative !important;
    inset: auto !important;
    display: grid;
    width: 100%;
    height: 70px;
    grid-template-columns: 1fr 1fr 84px 1fr 1fr;
    align-items: center;
    margin: 0;
    padding: 0;
    transform: none;
    border: 0;
    border-radius: 28px;
    background: rgba(255, 255, 255, .78);
    box-shadow: 0 1px 2px rgba(6, 40, 28, .06), 0 12px 32px -10px rgba(6, 40, 28, .25), inset 0 0 0 1px rgba(255, 255, 255, .7), inset 0 0 0 .5px rgba(10, 31, 23, .08);
    backdrop-filter: saturate(180%) blur(22px);
    -webkit-backdrop-filter: saturate(180%) blur(22px);
  }

  .bottom-nav .nav-item {
    position: relative;
    z-index: 0;
    display: flex;
    width: auto;
    height: 100%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    color: #8a9892;
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .01em;
    text-decoration: none;
  }

  .bottom-nav .nav-item::before {
    top: 8px;
    left: 50%;
    width: 46px;
    height: 30px;
    aspect-ratio: auto;
    border-radius: 12px;
    background: #e6f7ef;
    box-shadow: none;
    transform: translateX(-50%);
  }

  .bottom-nav .nav-item.active {
    color: #0e8a5a;
  }

  .bottom-nav .nav-item.active::before {
    opacity: 1;
    transform: translateX(-50%);
  }

  .bottom-nav .nav-item.active::after {
    position: absolute;
    bottom: 7px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #0e8a5a;
    content: "";
  }

  .bottom-nav .nav-item.active div {
    font-weight: 700;
  }

  .bottom-nav .nav-item svg,
  .bottom-nav .nav-item:not(.nav-item--scan) .nav-scan {
    z-index: 1;
    width: 22px;
    height: 22px;
  }

  .bottom-nav .nav-item--scan {
    position: relative;
    z-index: 2;
    display: grid;
    height: 100%;
    align-items: center;
    justify-items: center;
    overflow: visible;
    color: transparent;
    -webkit-tap-highlight-color: transparent;
  }

  .bottom-nav .nav-item--scan .nav-scan {
    position: relative;
    top: auto;
    display: grid;
    width: 60px;
    height: 60px;
    margin-top: -26px;
    border: 0;
    border-radius: 50%;
    background: radial-gradient(120% 120% at 30% 20%, #1fb97a, #0b7a50 45%, #054a31);
    box-shadow: 0 0 0 7px rgba(255, 255, 255, .96), 0 0 0 9px rgba(62, 224, 161, .16), 0 12px 26px -6px rgba(14, 138, 90, .55), 0 0 34px rgba(62, 224, 161, .35), inset 0 1px 0 rgba(255, 255, 255, .38), inset 0 -2px 4px rgba(0, 0, 0, .2);
    color: #fff;
    font-size: 30px;
    line-height: 1;
    pointer-events: none;
  }

  .bottom-nav .nav-item--scan .nav-scan::after {
    position: absolute;
    inset: 3px 8px auto;
    height: 45%;
    border-radius: 50% 50% 40% 40%;
    background: linear-gradient(180deg, rgba(255, 255, 255, .35), transparent);
    content: "";
    pointer-events: none;
  }

  .bottom-nav .nav-item--scan:focus-visible .nav-scan {
    outline: 3px solid #0e8a5a;
    outline-offset: 10px;
  }

  .dashboard-home-indicator {
    width: 134px;
    height: 5px;
    margin: 12px auto 0;
    border-radius: 99px;
    background: #0a1f17;
    opacity: .9;
  }

  .dashboard-tabbar-wrap .nav-item--scan::before,
  .dashboard-tabbar-wrap .nav-item--scan::after,
  .dashboard-tabbar-wrap .nav-item:not(.active)::before {
    display: none;
  }

  @media (min-width: 521px) {
    body {
      align-items: center;
      padding: 40px 0;
    }

    .dashboard-shell {
      margin: 0 auto;
    }
  }

  @media (max-width: 520px) {
    body {
      background: var(--dashboard-bg) !important;
    }

    .dashboard-shell {
      width: 100%;
      height: auto;
      min-height: 100vh;
      min-height: 100dvh;
      margin: 0;
      border-radius: 0;
      box-shadow: none;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .dashboard-promo,
    .dashboard-promo img {
      transition: none;
    }
  }

  @media (max-width: 480px) {
    .wallet-shell {
      padding: 0 10px;
    }
    .hero {
      min-height: 180px;
      padding: 16px;
    }
    .hero .balance-value {
      font-size: 30px;
    }
    .hero-kicker {
      font-size: 12px;
    }
    .hero-cta,
    .promo .cta {
      padding: 12px 14px;
      font-size: 14px;
      height: 36px;
    }
    .actions-grid {
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 10px;
    }
    .action {
      max-width: none;
    }
    .action .icon {
      width: 32px;
      height: 32px;
    }
    .action svg {
      width: 32px;
      height: 32px;
    }
    .discover-item {
      min-width: 128px;
      padding: 14px;
    }
    .banner-carousel {
      aspect-ratio: 18 / 7;
      min-height: 88px;
      padding: 0;
    }
    .banner-card {
      height: 100%;
    }
    .banner-slide img,
    .banner-card img {
      object-fit: contain;
    }
    .bottom-nav {
      width: calc(100vw - 32px);
      padding: 0 10px;
      gap: 0;
      height: 62px;
      bottom: calc(12px + env(safe-area-inset-bottom)) !important;
    }
    .nav-item {
      gap: 3px;
      width: 20%;
      min-width: 0;
      font-size: 8px;
    }
    .nav-item img,
    .nav-item svg {
      width: 17px;
      height: 17px;
    }
    .nav-scan {
      top: -14px;
      width: 52px;
      height: 52px;
      font-size: 28px;
    }
    .fab-panel {
      bottom: calc(74px + env(safe-area-inset-bottom));
    }
    .notification-panel {
      top: 68px;
    }
  }

  @media (min-width:760px) {
    .wallet-shell {
      max-width: 760px;
    }
    .actions-grid {
      grid-template-columns: repeat(4, minmax(0, 1fr));
    }
  }
</style>

@php
  $user = $user ?? auth()->user();
  $investments = $user->investments()->latest()->get();
  if (! isset($dailyInterest)) {
      $dailyInterest = $investments->sum(fn($i) => $i->dailyInterestAmount());
  }
  $availableBalance = (float) $user->balance;
  $approvedInvestments = $investments->where('status', 'approved');
  $totalInvestment = $approvedInvestments->sum(fn($investment) => (float) $investment->amount);
  $packageDefinitions = \App\Support\InvestmentPackages::all();
  $packageEarnings = $approvedInvestments
      ->groupBy('package_key')
      ->map(fn($packageInvestments) => $packageInvestments->sum(fn($investment) => $investment->creditedInterest()));
  $notificationsRead = $notificationsRead ?? [];
  $unreadCount = $unreadCount ?? 0;
@endphp

<main class="wallet-shell dashboard-shell">
  <header class="dashboard-topbar">
    <nav class="dashboard-dash-nav" aria-label="Dashboard">
      <a class="dashboard-logo" href="{{ route('dashboard') }}" aria-label="LuLu Philippines dashboard">
        <img src="{{ asset('logo.png') }}" alt="LuLu Philippines">
      </a>
      <a class="dashboard-profile" href="{{ route('profile') }}">
        <span class="dashboard-profile-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="8" r="4"/>
            <path d="M4.5 20.5a7.5 7.5 0 0 1 15 0"/>
          </svg>
        </span>
        <span>Profile</span>
      </a>
    </nav>
  </header>

  <div class="dashboard-content">
    <section class="dashboard-hero hero--balance" id="dashboardBalance" aria-label="Account balance">
      <svg class="hero__waves" viewBox="0 0 300 200" fill="none" aria-hidden="true">
        <defs>
          <linearGradient id="dashboardWaveGold" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#C6F36B" stop-opacity="0"/><stop offset=".6" stop-color="#C6F36B" stop-opacity=".65"/><stop offset="1" stop-color="#F3E27A" stop-opacity=".9"/></linearGradient>
          <linearGradient id="dashboardWaveMint" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#3EE0A1" stop-opacity="0"/><stop offset="1" stop-color="#3EE0A1" stop-opacity=".45"/></linearGradient>
        </defs>
        <path d="M0 150C70 148 120 120 170 88S260 30 310 22" stroke="url(#dashboardWaveGold)" stroke-width="1.6"/>
        <path d="M10 176C90 170 140 142 190 108S270 60 310 54" stroke="url(#dashboardWaveMint)" stroke-width="1.2"/>
        <path d="M40 200C110 196 160 172 210 140S280 98 310 94" stroke="url(#dashboardWaveMint)" stroke-width=".9" opacity=".7"/>
      </svg>
      <div class="dashboard-hero-top">
        <span class="dashboard-hero-label">Available balance</span>
        <button class="dashboard-balance-toggle eye" id="dashboardBalanceToggle" type="button" aria-label="Hide balance" aria-pressed="false">
          <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.6 5.1A10.4 10.4 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-2.6 3.5M6.5 6.6C3.8 8.3 2 12 2 12s3.6 7 10 7c1.9 0 3.5-.6 4.9-1.5M9.9 9.9a3 3 0 0 0 4.2 4.2M3 3l18 18"/></svg>
        </button>
      </div>
      @php
        $balanceParts = explode('.', number_format($availableBalance, 2));
        $assetParts = explode('.', number_format($totalInvestment, 2));
      @endphp
      <div class="dashboard-balance num" aria-live="polite">
        <span class="balance-readable"><span class="currency">$</span>{{ $balanceParts[0] }}<span class="decimal">.{{ $balanceParts[1] }}</span></span>
        <span class="balance-mask" aria-hidden="true">••••••</span>
      </div>
      <div class="dashboard-assets sub">
        <span class="dashboard-assets-icon sub-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 3 7.5l9 4.5 9-4.5L12 3Z"/><path d="m3 12 9 4.5 9-4.5M3 16.5 12 21l9-4.5"/></svg>
        </span>
        <div>
          <div class="dashboard-assets-label">Assets</div>
          <div class="dashboard-assets-value sub-val num">
            <span class="asset-readable">${{ $assetParts[0] }}<span class="decimal">.{{ $assetParts[1] }}</span></span>
            <span class="balance-mask asset-mask" aria-hidden="true">$••••</span>
          </div>
        </div>
      </div>
    </section>

    <section class="dashboard-promos promos" aria-label="Explore LuLu opportunities">
      <a class="dashboard-promo dashboard-card-link" href="{{ route('invest.advertisement') }}" aria-label="View bond investment advertisement">
        <img src="{{ asset('banner-bonds.jpg') }}" alt="Woman using her phone outside a LuLu Retail store">
        <span class="dashboard-promo-scrim"></span>
        <span class="dashboard-promo-edge"></span>
        <div class="dashboard-promo-content">
          <div>
            <h2>Purchase Bonds</h2>
            <span class="dashboard-promo-cta">View Bond Details
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </span>
          </div>
        </div>
      </a>
      <a class="dashboard-promo dashboard-card-link" href="{{ route('franchising') }}" aria-label="View franchise opportunities">
        <img src="{{ asset('banner-franchise.jpg') }}" alt="LuLu Daily storefront">
        <span class="dashboard-promo-scrim"></span>
        <span class="dashboard-promo-edge"></span>
        <div class="dashboard-promo-content">
          <div>
            <h2>Apply for Franchise</h2>
            <span class="dashboard-promo-cta">View Franchise Details
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </span>
          </div>
        </div>
      </a>
    </section>
  </div>

  <section class="package-section" aria-label="Account packages">
    <div id="Cards101" hidden>
      <div class="package-grid">
      @foreach ($packageDefinitions as $packageKey => $package)
        @php
          $hasPackageInvestment = $approvedInvestments->contains('package_key', $packageKey);
        @endphp

        @if ($hasPackageInvestment)
          <article class="package-card {{ $packageKey }}">
            <div class="package-card-actions">
              <a class="package-plus-action" href="{{ route('invest.purchase', ['package' => $packageKey]) }}" aria-label="Buy another {{ $package['name'] }} package">+</a>
            </div>
            <div class="package-brand">
              <div>
                <div class="package-name">{{ $package['name'] }}</div>
                <div class="package-caption">ACCOUNT PACKAGE</div>
              </div>
            </div>
            <div class="package-balance">
              <div class="package-label">EARNINGS</div>
              <div class="package-value">${{ number_format((float) $packageEarnings->get($packageKey, 0), 2) }}</div>
            </div>
          </article>
        @else
          <article class="package-card package-card--empty {{ $packageKey }}" aria-label="{{ $package['name'] }} package not activated">
            <div class="package-empty-card-copy">
              <div class="package-empty-name">{{ $package['name'] }}</div>
              <div class="package-empty-caption">Inactive</div>
            </div>
            <a class="package-empty-action" href="{{ route('invest.purchase', ['package' => $packageKey]) }}" aria-label="Buy {{ $package['name'] }} package">+</a>
          </article>
        @endif
      @endforeach
      </div>
    </div>
  </section>

  <div class="dashboard-tabbar-wrap">
    <nav class="bottom-nav" aria-label="Account navigation">
      <a class="nav-item active" href="{{ route('dashboard') }}" aria-label="Home" aria-current="page">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M10.6 3.5a2.2 2.2 0 0 1 2.8 0l6.8 5.6c.5.4.8 1 .8 1.7v8.4A2.3 2.3 0 0 1 18.7 21H15a1 1 0 0 1-1-1v-4.2a2 2 0 0 0-4 0V20a1 1 0 0 1-1 1H5.3A2.3 2.3 0 0 1 3 19.2v-8.4c0-.7.3-1.3.8-1.7l6.8-5.6Z"/></svg>
        <span>Home</span>
      </a>
      <a class="nav-item" href="{{ route('history') }}" aria-label="Transactions">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4.5" width="18" height="15" rx="3.5"/><path d="M7 9.5h10M7 13h6M7 16.5h4"/></svg>
        <span>Transactions</span>
      </a>
      <button class="nav-item nav-item--scan" type="button" id="fabToggle" aria-label="Open wallet actions" aria-expanded="false">
        <span class="nav-scan" aria-hidden="true">+</span>
      </button>
      <a class="nav-item" href="{{ route('referrals') }}" aria-label="Referral and affiliates">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0M16 4.7a3.5 3.5 0 0 1 0 6.6M18.5 14.2A6.5 6.5 0 0 1 21.5 20"/></svg>
        <span>Referral</span>
      </a>
      <button class="nav-item" type="button" id="moreToggle" aria-label="Open quick actions" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="2"/><rect x="13.5" y="3.5" width="7" height="7" rx="2"/><rect x="3.5" y="13.5" width="7" height="7" rx="2"/><rect x="13.5" y="13.5" width="7" height="7" rx="3.5"/></svg>
        <span>More</span>
      </button>
    </nav>
    <div class="dashboard-home-indicator" aria-hidden="true"></div>
  </div>

</main>

<div class="fab-scrim" id="fabScrim" aria-hidden="true"></div>
<div class="fab-panel" id="fabPanel" aria-hidden="true">
  <div class="fab-sheet" role="dialog" aria-label="Wallet actions">
    <div class="fab-sheet-handle"></div>
    <div class="fab-sheet-title">Wallet actions</div>
    <div class="fab-actions">
      <a class="fab-action" href="{{ route('send') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m21 3-7.4 18-3.8-7.8L2 9.4 21 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
        <span>Send Funds</span>
      </a>
      <a class="fab-action" href="{{ route('withdraw') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v13m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>Withdraw Funds</span>
      </a>
      <button class="fab-action" type="button" data-open-modal="addFundsModal">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4v16m-8-8h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <span>Add Funds</span>
      </button>
    </div>
  </div>
</div>

<div class="fab-panel" id="morePanel" aria-hidden="true">
  <div class="fab-sheet" role="dialog" aria-label="Quick actions">
    <div class="fab-sheet-handle"></div>
    <div class="fab-sheet-title">Quick actions</div>
    <div class="fab-actions">
      <a class="fab-action" href="{{ route('send') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m21 3-7.4 18-3.8-7.8L2 9.4 21 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
        <span>Send Funds</span>
      </a>
      <a class="fab-action" href="{{ route('withdraw') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v13m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>Withdraw Funds</span>
      </a>
      <button class="fab-action" type="button" data-open-modal="addFundsModal">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 4v16m-8-8h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <span>Add Funds</span>
      </button>
      <a class="fab-action" href="{{ route('history') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 9h10M7 13h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <span>Transactions</span>
      </a>
      <a class="fab-action" href="{{ route('franchising') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M12 3v3m9 6h-3m-6 9v-3m-9-6h3" stroke="currentColor" stroke-width="1.5"/></svg></span>
        <span>Franchise</span>
      </a>
    </div>
  </div>
</div>

<div class="payment-modal" id="addFundsModal" aria-hidden="true">
  <div class="payment-modal-card" role="dialog" aria-modal="true" aria-labelledby="addFundsTitle">
    <button class="payment-modal-close" type="button" data-close-modal="addFundsModal" aria-label="Close payment method selector">×</button>
    <h3 id="addFundsTitle">Choose payment method</h3>
    <div class="payment-method-options">
      <button type="button" class="payment-method-option is-selected" data-payment-method="bank">
        <span class="payment-method-label">Bank transfer</span>
        <span class="payment-method-copy">Direct deposit to company account</span>
      </button>
      <button type="button" class="payment-method-option" data-payment-method="gcash">
        <span class="payment-method-label">E-wallet</span>
        <span class="payment-method-copy">GCash / Maya / other wallet</span>
      </button>
      <button type="button" class="payment-method-option" data-payment-method="card">
        <span class="payment-method-label">Card</span>
        <span class="payment-method-copy">Visa / Mastercard / other cards</span>
      </button>
    </div>
    <button class="payment-modal-continue" type="button" id="continueAddFunds">Continue</button>
  </div>
</div>

<script>
  (function () {
    var dashboardBalance = document.getElementById('dashboardBalance');
    var dashboardBalanceToggle = document.getElementById('dashboardBalanceToggle');
    var fabToggle = document.getElementById('fabToggle');
    var moreToggle = document.getElementById('moreToggle');
    var fabScrim = document.getElementById('fabScrim');
    var fabPanel = document.getElementById('fabPanel');
    var morePanel = document.getElementById('morePanel');
    var addFundsModal = document.getElementById('addFundsModal');
    var paymentOptions = document.querySelectorAll('.payment-method-option');
    var continueAddFunds = document.getElementById('continueAddFunds');
    var selectedPaymentMethod = 'bank';
    var bottomNavItems = document.querySelectorAll('.bottom-nav .nav-item');
    var dashboardCardLinks = document.querySelectorAll('.dashboard-card-link');

    if (dashboardBalance && dashboardBalanceToggle) {
      dashboardBalanceToggle.addEventListener('click', function () {
        var hidden = dashboardBalance.classList.toggle('is-hidden');
        dashboardBalanceToggle.setAttribute('aria-pressed', String(hidden));
        dashboardBalanceToggle.setAttribute('aria-label', hidden ? 'Show balance' : 'Hide balance');
      });
    }

    dashboardCardLinks.forEach(function (link) {
      link.addEventListener('click', function (event) {
        var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reducedMotion || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
          return;
        }

        event.preventDefault();
        link.classList.remove('is-stomping');
        void link.offsetWidth;
        link.classList.add('is-stomping');
        window.setTimeout(function () {
          window.location.assign(link.href);
        }, 460);
      });
    });

    bottomNavItems.forEach(function (item) {
      item.addEventListener('click', function () {
        item.classList.remove('is-stomping');
        void item.offsetWidth;
        item.classList.add('is-stomping');
        window.setTimeout(function () {
          item.classList.remove('is-stomping');
        }, 460);
      });
    });

    function closePanels() {
      if (fabPanel) {
        fabPanel.classList.remove('is-open');
        fabPanel.setAttribute('aria-hidden', 'true');
      }
      if (morePanel) {
        morePanel.classList.remove('is-open');
        morePanel.setAttribute('aria-hidden', 'true');
      }
      if (fabScrim) {
        fabScrim.classList.remove('is-open');
        fabScrim.setAttribute('aria-hidden', 'true');
      }
      if (moreToggle) {
        moreToggle.classList.remove('is-open');
        moreToggle.setAttribute('aria-expanded', 'false');
      }
      if (fabToggle) {
        fabToggle.classList.remove('is-open');
        fabToggle.setAttribute('aria-expanded', 'false');
      }
    }

    function openWalletMenu(event) {
      if (event) event.preventDefault();
      if (!fabScrim || !fabPanel) return;

      var isOpen = fabPanel.classList.contains('is-open');
      if (isOpen) {
        closePanels();
        return;
      }

      closePanels();
      fabScrim.classList.add('is-open');
      fabScrim.setAttribute('aria-hidden', 'false');
      fabPanel.classList.add('is-open');
      fabPanel.setAttribute('aria-hidden', 'false');
      if (fabToggle) {
        fabToggle.setAttribute('aria-expanded', 'true');
      }
    }

    function openMoreMenu(event) {
      if (event) event.preventDefault();
      if (!fabScrim || !morePanel) return;

      var isOpen = morePanel.classList.contains('is-open');
      if (isOpen) {
        closePanels();
        return;
      }

      closePanels();
      fabScrim.classList.add('is-open');
      fabScrim.setAttribute('aria-hidden', 'false');
      morePanel.classList.add('is-open');
      morePanel.setAttribute('aria-hidden', 'false');
      moreToggle.classList.add('is-open');
      moreToggle.setAttribute('aria-expanded', 'true');
    }

    function openPaymentModal() {
      if (!addFundsModal) return;
      closePanels();
      addFundsModal.classList.add('is-open');
      addFundsModal.setAttribute('aria-hidden', 'false');
    }

    function closePaymentModal() {
      if (!addFundsModal) return;
      addFundsModal.classList.remove('is-open');
      addFundsModal.setAttribute('aria-hidden', 'true');
    }

    if (fabToggle) {
      fabToggle.addEventListener('click', function (event) {
        openWalletMenu(event);
        fabToggle.setAttribute('aria-expanded', 'true');
      });
    }

    if (moreToggle) {
      moreToggle.addEventListener('click', openMoreMenu);
    }

    if (fabScrim) {
      fabScrim.addEventListener('click', function () {
        closePanels();
        closePaymentModal();
      });
    }

    document.querySelectorAll('[data-open-modal="addFundsModal"]').forEach(function (button) {
      button.addEventListener('click', function (event) {
        event.preventDefault();
        openPaymentModal();
      });
    });

    document.querySelectorAll('[data-close-modal]').forEach(function (button) {
      button.addEventListener('click', function () {
        closePaymentModal();
      });
    });

    paymentOptions.forEach(function (button) {
      button.addEventListener('click', function () {
        paymentOptions.forEach(function (option) {
          option.classList.toggle('is-selected', option === button);
        });
        selectedPaymentMethod = button.getAttribute('data-payment-method');
      });
    });

    if (continueAddFunds) {
      continueAddFunds.addEventListener('click', function () {
        closePaymentModal();
        window.location.href = '{{ route('deposit') }}?method=' + encodeURIComponent(selectedPaymentMethod);
      });
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closePanels();
        closePaymentModal();
      }
    });

    function setBannerSlide(index) {
      var slides = document.querySelectorAll('.banner-slide');
      var indicators = document.querySelectorAll('.banner-indicator');
      if (!slides.length) return;

      var normalized = (index % slides.length + slides.length) % slides.length;
      slides.forEach(function (slide, slideIndex) {
        slide.classList.toggle('is-active', slideIndex === normalized);
      });
      indicators.forEach(function (indicator, indicatorIndex) {
        indicator.classList.toggle('is-active', indicatorIndex === normalized);
      });
      currentBannerIndex = normalized;
    }

    function startBannerAutoRotate() {
      if (bannerAutoTimer) return;
      bannerAutoTimer = setInterval(function () {
        setBannerSlide(currentBannerIndex + 1);
      }, 3000);
    }

    function stopBannerAutoRotate() {
      if (!bannerAutoTimer) return;
      clearInterval(bannerAutoTimer);
      bannerAutoTimer = null;
    }

    var currentBannerIndex = 0;
    var bannerAutoTimer = null;
    var bannerCarousel = document.getElementById('bannerCarousel');
    var bannerIndicators = document.querySelectorAll('.banner-indicator');
    var touchStartX = null;
    var touchDeltaX = 0;

    function setBannerSlide(index) {
      var slides = document.querySelectorAll('.banner-slide');
      var indicators = document.querySelectorAll('.banner-indicator');
      if (!slides.length) return;
      var normalized = (index % slides.length + slides.length) % slides.length;
      slides.forEach(function (slide, slideIndex) {
        slide.classList.toggle('is-active', slideIndex === normalized);
      });
      indicators.forEach(function (indicator, indicatorIndex) {
        indicator.classList.toggle('is-active', indicatorIndex === normalized);
      });
      currentBannerIndex = normalized;
    }

    function startBannerAutoRotate() {
      if (bannerAutoTimer) return;
      bannerAutoTimer = setInterval(function () {
        setBannerSlide(currentBannerIndex + 1);
      }, 5000);
    }

    function stopBannerAutoRotate() {
      if (!bannerAutoTimer) return;
      clearInterval(bannerAutoTimer);
      bannerAutoTimer = null;
    }

    if (bannerCarousel) {
      bannerIndicators.forEach(function (indicator) {
        indicator.addEventListener('click', function (event) {
          event.preventDefault();
          var targetIndex = parseInt(indicator.getAttribute('data-slide'), 10);
          setBannerSlide(targetIndex);
          stopBannerAutoRotate();
          startBannerAutoRotate();
        });
      });

      bannerCarousel.addEventListener('mouseenter', stopBannerAutoRotate);
      bannerCarousel.addEventListener('mouseleave', startBannerAutoRotate);

      bannerCarousel.addEventListener('click', function () {
        var activeSlide = document.querySelector('.banner-slide.is-active');
        if (activeSlide && activeSlide.dataset.href) {
          window.location.href = activeSlide.dataset.href;
        }
      });

      bannerCarousel.addEventListener('touchstart', function (event) {
        touchStartX = event.touches[0].clientX;
        touchDeltaX = 0;
        stopBannerAutoRotate();
      });

      bannerCarousel.addEventListener('touchmove', function (event) {
        if (touchStartX === null) return;
        touchDeltaX = event.touches[0].clientX - touchStartX;
      });

      bannerCarousel.addEventListener('touchend', function () {
        if (touchStartX === null) return;
        if (Math.abs(touchDeltaX) > 40) {
          setBannerSlide(currentBannerIndex + (touchDeltaX < 0 ? 1 : -1));
        }
        touchStartX = null;
        touchDeltaX = 0;
        startBannerAutoRotate();
      });

      startBannerAutoRotate();
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeFabMenu();
        closeRafflePopup();
      }
    });
  })();

</script>
@endsection
