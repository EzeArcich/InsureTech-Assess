document.querySelectorAll('[data-simulation-runtime]').forEach((panel) => {
    const button = panel.querySelector('[data-runtime-start]');
    const message = panel.querySelector('[data-runtime-message]');
    let fetching = false;
    const refresh = async () => {
        if (fetching || document.hidden) return;
        fetching = true;
        try {
            const response = await fetch(panel.dataset.simulationRuntime, { headers: { Accept: 'application/json' }, credentials: 'same-origin', cache: 'no-store' });
            if (!response.ok || response.redirected) throw new Error('status');
            const state = await response.json();
            panel.querySelectorAll('[data-runtime-service]').forEach((element) => {
                const running = state.services[element.dataset.runtimeService];
                element.textContent = running ? 'Activo' : 'Detenido';
                element.classList.toggle('text-success', running);
                element.classList.toggle('text-secondary', !running);
            });
            const running = Object.values(state.services).every(Boolean);
            button.disabled = running || !state.supported || !state.ready;
            button.textContent = running ? 'Motor encendido' : 'Encender motor';
            message.textContent = !state.supported ? 'El servidor no permite iniciar procesos desde el panel; usá los comandos de consola.'
                : !state.ready ? 'Hay migraciones pendientes del motor o la cola. Deben aplicarse antes de encenderlo.'
                    : running ? 'Motor disponible. Podés cerrar esta pestaña.'
                        : 'Podés iniciar los procesos detenidos. Si no arrancan, revisá los logs en storage/app/simulation-runtime.';
        } catch (_) {
            message.textContent = 'No se pudo consultar el estado. Probá actualizar la página.';
        } finally {
            fetching = false;
        }
    };
    panel.querySelector('form').addEventListener('submit', () => {
        button.disabled = true;
        button.textContent = 'Encendiendo…';
    });
    refresh();
    window.setInterval(refresh, 10000);
});
