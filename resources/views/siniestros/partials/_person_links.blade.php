<td>
    @if($siniestro->client)
        <a class="person-link" href="{{ route('clients.show', $siniestro->client_id) }}">{{ $siniestro->client->name ?: 'Ver asegurado' }}</a>
    @else
        <span class="text-muted">Sin asegurado</span>
    @endif
</td>
<td>
    @if($siniestro->inspector)
        <a class="person-link" href="{{ route('inspectors.show', $siniestro->inspector_id) }}">{{ $siniestro->inspector->name }}</a>
    @else
        <span class="text-muted">Sin asignar</span>
    @endif
</td>
