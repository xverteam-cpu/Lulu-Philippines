<section class="investors-pin-lock" aria-labelledby="investors-pin-title">
    <style>
        .investors-login-card.investors-pin-lock-card {
            box-sizing: border-box;
            width: min(390px, calc(100vw - 32px));
            height: auto !important;
            min-height: 0 !important;
            padding: 26px 30px 20px;
            border: 1px solid #e2e9e4;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 20px 55px rgba(0, 31, 24, .24), 0 2px 8px rgba(0, 31, 24, .08);
            color: #193b2c;
            backdrop-filter: none;
        }
        .investors-pin-lock {
            box-sizing: border-box;
            width: 100%;
            max-width: 330px;
            margin: 0 auto;
            color: #193b2c;
            text-align: center;
            font-family: Satoshi, Inter, Arial, sans-serif;
        }
        .investors-pin-lock *,
        .investors-pin-lock *::before,
        .investors-pin-lock *::after {
            box-sizing: border-box;
        }
        .investors-pin-lock .pin-brand {
            display: block;
            width: 50px;
            height: 50px;
            margin: 0 auto 12px;
            padding: 5px;
            border: 1px solid #e5eee8;
            border-radius: 15px;
            background: #f4f8f4;
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
        .investors-pin-lock h2 {
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
        .investors-pin-lock .pin-dots {
            display: flex;
            justify-content: center;
            gap: 13px;
            margin: 21px auto 8px;
        }
        .investors-pin-lock .pin-dot {
            width: 14px;
            height: 14px;
            border: 1.5px solid #c4d1c8;
            border-radius: 50%;
            background: transparent;
            transition: background .15s ease, border-color .15s ease, transform .15s ease;
        }
        .investors-pin-lock .pin-dot.filled {
            border-color: #087443;
            background: #087443;
            transform: scale(1.05);
        }
        .investors-pin-lock .pin-status {
            min-height: 32px;
            margin: 0;
            color: #738077;
            font-size: 12px;
            line-height: 1.45;
        }
        .investors-pin-lock .pin-status.error {
            color: #b42318;
        }
        .investors-pin-lock .pin-keypad {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 9px;
            margin: 2px auto 14px;
        }
        .investors-pin-lock .pin-key {
            display: flex;
            width: 100%;
            min-width: 0;
            min-height: 50px;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            border: 1px solid #e6ece7;
            border-radius: 13px;
            background: #f6f8f6;
            color: #193b2c;
            font: 600 20px/1 Satoshi, Inter, Arial, sans-serif;
            text-align: center;
            letter-spacing: 0;
            text-transform: none;
            cursor: pointer;
            transition: background .15s ease, border-color .15s ease, transform .12s ease;
        }
        .investors-pin-lock .pin-key:hover {
            border-color: #a9c9b5;
            background: #edf5ef;
        }
        .investors-pin-lock .pin-key:active {
            transform: scale(.96);
            background: #e5f0e8;
        }
        .investors-pin-lock .pin-key.utility {
            color: #66776d;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .investors-pin-lock .pin-submit {
            display: flex;
            width: 100%;
            min-width: 0;
            min-height: 48px;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 12px 16px;
            border: 0;
            border-radius: 12px;
            background: #087443;
            color: #fff;
            font: 700 13px/1 Satoshi, Inter, Arial, sans-serif;
            letter-spacing: .04em;
            text-transform: none;
            cursor: pointer;
            transition: background .15s ease, transform .12s ease;
        }
        .investors-pin-lock .pin-submit:not(:disabled):hover {
            background: #056238;
            transform: translateY(-1px);
        }
        .investors-pin-lock .pin-submit:disabled {
            background: #bdc9c1;
            cursor: not-allowed;
        }
        .investors-pin-lock .pin-signout {
            display: inline-flex;
            width: auto;
            min-width: 0;
            justify-content: center;
            margin: 12px 0 0;
            padding: 4px 8px;
            border: 0;
            background: none;
            color: #63756a;
            font: 600 11px/1.4 Satoshi, Inter, Arial, sans-serif;
            letter-spacing: 0;
            text-decoration: none;
            text-transform: none;
            cursor: pointer;
        }
        .investors-pin-lock .pin-signout:hover {
            color: #087443;
            text-decoration: underline;
        }
        .investors-pin-lock button:focus-visible {
            outline: 3px solid #95bd3c;
            outline-offset: 3px;
        }
        @media (max-width: 420px) {
            .investors-login-card.investors-pin-lock-card {
                width: calc(100vw - 28px);
                padding: 22px 19px 17px;
            }
            .investors-pin-lock .pin-keypad { gap: 8px; }
            .investors-pin-lock .pin-key { min-height: 46px; }
        }
        @media (max-height: 700px) {
            .investors-login-card.investors-pin-lock-card { padding-top: 18px; padding-bottom: 14px; }
            .investors-pin-lock .pin-brand { width: 42px; height: 42px; margin-bottom: 8px; }
            .investors-pin-lock .pin-dots { margin-top: 14px; }
            .investors-pin-lock .pin-keypad { gap: 6px; margin-bottom: 9px; }
            .investors-pin-lock .pin-key { min-height: 42px; }
            .investors-pin-lock .pin-signout { margin-top: 7px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .investors-pin-lock .pin-dot,
            .investors-pin-lock .pin-key,
            .investors-pin-lock .pin-submit { transition: none; }
        }
    </style>

    @php($pinSetupRequired = ! auth()->user()->pin_hash)
    @php($pinLength = 4)
    <img class="pin-brand" src="{{ asset('icons/lulu-512.png') }}" alt="LuLu Philippines">
    <p class="pin-eyebrow">Secure investor access</p>
    <h2 id="investors-pin-title">{{ $pinSetupRequired ? 'Create your PIN' : 'Welcome back' }}</h2>
    <p class="pin-description">
        {{ $pinSetupRequired ? 'Set a 4-digit PIN to protect your account.' : 'Enter your 4-digit PIN to continue.' }}
    </p>

    <div class="pin-dots" aria-hidden="true">
        @for ($i = 0; $i < $pinLength; $i++)
            <span class="pin-dot"></span>
        @endfor
    </div>
    <p class="pin-status @error('pin') error @enderror" id="investors-pin-status" role="status" aria-live="polite">
        @error('pin'){{ $message }}@else Ready when you are.@enderror
    </p>

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
        {{ $pinSetupRequired ? 'Set PIN' : 'Continue' }}
    </button>
    @unless ($pinSetupRequired)
        <a class="pin-signout investors-pin-signout" href="{{ route('password.request') }}">
            Forgot your PIN? Reset your password to set a new one.
        </a>
    @endunless
    <button class="pin-signout investors-pin-signout" type="submit" formaction="{{ route('logout') }}" formmethod="post">
        Sign out
    </button>

    <script>
        (() => {
            const form = document.querySelector('.investors-login-form[data-pin-lock="true"]');
            const setup = @json($pinSetupRequired);
            const pinLength = @json($pinLength);
            const pinInput = document.getElementById('investors-pin-value');
            const confirmationInput = document.getElementById('investors-pin-confirmation');
            const dots = [...document.querySelectorAll('.investors-pin-lock .pin-dot')];
            const status = document.getElementById('investors-pin-status');
            const submit = document.getElementById('investors-pin-submit');
            if (!form || !pinInput || !confirmationInput || !status || !submit) return;
            const pinAction = form.action;
            setTimeout(() => { form.action = pinAction; }, 0);

            let entered = '';
            let firstPin = '';
            let confirming = false;
            let submitting = false;

            const render = () => {
                dots.forEach((dot, index) => dot.classList.toggle('filled', index < entered.length));
                submit.disabled = submitting || entered.length !== pinLength;
            };
            const clear = () => {
                entered = '';
                pinInput.value = '';
                confirmationInput.value = '';
                render();
            };
            const addDigit = digit => {
                const max = pinLength;
                if (submitting || entered.length >= max) return;
                entered += digit;
                render();
                status.classList.remove('error');
                status.textContent = `${entered.length} digit${entered.length === 1 ? '' : 's'} entered`;
            };
            const submitPin = () => {
                if (submit.disabled || submitting) return;
                if (setup && !confirming) {
                    firstPin = entered;
                    entered = '';
                    confirming = true;
                    submit.textContent = 'Confirm PIN';
                    status.textContent = 'Enter your PIN one more time.';
                    render();
                    return;
                }
                if (setup && firstPin !== entered) {
                    firstPin = '';
                    confirming = false;
                    submit.textContent = 'Set PIN';
                    clear();
                    status.classList.add('error');
                    status.textContent = 'Those PINs did not match. Please start again.';
                    return;
                }
                pinInput.value = setup ? firstPin : entered;
                confirmationInput.value = setup ? entered : '';
                submitting = true;
                submit.textContent = setup ? 'Saving…' : 'Checking…';
                form.requestSubmit();
            };

            document.querySelectorAll('[data-pin-digit]').forEach(button => {
                button.addEventListener('click', () => addDigit(button.dataset.pinDigit));
            });
            document.querySelector('[data-pin-clear]')?.addEventListener('click', () => {
                if (!submitting) {
                    if (confirming) {
                        firstPin = '';
                        confirming = false;
                        submit.textContent = 'Set PIN';
                    }
                    clear();
                    status.classList.remove('error');
                    status.textContent = 'Ready when you are.';
                }
            });
            document.querySelector('[data-pin-backspace]')?.addEventListener('click', () => {
                if (!submitting) {
                    entered = entered.slice(0, -1);
                    render();
                    status.textContent = entered.length ? `${entered.length} digits entered` : 'Ready when you are.';
                }
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
                }
                else if (event.key === 'Backspace') {
                    event.preventDefault();
                    entered = entered.slice(0, -1);
                    render();
                } else if (event.key === 'Enter') {
                    event.preventDefault();
                    submitPin();
                }
            });
            render();
        })();
    </script>
</section>
