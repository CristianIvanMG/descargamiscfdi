(() => {
    const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    const INVALID_CHARS = /[\s<>()\[\]\\,;:"']+/g;

    const normalizeEmail = (value) => value
        .replace(INVALID_CHARS, '')
        .toLowerCase()
        .slice(0, 160);

    const attachEmailValidation = (input) => {
        const mode = input.dataset.emailValidation;
        const feedback = document.querySelector(`[data-email-feedback="${input.id}"]`);

        const validate = (showInvalid) => {
            input.value = normalizeEmail(input.value);

            if (input.value.length === 0) {
                input.classList.remove('is-valid', 'is-invalid');
                input.setCustomValidity(input.required && mode === 'strict' ? 'Ingresa un correo válido.' : '');
                if (feedback) feedback.textContent = '';
                return;
            }

            if (EMAIL_PATTERN.test(input.value)) {
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
                input.setCustomValidity('');
                if (feedback) feedback.textContent = '';
                return;
            }

            if (showInvalid) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                if (feedback) feedback.textContent = 'Ingresa un correo válido.';
            } else {
                input.classList.remove('is-valid', 'is-invalid');
                if (feedback) feedback.textContent = '';
            }

            input.setCustomValidity(mode === 'strict' ? 'Ingresa un correo válido.' : '');
        };

        input.addEventListener('input', () => validate(false));
        input.addEventListener('blur', () => validate(true));
        input.form?.addEventListener('submit', () => validate(true));
        validate(false);
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-email-validation]').forEach(attachEmailValidation);
    });
})();
