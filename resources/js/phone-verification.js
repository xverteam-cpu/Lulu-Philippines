import { getApp, getApps, initializeApp } from '@firebase/app';
import {
    getAuth,
    RecaptchaVerifier,
    signInWithPhoneNumber,
} from '@firebase/auth';

function normalizePhilippinePhone(value) {
    const compact = value.trim().replace(/[^\d+]/g, '');

    if (/^\+63\d{10}$/.test(compact)) return compact;
    if (/^63\d{10}$/.test(compact)) return `+${compact}`;
    if (/^09\d{9}$/.test(compact)) return `+63${compact.slice(1)}`;
    if (/^9\d{9}$/.test(compact)) return `+63${compact}`;

    return null;
}

function errorMessage(error) {
    const messages = {
        'auth/invalid-phone-number': 'Enter a valid phone number in international format.',
        'auth/invalid-verification-code': 'That code is incorrect. Check the SMS and try again.',
        'auth/code-expired': 'That code has expired. Request a new code.',
        'auth/too-many-requests': 'Too many attempts. Please wait before trying again.',
        'auth/captcha-check-failed': 'The security check failed. Please try again.',
        'auth/network-request-failed': 'A network error interrupted verification. Check your connection and try again.',
        'auth/app-not-authorized': 'Phone verification is not authorized for this website. Check your Firebase project settings.',
    };

    return messages[error?.code] || 'Phone verification failed. Please try again.';
}

function initializePhoneVerification(wrapper) {
    const form = wrapper.closest('form');
    const phoneInput = form?.querySelector('[name="phone"]');
    const tokenInput = wrapper.querySelector('[data-phone-token]');
    const sendButton = wrapper.querySelector('[data-send-phone-code]');
    const codeStep = wrapper.querySelector('[data-phone-code-step]');
    const codeInput = wrapper.querySelector('[data-phone-code]');
    const confirmButton = wrapper.querySelector('[data-confirm-phone-code]');
    const recaptchaContainer = wrapper.querySelector('[data-phone-recaptcha]');
    const status = wrapper.querySelector('[data-phone-verification-status]');
    let confirmation = null;
    let verifier = null;
    let verifiedPhone = null;
    let requestedPhone = null;
    let auth = null;

    if (!form || !phoneInput || !tokenInput || !sendButton || !codeStep || !codeInput || !confirmButton || !recaptchaContainer || !status) {
        return;
    }

    function setStatus(message, isError = false) {
        status.textContent = message;
        status.style.color = isError ? '#b42318' : '#4b5563';
    }

    function clearVerification(message) {
        tokenInput.value = '';
        verifiedPhone = null;
        requestedPhone = null;
        confirmation = null;
        codeStep.hidden = true;
        codeInput.value = '';
        if (verifier) {
            verifier.clear();
            verifier = null;
        }
        recaptchaContainer.replaceChildren();
        if (message) setStatus(message);
    }

    phoneInput.addEventListener('input', () => {
        const currentPhone = normalizePhilippinePhone(phoneInput.value);
        if ((verifiedPhone && currentPhone !== verifiedPhone) || (requestedPhone && currentPhone !== requestedPhone)) {
            clearVerification('The number changed. Verify the new number before continuing.');
        }
    });

    sendButton.addEventListener('click', async () => {
        const phone = normalizePhilippinePhone(phoneInput.value);
        if (!phone) {
            setStatus('Enter a valid number such as +63 917 123 4567.', true);
            phoneInput.focus();
            return;
        }

        const config = JSON.parse(wrapper.dataset.firebaseConfig || '{}');
        if (!config.api_key || !config.auth_domain || !config.project_id || !config.app_id) {
            setStatus('SMS verification is not configured yet. Contact support or try again later.', true);
            return;
        }

        phoneInput.value = phone;
        tokenInput.value = '';
        verifiedPhone = null;
        requestedPhone = phone;
        confirmation = null;
        codeStep.hidden = true;
        sendButton.disabled = true;
        sendButton.textContent = 'Sending code...';

        try {
            const firebaseConfig = {
                apiKey: config.api_key,
                authDomain: config.auth_domain,
                projectId: config.project_id,
                appId: config.app_id,
            };
            const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
            auth = getAuth(app);

            if (verifier) verifier.clear();
            recaptchaContainer.replaceChildren();
            verifier = new RecaptchaVerifier(auth, recaptchaContainer, { size: 'normal' });
            await verifier.render();
            confirmation = await signInWithPhoneNumber(auth, phone, verifier);
            codeStep.hidden = false;
            codeInput.focus();
            setStatus('Code sent. Check your phone and enter the 6-digit code.');
        } catch (error) {
            if (verifier) verifier.clear();
            verifier = null;
            recaptchaContainer.replaceChildren();
            setStatus(errorMessage(error), true);
        } finally {
            sendButton.disabled = false;
            sendButton.textContent = 'Resend verification code';
        }
    });

    confirmButton.addEventListener('click', async () => {
        if (!confirmation || !auth) {
            setStatus('Request a verification code first.', true);
            return;
        }

        const code = codeInput.value.trim();
        if (!/^\d{6}$/.test(code)) {
            setStatus('Enter the 6-digit code from the SMS.', true);
            codeInput.focus();
            return;
        }

        confirmButton.disabled = true;
        confirmButton.textContent = 'Verifying...';

        try {
            const result = await confirmation.confirm(code);
            const phone = result.user.phoneNumber;
            if (!phone || phone !== requestedPhone || phone !== phoneInput.value) {
                throw new Error('The verified number does not match the entered number.');
            }

            tokenInput.value = await result.user.getIdToken();
            verifiedPhone = phone;
            codeStep.hidden = true;
            sendButton.textContent = 'Number verified';
            setStatus('Your mobile number is verified. Continue to create or update your account.');
        } catch (error) {
            setStatus(errorMessage(error), true);
        } finally {
            confirmButton.disabled = false;
            confirmButton.textContent = 'Verify code';
        }
    });

    form.addEventListener('submit', (event) => {
        if (!tokenInput.value || !verifiedPhone || phoneInput.value !== verifiedPhone) {
            event.preventDefault();
            setStatus('Verify your mobile number by SMS before continuing.', true);
            sendButton.focus();
        }
    });
}

document.querySelectorAll('[data-phone-verification]').forEach(initializePhoneVerification);
