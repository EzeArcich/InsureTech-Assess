const mapElement = document.querySelector('#inspection-map');
const feedback = document.querySelector('#map-feedback');
let map, temporaryMarker;
let searchGeneration = 0;
let searchController;
const form = document.querySelector('#map-location-form');
const latitude = document.querySelector('#map-latitude');
const longitude = document.querySelector('#map-longitude');
const points = mapElement ? JSON.parse(mapElement.dataset.points) : [];
if (mapElement && window.L) {
    const L = window.L;
    map = L.map(mapElement).setView([-34.6037, -58.3816], 10);
    L.tileLayer(mapElement.dataset.tiles, { maxZoom: 19, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors' })
        .on('tileerror', () => { feedback.textContent = 'No se pudo cargar el fondo del mapa. La agenda y las coordenadas siguen disponibles.'; }).addTo(map);
    const group = L.featureGroup();
    const colors = ['green', 'blue', 'orange', 'yellow', 'gray'];
    const inspectors = [...new Set(points.map(point => point.inspector))];
    points.forEach(point => {
        const popup = document.createElement('div');
        const link = document.createElement('a'); link.href = point.url; link.textContent = point.number; popup.append(link);
        [point.inspector, point.time, point.address].filter(Boolean).forEach(text => { const line = document.createElement('div'); line.textContent = text; popup.append(line); });
        const color = getComputedStyle(document.documentElement).getPropertyValue(`--color-${colors[inspectors.indexOf(point.inspector) % colors.length]}`).trim();
        L.circleMarker([point.lat, point.lng], { radius: 9, color, fillColor: color, fillOpacity: 0.8 }).bindPopup(popup).addTo(group);
    });
    group.addTo(map);
    if (points.length) map.fitBounds(group.getBounds(), { padding: [35, 35], maxZoom: 15 });
    const updatePin = (lat, lng) => {
        if (temporaryMarker) temporaryMarker.setLatLng([lat, lng]);
        else temporaryMarker = L.marker([lat, lng]).addTo(map);
    };
    mapElement.addEventListener('location-preview', event => {
        const { lat, lng } = event.detail; updatePin(lat, lng); map.setView([lat, lng], 17);
    });
    map.on('click', event => {
        if (form.hidden) return;
        latitude.value = event.latlng.lat.toFixed(7); longitude.value = event.latlng.lng.toFixed(7);
        updatePin(event.latlng.lat, event.latlng.lng);
        feedback.textContent = 'Punto seleccionado. Confirmá el domicilio y guardá la ubicación.';
    });
    [latitude, longitude].forEach(input => input.addEventListener('change', () => {
        if (latitude.value && longitude.value && latitude.checkValidity() && longitude.checkValidity()) {
            updatePin(Number(latitude.value), Number(longitude.value));
            map.setView([Number(latitude.value), Number(longitude.value)], 15);
        }
    }));
} else if (feedback) feedback.textContent = 'No se pudo cargar el mapa. Podés completar latitud y longitud manualmente.';
document.addEventListener('click', event => {
    const locate = event.target.closest('[data-locate]');
    if (locate) {
        searchGeneration++; searchController?.abort();
        form.action = locate.dataset.url; form.dataset.geocodeUrl = locate.dataset.geocodeUrl; form.hidden = false;
        document.querySelector('#geocode-results').replaceChildren();
        document.querySelector('#geocode-address').disabled = false;
        document.querySelector('#map-location-title').textContent = `Ubicar ${locate.dataset.number}`;
        document.querySelector('#map-address').value = locate.dataset.address;
        latitude.value = locate.dataset.lat; longitude.value = locate.dataset.lng;
        if (temporaryMarker) { temporaryMarker.remove(); temporaryMarker = null; }
        if (map && latitude.value && longitude.value) map.setView([Number(latitude.value), Number(longitude.value)], 15);
        mapElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    const assignment = event.target.closest('[data-assign]');
    if (assignment) {
        const assignmentForm = document.querySelector('#route-assignment-form');
        assignmentForm.action = assignment.dataset.url; assignmentForm.hidden = false;
        document.querySelector('#assignment-title').textContent = `Asignar ${assignment.dataset.number}`;
        document.querySelector('#assignment-inspector').value = assignment.dataset.inspector || '';
        document.querySelector('#assignment-date').value = assignment.dataset.date;
        document.querySelector('#assignment-time').value = assignment.dataset.time || '';
        assignmentForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
document.querySelector('#cancel-location')?.addEventListener('click', () => {
    searchGeneration++; searchController?.abort();
    form.hidden = true; if (temporaryMarker) { temporaryMarker.remove(); temporaryMarker = null; }
});

const searchButton = document.querySelector('#geocode-address');
const addressInput = document.querySelector('#map-address');
const results = document.querySelector('#geocode-results');
addressInput?.addEventListener('input', () => {
    searchGeneration++; searchController?.abort(); results.replaceChildren();
    latitude.value = ''; longitude.value = '';
    if (temporaryMarker) { temporaryMarker.remove(); temporaryMarker = null; }
    searchButton.disabled = false;
});
searchButton?.addEventListener('click', async () => {
    const address = addressInput.value.trim();
    if (address.length < 5) { results.textContent = 'Ingresá calle, altura y localidad.'; return; }
    const generation = ++searchGeneration;
    searchController?.abort(); searchController = new AbortController();
    searchButton.disabled = true; results.textContent = 'Buscando domicilio…';
    try {
        const response = await fetch(form.dataset.geocodeUrl, { method: 'POST',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ address }), signal: searchController.signal });
        const data = await response.json();
        if (generation !== searchGeneration) return;
        if (!response.ok) { results.textContent = Object.values(data.errors || {}).flat().join(' ') || data.message || 'No se pudo buscar el domicilio.'; return; }
        results.replaceChildren();
        if (!data.results.length) { results.textContent = 'No encontramos ese domicilio. Revisá calle, altura y localidad, o marcá el punto manualmente.'; return; }
        data.results.forEach(result => {
            const button = document.createElement('button'); button.type = 'button'; button.className = 'list-group-item list-group-item-action';
            button.textContent = `${result.label} · ${result.precision}`;
            button.addEventListener('click', () => {
                latitude.value = result.latitude; longitude.value = result.longitude;
                mapElement.dispatchEvent(new CustomEvent('location-preview', { detail: { lat: result.latitude, lng: result.longitude } }));
                feedback.textContent = 'Revisá el punto encontrado y presioná «Confirmar ubicación» para guardarlo en la agenda.';
                results.replaceChildren();
            });
            results.append(button);
        });
    } catch (error) {
        if (error.name !== 'AbortError' && generation === searchGeneration) results.textContent = 'No se pudo conectar con el buscador. Podés marcar el punto manualmente.';
    } finally { if (generation === searchGeneration) searchButton.disabled = false; }
});
