@extends('layouts.app')

@section('css')

<link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />

@endsection




@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Ingresar siniestro</h3>
        </div>
        <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/home#">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/siniestros">Siniestros</a></li>
    <li class="breadcrumb-item active" aria-current="page">Crear siniestros</li> 
  </ol>
</nav>

        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                    <div class="card-header" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                                <h4 style="color:white">Datos del siniestro </h4>
                                </div>
                        <div class="card-body">     
                                                                      
                            @if ($errors->any())                                                
                            <div class="alert alert-dark alert-dismissible fade show" role="alert">
                            <strong>¡Revise los campos!</strong>                        
                                @foreach ($errors->all() as $error)                                    
                                    <span class="badge badge-danger">{{ $error }}</span>
                                @endforeach                        
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            </div>
                            @endif

                            <form action="{{ route('siniestros.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                        
                                        <label for="link">Link 2.0</label>
                                        
                                        <input type="text" name="link" class="form-control" id="link"  value="{{ $siniestro->link }}">
                                        </div>
                                    </div>   
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="siniestro">Siniestro</label>
                                        <input type="text" name="siniestro" class="form-control" id="siniestro" value="{{ $siniestro->siniestro }}">
                                        
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="patente">Patente</label>
                                        <input type="text" name="patente" class="form-control" id="patente" value="{{ $siniestro->patente }}">
                                        <span id="error_patente"></span>
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="compania">Compañía</label>
                                        <input type="text" name="compania" class="form-control" id="compania"  value="{{ $siniestro->compania }}">
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="nrocorto">Nro. corto</label>
                                        <input type="text" name="nrocorto" class="form-control" id="nrocorto"  value="{{ $siniestro->nrocorto }}">
                                        </div>
                                    </div>
                                    
                                      
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="cliente">Cliente</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" id="cliente" for="cliente" name="cliente"">
                                            <option selected value="{{ $siniestro->cliente }}">{{ $siniestro->cliente }}</option>
                                            <option value="Asegurado">Asegurado</option>
                                            <option value="Tercero">Tercero</option>
                                            <option value="Cotizacion">Cotizacion</option>
                                        </select>
                                    </div>
                                    
                                    
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="motivo">Motivo</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" id="motivo" for="motivo" name="motivo"">
                                            <option selected value="{{ $siniestro->motivo }}">{{ $siniestro->motivo }}</option>
                                            <option value="Todo riesgo">Todo riesgo</option>
                                            <option value="ampliacion">Ampliacion</option>
                                            <option value="actualizaciondevalores">Actualizacion de valores</option>
                                            <option value="cotizarsincerrar">Cotizar y devolver sin cerrar</option>
                                            <option value="robo">Robo</option>
                                            <option value="incendio">Incendio</option>
                                            <option value="posibleDT">Posible DT</option>
                                        </select>
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                    <label for="nombre" class="ml-2">Productor</label>    
                                        <div class="input-group ml-1">	
                                    <input type="text" class="form-control name" id="nombre" name="nombre">
                                        <span class="input-group-btn">
                                         <button class="btn btn-info pull-right" type="button" data-toggle="modal" href="#" data-target="#modal_productores" ><span class="fa fa-search-plus"  aria-hidden="true"></span></button>
                                    </span>	
                                    </div>	
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="correo">Email del PAS</label>
                                        <input type="text" name="correo" class="form-control" id="emailPas">
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="cc">En copia</label>
                                        <input type="text" name="cc" class="form-control" id="cc">
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="cc2">Segundo en copia</label>
                                        <input type="text" name="cc2" class="form-control" id="cc2">
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                    <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-info btn-lg ml-5 mb-3" onclick="Correo(event)">Contacto vía mail</button>
                                    </div>
                                    </div>
                                    

                                    <hr>
                                    <div class="card-header DatCord" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                                <h4 style="color:white">Datos de coordinación </h4>
                                </div>
                                    
                                    
                                    

                                    

                                                <!-- Datatable User --> 
                                                 
                                    
                                              


                                                <!-- Fin Datatable User -->
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-3 DatCord">           
                                        <label for="observaciones">Observaciones</label>    
                                        <div class="form-floating">
                                        <textarea class="form-control" name="observaciones" style="height:100px" id="observaciones" value="{{ $siniestro->observaciones }}">{{ $siniestro->observaciones }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                    <label for="nombretaller" class="ml-2">Nombre del taller</label>    
                                        <div class="input-group ml-1">	
                                    <input type="text" class="form-control name" id="nombretaller" name="nombretaller" value="{{ $siniestro->nombretaller }}">
                                        <span class="input-group-btn">
                                         <button class="btn btn-info pull-right" type="button" data-toggle="modal" href="#" data-target="#modal_talleres" ><span class="fa fa-search-plus"  aria-hidden="true"></span></button>
                                    </span>	
                                    </div>	
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4 DatCord">
                                        <div class="form-group">
                                        <label for="email">E-mail</label>
                                        <input type="text" name="email"  class="form-control" id="email" value="{{ $siniestro->email }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4 DatCord">
                                        <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" name="telefono"  class="form-control" id="telefono" value="{{ $siniestro->telefono }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                        <div class="form-group">
                                        <label for="direccion">Dirección</label>
                                        <input type="text" name="direccion"  class="form-control" id="direccion" value="{{ $siniestro->direccion }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                        <div class="form-group">
                                        <label for="localidad">Localidad</label>
                                        <input type="text" name="localidad"  class="form-control" id="localidad" value="{{ $siniestro->localidad }}">
                                        </div>
                                    </div>

                                     <div class="col-xs-3 col-sm-3 col-md-3 DatCord" hide="true">
                                        <div class="form-group">
                                        <label for="coordinador">Coordinador</label>
                                        <input type="text" name="coordinador"  class="form-control" id="coordinador" value="{{\Illuminate\Support\Facades\Auth::user()->name}}">
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                    <label for="modalidad">Tipo de inspeccion</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" id="modalidad" for="modalidad" name="modalidad" value="{{ $siniestro->modalidad }}">
                                            <option selected value="{{ $siniestro->modalidad }}">{{ $siniestro->modalidad }}</option>
                                            <option value="presencial">Presencial</option>
                                            <option value="videollamada">Videollamada</option>
                                            <option value="foto y presupuesto">Por foto y presupuesto</option>
                                            <option value="foto">Por foto</option>
                                        </select>
                                    </div>



                                    
                                    

                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                    <label for="estado">Estado</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" for="estado" id="estado" name="estado" value="{{ $siniestro->estado }}">
                                            <option selected value="Pendiente">Pendiente</option>
                                            <option value="Coordinado">Coordinado</option>
                                            <option value="Ausente">Ausente</option>
                                            @can('derivar-siniestro')
                                            <option value="Peritando">Derivado a inspector</option>
                                            @endcan('derivar-siniestro')
                                            <option value="Reclamo de repuestos">Reclamo de repuestos</option>
                                            <option value="Actualizacion de valores">Actualizacion de valores</option>
                                            <option value="Cargar ampliacion">Cargar ampliacion</option>
                                            
                                            <option value="Baja">Baja</option>
                
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                        <div class="form-group">
                                        <label for="fechaip">Fecha IP</label>
                                        <input type="date" name="fechaip" class="form-control" id="fechaip"  value="{{ $siniestro->fechaip }}">
                                        </div>
                                    </div>  
                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                        <div class="form-group">
                                        <label for="enviarorden">Enviar orden</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12" id="enviarorden"  aria-label="Default select example" for="enviarorden" name="enviarorden"" value="{{ $siniestro->enviarorden }}">
                                            <option selected value="si">Si</option>
                                            <option value="no">No</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3 DatCord">
                                        <div class="form-group">
                                        <label for="horario">Rango horario</label>
                                        <input type="text" name="horario"  class="form-control" id="horario" value="{{ $siniestro->horario }}" >
                                        </div>
                                    </div>
                                    
                                     <!--<div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="file" class="form-label">Adjuntos</label>
                                        <input type="file" name="urls[]" class="form-control" id="file" multiple>
                                        </div>
                                    </div>-->
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="imagen" class="form-label">Cover</label>
                                        <input type="file" name="imagen" class="form-control">
                                        </div>
                                    </div>
                                    <!-- <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <button type="submit" class="btn btn-warning ml-5 mb-3 mt-4">Cargar fotos</button>
                                        
                                        </div>
                                    </div> -->
                                    <div class="col-xs-12 col-sm-12 col-md-12 DatCord">           
                                        <label for="comentariosparaip">Comentarios para el perito</label>    
                                        <div class="form-floating">
                                        <textarea class="form-control"  name="comentariosparaip" id="comentariosparaip" value="{{ $siniestro->comentariosparaip }}" style="height:100px">{{ $siniestro->comentariosparaip }}</textarea>
                                        </div>
                                        </div> 
                                        <input id="id" hidden="true" value="{{ $siniestro->id }}">
                                </div>
                                </div>
                                </div>
                                </div>
                                
                                                    <hr> 
                                    
                                                    <button type="submit" class="btn btn-success btn-lg ml-5 mb-3">Aplicar cambios</button>
                                                    {{-- <button onclick="CorreoEdu(event)" class="btn btn-success btn-lg ml-5 mb-3">Enviar IP</button> --}}
                                    

                                    
                                    
                                    </div>
                                
                                </div>
                            </form>
                        </div>   
                    </div>    
                </div>
            </div>
        </div>
    </section>
<!-- Ventana Modal PERITOS   -->

<style>
    .selected{
        background-color: #3abaf4; font-weight: bold; color: black;
    }
</style>
	<div class="modal fade" id="modal_productores" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header" style="background-color:hsl(213, 99%, 49%);padding-top:5px;">
            <h4 class="modal-title" style="color:white;">Productores</h4>  
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              
            </div>      
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">  
                    <div style="width: 100%; padding-left: -10px;">    
		            <div class="table-responsive">        
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                           
                                                
                                                        
                                                    <table class="table mt-2 productores" id="productores" cellspacing="0" width="100%">
                                                        <thead style="background-color:hsl(213, 99%, 49%)">                                     
                                                            <th style="display: none;color:#fff;font-size:20px">ID</th>
                                                            <th style="color:#fff;font-size:17px">Productor</th>
                                                            <th style="color:#fff;font-size:17px">Telefono</th>
                                                            <th style="color:#fff;font-size:17px">E-mail</th>
                                                            
                                                                                                                                
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($productores as $productor)
                                                            <tr>
                                                                <td style="display: none;">{{ $productor->id }}</td>
                                                                <td onclick="selectedRow(),productorData('{{ $productor->id }}')">{{ $productor->nombre }}</td>
                                                                <td onclick="selectedRow(),productorData('{{ $productor->id }}')">{{ $productor->telefono }}</td>
                                                                <td onclick="selectedRow(),productorData('{{ $productor->id }}')">{{ $productor->correo }}</td>

                                                                
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                        </div>
                    </div>
                    </div>
                    </div>
                </div>	
                <button type="button" class="btn btn-primary" data-dismiss="modal">Confirmar</button>							
            </div>
            <div class="modal-footer">
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="modal_talleres" tabindex="-1" role="dialog" >
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header" style="background-color:hsl(213, 99%, 49%);padding-top:5px;">
            <h4 class="modal-title" style="color:white;">Buscar perito</h4>  
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              
            </div>      
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">  
                    <div style="width: 100%; padding-left: -10px;">    
		            <div class="table-responsive">        
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                            <label for="">Talleres homologados</label>
                                                
                                                        
                                                    <table class="table mt-2 talleres" id="talleres" cellspacing="0" width="100%">
                                                        <thead style="background-color:hsl(213, 99%, 49%)">                                     
                                                            <th style="display: none;font-size:20px">ID</th>
                                                            <th style="color:#fff;font-size:17px">Taller</th>
                                                            <th style="color:#fff;font-size:17px">Localidad</th>
                                                            <th style="color:#fff;font-size:17px">Domicilio</th>
                                                            
                                                                                                                                
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($talleres as $taller)
                                                            <tr>
                                                                <td style="display: none;">{{ $taller->id }}</td>
                                                                <td onclick="selectedRow3(),tallerData('{{ $taller->id }}')">{{ $taller->taller }}</td>
                                                                <td onclick="selectedRow3(),tallerData('{{ $taller->id }}')">{{ $taller->localidad }}</td>
                                                                <td onclick="selectedRow3(),tallerData('{{ $taller->id }}')">{{ $taller->direccion }}</td>

                                                                
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                        </div>
                    </div>
                    </div>
                    </div>
                </div>	
                <button type="button" class="btn btn-primary" data-dismiss="modal">Confirmar</button>							
            </div>
            <div class="modal-footer">
            </div>
          </div>
        </div>
      </div>
    

@endsection



@section('javas')


   <script>

$(document).ready(function(){
    $('#patente').blur(function(){
        var error_patente = '';
        var patente = $('#patente').val();
        var _token =  $('meta[name="csrf-token"]').attr('content');
        
        
      
   
  
  
   $.ajax({
    url:"{{ route('siniestros.check') }}",
    method:"POST",
    data:{patente:patente, _token:_token},
    success:function(result)
    {
     if(result == 'unique')
     {
        $('#error_patente').html('<label class="card">Primer siniestro con esta patente</label>');
      $('#patente').removeClass('has-error');
      $('#register').attr('disabled', false);
      
     }
     else
     {
      $('#error_patente').html('<label class="card">Ya hay otro siniestro con esta patente</label>');
      $('#patente').addClass('has-error');
      $('#register').attr('disabled', 'disabled');
      
     }
    }
   })
  
 });
 
});


    
    $(document).ready(function() {
    $('.productores').DataTable({
        pageLength : 15,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,
        
    
        
    });
    })
    

</script>   

<script>
    
    $(document).ready(function() {
    $('.talleres').DataTable({
        pageLength : 10,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,

        
    });
})

</script>  

<script>





   function selectedRow(){
                
                var index,
                    table = document.getElementById("productores");
            
                for(var i = 1; i < table.rows.length; i++)
                {
                    table.rows[i].onclick = function()
                    {
                         
                        {
                            $(this).addClass('selected').siblings().removeClass('selected')
                        }
                    };
                }
                
            }
            selectedRow();

            function selectedRow2(){
                
                var index,
                    table = document.getElementById("inspectores");
            
                for(var i = 1; i < table.rows.length; i++)
                {
                    table.rows[i].onclick = function()
                    {
                         
                        {
                            $(this).addClass('selected').siblings().removeClass('selected')
                        }
                    };
                }
                
            }
            selectedRow2();

            function selectedRow3(){
                
                var index,
                    table = document.getElementById("talleres");
            
                for(var i = 1; i < table.rows.length; i++)
                {
                    table.rows[i].onclick = function()
                    {
                         
                        {
                            $(this).addClass('selected').siblings().removeClass('selected')
                        }
                    };
                }
                
            }
            selectedRow3();




function clearData(){
 $('#siniestro').val('');
 $('#fechaip').val('');
 $('#inspector').val('');
 $('#localidad').val('');
 $('#direccion').val('');
 $('#email').val('');
 $('#nameError').text('');
 $('#titleError').text('');
 $('#instituteError').text('');
}

function reladData(){
    setTimeout(function() {
   location.reload();
   }); 
}

function addData(){

    
    
    var link = $('#link').val();
    var siniestro =  $('#siniestro').val();
    var patente = $('#patente').val();
    var nrocorto = $('#nrocorto').val();
    var cliente = $('#cliente').val();
    var modalidad = $('#modalidad').val();
    var motivo = $('#motivo').val();
    var correo = $('#correo').val();
    var observaciones = $('#observaciones').val();
    var email = $('#email').val();
    var nombretaller = $('#nombretaller').val();
    var telefonos = $('#telefono').val();
    var direccion = $('#direccion').val();
    var localidad = $('#localidad').val();
    var estado = $('#estado').val();
    var fechaip = $('#fechaip').val();
    var enviarorden = $('#enviarorden').val();
    var horario = $('#horario').val();
    var comentariosparaip = $('#comentariosparaip').val();

    $.ajax({
        type: "POST",
        dataType: "json",
        data: {link:link, siniestro:siniestro, patente:patente, nrocorto:nrocorto, cliente:cliente, modalidad:modalidad, motivo:motivo, correo:correo, observaciones:observaciones, email:email, 
        nombretaller:nombretaller, telefono:telefono, direccion:direccion, localidad:localidad, estado:estado, fechaip:fechaip, enviarorden:enviarorden, horario:horario, comentariosparaip:comentariosparaip},
        url: "/teacher/store/",
        success: function(data){
            
            
            console.log('Siniestro ingresado con éxito');
        }

    })

}




    function editData(id){
    



 
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/edit/"+id,
        success: function(data){
            $('#id').val(data.id);
            $('#siniestro').val(data.siniestro);
            $('#fechaip').val(data.fechaip);
            $('#patente').val(data.patente);
            $('#direccion').val(data.direccion);
            $('#localidad').val(data.localidad);
            console.log(data);
        }
    })
}

