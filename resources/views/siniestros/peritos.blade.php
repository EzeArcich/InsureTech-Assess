@extends('layouts.app')


@section('content')
 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.css"/>



    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Inspecciones a peritar el día de hoy</h3>
        </div>
        <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="https://InsureTechAsses.com/home#">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="https://InsureTechAsses.com/siniestros">Siniestros</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ip a ser vistas</li>
  </ol>
</nav>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                
            
                        

            
                        <table class="table table-sm m-1 p-1 table-bordered table-hover table-striped tablita" style="width:100%">
                                <thead style="background-color:hsl(213, 99%, 49%)">                                     
                                    

                               
                                    <th style="display: none;">ID</th>
                                    <th style="color:#fff;">Siniestro</th>
                                    <th style="color:#fff;">Compañía</th>
                                    
                                <th style="color:#fff;">Fecha IP</th>
                                <th style="color:#fff;">Nro Corto</th>
                                <th style="color:#fff;">Dirección</th>
                                <th style="color:#fff;">Localidad</th>
                                
                                    
                                                                        
                                    <th style="color:#fff;">Patente</th>
                                    
                                    
                                    
                                    <th style="color:#fff;">Modalidad</th>
                                    
                                    
                                    <th style="color:#fff;">Acciones</th>                                                                   
                              </thead>
                              <tbody>
                            @foreach ($siniestros as $siniestro)
                            <tr>
                                
                                
                                <td style="display: none;">{{ $siniestro->id }}</td>
                                <td>{{ $siniestro->siniestro }}</td>
                                <td>{{ $siniestro->compania }}</td>
                                
                                <td>{{ date('d-m-Y', strtotime($siniestro->fechaip)) }}</td>
                                <td>{{ $siniestro->nrocorto }}</td>
                                <td>{{ $siniestro->direccion }}</td>
                                <td>{{ $siniestro->localidad }}</td>
                                                                
                                
                                
                                <td>{{ $siniestro->patente }}</td>
                                
                                
                                <td>{{ $siniestro->modalidad }}</td>
                                
                                
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


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-html5-2.2.3/b-print-2.2.3/datatables.min.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

@section('javas')


<script>
    $(document).ready(function() {
    $('.tablita').DataTable({
        pageLength : 50,
        responsive: true,
        processing: true,
        dom: 'Bfrtilp',
        buttons:[
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i>',
                titleAttr: 'Imprimir',
                className: 'btn btn-lg btn-danger'
            }
        ]
        
    

        
    });
});


</script>

@endsection

