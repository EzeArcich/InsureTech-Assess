@php
    $statusStyle = match ($siniestro->status) {
        'Pendiente' => 'pending',
        'Coordinado', 'coordinado' => 'coordinated',
        'Peritando' => 'inspecting',
        'IPgestion', 'Resuelto' => 'progress',
        default => 'absent',
    };
@endphp
<span class="status-badge status-badge--{{ $statusStyle }}">{{ $siniestro->status === 'IPgestion' ? 'En gestión' : ($siniestro->status ?? 'Sin estado') }}</span>
