@extends('layouts.app')

@section('content')
<style>
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
    padding-top: 14px;
    border-top: 3px solid #087a48;
  }

  .package-heading {
    margin: 0 0 10px;
    color: var(--color-primary);
    font-size: 13px;
    font-weight: 900;
    letter-spacing: .1em;
    text-transform: uppercase;
  }

  .package-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
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
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.34);
    border: 1px solid rgba(255, 255, 255, 0.7);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.26);
    backdrop-filter: blur(2px);
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
    display: grid;
    width: clamp(30px, 5.5vw, 42px);
    aspect-ratio: 1;
    place-items: center;
    border-radius: 50%;
    background: rgba(0, 30, 25, .46);
    color: #fff;
    font-size: 12px;
  }

  .package-card-actions {
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 2;
  }

  .package-plus-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: clamp(20px, 3.8vw, 28px);
    height: clamp(20px, 3.8vw, 28px);
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.86);
    color: #0b5d3b;
    text-decoration: none;
    font-size: 20px;
    line-height: 1;
    font-weight: 700;
    box-shadow: 0 10px 22px rgba(15, 118, 85, 0.18);
  }

  .package-plus-action:hover {
    transform: translateY(-1px);
  }

  .package-empty-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: clamp(56px, 12vw, 88px);
    height: clamp(56px, 12vw, 88px);
    border: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.78);
    color: #0d5d3c;
    text-decoration: none;
    box-shadow: 0 10px 25px rgba(15, 118, 85, 0.18);
    font-size: clamp(32px, 7vw, 52px);
    line-height: 1;
    font-weight: 600;
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .package-empty-action:hover {
    transform: scale(1.04);
    box-shadow: 0 14px 30px rgba(15, 118, 85, 0.22);
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
    bottom: max(12px, env(safe-area-inset-bottom)) !important;
    width: min(480px, calc(100vw - 24px));
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0;
    max-width: 480px;
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
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 25%;
    min-width: 0;
    color: #66747b;
    font-weight: 500;
    font-size: 9px;
    white-space: nowrap;
    text-decoration: none;
    transition: transform .2s ease, color .2s ease;
  }

  .nav-item:hover {
    transform: translateY(-2px);
    color: var(--color-title);
  }

  .nav-item.active {
    color: #087a48;
    font-weight: 800;
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
    transform: translate(-50%, 12px) scale(.96);
    transform-origin: center bottom;
    transition: opacity .2s ease, transform .24s cubic-bezier(.22,1,.36,1), visibility .2s ease;
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
      width: calc(100vw - 16px);
      padding: 0 12px;
      gap: 0;
      bottom: max(8px, env(safe-area-inset-bottom)) !important;
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
      width: calc(100vw - 12px);
      padding: 0 10px;
      gap: 0;
      height: 62px;
      bottom: max(6px, env(safe-area-inset-bottom)) !important;
    }
    .nav-item {
      gap: 3px;
      width: 48px;
      min-width: 48px;
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

<main class="wallet-shell">
  <header class="topbar">
    <div class="brand">
      <div class="brand-mark">
        <img src="https://www.realclipart.com/png/middle/222-2226460_price-age-lulu-group-international-lulu-logo.png" alt="Lulu logo">
      </div>
    </div>
    <a class="account-profile-link" href="{{ route('profile') }}">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
        <path d="M5 20c.7-3.5 3-5.5 7-5.5s6.3 2 7 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
      <span>Profile</span>
    </a>
  </header>

  <section class="hero" aria-label="Account balance">
    <div class="hero-top">
      <div class="hero-kicker">Available balance</div>
    </div>
    <div class="hero-balance">
      <div class="balance-value">${{ number_format($availableBalance, 2) }}</div>
    </div>
    <div class="hero-stats">
      <div class="hero-stat">
        <div class="hero-stat-label">Assets</div>
        <div class="hero-stat-value">${{ number_format($totalInvestment, 2) }}</div>
      </div>
    </div>
  </section>

  <section class="package-section" aria-labelledby="packageHeading">
    <h2 class="package-heading" id="packageHeading">Available Bonds</h2>
    <div class="package-grid">
      @foreach ($packageDefinitions as $packageKey => $package)
        @php
          $hasPackageInvestment = $approvedInvestments->contains('package_key', $packageKey);
        @endphp

        @if ($hasPackageInvestment)
          <article class="package-card {{ $packageKey }}">
            <div class="package-card-actions">
              <a class="package-plus-action" href="{{ route('invest.purchase', ['package' => $packageKey]) }}" aria-label="Buy another {{ $package['name'] }} package">+</a>
              <span class="package-menu" aria-hidden="true">•••</span>
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
            <a class="package-empty-action" href="{{ route('invest.purchase', ['package' => $packageKey]) }}" aria-label="Buy {{ $package['name'] }} package">+</a>
          </article>
        @endif
      @endforeach
    </div>
  </section>

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

<nav class="bottom-nav" aria-label="Account navigation">
  <a class="nav-item active" href="{{ route('dashboard') }}" aria-current="page">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 11 9-7 9 7v9H3v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-5h6v5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
    <div>Home</div>
  </a>
  <a class="nav-item" href="{{ route('history') }}">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M7 9h10M7 13h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
    <div>Transactions</div>
  </a>
  <a class="nav-item" href="{{ route('franchising') }}">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/><path d="M12 3v3m9 6h-3m-6 9v-3m-9-6h3" stroke="currentColor" stroke-width="1.5"/></svg>
    <div>Franchise</div>
  </a>
  <button class="nav-item" type="button" id="moreToggle" aria-label="Open quick actions" aria-expanded="false">
    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 6h18l-3 12H2L3 6Z"/></svg>
    <div>More</div>
  </button>
</nav>

<script>
  (function () {
    var moreToggle = document.getElementById('moreToggle');
    var fabScrim = document.getElementById('fabScrim');
    var fabPanel = document.getElementById('fabPanel');
    var morePanel = document.getElementById('morePanel');
    var addFundsModal = document.getElementById('addFundsModal');
    var paymentOptions = document.querySelectorAll('.payment-method-option');
    var continueAddFunds = document.getElementById('continueAddFunds');
    var selectedPaymentMethod = 'bank';

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
    }

    function openWalletMenu(event) {
      if (event) event.preventDefault();
      if (!fabScrim || !fabPanel) return;
      closePanels();
      fabScrim.classList.add('is-open');
      fabScrim.setAttribute('aria-hidden', 'false');
      fabPanel.classList.add('is-open');
      fabPanel.setAttribute('aria-hidden', 'false');
    }

    function openMoreMenu(event) {
      if (event) event.preventDefault();
      if (!fabScrim || !morePanel) return;
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
