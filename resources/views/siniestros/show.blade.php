@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css">
<style>
    #fotos {
        border-radius: 15px;
        padding: 10px;
        transition: .5s ease-in-out;
    }
    
    .foto-card {
        border: 1px solid rgba(15, 23, 42, .10);
        border-radius: 1rem;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    
    .foto-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .6rem 1.6rem rgba(15,23,42,.12);
    }
</style>
@endpush

@section('content')
<section class="py-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Adjuntos</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/siniestros') }}" class="text-decoration-none">Siniestros</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Adjuntos</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ url('/siniestros') }}" class="btn btn-light border btn-sm rounded-3">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    {{-- Galería de fotos --}}
    @if($files->count() > 0)
        <div class="row g-3">
            @foreach($files as $file)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="foto-card panel">
                        <a href="../urls/{{ $siniestro->siniestro }}/{{ $file->url }}" 
                           class="fancybox" 
                           data-fancybox="gallery1">
                            <img src="../urls/{{ $siniestro->siniestro }}/{{ $file->url }}" 
                                 class="img-fluid w-100" 
                                 id="fotos" 
                                 alt="{{ $file->url }} - Vista preliminar no disponible - Click para descargar archivo" 
                                 style="object-fit: cover; height: 250px;">
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="panel">
            <div class="text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No hay archivos adjuntos para este siniestro</p>
            </div>
        </div>
    @endif

</section>
@endsection

@section('javas')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
@endsection
