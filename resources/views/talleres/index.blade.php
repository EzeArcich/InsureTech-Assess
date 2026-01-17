@extends('layouts.app')

@section('content')
<section class="p-2">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h3 class="mb-0 fw-bold">Talleres homologados</h3>
            <nav aria-label="breadcrumb" class="mt-1">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Talleres</li>
                </ol>
            </nav>
        </div>

        <a class="btn btn-brand btn-sm" href="{{ route('talleres.create') }}">
            <i class="fas fa-plus me-2"></i>
            Nuevo taller
        </a>
    </div>

    {{-- Panel principal --}}
    <div class="panel">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0 tablita w-100">
                <thead class="table-success">
                    <tr>
                        <th class="d-none">ID</th>
                        <th>Taller</th>
                        <th>Dirección</th>
                        <th>Barrio</th>
                        <th>Localidad</th>
                        <th>Teléfonos</th>
                        <th>E-mail</th>
                        <th>Zona</th>
                        <th>Grúa</th>
                        <th>Traslado</th>
                        <th>Granizo</th>
                        <th>Pone repuestos</th>
                        <th>Vacaciones</th>
                        <th>Desc. franquicia</th>
                        <th class="text-center" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($talleres as $taller)
                        <tr>
                            <td class="d-none">{{ $taller->id }}</td>
                            <td class="fw-semibold">{{ $taller->taller }}</td>
                            <td>{{ $taller->direccion }}</td>
                            <td>{{ $taller->barrio }}</td>
                            <td>{{ $taller->localidad }}</td>
                            <td>{{ $taller->telefonos }}</td>
                            <td>{{ $taller->email }}</td>
                            <td>{{ $taller->zona }}</td>
                            <td>{{ $taller->grua }}</td>
                            <td>{{ $taller->traslado }}</td>
                            <td>{{ $taller->granizo }}</td>
                            <td>{{ $taller->ponerep }}</td>
                            <td>{{ $taller->vacaciones }}</td>
                            <td>{{ $taller->descfranq }}</td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="{{ route('talleres.edit', $taller->id) }}"
                                       class="btn btn-outline-primary btn-sm px-3">
                                        <i class="fas fa-pen me-1"></i>
                                        Editar
                                    </a>

                                    <form action="{{ route('talleres.destroy', $taller->id) }}"
                                          method="POST"
                                          class="m-0 p-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-outline-danger btn-sm px-3"
                                                onclick="return confirm('¿Seguro que querés borrar este taller?')">
                                            <i class="fas fa-trash me-1"></i>
                                            Borrar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@section('javas')
<script>
    $(function () {
        $('.tablita').DataTable({
            paging: true,
            pageLength: 25,
            info: true,
            searching: true,
            order: [],
            responsive: true,
            processing: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-light btn-sm',
                    text: '<i class="fas fa-copy me-1"></i> Copiar'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-light btn-sm',
                    text: '<i class="fas fa-file-csv me-1"></i> CSV'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-light btn-sm',
                    text: '<i class="fas fa-file-excel me-1"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-light btn-sm',
                    text: '<i class="fas fa-file-pdf me-1"></i> PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-light btn-sm',
                    text: '<i class="fas fa-print me-1"></i> Imprimir'
                }
            ]
        });
    });
</script>
@endsection
