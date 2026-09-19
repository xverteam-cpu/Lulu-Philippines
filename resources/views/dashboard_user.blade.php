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
    aspect-ratio: 4 / 1;
    padding: 0;
    color: #fff;
    overflow: hidden;
  }

  .hero-card-art {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    display: block;
    pointer-events: none;
    z-index: 0;
    transform: none;
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
    justify-content: center;
    gap: 16px;
    position: absolute;
    top: 42%;
    left: 5%;
    right: 5%;
    margin: 0;
    text-align: center;
  }

  .hero-kicker {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .12em;
    text-transform: uppercase;
    opacity: 1;
    color: #ffffff;
  }

  .hero-balance {
    position: absolute;
    top: 52%;
    left: 5%;
    right: 5%;
    bottom: auto;
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 16px;
    text-align: center;
  }

  .hero .balance-value {
    font-size: 44px;
    font-weight: 700;
    letter-spacing: -1px;
    line-height: 1.05;
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
    width: min(640px, calc(100vw - 24px));
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    justify-content: space-around;
    gap: 8px;
    max-width: 640px;
    margin: 0 auto;
    padding: 0 12px;
    height: 86px;
    background: rgba(255,255,255,.95);
    border-radius: 14px;
    border: 1px solid rgba(239,239,247,.90);
    backdrop-filter: blur(18px);
    box-shadow: 0 8px 24px rgba(15,23,42,.06);
    z-index: 9999;
    visibility: visible;
    opacity: 1;
  }

  .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    min-width: 0;
    flex: 1 1 0;
    color: var(--color-muted);
    font-weight: 500;
    font-size: 13px;
    white-space: nowrap;
    text-decoration: none;
    transition: transform .2s ease, color .2s ease;
  }

  .nav-item:hover {
    transform: translateY(-2px);
    color: var(--color-title);
  }

  .nav-item img,
  .nav-item svg {
    width: 22px;
    height: 22px;
    color: var(--color-primary);
  }

  .bottom-nav a {
    text-decoration: none;
  }

  .discover-item,
  .section-link {
    text-decoration: none;
  }

  .nav-scan {
    position: relative;
    top: 0;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    box-shadow: none;
    transition: transform .18s ease;
  }

  .nav-scan:hover {
    transform: translateY(-2px);
  }

  .nav-scan img,
  .nav-scan svg {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    color: var(--color-primary);
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

  .fab-panel.is-open {
    transform: translateY(0);
  }

  .fab-sheet {
    border-radius: 28px 28px 0 0;
    padding: 18px 18px 28px;
    background: #fff;
    box-shadow: 0 -18px 60px rgba(3,7,18,0.14);
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

  .fab-actions {
    display: grid;
    gap: 12px;
  }

  .fab-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 6px;
    border-radius: 0;
    background: transparent;
    color: #121212;
    text-decoration: none;
    font-weight: 800;
    transition: transform .2s ease, background .2s ease;
    transform: translateY(24px);
    opacity: 0;
  }

  .fab-panel.is-open .fab-action {
    transform: translateY(0);
    opacity: 1;
  }

  .fab-action:hover {
    background: transparent;
    transform: translateY(-2px);
  }

  .fab-action-icon {
    width: 72px;
    height: 72px;
    border-radius: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    box-shadow: none;
    overflow: hidden;
    flex-shrink: 0;
    padding: 0;
  }

  .fab-action-icon img,
  .fab-action-icon svg {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    padding: 0;
    border-radius: 0;
    color: var(--color-primary);
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

  @media (max-width: 760px) {
    .wallet-shell {
      margin: 0 auto 110px;
      padding: 0 12px;
    }
    .hero {
      aspect-ratio: 3 / 1;
    }
    .hero-top {
      flex-direction: row;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }
    .hero-balance {
      align-items: center;
      gap: 16px;
      justify-content: space-between;
      flex-wrap: wrap;
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
      padding: 0 8px;
      gap: 4px;
      bottom: max(8px, env(safe-area-inset-bottom)) !important;
    }
    .nav-scan {
      top: 0;
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
      aspect-ratio: 3 / 1;
    }
    .hero-top {
      gap: 10px;
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
      padding: 0 4px;
      gap: 2px;
      height: 72px;
      bottom: max(6px, env(safe-area-inset-bottom)) !important;
    }
    .nav-item {
      gap: 3px;
      font-size: 11px;
    }
    .nav-item img,
    .nav-item svg {
      width: 20px;
      height: 20px;
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
  $activeCapital = $investments->sum(fn($i) => (float) $i->amount);
  if (! isset($dailyInterest)) {
      $dailyInterest = $investments->sum(fn($i) => $i->dailyInterestAmount());
  }
  $earnedIncome = $investments->sum(fn($i) => $i->earnedInterest());
  $availableBalance = (float) $user->balance + $earnedIncome;
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

  <section class="hero">
    <svg class="hero-card-art" viewBox="0 0 1200 300" role="img"
         aria-label="Lulu Retail green card background" preserveAspectRatio="none"
         xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="heroBase" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#006f51"/>
          <stop offset=".48" stop-color="#008f65"/>
          <stop offset="1" stop-color="#006c50"/>
        </linearGradient>
        <linearGradient id="heroLime" x1="0" y1="1" x2="1" y2="0">
          <stop offset="0" stop-color="#67c936" stop-opacity=".10"/>
          <stop offset=".55" stop-color="#79d83e" stop-opacity=".72"/>
          <stop offset="1" stop-color="#25ae5e" stop-opacity=".30"/>
        </linearGradient>
        <linearGradient id="heroGlow" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0" stop-color="#f9dd3c" stop-opacity="0"/>
          <stop offset=".55" stop-color="#ffe45c" stop-opacity=".95"/>
          <stop offset="1" stop-color="#fff29a" stop-opacity=".76"/>
        </linearGradient>
        <radialGradient id="heroCornerGlow" cx="0" cy="0" r="1">
          <stop offset="0" stop-color="#9de052" stop-opacity=".72"/>
          <stop offset="1" stop-color="#9de052" stop-opacity="0"/>
        </radialGradient>
        <clipPath id="heroCardClip">
          <rect width="1200" height="300" rx="32"/>
        </clipPath>
      </defs>
      <g clip-path="url(#heroCardClip)">
        <rect width="1200" height="300" fill="url(#heroBase)"/>
        <ellipse cx="195" cy="-35" rx="300" ry="185" fill="url(#heroCornerGlow)"/>
        <path d="M-40 264 C170 72 285 30 470 -16 L250 -25 C133 48 52 111 -40 206Z" fill="#1fac67" opacity=".22"/>
        <path d="M580 330 C785 300 886 150 1240 58 L1240 330Z" fill="#1bb966" opacity=".42"/>
        <path d="M705 330 C884 286 1003 193 1240 132 L1240 330Z" fill="url(#heroLime)"/>
        <path d="M760 330 C922 274 1058 207 1240 169" fill="none" stroke="url(#heroGlow)" stroke-width="9" stroke-linecap="round"/>
        <path d="M-55 110 C70 92 132 35 190 -18" fill="none" stroke="#f6d63b" stroke-width="3" opacity=".9"/>
        <path d="M845 330 C1000 270 1118 248 1240 278 L1240 330Z" fill="#70cf3a" opacity=".28"/>
        <rect width="1200" height="300" rx="32" fill="none" stroke="#ffffff" stroke-opacity=".08" stroke-width="2"/>
      </g>
    </svg>
    <div class="hero-top">
      <div>
        <div class="hero-kicker">Available balance</div>
      </div>
    </div>
    <div class="hero-balance">
      <div>
        <div class="balance-value">${{ number_format($availableBalance, 2) }}</div>
      </div>
    </div>
  </section>

  <div class="card">
    <div class="actions-grid" role="list">
      <a class="action" href="{{ route('send') }}">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m21 3-7.4 18-3.8-7.8L2 9.4 21 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m9.8 13.2 5.4-5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <div>Send</div>
      </a>

      <a class="action" href="{{ route('withdraw') }}">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v13m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <div>Withdraw</div>
      </a>

      <a class="action" href="{{ route('invest') }}">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5m0 14h16M7 16l3-4 3 2 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
        <div>Investment</div>
      </a>

      <a class="action" href="{{ route('franchising') }}">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V9.5L12 4l8 5.5V20H4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-5h6v5M8 10h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div>
        <div>Franchise</div>
      </a>

    </div>
  </div>

</main>

<div class="fab-scrim" id="fabScrim" aria-hidden="true"></div>
<div class="fab-panel" id="fabPanel" aria-hidden="true">
  <div class="fab-sheet" role="dialog" aria-modal="true" aria-label="Quick actions menu">
    <div class="fab-sheet-handle"></div>
    <div class="fab-sheet-title">Quick actions</div>
    <div class="fab-actions">
      <a class="fab-action" href="{{ route('invest') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5m0 14h16M7 16l3-4 3 2 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>Buy shares</span>
      </a>
      <a class="fab-action" href="{{ route('send') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m21 3-7.4 18-3.8-7.8L2 9.4 21 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
        <span>Send</span>
      </a>
      <a class="fab-action" href="{{ route('withdraw') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v13m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <span>Withdraw</span>
      </a>
      <a class="fab-action" href="{{ route('referrals') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><circle cx="17" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 20c.5-3.2 2.2-5 5.5-5s5 1.8 5.5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <span>Referrals</span>
      </a>
      <a class="fab-action" href="{{ route('franchising') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 20V9.5L12 4l8 5.5V20H4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-5h6v5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
        <span>Franchise</span>
      </a>
      <a class="fab-action" href="{{ route('unavailable') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 10h18M7 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <span>Cards</span>
      </a>
      <a class="fab-action" href="{{ route('unavailable') }}">
        <span class="fab-action-icon"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 10h16M6 10V8a6 6 0 0 1 12 0v2M5 10h14v9H5v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M12 13v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></span>
        <span>Loans</span>
      </a>
    </div>
    <button class="fab-close" type="button" id="fabClose">Close menu</button>
  </div>
</div>

<nav class="bottom-nav" aria-label="Account navigation">
  <a class="nav-item active" href="{{ route('dashboard') }}">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 11 9-7 9 7v9H3v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 20v-5h6v5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
    <div>Home</div>
  </a>
  <a class="nav-item" href="{{ route('invest') }}">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19V5m0 14h16M7 16l3-4 3 2 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <div>Buy Shares</div>
  </a>
  <a class="nav-item" href="{{ route('withdraw') }}">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3v13m0 0 4-4m-4 4-4-4M5 20h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
    <div>Withdraw</div>
  </a>
  <a class="nav-item" href="{{ route('referrals') }}">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="9" cy="8" r="3" stroke="currentColor" stroke-width="1.8"/><circle cx="17" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 20c.5-3.2 2.2-5 5.5-5s5 1.8 5.5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
    <div>Referrals</div>
  </a>
  <a class="nav-item nav-more" href="#" id="fabToggle" aria-label="More account actions" aria-controls="fabPanel" aria-expanded="false">
    <div class="nav-scan">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" fill="#46a42d"/><path d="M8 9h8M8 12h8M8 15h8" stroke="#fff" stroke-width="1.8" stroke-linecap="round"/></svg>
    </div>
    <div>More</div>
  </a>
</nav>

<script>
  (function () {
    var fabToggle = document.getElementById('fabToggle');
    var fabScrim = document.getElementById('fabScrim');
    var fabPanel = document.getElementById('fabPanel');
    var fabClose = document.getElementById('fabClose');

    function toggleFabMenu(event) {
      if (event) event.preventDefault();
      if (!fabScrim || !fabPanel) return;
      var isOpen = fabPanel.classList.contains('is-open');
      if (isOpen) {
        fabScrim.classList.remove('is-open');
        fabPanel.classList.remove('is-open');
        fabScrim.setAttribute('aria-hidden', 'true');
        fabPanel.setAttribute('aria-hidden', 'true');
      } else {
        fabScrim.classList.add('is-open');
        fabPanel.classList.add('is-open');
        fabScrim.setAttribute('aria-hidden', 'false');
        fabPanel.setAttribute('aria-hidden', 'false');
      }
    }

    function closeFabMenu() {
      if (!fabScrim || !fabPanel) return;
      fabScrim.classList.remove('is-open');
      fabPanel.classList.remove('is-open');
      fabScrim.setAttribute('aria-hidden', 'true');
      fabPanel.setAttribute('aria-hidden', 'true');
    }

    if (fabToggle) {
      fabToggle.addEventListener('click', toggleFabMenu);
    }

    if (fabScrim) {
      fabScrim.addEventListener('click', closeFabMenu);
    }

    if (fabClose) {
      fabClose.addEventListener('click', closeFabMenu);
    }

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
