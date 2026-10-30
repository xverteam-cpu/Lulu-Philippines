<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#087e50">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Lulu">
    <title>Investor Relations | LuLu Philippines</title>
    <meta name="description" content="Investor Relations sign in for LuLu Philippines.">
    <link rel="icon" type="image/png" href="{{ asset('icons/lulu-192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('icons/lulu-512.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('icons/lulu-512.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('lulu-public.css') }}?v={{ filemtime(public_path('lulu-public.css')) }}">
    @env('production')<script src="{{ asset('public-protection.js') }}" defer></script>@endenv
</head>
<body class="investors-page @auth investors-pin-screen @endauth">
<main>
    <section class="investors-banner">
        <div class="investors-container">
            <div class="investors-auth-header">
                <img class="investors-auth-logo" src="{{ asset('cxcx.png') }}" alt="LuLu Philippines">
                <span class="investors-auth-badge">Account login</span>
            </div>
            <h1>Investor Relations</h1>
            <p>Welcome to Lulu Retail Investor Relations website.</p>
        </div>
        @guest
        <section class="investors-login-card" id="investor-login" aria-label="Sign in to LuLu">
            <div class="investors-login-heading">
                <span>Account login</span>
                <h2>Your everyday.<br>A little easier.</h2>
                <p>Sign in to your LuLu account.</p>
            </div>
            <form class="investors-login-form" method="post" action="{{ route('login.submit') }}" autocomplete="on" data-login-action="{{ route('login.submit') }}" data-register-action="{{ route('register.partner') }}">
                @csrf
                <div class="investor-identity-step">
                    <a class="investors-google-button" href="{{ route('login.google') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path><path d="M5.84 14.09a6.6 6.6 0 0 1 0-4.18V7.07H2.18A11 11 0 0 0 1 12c0 1.78.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path></svg>
                        <span>Continue with Google</span>
                    </a>
                    <div class="investors-login-divider"><span>or</span></div>
                </div>
                <label class="sr-only" for="investor-name">Full name</label>
                <input id="investor-name" name="fullname" type="text" autocomplete="name" placeholder="Full name" hidden>
                <label class="sr-only" for="investor-email">Email or username</label>
                <input id="investor-email" name="email" type="text" autocomplete="username" placeholder="Email or username" value="{{ old('email', request('email', '')) }}" aria-describedby="investor-login-error" required>
                <label class="sr-only" for="investor-referral">Referral code</label>
                <input id="investor-referral" name="referral" type="text" autocomplete="off" placeholder="Referral code" value="{{ request('ref', old('referral', '')) }}" hidden>
                <label class="sr-only" for="investor-password">Password</label>
                <input id="investor-password" class="investors-password-morph" name="password" type="password" autocomplete="current-password" placeholder="Password" aria-describedby="investor-login-error" hidden>
                <input name="password_confirmation" id="investor-password-confirmation" type="hidden">
                <button class="investors-continue-button" type="submit">Continue</button>
            </form>
            <a class="investors-mode-button" href="{{ route('password.request') }}">Forgot password?</a>
            @if ($errors->any())
                <p class="investors-login-error" id="investor-login-error" role="alert">{{ $errors->first() }}</p>
            @endif
            <button type="button" class="investors-mode-button" id="investor-mode">Create an account</button>
            <p class="investors-login-terms">By continuing, you agree to our <a href="{{ route('terms-of-service') }}">Terms of Service</a>, <a href="{{ route('privacy-policy') }}">Privacy Policy</a> and Cookie Use.</p>
        </section>
        @else
        <section class="investors-login-card investors-pin-lock-card" id="investor-login">
            <form class="investors-pin-lock-form" method="post" action="{{ auth()->user()->pin_hash ? route('investor.pin.verify') : route('investor.pin.setup') }}" data-pin-lock="true">
                @csrf
                @include('public.partials.investor-pin-lock')
            </form>
        </section>
        @endguest
    </section>
</main>
@guest
<script>
(function () {
    window.addEventListener('pageshow', function (event) {
        if (!event.persisted) return;

        document.documentElement.style.visibility = 'hidden';
        window.location.reload();
    });

    const card = document.querySelector('.investors-login-card');
    const form = card?.querySelector('form');
    const modeButton = document.querySelector('#investor-mode');
    const nameInput = document.querySelector('#investor-name');
    const emailInput = document.querySelector('#investor-email');
    const referralInput = document.querySelector('#investor-referral');
    const passwordInput = document.querySelector('#investor-password');
    const confirmationInput = document.querySelector('#investor-password-confirmation');
    const continueButton = card?.querySelector('.investors-continue-button');
    const identityStep = card?.querySelector('.investor-identity-step');
    const googleButton = card?.querySelector('.investors-google-button');
    const screen = document.querySelector('.investors-banner');
    if (!card || !form || !modeButton || !nameInput || !emailInput || !referralInput || !passwordInput || !confirmationInput || !continueButton || !identityStep || !screen) return;

    let mode = 'login';
    let step = 'identity';
    function sync() {
        const passwordStep = step === 'password';
        const registering = mode === 'register';
        nameInput.hidden = !registering || passwordStep;
        nameInput.required = registering && !passwordStep;
        emailInput.hidden = passwordStep;
        emailInput.type = registering ? 'email' : 'text';
        emailInput.placeholder = registering ? 'Email address' : 'Email or username';
        referralInput.hidden = !registering || passwordStep;
        referralInput.required = registering && !passwordStep;
        passwordInput.hidden = !passwordStep;
        passwordInput.required = passwordStep;
        passwordInput.autocomplete = registering ? 'new-password' : 'current-password';
        identityStep.hidden = passwordStep;
        card.classList.toggle('investors-password-step', passwordStep);
        card.classList.toggle('register-mode', registering);
        continueButton.textContent = 'Continue';
        form.action = registering ? form.dataset.registerAction : form.dataset.loginAction;
        modeButton.textContent = registering ? 'Already have an account? Sign in' : 'Create an account';
    }

    modeButton.addEventListener('click', function () {
        mode = mode === 'login' ? 'register' : 'login';
        step = 'identity';
        sync();
        (mode === 'register' ? nameInput : emailInput).focus();
    });
    form.addEventListener('submit', function (event) {
        if (step === 'identity') {
            event.preventDefault();
            step = 'password';
            sync();
            passwordInput.focus();
            return;
        }
        if (mode === 'register') confirmationInput.value = passwordInput.value;
    });
    googleButton?.addEventListener('click', function (event) {
        if (mode !== 'register') return;
        const code = referralInput.value.trim();
        if (!code) {
            event.preventDefault();
            referralInput.hidden = false;
            referralInput.required = true;
            referralInput.focus();
            return;
        }
        const url = new URL(googleButton.href, window.location.origin);
        url.searchParams.set('ref', code);
        googleButton.href = url.toString();
    });

    function fitScreen() {
        const height = window.visualViewport?.height || window.innerHeight;
        screen.classList.toggle('keyboard-open', height < 500 && [nameInput, emailInput, referralInput, passwordInput].includes(document.activeElement));
    }
    window.addEventListener('resize', fitScreen);
    window.visualViewport?.addEventListener('resize', fitScreen);
    [nameInput, emailInput, referralInput, passwordInput].forEach(function (input) {
        input.addEventListener('focus', fitScreen);
        input.addEventListener('blur', fitScreen);
    });
    sync();
})();
</script>
@endguest
</body>
</html>
