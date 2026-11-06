const root = document.querySelector('#coordination-location');
if (root) {
    const form = root.closest('form');
    const address = form.querySelector('#address');
    const city = form.querySelector('#city');
    const mode = form.querySelector('#inspection_mode');
    const status = form.querySelector('#status');
    const latitude = root.querySelector('#coordination-latitude');
    const longitude = root.querySelector('#coordination-longitude');
    const savedAddress = root.querySelector('#coordination-inspection-address');
    const search = root.querySelector('#coordination-location-search');
    const confirm = root.querySelector('#coordination-location-confirm');
    const feedback = root.querySelector('#coordination-location-feedback');
    const results = root.querySelector('#coordination-location-results');
    const mapElement = root.querySelector('#coordination-location-map');
    const nearby = root.querySelector('#coordination-nearby');
    const dateInput = form.querySelector('#inspection_date');
    let nearbyGeneration = 0, nearbyController, nearbyTimer;
    const loadNearby = async () => {
        const current = ++nearbyGeneration;
        nearbyController?.abort(); nearby.replaceChildren(); nearby.hidden = true;
        if (!isPresential() || city.value.trim().length < 3) return;
        nearbyController = new AbortController();
        const params = new URLSearchParams({ city: city.value.trim(), date: dateInput.value });
        if (latitude.value !== '' && longitude.value !== '') { params.set('latitude', latitude.value); params.set('longitude', longitude.value); }
        try {
            const response = await fetch(`${root.dataset.nearbyUrl}?${params}`, { headers: { Accept: 'application/json' }, signal: nearbyController.signal });
            const data = await response.json();
            if (current !== nearbyGeneration) return;
            nearby.hidden = false;
            if (!response.ok) { nearby.textContent = 'No se pudo consultar la agenda de la zona. Podés continuar con la coordinación.'; return; }
            const heading = document.createElement('h3'); heading.className = 'h6';
            heading.textContent = data.total ? `Hay ${data.total} pericia(s) presencial(es) próxima(s) en esta zona` : 'No hay otras pericias presenciales próximas para esta zona en el período consultado.';
            nearby.append(heading);
            data.results.forEach(result => {
                const row = document.createElement('div'); row.className = 'd-flex flex-wrap align-items-center gap-2 py-2';
                const text = document.createElement('span');
                const [year, month, day] = result.date.split('-');
                text.textContent = `${day}/${month}/${year} · ${result.inspector || 'Perito pendiente de asignar'} · ${result.number} · ${result.distance_km === null ? 'Misma localidad, distancia sin confirmar' : `${result.distance_km} km en línea recta`}`;
                const button = document.createElement('button'); button.type = 'button'; button.className = 'btn btn-palette-yellow btn-sm'; button.textContent = 'Usar este día';
                button.addEventListener('click', () => { dateInput.value = result.date; dateInput.dispatchEvent(new Event('change', { bubbles: true })); });
                row.append(text, button); nearby.append(row);
            });
            if (data.total) { const hint = document.createElement('small'); hint.className = 'text-muted'; hint.textContent = 'Intentá coordinar el mismo día. Hojas de ruta confirma la asignación del perito. Se muestran hasta 10 coincidencias: próximos 60 días o ±14 días de la fecha elegida.'; nearby.append(hint); }
        } catch (error) {
            if (error.name !== 'AbortError' && current === nearbyGeneration) { nearby.hidden = false; nearby.textContent = 'No se pudo consultar la agenda de la zona.'; }
        }
    };
    const scheduleNearby = () => {
        clearTimeout(nearbyTimer); nearbyGeneration++; nearbyController?.abort(); nearby.hidden = true;
        nearbyTimer = setTimeout(loadNearby, 400);
    };
    const locationText = () => [address.value.trim(), city.value.trim()].filter(Boolean).join(', ');
    const isPresential = () => ['presencial', 'in_person'].includes(mode.value.trim().toLowerCase());
    let confirmedText = locationText();
    let pendingPoint, marker, map, generation = 0, controller;
    const clear = () => {
        generation++; controller?.abort(); results.replaceChildren(); search.disabled = false;
        latitude.value = ''; longitude.value = ''; savedAddress.value = '';
        pendingPoint = null; confirm.disabled = true;
        marker?.remove(); marker = null;
        scheduleNearby();
        feedback.textContent = 'El domicilio cambió. Buscá y confirmá nuevamente la ubicación.';
    };
    const preview = (lat, lng) => {
        pendingPoint = { lat, lng }; confirm.disabled = false;
        latitude.value = ''; longitude.value = ''; savedAddress.value = '';
        if (map) {
            if (marker) marker.setLatLng([lat, lng]);
            else marker = window.L.marker([lat, lng]).addTo(map);
            map.setView([lat, lng], 17);
        }
        feedback.textContent = 'Revisá el lugar y presioná «Confirmar punto». Se guardará al aplicar los cambios del siniestro.';
    };
    if (window.L) {
        map = window.L.map(mapElement).setView([-34.6037, -58.3816], 10);
        window.L.tileLayer(root.dataset.tiles, { maxZoom: 19, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' }).addTo(map);
        map.on('click', event => preview(event.latlng.lat, event.latlng.lng));
        if (latitude.value !== '' && longitude.value !== '') {
            const lat = Number(latitude.value), lng = Number(longitude.value);
            marker = window.L.marker([lat, lng]).addTo(map); map.setView([lat, lng], 16);
            feedback.textContent = 'Ubicación guardada. Se conservará mientras no cambie el domicilio.';
        }
    } else feedback.textContent = 'No se pudo cargar el mapa. Recargá la página para confirmar la ubicación.';
    const toggle = () => {
        root.hidden = !isPresential();
        if (!isPresential()) clear();
        else if (map) requestAnimationFrame(() => map.invalidateSize());
        scheduleNearby();
    };
    form.addEventListener('claim-step-shown', event => {
        if (event.detail.step === 'coordination' && map) requestAnimationFrame(() => map.invalidateSize());
    });
    mode.addEventListener('change', toggle);
    dateInput.addEventListener('change', scheduleNearby);
    [address, city].forEach(input => {
        input.addEventListener('input', clear);
        input.addEventListener('change', () => { if (confirmedText !== locationText()) clear(); });
    });
    search.addEventListener('click', async () => {
        if (!address.value.trim() || !city.value.trim()) { feedback.textContent = 'Completá dirección y localidad antes de buscar.'; return; }
        const query = locationText();
        if (query.length > 191) { feedback.textContent = 'El domicilio completo debe tener hasta 191 caracteres.'; return; }
        controller?.abort(); controller = new AbortController(); const current = ++generation;
        search.disabled = true; results.textContent = 'Buscando domicilio…';
        try {
            const response = await fetch(root.dataset.searchUrl, { method: 'POST', headers: {
                Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }, body: JSON.stringify({ address: query, inspection_mode: mode.value }), signal: controller.signal });
            const data = await response.json();
            if (current !== generation || query !== locationText()) return;
            if (!response.ok) { results.textContent = Object.values(data.errors || {}).flat().join(' ') || data.message || 'No se pudo buscar el domicilio.'; return; }
            results.replaceChildren();
            if (!data.results.length) { results.textContent = 'No encontramos el domicilio. Corregí los datos o marcá el lugar exacto en el mapa.'; return; }
            data.results.forEach(result => {
                const button = document.createElement('button'); button.type = 'button'; button.className = 'list-group-item list-group-item-action';
                button.textContent = `${result.label} · ${result.precision}`;
                button.addEventListener('click', () => { preview(result.latitude, result.longitude); results.replaceChildren(); });
                results.append(button);
            });
        } catch (error) {
            if (error.name !== 'AbortError' && current === generation) results.textContent = 'No se pudo consultar el domicilio. Podés marcar el punto manualmente.';
        } finally { if (current === generation) search.disabled = false; }
    });
    confirm.addEventListener('click', () => {
        if (!pendingPoint) return;
        latitude.value = pendingPoint.lat.toFixed(7); longitude.value = pendingPoint.lng.toFixed(7);
        savedAddress.value = locationText(); confirmedText = locationText();
        confirm.disabled = true;
        scheduleNearby();
        feedback.textContent = 'Punto confirmado. Al aplicar los cambios se guardará junto con la coordinación.';
    });
    form.addEventListener('submit', event => {
        if (isPresential() && confirmedText !== locationText()) clear();
        if (isPresential() && status.value === 'Coordinado' && (latitude.value === '' || longitude.value === '')) {
            event.preventDefault(); form.dispatchEvent(new CustomEvent('claim-wizard-show', { detail: { step: 'coordination' } })); feedback.textContent = 'Buscá y confirmá el punto antes de marcar la pericia como Coordinado.';
            root.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
    toggle();
}
