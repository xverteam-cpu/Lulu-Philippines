<section class="investors-pin-lock" aria-labelledby="investors-pin-title">
    <style>
        .investors-login-card.investors-pin-lock-card {
            box-sizing: border-box;
            width: min(390px, calc(100vw - 32px));
            height: auto !important;
            min-height: 0 !important;
            padding: 0;
            overflow: hidden;
            border: 1px solid #e2e9e4;
            border-radius: 24px;
            background: #fcfdf9;
            box-shadow: 0 20px 55px rgba(0, 31, 24, .24), 0 2px 8px rgba(0, 31, 24, .08);
            color: #193b2c;
            backdrop-filter: none;
        }
        .investors-pin-lock {
            box-sizing: border-box;
            width: 100%;
            color: #193b2c;
            text-align: center;
            font-family: Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .investors-pin-lock-form {
            display: flex;
            width: 100%;
            min-height: 0;
            flex: 1;
            flex-direction: column;
        }
        .investors-pin-lock *,
        .investors-pin-lock *::before,
        .investors-pin-lock *::after {
            box-sizing: border-box;
        }
        .investors-pin-lock .pin-hero {
            display: none;
        }
        .investors-pin-lock .pin-panel {
            padding: 26px 30px 20px;
        }
        .investors-pin-lock .pin-intro {
            width: 100%;
        }
        .investors-pin-lock .pin-lock-badge {
            display: none;
        }
        .investors-pin-lock .pin-brand {
            display: block;
            width: 128px;
            height: 48px;
            margin: 0 auto 12px;
            object-fit: contain;
        }
        .investors-pin-lock .pin-eyebrow {
            margin: 0 0 5px;
            color: #087443;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }
        .investors-pin-lock h1 {
            margin: 0;
            color: #193b2c;
            font-size: 25px;
            font-weight: 700;
            letter-spacing: -.04em;
        }
        .investors-pin-lock .pin-description {
            margin: 7px 0 0;
            color: #738077;
            font-size: 13px;
            line-height: 1.5;
        }
        .investors-pin-lock .pin-region {
            width: 100%;
            margin-top: 21px;
        }
        .investors-pin-lock .pin-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0 1px 10px;
        }
        .investors-pin-lock .pin-label {
            color: #66806e;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.7px;
        }
        .investors-pin-lock .pin-reveal {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin: 0;
            padding: 4px;
            border: 0;
            background: transparent;
            color: #64816b;
            font: 600 11px/1.3 Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
            text-transform: none;
            cursor: pointer;
        }
        .investors-pin-lock .pin-reveal:disabled {
            cursor: not-allowed;
            opacity: .5;
        }
        .investors-pin-lock .pin-reveal svg {
            width: 15px;
            height: 15px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }
        .investors-pin-lock .pin-dots {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
            margin: 0 auto;
        }
        .investors-pin-lock .pin-dot {
            display: grid;
            min-width: 0;
            height: 49px;
            place-items: center;
            border: 1px solid #e6ece1;
            border-radius: 12px;
            background: #f5f7f0;
            color: #163c2e;
            font-size: 20px;
            font-weight: 650;
            transition: background .15s, border-color .15s;
        }
        .investors-pin-lock .pin-dot.filled {
            border-color: #a4be8a;
            background: #eff4e8;
        }
        .investors-pin-lock .pin-dot.active {
            border-color: #3c8d50;
            box-shadow: 0 0 0 2px #5e9f4d14;
        }
        .investors-pin-lock .pin-mask {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #087b45;
        }
        .investors-pin-lock .pin-status {
            display: flex;
            min-height: 32px;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #738077;
            font-size: 11px;
            line-height: 1.4;
        }
        .investors-pin-lock .pin-status.error {
            color: #b42318;
        }
        .investors-pin-lock .pin-keypad {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 5px 8px;
            margin: 9px auto 10px;
        }
        .investors-pin-lock .pin-key {
            display: flex;
            width: 100%;
            min-width: 0;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            border: 1px solid transparent;
            border-radius: 14px;
            background: transparent;
            color: #163c2e;
            font: 500 24px/1 Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            text-align: center;
            letter-spacing: 0;
            text-transform: none;
            cursor: pointer;
            transition: background .15s, transform .1s;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }
        .investors-pin-lock .pin-key:hover {
            background: #edf3e6;
        }
        .investors-pin-lock .pin-key:active {
            transform: scale(.93);
            background: #e0ebd3;
        }
        .investors-pin-lock .pin-key.utility {
            color: #71826c;
            font-size: 10px;
            font-weight: 600;
        }
        .investors-pin-lock .pin-key:disabled {
            cursor: not-allowed;
            opacity: .35;
        }
        .investors-pin-lock .pin-submit {
            display: flex;
            width: 100%;
            min-width: 0;
            min-height: 49px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            margin: 0;
            padding: 12px 16px;
            border: 0;
            border-radius: 15px;
            background: #087b45;
            color: #fff;
            font: 700 13px/1 Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
            text-transform: none;
            cursor: pointer;
            box-shadow: 0 7px 16px #087b4524;
            transition: background .15s, transform .15s;
        }
        .investors-pin-lock .pin-submit:not(:disabled):hover {
            background: #056238;
            transform: translateY(-1px);
        }
        .investors-pin-lock .pin-submit:disabled {
            background: #e1e8da;
            color: #91a08a;
            cursor: not-allowed;
            box-shadow: none;
        }
        .investors-pin-lock .pin-submit svg {
            width: 17px;
            height: 17px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.8;
        }
        .investors-pin-lock .pin-help-link,
        .investors-pin-lock .pin-signout {
            display: inline-flex;
            width: auto;
            min-width: 0;
            justify-content: center;
            margin: 0;
            padding: 13px 10px 10px;
            border: 0;
            background: transparent;
            color: #087443;
            font: 650 11px/1.4 Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
            text-decoration: none;
            text-transform: none;
            cursor: pointer;
        }
        .investors-pin-lock .pin-help-link:hover,
        .investors-pin-lock .pin-signout:hover {
            text-decoration: underline;
        }
        .investors-pin-lock .pin-signout {
            margin-top: 0;
            padding: 5px 8px;
            color: #63756a;
            font-weight: 600;
        }
        .investors-pin-lock .pin-footer {
            width: 100%;
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px solid #e6ece1;
        }
        .investors-pin-lock .pin-privacy {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color: #7e8c7a;
            font-size: 9px;
        }
        .investors-pin-lock .pin-privacy svg {
            width: 12px;
            height: 12px;
            fill: none;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 1.7;
        }
        .investors-pin-lock .pin-progress,
        .investors-pin-lock .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            clip-path: inset(50%);
        }
        .investors-pin-lock button:focus-visible,
        .investors-pin-lock a:focus-visible {
            outline: 3px solid #70ad43;
            outline-offset: 3px;
        }
        @media (max-width: 780px) {
            body.investors-pin-screen {
                width: 100%;
                height: 100%;
                overflow: hidden;
                background: #032818;
                background-image: url("{{ asset('investor-mobile-background.png') }}");
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            body.investors-pin-screen .investors-login-card.investors-pin-lock-card {
                position: fixed;
                z-index: 99999;
                inset: 0;
                display: flex;
                width: 100%;
                max-width: none;
                height: 100vh !important;
                height: 100svh !important;
                min-height: 0 !important;
                margin: 0;
                padding: 0;
                overflow: auto;
                border: 0;
                border-radius: 0;
                background: #062b19;
                background-image: url("{{ asset('investor-mobile-background.png') }}");
                background-position: center;
                background-size: cover;
                box-shadow: 0 28px 90px #0008;
            }
            .investors-pin-lock {
                display: flex;
                min-height: 100%;
                flex-direction: column;
                text-align: center;
                color: #fffdf0;
            }
            .investors-pin-lock .pin-hero {
                position: relative;
                display: block;
                height: 164px;
                flex: 0 0 164px;
                overflow: hidden;
                padding: max(24px, env(safe-area-inset-top)) 27px 18px;
                background: linear-gradient(180deg, #001a1080, #001a1029);
                color: #fff;
                text-align: left;
            }
            .investors-pin-lock .pin-hero::before,
            .investors-pin-lock .pin-hero::after {
                position: absolute;
                top: 15px;
                right: -120px;
                width: 260px;
                height: 260px;
                border: 1px solid #ffffff12;
                border-radius: 50%;
                content: "";
            }
            .investors-pin-lock .pin-hero::after {
                top: -30px;
                right: -165px;
                width: 350px;
                height: 350px;
            }
            .investors-pin-lock .pin-hero::before,
            .investors-pin-lock .pin-hero::after { display: none; }
            .investors-pin-lock .pin-topbar,
            .investors-pin-lock .pin-hero-copy {
                position: relative;
                z-index: 1;
            }
            .investors-pin-lock .pin-topbar {
                display: flex;
                align-items: center;
                gap: 11px;
            }
            .investors-pin-lock .pin-wordmark {
                color: #fff;
                font-family: "Arial Rounded MT Bold", "Trebuchet MS", sans-serif;
                font-size: 36px;
                font-style: italic;
                font-weight: 900;
                letter-spacing: -3px;
                line-height: 1;
            }
            .investors-pin-lock .pin-brand-divider {
                width: 1px;
                height: 28px;
                background: #ffffff40;
            }
            .investors-pin-lock .pin-country {
                color: #fff;
                font-size: 9px;
                font-weight: 700;
                letter-spacing: 2px;
                line-height: 1.65;
            }
            .investors-pin-lock .pin-hero-copy {
                margin: 21px 0 0;
                color: #e0eddd;
                font-size: 12px;
                letter-spacing: .3px;
            }
            .investors-pin-lock .pin-hero-copy span {
                color: #d9ef8c;
            }
            .investors-pin-lock .pin-panel {
                z-index: 1;
                display: flex;
                min-height: 0;
                flex: 1;
                flex-direction: column;
                align-items: center;
                margin-top: -22px;
                padding: 22px 25px max(14px, env(safe-area-inset-bottom));
                border-radius: 28px 28px 0 0;
                border-top: 1px solid #dce7b445;
                background: linear-gradient(155deg, #05291bd9, #042418ad);
                box-shadow: 0 -8px 35px #00180c20;
                backdrop-filter: blur(5px);
                -webkit-backdrop-filter: blur(5px);
            }
            .investors-pin-lock .pin-lock-badge {
                display: grid;
                width: 43px;
                height: 43px;
                place-items: center;
                margin: 0 auto 12px;
                border: 1px solid #dfebd6;
                border-radius: 15px;
                border-color: #e3edb033;
                background: #e3edb015;
                color: #e4edb1;
            }
            .investors-pin-lock .pin-lock-badge svg {
                width: 21px;
                height: 21px;
                fill: none;
                stroke: currentColor;
                stroke-linecap: round;
                stroke-linejoin: round;
                stroke-width: 1.7;
            }
            .investors-pin-lock .pin-brand,
            .investors-pin-lock .pin-eyebrow {
                display: none;
            }
            .investors-pin-lock h1 {
                font-size: 28px;
                letter-spacing: -1px;
                line-height: 1.1;
                color: #fffdf0;
                text-shadow: 0 2px 15px #001a1440;
            }
            .investors-pin-lock .pin-description {
                margin-top: 7px;
                font-size: 12px;
                color: #e2e9cf;
            }
            .investors-pin-lock .pin-label,
            .investors-pin-lock .pin-reveal {
                color: #e2e9cf;
            }
            .investors-pin-lock .pin-region {
                margin-top: 24px;
            }
            .investors-pin-lock .pin-dot {
                border-color: #d3e0b745;
                background: #e1edcc10;
                color: #fffce8;
                box-shadow: inset 0 1px 3px #001c1822;
            }
            .investors-pin-lock .pin-dot.filled {
                border-color: #d6e4ab9c;
                background: #cddd9d25;
            }
            .investors-pin-lock .pin-dot.active {
                border-color: #e3eaaa;
                box-shadow: 0 0 0 2px #dee9a214;
            }
            .investors-pin-lock .pin-mask { background: #e5edbb; }
            .investors-pin-lock .pin-status { color: #d5dfc4; }
            .investors-pin-lock .pin-status.error { color: #ffd0c2; }
            .investors-pin-lock .pin-keypad {
                gap: 5px 8px;
                margin: auto 0 10px;
            }
            .investors-pin-lock .pin-key {
                min-height: 51px;
                font-size: 25px;
                color: #fffdeb;
                text-shadow: 0 2px 6px #00241666;
            }
            .investors-pin-lock .pin-key:hover { background: #ebf5c014; }
            .investors-pin-lock .pin-key:active { background: #e5efbc24; }
            .investors-pin-lock .pin-key.utility { color: #e5edcc; }
            .investors-pin-lock .pin-key:disabled { opacity: .35; }
            .investors-pin-lock .pin-submit {
                color: #143e25;
                background: linear-gradient(110deg, #dceba5, #f1e9b1);
                box-shadow: 0 7px 22px #001a1a44;
            }
            .investors-pin-lock .pin-submit:disabled {
                border: 1px solid #dce7b02e;
                background: #dce7b01a;
                color: #d0dbbd80;
                box-shadow: none;
            }
            .investors-pin-lock .pin-help-link,
            .investors-pin-lock .pin-signout {
                color: #e6edb9;
            }
            .investors-pin-lock .pin-submit {
                min-height: 49px;
            }
            .investors-pin-lock .pin-footer {
                margin-top: auto;
                border-color: #deebba30;
            }
            .investors-pin-lock .pin-privacy {
                color: #d4dfc1;
            }
        }
        @media (max-width: 780px) and (max-height: 740px) {
            .investors-pin-lock .pin-hero {
                height: 140px;
                flex-basis: 140px;
                padding-top: max(20px, env(safe-area-inset-top));
            }
            .investors-pin-lock .pin-hero-copy { margin-top: 17px; }
            .investors-pin-lock .pin-panel { padding-top: 17px; }
            .investors-pin-lock .pin-lock-badge {
                width: 35px;
                height: 35px;
                margin-bottom: 9px;
                border-radius: 12px;
            }
            .investors-pin-lock h1 { font-size: 26px; }
            .investors-pin-lock .pin-region { margin-top: 17px; }
            .investors-pin-lock .pin-dot { height: 45px; }
            .investors-pin-lock .pin-key { min-height: 44px; }
            .investors-pin-lock .pin-keypad { gap: 2px 8px; margin-bottom: 7px; }
            .investors-pin-lock .pin-help-link { padding-top: 10px; padding-bottom: 8px; }
            .investors-pin-lock .pin-footer { padding-top: 7px; }
        }
        @media (max-width: 780px) and (max-height: 620px) {
            .investors-pin-lock .pin-hero {
                height: 109px;
                flex-basis: 109px;
                padding: max(16px, env(safe-area-inset-top)) 23px 12px;
            }
            .investors-pin-lock .pin-wordmark { font-size: 31px; }
            .investors-pin-lock .pin-hero-copy { margin-top: 12px; font-size: 11px; }
            .investors-pin-lock .pin-panel { padding: 14px 22px max(9px, env(safe-area-inset-bottom)); }
            .investors-pin-lock .pin-lock-badge { display: none; }
            .investors-pin-lock h1 { font-size: 24px; }
            .investors-pin-lock .pin-description { font-size: 11px; }
            .investors-pin-lock .pin-region { margin-top: 12px; }
            .investors-pin-lock .pin-head { margin-bottom: 5px; }
            .investors-pin-lock .pin-dot { height: 39px; border-radius: 10px; }
            .investors-pin-lock .pin-status { min-height: 25px; font-size: 10px; }
            .investors-pin-lock .pin-key { min-height: 36px; font-size: 23px; }
            .investors-pin-lock .pin-keypad { gap: 0 8px; margin-bottom: 5px; }
            .investors-pin-lock .pin-submit { min-height: 42px; border-radius: 12px; }
            .investors-pin-lock .pin-help-link { padding: 8px; }
            .investors-pin-lock .pin-footer { padding-top: 5px; }
            .investors-pin-lock .pin-privacy { font-size: 8px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .investors-pin-lock .pin-dot,
            .investors-pin-lock .pin-key,
            .investors-pin-lock .pin-submit { transition: none; }
        }

        .investors-login-card.investors-pin-lock-card {
            width: min(392px, calc(100vw - 32px));
            height: min(844px, calc(100svh - 48px)) !important;
            min-height: 650px !important;
            padding: 0;
            overflow: hidden;
            border: 0;
            border-radius: 38px;
            background: #f3f7f5 url("{{ asset('investor-mobile-background.png') }}") center / cover no-repeat;
            box-shadow: 0 28px 70px rgba(8, 42, 28, .22);
        }
        .investors-pin-lock {
            display: flex;
            height: 100%;
            min-height: 0;
            flex-direction: column;
            background: transparent;
            color: #082a1c;
            font-family: "Plus Jakarta Sans", Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .investors-pin-lock .pin-hero {
            display: flex;
            height: auto;
            flex: 0 0 auto;
            flex-direction: column;
            padding: 20px 24px 12px;
            background: transparent;
            color: #082a1c;
            text-align: left;
        }
        .investors-pin-lock .pin-topbar { gap: 10px; }
        .investors-pin-lock .pin-wordmark {
            color: #116337;
            font-size: 34px;
        }
        .investors-pin-lock .pin-brand-divider { background: #11633740; }
        .investors-pin-lock .pin-country {
            color: #67836d;
            font-size: 9px;
            letter-spacing: 1.8px;
        }
        .investors-pin-lock .pin-hero-copy {
            margin: 9px 0 0 3px;
            color: #67836d;
            font-size: 10px;
        }
        .investors-pin-lock .pin-hero-copy span { color: #116337; }
        .investors-pin-lock .pin-wordmark {
            display: block;
            width: 112px;
            height: 42px;
            object-fit: contain;
            object-position: left center;
        }
        .investors-pin-lock .pin-panel {
            display: flex;
            min-height: 0;
            flex: 1;
            flex-direction: column;
            justify-content: space-between;
            margin: 0;
            padding: 8px 24px 20px;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }
        .investors-pin-lock .pin-card {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 21px;
            border-radius: 28px;
            background:
                radial-gradient(110% 95% at 15% 10%, #247a54 0%, rgba(28, 107, 72, .35) 45%, transparent 75%),
                linear-gradient(140deg, #1c6b48 0%, #0d422c 55%, #082a1c 100%);
            box-shadow: 0 18px 36px -12px rgba(8, 42, 28, .35), inset 0 1px 1px rgba(255, 255, 255, .22);
            color: #fff;
        }
        .investors-pin-lock .pin-intro { position: relative; z-index: 1; }
        .investors-pin-lock .pin-brand,
        .investors-pin-lock .pin-eyebrow,
        .investors-pin-lock .pin-lock-badge { display: none; }
        .investors-pin-lock h1 {
            color: #fff;
            font-size: 25px;
            letter-spacing: -.8px;
        }
        .investors-pin-lock .pin-description {
            margin-top: 6px;
            color: #cae4d1;
            font-size: 12px;
        }
        .investors-pin-lock .pin-region {
            position: relative;
            z-index: 1;
            width: 100%;
            margin-top: 17px;
        }
        .investors-pin-lock .pin-head { justify-content: center; margin-bottom: 11px; }
        .investors-pin-lock .pin-label {
            color: #b4e4cb;
            font-size: 10px;
            letter-spacing: .2em;
        }
        .investors-pin-lock .pin-reveal {
            position: absolute;
            right: 0;
            color: #d9eee1;
        }
        .investors-pin-lock .pin-dots {
            display: flex;
            min-height: 58px;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 15px 18px;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 20px;
            background: rgba(255, 255, 255, .12);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .14);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .investors-pin-lock .pin-dot {
            width: 13px;
            height: 13px;
            min-width: 13px;
            border: 1.5px solid rgba(255, 255, 255, .35);
            border-radius: 50%;
            background: rgba(255, 255, 255, .24);
            transition: all .22s cubic-bezier(.34, 1.56, .64, 1);
        }
        .investors-pin-lock .pin-dot.filled {
            border-color: #76e072;
            background: #76e072;
            box-shadow: 0 0 12px #76e072, 0 0 4px #fff;
            transform: scale(1.22);
        }
        .investors-pin-lock .pin-dot.active {
            border-color: rgba(255, 255, 255, .8);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, .12);
        }
        .investors-pin-lock .pin-dot.filled.active {
            border-color: #76e072;
            box-shadow: 0 0 12px #76e072, 0 0 4px #fff;
        }
        .investors-pin-lock .pin-dot.revealed {
            width: 26px;
            height: 26px;
            min-width: 26px;
            border-radius: 50%;
            background: #f3f7f5;
            color: #082a1c;
            font-size: 12px;
            font-weight: 700;
            box-shadow: none;
            transform: none;
        }
        .investors-pin-lock .pin-mask { background: #082a1c; }
        .investors-pin-lock .pin-status {
            min-height: 26px;
            color: #cae4d1;
            font-size: 10px;
        }
        .investors-pin-lock .pin-status.error { color: #ffe0da; }
        .investors-pin-lock .pin-dots.error .pin-dot {
            border-color: #ffd2cc;
            background: #ffd2cc;
        }
        .investors-pin-lock .pin-keypad {
            width: min(100%, 296px);
            grid-template-columns: repeat(3, 1fr);
            justify-items: center;
            gap: 8px 18px;
            margin: auto auto 10px;
        }
        .investors-pin-lock .pin-key {
            width: clamp(52px, 8.5vh, 76px);
            min-height: clamp(52px, 8.5vh, 76px);
            border: 1px solid #e1ece6;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 6px 16px -4px rgba(8, 42, 28, .07), 0 2px 4px rgba(8, 42, 28, .04), inset 0 1px 0 #fff;
            color: #082a1c;
            font-family: "Space Grotesk", "Courier New", monospace;
            font-size: 25px;
            font-weight: 600;
            transition: transform .1s cubic-bezier(.34, 1.56, .64, 1), background-color .1s ease, border-color .1s ease;
        }
        .investors-pin-lock .pin-key:hover,
        .investors-pin-lock .pin-key:active {
            border-color: #a8dec0;
            background: #e6f5ec;
        }
        .investors-pin-lock .pin-key:active { transform: scale(.94); }
        .investors-pin-lock .pin-key.utility {
            border-color: #dfeae4;
            background: rgba(255, 255, 255, .65);
            color: #1c6b48;
            font-family: inherit;
            font-size: 9px;
            font-weight: 700;
        }
        .investors-pin-lock .pin-submit { display: none; }
        .investors-pin-lock .pin-help-link,
        .investors-pin-lock .pin-signout {
            padding: 8px;
            color: #1c6b48;
        }
        .investors-pin-lock .pin-footer { padding-top: 9px; }
        @media (max-width: 780px) {
            body.investors-pin-screen {
                background: #dce5df;
                background-image: url("{{ asset('investor-mobile-background.png') }}");
                background-position: center;
                background-size: cover;
                background-repeat: no-repeat;
            }
            body.investors-pin-screen .investors-login-card.investors-pin-lock-card {
                height: 100vh !important;
                height: 100dvh !important;
                min-height: 0 !important;
                background: #f3f7f5 url("{{ asset('investor-mobile-background.png') }}") center / cover no-repeat;
                box-shadow: none;
            }
            .investors-pin-lock .pin-hero {
                padding: max(15px, env(safe-area-inset-top)) 24px 10px;
            }
            .investors-pin-lock .pin-panel {
                padding: 8px 24px max(14px, env(safe-area-inset-bottom));
            }
        }
        @media (max-width: 780px) and (max-height: 700px) {
            .investors-pin-lock .pin-hero { padding-top: max(9px, env(safe-area-inset-top)); }
            .investors-pin-lock .pin-hero-copy { margin-top: 5px; }
            .investors-pin-lock .pin-panel { padding-top: 5px; }
            .investors-pin-lock .pin-card { padding: 15px; border-radius: 23px; }
            .investors-pin-lock .pin-region { margin-top: 11px; }
            .investors-pin-lock .pin-dots { min-height: 48px; gap: 16px; padding: 12px; }
            .investors-pin-lock .pin-keypad { gap: 5px 16px; margin-bottom: 5px; }
            .investors-pin-lock .pin-key {
                width: clamp(46px, 8vh, 64px);
                min-height: clamp(46px, 8vh, 64px);
            }
            .investors-pin-lock .pin-help-link,
            .investors-pin-lock .pin-signout { padding: 5px 8px; }
            .investors-pin-lock .pin-footer { padding-top: 5px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .investors-pin-lock .pin-dot,
            .investors-pin-lock .pin-key { transition: none; }
        }
    </style>

    @php($pinSetupRequired = ! auth()->user()->pin_hash)
    @php($pinLength = 4)
    <header class="pin-hero">
        <div class="pin-topbar">
            <img class="pin-wordmark" src="{{ asset('cxcx.png') }}" alt="LuLu Philippines">
            <span class="pin-brand-divider" aria-hidden="true"></span>
            <span class="pin-country">PHILIPPINES</span>
        </div>
        <p class="pin-hero-copy">Your everyday, <span>elevated.</span></p>
    </header>

    <div class="pin-panel">
        <div class="pin-card">
            <div class="pin-intro">
                <div class="pin-lock-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="11" rx="3"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"></path></svg>
                </div>
                <img class="pin-brand" src="{{ asset('cxcx.png') }}" alt="LuLu Philippines">
                <p class="pin-eyebrow">Secure investor access</p>
                <h1 id="investors-pin-title">{{ $pinSetupRequired ? 'Create your PIN' : 'Welcome back.' }}</h1>
                <p class="pin-description">
                    {{ $pinSetupRequired ? 'Set a 4-digit PIN to protect your account.' : 'Enter your 4-digit PIN to continue.' }}
                </p>
            </div>

            <div class="pin-region">
                <div class="pin-head">
                    <span class="pin-label" id="investors-pin-label">{{ $pinSetupRequired ? 'YOUR PIN' : 'ENTER PIN' }}</span>
                    <button class="pin-reveal" type="button" id="investors-pin-reveal" aria-pressed="false">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <span>Show</span>
                    </button>
                </div>
                <div class="pin-dots" id="investors-pin-dots" aria-hidden="true">
                    @for ($i = 0; $i < $pinLength; $i++)
                        <span class="pin-dot"></span>
                    @endfor
                </div>
                <span class="pin-progress" id="investors-pin-progress" role="status" aria-live="polite">0 of {{ $pinLength }} digits entered.</span>
                <p class="pin-status @error('pin') error @enderror" id="investors-pin-status" role="status" aria-live="polite">
                    @error('pin'){{ $message }}@else Use the keypad below.@enderror
                </p>
            </div>
        </div>

        <div class="pin-keypad" aria-label="PIN keypad">
            @foreach (['1', '2', '3', '4', '5', '6', '7', '8', '9'] as $digit)
                <button class="pin-key" type="button" data-pin-digit="{{ $digit }}" aria-label="Digit {{ $digit }}">{{ $digit }}</button>
            @endforeach
            <button class="pin-key utility" type="button" data-pin-clear>Clear</button>
            <button class="pin-key" type="button" data-pin-digit="0" aria-label="Digit 0">0</button>
            <button class="pin-key utility" type="button" data-pin-backspace aria-label="Delete last digit">Delete</button>
        </div>

        <input type="hidden" name="pin" id="investors-pin-value">
        <input type="hidden" name="pin_confirmation" id="investors-pin-confirmation">
        <button class="pin-submit" type="submit" disabled id="investors-pin-submit">
            <span>{{ $pinSetupRequired ? 'Set PIN' : 'Unlock' }}</span>
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m-5-5 5 5-5 5"></path></svg>
        </button>
        @unless ($pinSetupRequired)
            <a class="pin-help-link" href="{{ route('pin.request') }}">Forgot your PIN? Reset it by email.</a>
        @endunless
        <button class="pin-signout investors-pin-signout" type="submit" formaction="{{ route('logout') }}" formmethod="post">
            Sign out
        </button>

        <footer class="pin-footer">
            <div class="pin-privacy">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6l8-3Z"></path><path d="m8 12 3 3 5-6"></path></svg>
                <span>Your PIN stays hidden on this screen.</span>
            </div>
        </footer>
    </div>

    <script>
        (() => {
            const form = document.querySelector('.investors-pin-lock-form[data-pin-lock="true"]');
            const setup = @json($pinSetupRequired);
            const pinLength = @json($pinLength);
            const pinInput = document.getElementById('investors-pin-value');
            const confirmationInput = document.getElementById('investors-pin-confirmation');
            const slots = [...document.querySelectorAll('.investors-pin-lock .pin-dot')];
            const status = document.getElementById('investors-pin-status');
            const progress = document.getElementById('investors-pin-progress');
            const submit = document.getElementById('investors-pin-submit');
            const reveal = document.getElementById('investors-pin-reveal');
            const revealLabel = reveal?.querySelector('span');
            const pinLabel = document.getElementById('investors-pin-label');
            const keypad = document.querySelector('.investors-pin-lock .pin-keypad');
            if (!form || !pinInput || !confirmationInput || !status || !progress || !submit || !keypad) return;
            const pinAction = form.action;
            setTimeout(() => { form.action = pinAction; }, 0);

            let entered = '';
            let firstPin = '';
            let confirming = false;
            let submitting = false;
            let revealed = false;

            const render = () => {
                slots.forEach((slot, index) => {
                    slot.replaceChildren();
                    const filled = index < entered.length;
                    slot.classList.toggle('filled', filled);
                    slot.classList.toggle('active', index === entered.length && !submitting);
                    slot.classList.toggle('revealed', filled && revealed);
                    if (!filled) return;
                    if (revealed) {
                        slot.textContent = entered[index];
                    } else {
                        const mask = document.createElement('span');
                        mask.className = 'pin-mask';
                        slot.appendChild(mask);
                    }
                });

                progress.textContent = `${entered.length} of ${pinLength} digits entered.`;
                submit.disabled = submitting || entered.length !== pinLength;
                keypad.querySelectorAll('[data-pin-digit]').forEach(button => {
                    button.disabled = submitting || entered.length >= pinLength;
                });
                keypad.querySelector('[data-pin-clear]').disabled = submitting || !entered.length;
                keypad.querySelector('[data-pin-backspace]').disabled = submitting || !entered.length;
                if (reveal) reveal.disabled = submitting || !entered.length;
            };
            const clear = () => {
                entered = '';
                pinInput.value = '';
                confirmationInput.value = '';
                render();
            };
            const submitPin = () => {
                if (submit.disabled || submitting) return;
                if (setup && !confirming) {
                    firstPin = entered;
                    entered = '';
                    confirming = true;
                    if (pinLabel) pinLabel.textContent = 'CONFIRM YOUR PIN';
                    submit.querySelector('span').textContent = 'Confirm PIN';
                    status.textContent = 'Enter your PIN one more time.';
                    render();
                    return;
                }
                if (setup && firstPin !== entered) {
                    firstPin = '';
                    confirming = false;
                    if (pinLabel) pinLabel.textContent = 'YOUR PIN';
                    submit.querySelector('span').textContent = 'Set PIN';
                    clear();
                    status.classList.add('error');
                    status.textContent = 'Those PINs did not match. Please start again.';
                    return;
                }
                pinInput.value = setup ? firstPin : entered;
                confirmationInput.value = setup ? entered : '';
                submitting = true;
                submit.querySelector('span').textContent = setup ? 'Saving…' : 'Checking…';
                form.requestSubmit();
            };
            const addDigit = digit => {
                if (submitting || entered.length >= pinLength) return;
                entered += digit;
                status.classList.remove('error');
                status.textContent = entered.length === pinLength
                    ? 'Ready when you are. Tap to continue.'
                    : 'Use the keypad below.';
                render();
                if (entered.length === pinLength) submitPin();
            };
            const eraseDigit = () => {
                if (submitting) return;
                entered = entered.slice(0, -1);
                status.classList.remove('error');
                status.textContent = entered.length ? `${entered.length} digits entered.` : 'Use the keypad below.';
                render();
            };

            keypad.querySelectorAll('[data-pin-digit]').forEach(button => {
                button.addEventListener('click', () => addDigit(button.dataset.pinDigit));
            });
            keypad.querySelector('[data-pin-clear]').addEventListener('click', () => {
                if (submitting) return;
                if (confirming) {
                    firstPin = '';
                    confirming = false;
                    if (pinLabel) pinLabel.textContent = 'YOUR PIN';
                    submit.querySelector('span').textContent = 'Set PIN';
                }
                clear();
                status.classList.remove('error');
                status.textContent = 'Use the keypad below.';
            });
            keypad.querySelector('[data-pin-backspace]').addEventListener('click', eraseDigit);
            reveal?.addEventListener('click', () => {
                revealed = !revealed;
                reveal.setAttribute('aria-pressed', String(revealed));
                revealLabel.textContent = revealed ? 'Hide' : 'Show';
                document.querySelector('.investors-pin-lock .pin-privacy span').textContent = revealed
                    ? 'Your PIN is visible. Tap Hide to mask it.'
                    : 'Your PIN stays hidden on this screen.';
                render();
            });
            submit.addEventListener('click', event => {
                event.preventDefault();
                submitPin();
            });
            form.addEventListener('submit', event => {
                if (event.submitter?.classList.contains('investors-pin-signout') || submitting) {
                    event.stopImmediatePropagation();
                    return;
                }
                event.preventDefault();
                submitPin();
            });
            document.addEventListener('keydown', event => {
                if (event.ctrlKey || event.metaKey || event.altKey || submitting) return;
                if (/^\d$/.test(event.key)) {
                    event.preventDefault();
                    addDigit(event.key);
                } else if (event.key === 'Backspace') {
                    event.preventDefault();
                    eraseDigit();
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    submitPin();
                }
            });
            render();
        })();
    </script>
</section>
