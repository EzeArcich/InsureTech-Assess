const form = document.querySelector('#producer-email-form');
if (form) {
    const template = document.querySelector('#producer-email-template');
    const feedback = document.querySelector('#producer-email-feedback');
    const send = document.querySelector('#producer-email-send');
    const preview = document.querySelector('#producer-email-preview');
    const ccInput = document.querySelector('#producer-email-cc');
    const copies = new Set();
    const newRequestId = () => {
        const bytes = crypto.getRandomValues(new Uint8Array(16)); bytes[6] = (bytes[6] & 15) | 64; bytes[8] = (bytes[8] & 63) | 128;
        const hex = [...bytes].map(value => value.toString(16).padStart(2, '0')).join('');
        return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
    };
    let reviewed, requestId = newRequestId(), busy = false, generation = 0;
    const values = () => ({ producer_id: document.querySelector('#producer_id').value, template_version_id: template.value, cc: [...copies], request_id: requestId });
    const invalidate = () => { reviewed = null; generation++; send.disabled = true; document.querySelector('#producer-email-message').hidden = true; };
    const drawCopies = () => {
        const list = document.querySelector('#producer-email-copies'); list.replaceChildren();
        copies.forEach(email => { const button = document.createElement('button'); button.type = 'button'; button.className = 'btn btn-palette-gray btn-sm'; button.textContent = `${email} ×`; button.addEventListener('click', () => { copies.delete(email); drawCopies(); invalidate(); }); list.append(button); });
    };
    document.querySelector('#producer-email-add-cc').addEventListener('click', () => {
        if (!ccInput.value.trim() || !ccInput.checkValidity()) { ccInput.reportValidity(); return; }
        if (copies.size >= 10) { feedback.textContent = 'Se permiten hasta 10 contactos en copia.'; return; }
        copies.add(ccInput.value.trim().toLowerCase()); ccInput.value = ''; drawCopies(); invalidate();
    });
    template.addEventListener('change', invalidate);
    document.querySelector('#producer-email-modal').addEventListener('show.bs.modal', () => { invalidate(); feedback.textContent = ''; requestId = newRequestId(); });
    const request = async (url, payload) => {
        const response = await fetch(url, { method: 'POST', headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name=_token]').value }, body: JSON.stringify(payload) });
        const data = await response.json();
        if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || data.message || 'No se pudo procesar el correo.');
        return data;
    };
    preview.addEventListener('click', async () => {
        if (busy) return;
        busy = true; invalidate(); const current = generation; preview.disabled = true; feedback.textContent = 'Preparando vista previa…';
        const payload = values();
        if (!payload.producer_id || !payload.template_version_id) { feedback.textContent = 'Seleccioná un productor y una plantilla.'; busy = false; preview.disabled = false; return; }
        try {
            const data = await request(form.dataset.previewUrl, payload);
            if (current !== generation) return;
            reviewed = JSON.stringify(payload); document.querySelector('#producer-email-recipient').textContent = data.recipient;
            document.querySelector('#producer-email-cc-summary').textContent = data.cc.join(', ') || 'Sin copias';
            document.querySelector('#producer-email-subject').textContent = data.subject;
            document.querySelector('#producer-email-body').srcdoc = `<!doctype html><html lang="es"><head><meta charset="utf-8"></head><body>${data.body}</body></html>`;
            document.querySelector('#producer-email-message').hidden = false; feedback.textContent = 'Revisá destinatarios y contenido antes de enviar.'; send.disabled = false;
        } catch (error) { feedback.textContent = error.message; }
        finally { busy = false; preview.disabled = false; }
    });
    form.addEventListener('submit', async event => {
        event.preventDefault(); if (busy) return;
        const payload = values();
        if (ccInput.value.trim()) { feedback.textContent = 'Agregá el contacto en copia o vaciá ese campo antes de enviar.'; return; }
        if (!reviewed || reviewed !== JSON.stringify(payload)) { invalidate(); feedback.textContent = 'Prepará nuevamente la vista previa.'; return; }
        busy = true; send.disabled = true; preview.disabled = true; feedback.textContent = 'Enviando correo…';
        try { const data = await request(form.action, payload); invalidate(); feedback.textContent = data.message; requestId = newRequestId(); }
        catch (error) { feedback.textContent = error.message; invalidate(); }
        finally { busy = false; preview.disabled = false; }
    });
}
