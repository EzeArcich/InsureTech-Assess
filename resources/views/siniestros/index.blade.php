@extends('layouts.app')


@section('content')
 
<style>
    .table td, .table th {
        padding: 0px!important;
        vertical-align: middle; /* Para centrar verticalmente el contenido */
        text-align: center; /* Para centrar horizontalmente el contenido */
        border-top: 3px solid #dee2e6!important;
        height: 10px!important;
}
</style>



    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Siniestros</h3>
        </div>
        <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="https://InsureTechAsses.com/home#">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="https://InsureTechAsses.com/siniestros">Siniestros</a></li>
    <!-- <li class="breadcrumb-item active" aria-current="page">Index</li> -->
  </ol>
</nav>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                
            
                        @can('crear-siniestro')
                        <div class="row px-3 py-4">
                            <a class="btn btn-primary btn-sm" href="{{ route('siniestros.create') }}">Ingresar siniestro</a>
                        </div>
                        
                        @endcan
                        @can('derivar-siniestro')
                        
                    
                        @endcan
                        <table class="table table-sm m-1 p-1 table-bordered table-hover table-striped tablita" style="width:100%">
                                <thead style="background-color:hsl(213, 99%, 49%)">                                     
 
                                    <th style="display: none;">ID</th>
                                    <th style="color:#000;">Siniestro</th>
                                    <th style="color:#000;">Compañía</th>
                                    @can('peritos-siniestro')
                                    <th style="color:#000;">Fecha IP</th>
                                    <th style="color:#000;">Nro Corto</th>
                                    <th style="color:#000;">Dirección</th>
                                    <th style="color:#000;">Localidad</th>
                                    @endcan                                   
                                    <th style="color:#000;">Patente</th>
                                    @can('derivar-siniestro')
                                    <th style="color:#000;">Fecha IP</th>
                                    @endcan
                                    @can('ver-siniestro')
                                    <th style="color:#000;">Estado</th>
                                    <th style="color:#000;">Fecha ingreso</th>
                                    <th style="color:#000;">Fecha gestión</th>
                                    <th style="color:#000;">Observaciones</th>
                                    @endcan 
                                    <th style="color:#000;">Modalidad</th> 
                                    @can('derivar-siniestro')
                                    <!-- <th style="color:#000;">Screenshot</th> 
                                    <th style="color:#000;">Captura de pantalla</th>  -->
                                    <th style="color:#000;">Cliente</th>
                                    <th style="color:#000;">Dirección</th>
                                    <th style="color:#000;">Localidad</th>
                                    <th style="color:#000;">Inspector</th>
                                    <th style="color:#000;">Motivo</th>
                                    <!-- <th style="color:#000;">Enviar Orden</th> -->
                                    @endcan
                                    <th style="color:#000;">Acciones</th>                                                                   
                              </thead>
                              <tbody>
                            @foreach ($siniestros as $siniestro)
                            <tr>
                                <td style="display: none;">{{ $siniestro->id }}</td>
                                <td>{{ $siniestro->siniestro }}</td>
                                <td>{{ $siniestro->compania }}</td>
                                @can('peritos-siniestro')
                                <td>{{ date('d-m-Y', strtotime($siniestro->fechaip)) }}</td>
                                <td>{{ $siniestro->nrocorto }}</td>
                                <td>{{ $siniestro->direccion }}</td>
                                <td>{{ $siniestro->localidad }}</td>
                                @endcan                                
                                <td>{{ $siniestro->patente }}</td>
                                @can('derivar-siniestro')
                                <td>{{ date('d-m-Y', strtotime($siniestro->fechaip)) }}</td>
                                @endcan
                                @can('ver-siniestro')
                                <td>{{ $siniestro->estado }}</td>
                                <td>{{ date('d-m-Y', strtotime($siniestro->created_at)) }}</td>
                                <td>{{ date('d-m-Y', strtotime($siniestro->updated_at)) }}</td>
                                <td>{{ $siniestro->observaciones }}</td>
                                @endcan
                                <td>{{ $siniestro->modalidad }}</td>
                                @can('derivar-siniestro')
                                <!-- <td><a href="{{ $siniestro->url }}" target="blank_" >Ver documento</a></td>
                                <td><img alt="img" src="/img/{{ $siniestro->imagen }}" width="100px"></td> -->
                                <td>{{ $siniestro->cliente }}</td>
                                <td>{{ $siniestro->direccion }}</td>
                                <td>{{ $siniestro->localidad }}</td>
                                <td>{{ $siniestro->inspector }}</td>
                                <td>{{ $siniestro->motivo }}</td>
                                <!-- <td>{{ $siniestro->enviarorden }}</td> -->
                                @endcan
                                <td>
                                    <form action="{{ route('siniestros.destroy',$siniestro->id) }}" method="POST">                                            
                                        <a class="btn btn-outline-success px-2 py-1 mx-0 my-0" href="{{ route('siniestros.edit',$siniestro->id) }}"><i class="fa-solid fa-pen-to-square fa-xl"></i></a>
                                    </form>
                                    
                                    <form action="{{ route('siniestros.update',$siniestro->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                        @if (session('info'))
                                            <script>
                                                alert('{{session('info')}}');
                                            </script>

                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <!-- Paginacion a la derecha -->
                        <div class="pagination justify-content-end">
                            {!! $siniestros->links() !!}
                          </div>
                        
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </section>
    
    
    
    
    
   

    
@endsection

@section('javas')

<script>
    $(document).ready(function () {
        console.log('andaJquery');
        $('.tablita').DataTable({
            pageLength: 10, // Personaliza la cantidad de registros por página
        });
    });

</script>

@endsection

