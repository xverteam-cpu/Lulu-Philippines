<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="theme-color" content="#166534">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" content="Lulu">
  <link rel="icon" type="image/svg+xml" href="{{ asset('LuluLogo.svg') }}" />
  <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('LuluLogo.svg') }}" />
  <link rel="apple-touch-icon" href="{{ asset('LuluLogo.svg') }}" />
  <link rel="manifest" href="{{ asset('manifest.json') }}" />
  <title>Lulu</title>

  <!-- Open Graph Meta Tags for Social Sharing -->
  <meta property="og:title" content="Lulu">
  <meta property="og:description" content="Investment packages with daily returns. Start investing with Lulu.">
  <meta property="og:image" content="{{ url('Lotteria.png') }}">
  <meta property="og:url" content="{{ url('/') }}">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Lulu">
  <meta name="twitter:description" content="Investment packages with daily returns. Start investing with Lulu.">
  <meta name="twitter:image" content="{{ url('Lotteria.png') }}">
  <style>
    @media only screen and (max-width: 600px) {
      .container { width: 100% !important; }
      .content-padding { padding: 18px !important; }
      .nav-button { display: table-cell !important; width: 33.333% !important; margin: 0 !important; padding: 0 4px !important; vertical-align: top !important; }
      .nav-button a { box-sizing: border-box !important; width: 100% !important; min-height: 70px !important; padding: 13px 5px !important; font-size: 11px !important; line-height: 16px !important; border-radius: 28px !important; }
      .mobile-center { text-align: center !important; }
      .card-title { font-size: 22px !important; }
      .card-text { font-size: 16px !important; }
    }
    /* Cards grid and square thumbnails */
    .cards-grid { display:flex; gap:18px; flex-direction:column; }
    .card { display:block; text-decoration:none; color:inherit; }
    .card-table { width:100%; border-radius:36px; overflow:hidden; background-color:#ffffff; box-shadow:0 6px 18px rgba(0,0,0,0.06); transition:transform .12s; }
    .card-thumb { width:100%; aspect-ratio:1/1; background-size:cover; background-position:center; border-radius:8px; display:block; }
    @media only screen and (max-width:600px) {
      .cards-grid { flex-direction:row; }
      .cards-grid .card { flex:1; }
      .card-table { border-radius:20px; }
    }
    .page-loader {
      position:fixed;
      inset:0;
      z-index:9999;
      display:none;
      align-items:center;
      justify-content:center;
      background:#ffffff;
    }
    .page-loader.is-active { display:flex; }
    .page-loader-circle {
      width:58px;
      height:58px;
      border-radius:50%;
      border:6px solid rgba(22,101,52,0.18);
      border-top-color:#166534;
      animation:page-loader-spin .8s linear infinite;
    }
    @keyframes page-loader-spin {
      to { transform:rotate(360deg); }
    }
    body { margin:0; padding:0; background-color:#eef1f4; font-family:'Helvetica Neue',Helvetica,Arial,-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif; }
  </style>
</head>
<body>
  <div class="page-loader" aria-hidden="true">
    <div class="page-loader-circle"></div>
  </div>

  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:0; padding:0; background-color:#eef1f4;">
    <tr>
      <td align="center">

    <table class="container" width="600" border="0" cellspacing="0" cellpadding="0" style="width:600px; max-width:600px; background-color:#ffffff; background-image:url('/MGames%20Festival.png'); background-size:cover; background-position:center top;">

      <tr>
        <td style="padding:18px 22px 10px 22px;">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="left" width="90">
                <img src="/LuluLogo.svg" width="60" alt="Lulu" style="display:block; border:0; width:60px; max-width:60px; height:auto; border-radius:12px;">
              </td>
              <td align="right">
                <table border="0" cellspacing="0" cellpadding="0" align="right">
                  <tr>
                    <td style="padding:0 6px;">
                      <a href="/unavailable" style="display:inline-block; background-color:#ffffff; color:#14532d; text-decoration:none; font-size:13px; font-weight:bold; letter-spacing:0.4px; padding:12px 18px; border-radius:24px; cursor:pointer;">
                        DOWNLOAD APP →
                      </a>
                    </td>
                    <td style="padding:0 6px;">
                      <a href="#" style="display:inline-block; background-color:#ffffff; color:#14532d; text-decoration:none; font-size:13px; font-weight:bold; padding:12px 18px; border-radius:24px;">
                        EN
                      </a>
                    </td>
                    <td style="padding-left:6px;">
                      <a href="#" style="display:inline-block; background-color:#ffffff; color:#14532d; text-decoration:none; font-size:20px; font-weight:bold; padding:8px 16px; border-radius:24px; line-height:20px;">
                        ☰
                      </a>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td class="content-padding" style="padding:12px 22px 22px 22px;">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td align="center" style="padding:0 0 22px 0;">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="center" class="nav-button" style="padding:0 5px;">
                      <a href="/unavailable" style="display:inline-block; width:160px; background-color:#ffffff; color:#14532d; text-decoration:none; font-size:14px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; padding:15px 10px; border-radius:32px; line-height:20px; cursor:pointer;">
                        Find a Store<br>Near You
                      </a>
                    </td>
                    <td align="center" class="nav-button" style="padding:0 5px;">
                      <a href="/unavailable" style="display:inline-block; width:160px; background-color:#d6a84f; color:#0f3d2e; text-decoration:none; font-size:14px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; padding:15px 10px; border-radius:32px; line-height:20px; cursor:pointer;">
                        Career<br>Opportunities
                      </a>
                    </td>
                    <td align="center" class="nav-button" style="padding:0 5px;">
                      <a href="/unavailable" style="display:inline-block; width:160px; background-color:#14532d; color:#ffffff; text-decoration:none; font-size:14px; font-weight:bold; letter-spacing:0.5px; text-transform:uppercase; padding:15px 10px; border-radius:32px; line-height:20px; cursor:pointer;">
                        Product<br>Proposal
                      </a>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>

            <tr>
              <td align="center" style="padding-bottom:28px;">
                <a href="{{ route('order') }}" style="text-decoration:none; color:inherit; display:block;">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#ffffff; border-radius:36px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.06); transition:transform .12s;">
                    <tr>
                      <td align="center" style="padding:22px 24px 28px 24px;">
                        <div class="card-title" style="font-size:24px; line-height:30px; font-weight:bold; color:#166534; letter-spacing:1px; text-transform:uppercase;">
                          BE A PARTNER NOW
                        </div>
                        <div style="padding:12px 0;">
                          <div style="width:100%; height:160px; border-radius:8px; background-image:url('https://www.lotteria.vn/grs-static/images/img-franchise.png'); background-size:cover; background-position:center; box-shadow: inset 0 0 0 9999px rgba(255,255,255,0.02);"></div>
                        </div>
                        <div class="card-text" style="font-size:17px; line-height:24px; color:#166534; padding-top:6px;">
                          Start your partnership journey
                        </div>
                      </td>
                    </tr>
                  </table>
                </a>
              </td>
            </tr>

            <tr>
              <td align="center" style="padding-bottom:34px;">
                <a href="{{ route('franchising') }}" style="text-decoration:none; color:inherit; display:block;">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:#ffffff; border-radius:36px; overflow:hidden; box-shadow:0 6px 18px rgba(0,0,0,0.06); transition:transform .12s;">
                    <tr>
                      <td align="center" style="padding:22px 24px 30px 24px;">
                        <div class="card-title" style="font-size:24px; line-height:30px; font-weight:bold; color:#166534; letter-spacing:1px; text-transform:uppercase;">
                          FRANCHISING
                        </div>
                        <div style="padding:12px 0;">
                          <div style="width:100%; height:160px; border-radius:8px; background-image:url('/MGames%20Festival.png'); background-size:cover; background-position:center; box-shadow: inset 0 0 0 9999px rgba(255,255,255,0.02);"></div>
                        </div>
                        <div class="card-text" style="font-size:17px; line-height:24px; color:#166534; padding-top:6px;">
                          Lulu store franchise
                        </div>
                      </td>
                    </tr>
                  </table>
                </a>
              </td>
            </tr>

          </table>
        </td>
      </tr>

      <tr>
        <td align="center" style="padding:0 24px 28px 24px;">
          <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color:rgba(255,255,255,0.95); border-radius:22px;">
            <tr>
              <td align="center" style="padding:24px 20px;">
                <div style="font-size:18px; line-height:24px; color:#14532d; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px;">
                  Lulu
                </div>
                <div style="font-size:12px; line-height:18px; color:#888888; padding-top:12px;">
                  © 2026 Lulu. All rights reserved.
                </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>

    </table>

  </td>
</tr>
  </table>

  <script>
    (function () {
      var loader = document.querySelector('.page-loader');
      if (!loader) return;

      document.addEventListener('click', function (event) {
        var link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if (link.target && link.target !== '_self') return;

        var href = link.getAttribute('href');
        if (!href || href === '#' || href.charAt(0) === '#') return;

        var nextUrl = new URL(href, window.location.href);
        if (nextUrl.origin !== window.location.origin || nextUrl.href === window.location.href) return;

        event.preventDefault();
        loader.classList.add('is-active');
        setTimeout(function () {
          window.location.href = nextUrl.href;
        }, 120);
      });
    })();
  </script>
</body>
</html>
