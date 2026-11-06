import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.css';
import { Modal } from 'bootstrap';
select2(window, $);
const root = document.querySelector('#repair-estimate');
if (root) {
    const config = JSON.parse(root.dataset.config);
    const money = cents => new Intl.NumberFormat('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(cents / 100);
    const scaled = value => Math.round(Number(value || 0) * 100);
    const calculate = () => {
        let total = 0;
        root.querySelectorAll('[data-labor]').forEach(row => {
            const kind = row.dataset.labor;
            const unit = row.querySelector('[data-unit]')?.value || 'panel';
            const saved = config.saved[`${kind}_rate`];
            const rate = saved !== null && (kind === 'paint' || unit === config.saved[`${kind}_unit`]) ? saved : config.rates[`${kind}_${unit}`];
            const subtotal = Math.round(scaled(row.querySelector('[data-quantity]').value) * scaled(rate) / 100);
            row.querySelector('[data-rate]').textContent = rate == null ? 'Sin configurar' : money(scaled(rate));
            row.querySelector('[data-subtotal]').textContent = money(subtotal);
            total += subtotal;
        });
        document.querySelector('#repair-total').textContent = money(total);
    };
    root.addEventListener('input', calculate);
    root.addEventListener('change', calculate);
    calculate();
    $('#inspection-parts').select2({ width: '100%', tags: true, placeholder: 'Buscar o escribir un repuesto', maximumSelectionLength: 100,
        ajax: { url: config.partsUrl, dataType: 'json', delay: 300, data: params => ({ q: params.term || '' }) },
        createTag: params => { const text = params.term.trim(); return text && text.length <= 120 ? { id: text, text } : null; }
    });
    const modalElement = document.querySelector('#workshop-modal');
    const form = document.querySelector('#inspection-workshop-form');
    const errors = document.querySelector('#workshop-errors');
    const selectWorkshop = workshop => {
        const select = document.querySelector('#inspection-workshop');
        let option = [...select.options].find(option => String(option.value) === String(workshop.id));
        if (!option) { option = new Option(`${workshop.name} · ${workshop.address}`, workshop.id); select.add(option); }
        select.value = workshop.id;
        Modal.getInstance(modalElement)?.hide();
        document.querySelector('#workshop-results').replaceChildren();
        errors.textContent = '';
    };
    const wireSearch = (inputs, output, parameters) => {
        let timer, controller;
        inputs.forEach(input => input.addEventListener('input', () => {
            clearTimeout(timer); controller?.abort(); output.replaceChildren();
            timer = setTimeout(async () => {
                const params = parameters();
                if (![...params.values()].some(value => value.trim().length >= 2)) return;
                controller = new AbortController();
                try {
                    const response = await fetch(`${config.workshopsUrl}?${params}`, { headers: { Accept: 'application/json' }, signal: controller.signal });
                    if (!response.ok) throw new Error('No se pudo buscar talleres.');
                    const data = await response.json();
                    output.replaceChildren();
                    data.results.forEach(workshop => {
                        const button = document.createElement('button'); button.type = 'button'; button.className = 'list-group-item list-group-item-action';
                        button.textContent = `${workshop.name} · ${workshop.address} · ${workshop.phone || 'Sin teléfono'} · ${workshop.opening_hours || 'Sin horarios'} · ${workshop.is_approved ? 'Homologado' : 'No homologado'}`;
                        button.addEventListener('click', () => selectWorkshop(workshop)); output.append(button);
                    });
                } catch (error) { if (error.name !== 'AbortError') output.textContent = error.message; }
            }, 300);
        }));
    };
    const search = document.querySelector('#workshop-search');
    wireSearch([search], document.querySelector('#workshop-results'), () => new URLSearchParams({ q: search.value }));
    wireSearch([form.elements.name, form.elements.address], document.querySelector('#workshop-duplicates'), () => new URLSearchParams({ name: form.elements.name.value, address: form.elements.address.value }));
    form.addEventListener('submit', async event => {
        event.preventDefault(); const submit = form.querySelector('[type=submit]'); submit.disabled = true; errors.textContent = '';
        try {
            const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' } });
            const data = await response.json();
            if (!response.ok) { errors.textContent = Object.values(data.errors || {}).flat().join(' ') || 'No se pudo guardar el taller.'; return; }
            selectWorkshop(data.workshop); form.reset(); document.querySelector('#workshop-duplicates').replaceChildren();
        } catch { errors.textContent = 'No se pudo guardar el taller. Revisá la conexión.'; }
        finally { submit.disabled = false; }
    });
}
