<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#166534">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="default">
  <meta name="apple-mobile-web-app-title" content="Lulu">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="dns-prefetch" href="https://fonts.googleapis.com">
  <link rel="dns-prefetch" href="https://fonts.gstatic.com">
  <link rel="icon" type="image/svg+xml" href="{{ asset('LuluLogo.svg') }}">
  <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('LuluLogo.svg') }}">
  <link rel="apple-touch-icon" href="{{ asset('LuluLogo.svg') }}">
  <link rel="manifest" href="{{ asset('manifest.json') }}">
  <title>Lulu</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Open Graph Meta Tags for Social Sharing -->
  <meta property="og:title" content="Lulu">
  <meta property="og:description" content="Investment packages with daily returns. Start investing with Lulu.">
  <meta property="og:image" content="{{ asset('Lotteria.png') }}">
  <meta property="og:image:secure_url" content="{{ asset('Lotteria.png') }}">
  <meta property="og:image:type" content="image/png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:url" content="{{ url('/') }}">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Lulu">
  <meta name="twitter:description" content="Investment packages with daily returns. Start investing with Lulu.">
  <meta name="twitter:image" content="{{ asset('Lotteria.png') }}">
  <meta name="twitter:image:alt" content="Lulu referral invite image">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body { background:#eef1f4; font-family:Inter, 'Helvetica Neue', Helvetica, Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif; margin:0; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    .container { max-width:1100px; margin:0 auto; padding:18px; }
    a { color:#14532d; }
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
  </style>
</head>
<body>
  <div class="page-loader" aria-hidden="true">
    <div class="page-loader-circle"></div>
  </div>
  <div class="container">
    @yield('content')
  </div>
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
