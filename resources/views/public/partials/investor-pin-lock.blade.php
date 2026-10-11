<section class="investors-pin-lock" aria-labelledby="investors-pin-title">
    <style>
        .investors-login-card.investors-pin-lock-card {
            height: 500px !important;
            min-height: 500px !important;
            padding-top: 26px;
            padding-bottom: 24px;
        }
        .investors-pin-lock {
            width: 100%;
            max-width: 350px;
            margin: 0 auto;
            color: #183329;
            text-align: center;
            font-family: Satoshi, sans-serif;
        }
        .investors-pin-lock .pin-brand {
            width: 46px;
            height: 46px;
            margin: 0 auto 9px;
            object-fit: contain;
        }
        .investors-pin-lock h2 {
            margin: 0;
            color: #183329;
            font-size: 20px;
            font-weight: 750;
        }
        .investors-pin-lock .pin-description {
            margin: 5px 0 14px;
            color: #69776f;
            font-size: 13px;
        }
        .investors-pin-lock .pin-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 0 auto 8px;
        }
        .investors-pin-lock .pin-dot {
            width: 12px;
            height: 12px;
            border: 1.5px solid #9ba9a0;
            border-radius: 50%;
            background: transparent;
        }
        .investors-pin-lock .pin-dot.filled {
            border-color: #16804a;
            background: #16804a;
        }
        .investors-pin-lock .pin-status {
            min-height: 18px;
            margin: 0 0 9px;
            color: #718078;
            font-size: 12px;
        }
        .investors-pin-lock .pin-status.error {
            color: #b42318;
        }
        .investors-pin-lock .pin-keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 7px;
            max-width: 244px;
            margin: 0 auto 12px;
        }
        .investors-pin-lock .pin-key {
            min-height: 39px;
            border: 1px solid #e4ebe6;
            border-radius: 10px;
            background: #f8faf8;
            color: #183329;
            font: 650 16px Satoshi, sans-serif;
            cursor: pointer;
        }
        .investors-pin-lock .pin-key:hover {
            border-color: #16804a;
            background: #eff7f1;
        }
        .investors-pin-lock .pin-key.utility {
            color: #5f6f65;
            font-size: 12px;
        }
        .investors-pin-lock .pin-submit {
            width: 100%;
            min-height: 44px;
            border: 0;
            border-radius: 999px;
            background: #16804a;
            color: #fff;
            font: 700 14px Satoshi, sans-serif;
            cursor: pointer;
        }
        .investors-pin-lock .pin-submit:disabled {
            background: #aab7ae;
            cursor: not-allowed;
        }
        .investors-pin-lock .pin-signout {
            margin: 10px 0 0;
            border: 0;
            background: none;
            color: #68776e;
            font: 600 12px Satoshi, sans-serif;
            text-decoration: underline;
            cursor: pointer;
        }
        @media (max-height: 720px) {
            .investors-login-card.investors-pin-lock-card {
                height: auto !important;
                min-height: 0 !important;
                padding-top: 18px;
                padding-bottom: 18px;
            }
            .investors-pin-lock .pin-brand { width: 38px; height: 38px; }
            .investors-pin-lock .pin-description { margin-bottom: 8px; }
            .investors-pin-lock .pin-keypad { gap: 5px; margin-bottom: 8px; }
            .investors-pin-lock .pin-key { min-height: 34px; }
        }
    </style>

    @php($pinSetupRequired = ! auth()->user()->pin_hash)
    <img class="pin-brand" src="{{ asset('icons/lulu-512.png') }}" alt="LuLu Philippines">
    <h2 id="investors-pin-title">{{ $pinSetupRequired ? 'Create your PIN' : 'Welcome back' }}</h2>
    <p class="pin-description">
        {{ $pinSetupRequired ? 'Set a 4-digit PIN to protect your account.' : 'Enter your 4–6 digit PIN to continue.' }}
    </p>

    <div class="pin-dots" aria-hidden="true">
        @for ($i = 0; $i < 6; $i++)
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
    <button class="pin-signout investors-pin-signout" type="submit" formaction="{{ route('logout') }}" formmethod="post">
        Forgot your PIN? Sign out
    </button>

    <script>
        (() => {
            const form = document.querySelector('.investors-login-form[data-pin-lock="true"]');
            const setup = @json($pinSetupRequired);
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
                submit.disabled = submitting || (setup ? entered.length !== 4 : entered.length < 4);
            };
            const clear = () => {
                entered = '';
                pinInput.value = '';
                confirmationInput.value = '';
                render();
            };
            const addDigit = digit => {
                const max = setup ? 4 : 6;
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
