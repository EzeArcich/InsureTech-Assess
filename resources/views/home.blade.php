@extends('layouts.app')
@section('page-title', 'Dashboard')
@section('page-description', 'Una mirada a los siniestros, sus estados y compañías.')

@section('content')
@php
    $listRoute = auth()->user()->can('view-all-claims') ? 'claims.all' : 'claims.index';
    $stateColors = ['Pendiente' => 'orange', 'Ausente' => 'gray', 'Coordinado' => 'yellow', 'Peritando' => 'blue', 'IPgestion' => 'green', 'Resuelto' => 'green'];
    $highestCount = max(1, (int) ($companies->first()?->claims_count ?? 0));
@endphp
<div class="dashboard-summary">
    <div><span class="dashboard-eyebrow">RESUMEN GENERAL</span><p class="mb-0"><strong>{{ number_format($totalClaims) }}</strong> siniestros en <strong>{{ $companies->where('claims_count', '>', 0)->count() }}</strong> compañías</p></div>
    <a href="{{ route($listRoute) }}" class="claims-create"><i class="bi bi-arrow-up-right"></i> Ver siniestros</a>
</div>
<div class="dashboard-states">
    @foreach($statusCounts as $status => $count)
        <a href="{{ route($listRoute, ['status' => $status]) }}" class="dashboard-state dashboard-state--{{ $stateColors[$status] ?? 'gray' }}">
            <span class="dashboard-state-heading"><span>{{ $status === '__unset__' ? 'Sin estado' : ($status === 'IPgestion' ? 'En gestión' : $status) }}</span><i class="bi bi-arrow-up-right"></i></span>
            <strong>{{ number_format($count) }}</strong>
            <span>{{ $totalClaims > 0 ? round($count * 100 / $totalClaims, 1) : 0 }}% del total</span>
        </a>
    @endforeach
</div>
<div class="dashboard-grid">
    <section class="panel dashboard-companies" aria-labelledby="company-chart-title">
        <div class="dashboard-section-heading"><div><h2 id="company-chart-title">Siniestros por compañía</h2><p>De mayor a menor cantidad de casos</p></div><i class="bi bi-bar-chart-line"></i></div>
        @forelse($companies as $company)
            <a class="dashboard-bar-row" href="{{ route($listRoute, ['company_id' => $company->id]) }}" aria-label="{{ $company->name }}: {{ $company->claims_count }} siniestros">
                <span class="dashboard-bar-label"><span>{{ $company->name }}</span><strong>{{ $company->claims_count }}</strong></span>
                <span class="dashboard-bar-track"><span class="dashboard-bar" style="width: {{ round($company->claims_count * 100 / $highestCount, 2) }}%"></span></span>
                <small>{{ $totalClaims > 0 ? round($company->claims_count * 100 / $totalClaims, 1) : 0 }}% de todos los siniestros</small>
            </a>
        @empty
            <p class="text-muted py-4">Todavía no hay compañías cargadas.</p>
        @endforelse
    </section>
    <section class="panel" aria-labelledby="recent-claims-title">
        <div class="dashboard-section-heading"><div><h2 id="recent-claims-title">Últimos siniestros</h2><p>Los cinco registros más recientes</p></div><i class="bi bi-clock-history"></i></div>
        <ul class="dashboard-recent">
            @forelse($recentClaims as $siniestro)
                <li>
                    <div><strong>{{ $siniestro->claim_number ?: 'Sin número' }}</strong>@include('siniestros.partials._status')</div>
                    <small>{{ $siniestro->company?->name ?? 'Sin compañía' }} · {{ $siniestro->created_at?->format('d/m/Y') }}</small>
                    @if($siniestro->client)
                        <a class="person-link" href="{{ route('clients.show', $siniestro->client_id) }}">{{ $siniestro->client->name ?: 'Ver asegurado' }}</a>
                    @endif
                </li>
            @empty
                <li class="text-muted">Todavía no hay siniestros cargados.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
