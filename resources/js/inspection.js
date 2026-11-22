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
    const filter = document.querySelector('#catalog-parts-filter');
    if (filter) {
        const results = document.querySelector('[data-catalog-results]');
        const selected = document.querySelector('[data-catalog-selected]');
        const empty = document.querySelector('[data-selected-empty]');
        const status = document.querySelector('#catalog-search-status');
        const more = document.querySelector('[data-catalog-more]');
        const count = document.querySelector('[data-catalog-count]');
        const badge = document.querySelector('.parts-management__badge');
        const modal = document.querySelector('#parts-modal');
        const saveButton = modal.querySelector('[data-catalog-save]');
        const saveError = modal.querySelector('[data-catalog-save-error]');
        const feedback = document.querySelector('[data-catalog-save-feedback]');
        let savedSelection = selected.innerHTML;
        let isSaving = false;
        let timer, controller, page = 1, query = '';
        const updateCount = () => {
            const total = selected.querySelectorAll('input:checked').length;
            count.textContent = `${total} seleccionados`;
            empty.hidden = total > 0;
        };
        const isSelected = code => [...selected.querySelectorAll('input')].some(input => input.value === code);
        const createItem = part => {
            const label = document.createElement('label'); label.className = 'parts-modal__item';
            const checkbox = document.createElement('input'); checkbox.className = 'form-check-input'; checkbox.type = 'checkbox'; checkbox.value = part.code; checkbox.checked = isSelected(part.code);
            const content = document.createElement('span'); const name = document.createElement('strong'); name.textContent = part.name;
            const detail = document.createElement('small'); detail.textContent = `${part.category}${part.equipment_dependent ? ' · Depende del equipamiento' : ''}`;
            content.append(name, detail); label.append(checkbox, content);
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    if (selected.querySelectorAll('input').length >= 100) { checkbox.checked = false; status.textContent = 'Podés seleccionar hasta 100 repuestos.'; return; }
                    const chosen = label.cloneNode(true); chosen.querySelector('input').checked = true; selected.append(chosen);
                } else {
                    [...selected.querySelectorAll('input')].find(input => input.value === part.code)?.closest('label').remove();
                }
                updateCount();
            });
            return label;
        };
        selected.addEventListener('change', event => {
            if (event.target.matches('input[type=checkbox]') && !event.target.checked) {
                const code = event.target.value; event.target.closest('label').remove();
                [...results.querySelectorAll('input')].filter(input => input.value === code).forEach(input => { input.checked = false; });
                updateCount();
            }
        });
        const search = async (term, nextPage) => {
            controller?.abort(); controller = new AbortController();
            status.textContent = 'Buscando repuestos...'; more.hidden = true;
            try {
                const params = new URLSearchParams({ q: term, page: nextPage });
                const response = await fetch(`${config.catalogPartsUrl}?${params}`, { headers: { Accept: 'application/json' }, signal: controller.signal });
                if (!response.ok) throw new Error('No se pudo buscar. Probá nuevamente.');
                const data = await response.json();
                if (term !== query) return;
                if (nextPage === 1) results.replaceChildren();
                data.results.forEach(part => results.append(createItem(part)));
                page = nextPage;
                status.textContent = results.childElementCount ? 'Elegí los repuestos que necesitás.' : 'No encontramos repuestos para esa búsqueda.';
                more.hidden = !data.has_more;
            } catch (error) { if (error.name !== 'AbortError') status.textContent = error.message; }
        };
        filter.addEventListener('input', () => {
            clearTimeout(timer); controller?.abort(); query = filter.value.trim(); page = 1; results.replaceChildren(); more.hidden = true;
            if ([...query].length < 3) { status.textContent = 'Escribí al menos 3 caracteres para buscar.'; return; }
            status.textContent = 'Buscando repuestos...'; timer = setTimeout(() => search(query, 1), 300);
        });
        more.addEventListener('click', () => search(query, page + 1));
        modal.addEventListener('hide.bs.modal', event => {
            if (isSaving) { event.preventDefault(); return; }
            selected.innerHTML = savedSelection;
            selected.querySelectorAll('input').forEach(input => { input.checked = true; });
            results.querySelectorAll('input').forEach(input => { input.checked = isSelected(input.value); });
            saveError.hidden = true;
            updateCount();
        });
        saveButton.addEventListener('click', async () => {
            isSaving = true; saveButton.disabled = true; saveError.hidden = true;
            try {
                const codes = [...selected.querySelectorAll('input:checked')].map(input => input.value);
                const response = await fetch(config.catalogPartsSaveUrl, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ catalog_part_codes: codes }),
                });
                const data = await response.json();
                if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || data.message || 'No se pudieron guardar los repuestos.');
                savedSelection = selected.innerHTML;
                badge.textContent = `${codes.length} seleccionados`;
                feedback.textContent = data.message;
                isSaving = false;
                Modal.getOrCreateInstance(modal).hide();
            } catch (error) {
                saveError.textContent = error.message || 'No se pudieron guardar los repuestos. Revisá la conexión.';
                saveError.hidden = false;
            } finally { isSaving = false; saveButton.disabled = false; }
        });
        updateCount();
    }
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
