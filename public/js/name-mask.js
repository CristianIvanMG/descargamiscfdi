(() => {
    const cleanName = (value) => value
        .toUpperCase()
        .replace(/[^\p{L}\s]/gu, '')
        .replace(/\s{2,}/g, ' ')
        .slice(0, 160);

    const attachNameMask = (input) => {
        const validate = () => {
            input.value = cleanName(input.value);
        };

        input.addEventListener('input', validate);
        input.addEventListener('blur', () => {
            validate();
            input.value = input.value.trim();
        });
        validate();
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-name-mask]').forEach(attachNameMask);
    });
})();
