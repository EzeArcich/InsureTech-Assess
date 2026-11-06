<div class="col-12" id="coordination-location" data-nearby-url="{{ route('claims.nearby', $siniestro) }}" data-search-url="{{ route('claims.location.search', $siniestro->id) }}" data-tiles="{{ config('services.inspection_map.tile_url') }}">
<div id="coordination-nearby" class="panel mb-3" aria-live="polite" hidden></div>
<h3 class="h6">Ubicación de la pericia presencial</h3>
<p class="text-muted">Completá dirección y localidad, buscá el domicilio y confirmá el punto antes de pasar a Coordinado. La agenda del perito usará esta ubicación.</p>
<button class="btn btn-palette-blue mb-2" id="coordination-location-search" type="button">Buscar domicilio en el mapa</button>
<div id="coordination-location-results" class="list-group mb-2" aria-live="polite"></div>
<div class="inspection-map" id="coordination-location-map"></div>
<p id="coordination-location-feedback" class="text-muted mt-2" role="status">Revisá el resultado o marcá el lugar exacto con un clic en el mapa.</p>
<button type="button" class="btn btn-palette-green mb-2" id="coordination-location-confirm" disabled>Confirmar punto</button>
<input type="hidden" name="inspection_address" id="coordination-inspection-address" value="{{ old('inspection_address', $siniestro->inspection_address) }}">
<input type="hidden" name="inspection_latitude" id="coordination-latitude" value="{{ old('inspection_latitude', $siniestro->inspection_latitude) }}">
<input type="hidden" name="inspection_longitude" id="coordination-longitude" value="{{ old('inspection_longitude', $siniestro->inspection_longitude) }}">
<small class="d-block text-muted">Buscador Nominatim / OpenStreetMap. Ingresá solo calle, altura y localidad; no incluyas nombres ni información personal.</small>
</div>
