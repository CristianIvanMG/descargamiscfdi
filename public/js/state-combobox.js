(() => {
    const normalize = (value) => value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toUpperCase();

    const attachStateCombobox = (select) => {
        const options = Array.from(select.options)
            .filter((option) => option.value !== '')
            .map((option) => option.textContent.trim());

        const wrapper = document.createElement('div');
        wrapper.className = 'state-combobox';

        const input = document.createElement('input');
        input.type = 'text';
        input.autocomplete = 'off';
        input.required = select.required;
        input.className = 'state-combobox-input';
        input.placeholder = 'Escribe para filtrar estado';
        input.value = select.value;
        input.setAttribute('aria-haspopup', 'listbox');
        input.setAttribute('aria-expanded', 'false');

        const list = document.createElement('div');
        list.className = 'state-combobox-list';
        list.setAttribute('role', 'listbox');

        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);
        wrapper.appendChild(input);
        wrapper.appendChild(list);
        select.classList.add('state-native-select');

        const setValue = (value) => {
            input.value = value;
            select.value = value;
            input.setCustomValidity('');
            closeList();
        };

        const closeList = () => {
            list.innerHTML = '';
            input.setAttribute('aria-expanded', 'false');
        };

        const render = () => {
            const query = normalize(input.value);
            const matches = options.filter((option) => normalize(option).includes(query));

            list.innerHTML = '';
            matches.forEach((option) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = option;
                button.setAttribute('role', 'option');
                button.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    setValue(option);
                });
                list.appendChild(button);
            });

            input.setAttribute('aria-expanded', matches.length > 0 ? 'true' : 'false');
        };

        const validate = () => {
            const exact = options.find((option) => normalize(option) === normalize(input.value));
            if (exact) {
                select.value = exact;
                input.value = exact;
                input.setCustomValidity('');
                return;
            }

            select.value = '';
            input.setCustomValidity('Selecciona un estado válido de la lista.');
        };

        input.addEventListener('focus', render);
        input.addEventListener('input', () => {
            select.value = '';
            input.setCustomValidity('');
            render();
        });
        input.addEventListener('blur', () => {
            validate();
            window.setTimeout(closeList, 120);
        });
        input.form?.addEventListener('submit', validate);

        if (select.value) {
            input.value = select.value;
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-state-combobox]').forEach(attachStateCombobox);
    });
})();
