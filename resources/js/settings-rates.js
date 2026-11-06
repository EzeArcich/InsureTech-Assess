const form = document.querySelector('#company-rate-form');
if (form) {
    const rates = JSON.parse(form.dataset.rates);
    const fields = JSON.parse(form.dataset.fields);
    const scope = document.querySelector('#rate-scope');
    const company = document.querySelector('#rate-company');
    const toggle = () => {
        const all = scope.value === 'all';
        document.querySelector('#rate-company-container').hidden = all;
        document.querySelector('#rate-all-warning').hidden = !all;
        company.disabled = all; company.required = !all;
    };
    const fill = () => fields.forEach(field => { form.elements[field].value = rates[company.value]?.[field] ?? ''; });
    scope.addEventListener('change', toggle);
    company.addEventListener('change', fill);
    form.addEventListener('submit', event => {
        if (scope.value === 'all' && !confirm('¿Aplicar estos valores a todas las compañías actuales?')) event.preventDefault();
    });
    toggle();
}
