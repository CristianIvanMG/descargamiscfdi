(() => {
    const PERSONA_FISICA = /^[A-Z&Ñ]{4}\d{6}[A-Z0-9]{3}$/u;
    const PERSONA_MORAL = /^[A-Z&Ñ]{3}\d{6}[A-Z0-9]{3}$/u;

    const isValidDatePart = (datePart) => {
        const year = Number(datePart.slice(0, 2));
        const month = Number(datePart.slice(2, 4));
        const day = Number(datePart.slice(4, 6));
        const fullYear = year <= 30 ? 2000 + year : 1900 + year;
        const date = new Date(Date.UTC(fullYear, month - 1, day));

        return date.getUTCFullYear() === fullYear
            && date.getUTCMonth() === month - 1
            && date.getUTCDate() === day;
    };

    const getDatePart = (value) => value.length === 13 ? value.slice(4, 10) : value.slice(3, 9);

    const isValidRfc = (value) => {
        const rfc = value.toUpperCase();

        if (!PERSONA_FISICA.test(rfc) && !PERSONA_MORAL.test(rfc)) {
            return false;
        }

        return isValidDatePart(getDatePart(rfc));
    };

    const normalizeRfc = (value) => value
        .toUpperCase()
        .replace(/[^A-Z0-9&Ñ]/gu, '')
        .slice(0, 13);

    const attachRfcMask = (input) => {
        const help = document.querySelector(`[data-rfc-feedback="${input.id}"]`);

        input.setAttribute('maxlength', '13');
        input.setAttribute('minlength', '12');
        input.setAttribute('pattern', '^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$');
        input.setAttribute('autocomplete', 'off');
        input.setAttribute('autocapitalize', 'characters');
        input.classList.add('rfc-input');

        const validate = () => {
            input.value = normalizeRfc(input.value);

            if (input.value.length === 0 && !input.required) {
                input.setCustomValidity('');
                input.classList.remove('is-valid', 'is-invalid');
                if (help) help.textContent = 'Opcional al inicio. Formato: 12 o 13 caracteres.';
                return;
            }

            if (isValidRfc(input.value)) {
                input.setCustomValidity('');
                input.classList.add('is-valid');
                input.classList.remove('is-invalid');
                if (help) help.textContent = 'RFC con formato válido.';
                return;
            }

            input.setCustomValidity('Ingresa un RFC válido: 12 o 13 caracteres, fecha real y homoclave.');
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            if (help) help.textContent = 'Revisa el RFC: debe incluir fecha real y homoclave.';
        };

        input.addEventListener('input', validate);
        input.addEventListener('blur', validate);
        validate();
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-rfc-mask]').forEach(attachRfcMask);
    });
})();
