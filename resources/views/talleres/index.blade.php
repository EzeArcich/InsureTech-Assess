@extends('layouts.app')


@section('content')
 


    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Talleres homologados</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                
            
                        
                        <a class="btn btn-primary" href="{{ route('talleres.create') }}">Nuevo</a>
                        
            
                        <table class="table table-sm table-bordered table-hover table-striped tablita" style="width:100%">
                                <thead style="background-color:hsl(213, 99%, 49%)">                                     
                                    <th style="display: none;">ID</th>
                                    <th style="color:#fff;">Vacaciones</th>
                                    <th style="color:#fff;">Descuento de franquicia</th>
                                    <th style="color:#fff;">Granizo</th>                                    
                                    <th style="color:#fff;">Pone repuestos</th>
                                    <th style="color:#fff;">Taller</th>
                                    <th style="color:#fff;">Dirección</th>
                                    <th style="color:#fff;">Barrio</th>
                                    <th style="color:#fff;">Localidad</th>
                                    <th style="color:#fff;">Teléfonos</th>
                                    <th style="color:#fff;">E-mail</th>
                                    <th style="color:#fff;">Zona</th>
                                    <th style="color:#fff;">Grúa</th>
                                    <th style="color:#fff;">Traslado</th>
                                                                                                      
                              </thead>
                              <tbody>
                            @foreach ($talleres as $taller)
                            <tr>
                                <td style="display: none;">{{ $taller->id }}</td>                                
                                <td>{{ $taller->vacaciones }}</td>
                                <td>{{ $taller->descfranq }}</td>
                                <td>{{ $taller->granizo }}</td>
                                <td>{{ $taller->ponerep }}</td>
                                <td>{{ $taller->taller }}</td>
                                <td>{{ $taller->direccion }}</td>
                                <td>{{ $taller->barrio }}</td>
                                <td>{{ $taller->localidad }}</td>
                                <td>{{ $taller->telefonos }}</td>
                                <td>{{ $taller->email }}</td>
                                <td>{{ $taller->zona }}</td>
                                <td>{{ $taller->grua }}</td>
                                <td>{{ $taller->traslado }}</td>
                                
                            </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <!-- Paginacion a la derecha -->
                        <div class="pagination justify-content-end">
                            
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
    $(document).ready(function() {
    $('.tablita').DataTable({
        responsive: true,
        processing: true,

        
    });
})
</script>
<!-- DataTables JS -->

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/r-2.2.9/datatables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
        $(function() {
            const languages = {
                'es': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Spanish.json'
            };

            $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {
                className: 'btn btn-sm'
            })
            $.extend(true, $.fn.dataTable.defaults, {
                responsive: true,
                language: {
                    url: languages['es']
                },
                pageLength: 25,
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn-light',
                        text: 'Copiar',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'csv',
                        className: 'btn-light',
                        text: 'CSV',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'excel',
                        className: 'btn-light',
                        text: 'Excel',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-light',
                        text: 'PDF',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        className: 'btn-light',
                        text: 'Imprimir',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'colvis',
                        className: 'btn-light',
                        text: 'Visibilidad Columnas',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }
                ]
            });
        });
    </script>

@endsection

