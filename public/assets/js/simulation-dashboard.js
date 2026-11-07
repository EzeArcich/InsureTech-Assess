document.querySelectorAll('[data-simulation-metrics]').forEach((panel) => {
    let fetching = false;
    window.setInterval(async () => {
        if (document.hidden || fetching) return;
        fetching = true;
        try {
            const response = await fetch(panel.dataset.simulationMetrics, { headers: { Accept: 'application/json' }, credentials: 'same-origin', cache: 'no-store' });
            if (!response.ok || response.redirected) return;
            const metrics = await response.json();
            (metrics.runs || []).forEach((run) => {
                document.querySelectorAll('[data-run-status]').forEach((element) => {
                    if (Number(element.dataset.runStatus) === run.id) element.textContent = run.status;
                });
                document.querySelectorAll('[data-run-next]').forEach((element) => {
                    if (Number(element.dataset.runNext) === run.id) element.textContent = run.next_dispatch_at;
                });
            });
            panel.querySelectorAll('[data-metric]').forEach((element) => {
                if (typeof metrics[element.dataset.metric] === 'number') element.textContent = metrics[element.dataset.metric];
            });
        } catch (_) {
            // Keep the last confirmed values during an interrupted connection.
        } finally {
            fetching = false;
        }
    }, 15000);
});
document.querySelectorAll('[data-simulation-profile]').forEach((form) => {
    const preview = form.querySelector('[data-simulation-preview]');
    const update = () => {
        const number = (key) => Number(form.elements[`configuration[${key}]`].value) || 0;
        const rate = Math.min(number('rate_per_hour'), number('max_per_hour'));
        const duration = number('duration_minutes');
        const peak = ['peak', 'weather'].includes(form.elements['configuration[scenario]'].value);
        const multiplied = Math.min(rate * number('peak_multiplier'), number('max_per_hour'));
        preview.textContent = `Promedio base: ${rate}/hora${peak ? `; pico inicial: ${multiplied}/hora durante ${number('peak_minutes')} minutos` : ''}. ${duration ? `Duración: ${duration} minutos` : 'Duración indefinida'}. Volumen variable y sujeto a límites de intentos, pendientes y concurrencia. No se recuperan intervalos perdidos en ráfaga; el tiempo pausado cuenta dentro de la duración.`;
    };
    form.addEventListener('input', update);
    update();
});
