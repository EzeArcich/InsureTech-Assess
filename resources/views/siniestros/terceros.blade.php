@extends('layouts.app')


@section('content')
 



    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Reclamos de terceros</h3>
        </div>
        <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/home#">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/siniestros">Terceros</a></li>
    <li class="breadcrumb-item active" aria-current="page">Index</li>
  </ol>
</nav>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                
            
                        @can('terceros-siniestro')
                        <a class="btn btn-primary btn-sm mb-4" href="{{ route('siniestros.create') }}">Nuevo</a>
                        @endcan
                        

            
                        <table class="table table-sm m-1 p-1 table-bordered table-hover table-striped tablita" style="width:100%">
                                <thead style="background-color:hsl(213, 99%, 49%)">                                     
                                    

                               
                                    <th style="display: none;">ID</th>
                                    @can('perito-siniestro')
                                <th style="color:#fff;">Fecha IP</th>
                                <th style="color:#fff;">Nro Corto</th>
                                @endcan
                                    <th style="color:#fff;">Siniestro</th>
                                                                        
                                    <th style="color:#fff;">Patente</th>
                                    @can('derivar-siniestro')
                                    
                                    <th style="color:#fff;">Fecha IP</th>
                                    @endcan
                                    @can('ver-siniestro')
                                    
                                    <th style="color:#fff;">Coordinador</th>
                                    <th style="color:#fff;">Actualizado</th>
                                    <th style="color:#fff;">Fecha ingreso</th>
                                    <th style="color:#fff;">Fecha gestión</th>
                                    <th style="color:#fff;">Observaciones</th>
                                    @endcan
                                    <th style="color:#fff;">Estado</th>
                                    <th style="color:#fff;">Modalidad</th>
                                    
                                    @can('derivar-siniestro')
                                    <!-- <th style="color:#fff;">Screenshot</th> 
                                    <th style="color:#fff;">Captura de pantalla</th>  -->
                                    <th style="color:#fff;">Cliente</th>
                                    <th style="color:#fff;">Dirección</th>
                                    <th style="color:#fff;">Localidad</th>
                                    <th style="color:#fff;">Inspector</th>
                                    <th style="color:#fff;">Motivo</th>
                                    <!-- <th style="color:#fff;">Enviar Orden</th> -->
                                    @endcan
                                    <th style="color:#fff;">Acciones</th>                                                                   
                              </thead>
                              <tbody>
                            @foreach ($siniestros as $siniestro)
                            <tr>
                                
                                
                                <td style="display: none;">{{ $siniestro->id }}</td>
                                @can('perito-siniestro')
                                <td>{{ $siniestro->fechaip }}</td>
                                <td>{{ $siniestro->nrocorto }}</td>
                                @endcan                                
                                <td>{{ $siniestro->siniestro }}</td>
                                
                                <td>{{ $siniestro->patente }}</td>
                                @can('derivar-siniestro')
                                
                                <td>{{ $siniestro->fechaip }}</td>
                                @endcan
                                @can('ver-siniestro')
                                
                                <td>{{ $siniestro->creator->name }}</td>
                                <td>{{ $siniestro->editor->name }}</td>
                                <td>{{ $siniestro->created_at }}</td>
                                <td>{{ $siniestro->updated_at }}</td>
                                <td>{{ $siniestro->observaciones }}</td>
                                @endcan
                                <td>{{ $siniestro->estado }}</td>
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
                                        
                                        <a class="btn btn-outline-success btn-lg" href="{{ route('siniestros.edit',$siniestro->id) }}"><i class="fa-solid fa-pen-to-square fa-xl"></i></a>
                                        
                                        
                                        
                                        
                                        
                                      
                                        

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
                        
                        </div>
                    </div>
                </div>
               
            </div>
        </div>
    </section>
    
    
    
    
    
   

    
@endsection



<!-- DataTables JS -->

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>


<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/r-2.2.9/datatables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@section('javas')


<script>
    $(document).ready(function() {
    $('.tablita').DataTable({
        responsive: true,
        processing: true,
        
    

        
    });
});


</script>

@endsection