function userData(id){
    



 
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/users/"+id,
        success: function(data){
           

            // $('#id_inspector').val(data.id);
            $('.name').val(data.name);
            $('#email').val(data.email);
            
           

            console.log(data);
        }
    })
}

function productorData(id){
    



 
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/productores/"+id,
        success: function(data){
           

            // $('#id_inspector').val(data.id);
            $('#nombre').val(data.nombre);
            $('#emailPas').val(data.correo);
            
           

            console.log(data);
        }
    })
}

function tallerData(id){
    



 
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/taller/"+id,
        success: function(data){
           

            // $('#id_inspector').val(data.id);
            $('#nombretaller').val(data.taller);
            $('#telefono').val(data.telefonos);
            $('#email').val(data.email);
            $('#direccion').val(data.direccion);
            $('#localidad').val(data.localidad);
            
           

            console.log(data);
        }
    })
}


 // --------------------------------------------- Update de registros a la table de BD -----------------------------------------------------




    function updateData(event){

   event.preventDefault();
    var id = $('#id').val();
    var link = $('#link').val();
    var siniestro =  $('#siniestro').val();
    var patente = $('#patente').val();
    var nrocorto = $('#nrocorto').val();
    var cliente = $('#cliente').val();
    var modalidad = $('#modalidad').val();
    
    var motivo = $('#motivo').val();
    var correo = $('#correo').val();
    var observaciones = $('#observaciones').val();
    var email = $('#email').val();
    var nombretaller = $('#nombretaller').val();
    var telefonos = $('#telefono').val();
    var direccion = $('#direccion').val();
    var localidad = $('#localidad').val();
    var estado = $('#estado').val();
    var fechaip = $('#fechaip').val();
    var enviarorden = $('#enviarorden').val();
    var horario = $('#horario').val();
    var comentariosparaip = $('#comentariosparaip').val();
    
    
    
    
     
    
    $.ajaxSetup({
   headers:{
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   }
   })
 

    $.ajax({
        type: "PUT",
        
        data: {modalidad:modalidad, motivo:motivo, fechaip:fechaip, patente:patente, siniestro:siniestro, localidad:localidad, direccion:direccion, email:email, estado:estado,
        observaciones:observaciones, nombretaller:nombretaller, telefono:telefonos, localidad:localidad, enviarorden:enviarorden, horario:horario, comentariosparaip:comentariosparaip, link:link,
        nrocorto:nrocorto, cliente:cliente},
        url: "/teacher/update/"+id,
        success: function(response){
         Swal.fire({
             icon: 'success',
             position: 'top-end',
             showConfirmButton: false,
             title: 'Siniestro actualizado con éxito',
         })
         timer: 1500;
        
           
         
        console.log('Siniestro asignado con éxito');
        }
     
    })


   }
// <-------------------------------------- Para enviar correo ---------------------------------------------------------------------------------->

function Correo(event){

event.preventDefault();
// var id = $('#id').val();
var siniestro =  $('#siniestro').val();
var emailPas = $('#emailPas').val();
var coordinador = $('#coordinador').val();
var patente = $('#patente').val();
var cc = $('#cc').val();
var cc2 = $('#cc2').val();
var nrocorto = $('#nrocorto').val();
 
 $.ajaxSetup({
headers:{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
})


 $.ajax({
     type: "POST",
     
     data: {siniestro:siniestro, emailPas:emailPas, patente:patente, coordinador:coordinador, cc:cc, cc2:cc2, nrocorto:nrocorto},
     url: "/correo",
     success: function(response){
            
         Swal.fire({
             icon: 'success',
             position: 'top-end',
             showConfirmButton: false,
             title: 'Correo enviado con éxito',
         })
         timer: 500;
        
           
         
        console.log('Correo enviado con éxito');
        }
  
 })


}

// <-------------------------------------- Para enviar correo a Edu ---------------------------------------------------------------------------------->

function CorreoEdu(event){

event.preventDefault();
// var id = $('#id').val();
var siniestro =  $('#siniestro').val();
// var email = $('#email').val();
var fechaip = $('#fechaip').val();
var patente = $('#patente').val();
var nrocorto =  $('#nrocorto').val();
var comentariosparaip = $('#comentariosparaip').val();
var telefono = $('#telefono').val();
var localidad = $('#localidad').val();
var direccion =  $('#direccion').val();
var modalidad = $('#modalidad').val();
// var imagen = $('#imagen').val();
var nombretaller = $('#nombretaller').val();
var motivo = $('#motivo').val();
var horario = $('#horario').val();
var cliente = $('#cliente').val();
var enviarorden = $('#enviarorden').val();




 
 $.ajaxSetup({
headers:{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
})


 $.ajax({
     type: "POST",
     
     data: {siniestro:siniestro, fechaip:fechaip, patente:patente, nrocorto:nrocorto, comentariosparaip:comentariosparaip, telefono:telefono, localidad:localidad, direccion:direccion,
    modalidad:modalidad, nombretaller:nombretaller, motivo:motivo, horario:horario, enviarorden:enviarorden, cliente:cliente},
     url: "/correoEdu",
     success: function(response){
            
         Swal.fire({
             icon: 'success',
             position: 'top-end',
             showConfirmButton: false,
             title: 'Enviado a Edu con exito',
         })
         timer: 500;
        
           
         
        console.log('Correo enviado con exito');
        }
  
 })


}




 


</script>
@endsection