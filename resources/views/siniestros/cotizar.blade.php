@extends('layouts.app')

@section('content')


<style>
    .selected{
        background-color: #bdffff; font-weight: bold; color: black;
    }
</style>

    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Inspecciones</h3>
        </div>
        <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/home#">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/siniestros">Siniestros</a></li>
    <li class="breadcrumb-item active" aria-current="page">Cotizaciones</li> 
    
  </ol>
 
</nav>
        <div class="section-body">
        
            <div class="row">
            
                <!-- <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">  -->
                   
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



                                
                                @can('terceros-siniestro')
                                <div class="col-lg-12">
                                <div class="card">

                                <div class="card-header" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                                <h4 style="color:white">Datos del siniestro </h4>
                                </div>
                                <div class="card-body">
                                <form action="{{ route('siniestros.update',$siniestro->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                
                                

                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                        
                                        <label for="link">Link 2.0</label>
                                        <a href="{{ route('siniestros.show',$siniestro->id) }}" class="badge badge-primary float-right">Adjuntos</a>
                                        <input type="text" name="link" class="form-control" id="link"  value="{{ $siniestro->link }}">
                                        </div>
                                    </div>   
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="siniestro">Siniestro</label>
                                        <input type="text" name="siniestro" class="form-control" id="siniestro" value="{{ $siniestro->siniestro }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="analista">Analista</label>
                                        <input type="text" name="analista" class="form-control" id="analista" value="{{ $siniestro->analista }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="nombretercero">Nombre del tercero</label>
                                        <input type="text" name="nombretercero" class="form-control" id="nombretercero" value="{{ $siniestro->nombretercero }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="patente">Patente</label>
                                        <input type="text" name="patente" class="form-control" id="patente" value="{{ $siniestro->patente }}">
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="compania">Compañía</label>
                                        <input type="text" name="compania" class="form-control" id="compania"  value="{{ $siniestro->compania }}">
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="nrocorto">Nro. corto</label>
                                        <input type="text" name="nrocorto" class="form-control" id="nrocorto"  value="{{ $siniestro->nrocorto }}">
                                        </div>
                                    </div>
                                    
                                      
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="cliente">Cliente</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" id="cliente" for="cliente" name="cliente"">
                                            <option selected value="Tercero">Tercero</option>
                                           
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="modalidad">Tipo de inspeccion</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" id="modalidad" for="modalidad" name="modalidad" value="{{ $siniestro->modalidad }}">
                                            <option selected value="{{ $siniestro->modalidad }}">{{ $siniestro->modalidad }}</option>
                                            <option value="presencial">Presencial</option>
                                            <option value="videollamada">Videollamada</option>
                                            <option value="foto y presupuesto">Por foto y presupuesto</option>
                                            <option value="foto">Por foto</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="motivo">Motivo</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" id="motivo" for="motivo" name="motivo"">
                                            <option selected value="{{ $siniestro->motivo }}">{{ $siniestro->motivo }}</option>
                                            <option value="Reclamo">Reclamo</option>
                                            
                                        </select>
                                        </div>
                                    </div>

                                    

                                    <hr>
                                    <div class="card-header" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                                <h4 style="color:white">Datos de coordinación </h4>
                                </div>
                         
                                    
                                    <div class="col-xs-12 col-sm-12 col-md-12 mt-3">           
                                        <label for="observaciones">Observaciones</label>    
                                        <div class="form-floating">
                                        <textarea class="form-control" name="observaciones" style="height:100px" id="observaciones" value="{{ $siniestro->observaciones }}">{{ $siniestro->observaciones }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="nombretaller" class="ml-2">Nombre del taller</label>    
                                        <div class="input-group ml-1">	
                                    <input type="text" class="form-control name" id="nombretaller" name="nombretaller" value="{{ $siniestro->nombretaller }}">
                                        <span class="input-group-btn">
                                         <button class="btn btn-info pull-right" type="button" data-toggle="modal" href="#" data-target="#modal_talleres" ><span class="fa fa-search-plus"  aria-hidden="true"></span></button>
                                    </span>	
                                    </div>	
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="email">E-mail</label>
                                        <input type="text" name="email"  class="form-control" id="email" value="{{ $siniestro->email }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" name="telefono"  class="form-control" id="telefono" value="{{ $siniestro->telefono }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="direccion">Dirección</label>
                                        <input type="text" name="direccion"  class="form-control" id="direccion" value="{{ $siniestro->direccion }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="localidad">Localidad</label>
                                        <input type="text" name="localidad"  class="form-control" id="localidad" value="{{ $siniestro->localidad }}">
                                        </div>
                                    </div>

                                     <div class="col-xs-4 col-sm-4 col-md-4" hide="true">
                                        <div class="form-group">
                                        <label for="coordinador">Coordinador</label>
                                        <input type="text" name="coordinador"  class="form-control" id="coordinador" value="{{\Illuminate\Support\Facades\Auth::user()->name}}">
                                        </div>
                                    </div>

                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="inspector">Asignado a inspector</label>
                                        <input type="text" name="inspector"  class="form-control" id="inspector" value="{{ $siniestro->inspector }}">
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="estado">Estado</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" for="estado" id="estado" name="estado" value="{{ $siniestro->estado }}">
                                            <option selected value="{{ $siniestro->estado }}">{{ $siniestro->estado }}</option>
                                            <option value="Tercero-gestion">En gestión</option>
                                            <option value="Cargar ampliacion">Cargar ampliacion</option>
                                            <option value="Baja">Baja</option>
                
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="fechaip">Fecha IP</label>
                                        <input type="date" name="fechaip" class="form-control" id="fechaip"  value="{{ $siniestro->fechaip }}">
                                        </div>
                                    </div>  
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="horario">Rango horario</label>
                                        <input type="text" name="horario"  class="form-control" id="horario" value="{{ $siniestro->horario }}" >
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="file" class="form-label">Adjuntos</label>
                                        <input type="file" name="urls[]" class="form-control" id="file" multiple>
                                        </div>
                                    </div>
                                     <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <button type="submit" class="btn btn-warning ml-5 mb-3 mt-4">Cargar fotos</button>
                                        
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">           
                                        <label for="comentariosparaip">Comentarios para el perito</label>    
                                        <div class="form-floating">
                                        <textarea class="form-control"  name="comentariosparaip" id="comentariosparaip" value="{{ $siniestro->comentariosparaip }}" style="height:100px">{{ $siniestro->comentariosparaip }}</textarea>
                                    </div>
                                    </div> 

                                    
                                    
                                    <hr>
                                    <div class="card-header" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                                <h4 style="color:white">Negociación</h4>
                                </div>
                                
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="negociacion">Negociación</label>
                                        <input type="text" name="negociacion"  class="form-control" id="negociacion" value="{{ $siniestro->negociacion }}" >
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="presupuesto">Presupuesto</label>
                                        <input type="text" name="presupuesto"  class="form-control" id="presupuesto" value="{{ $siniestro->presupuesto }}" >
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="cotizado">Cotizado</label>
                                        <input type="text" name="cotizado"  class="form-control" id="cotizado" value="{{ $siniestro->cotizado }}" >
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="ofrecimiento1">Ofrecimiento N°1</label>
                                        <input type="text" name="ofrecimiento1"  class="form-control" id="ofrecimiento1" value="{{ $siniestro->ofrecimiento1 }}" >
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="ofrecimiento2">Ofrecimiento N°2</label>
                                        <input type="text" name="ofrecimiento2"  class="form-control" id="ofrecimiento2" value="{{ $siniestro->ofrecimiento2 }}" >
                                        </div>
                                    </div>

                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="cierre">Cierre</label>
                                        <input type="text" name="cierre"  class="form-control" id="cierre" value="{{ $siniestro->cierre }}" >
                                        </div>
                                    </div>
                                
                                    




                                        <input id="id" hidden="true" value="{{ $siniestro->id }}">
                                </div>
                                </div>
                                </div>
                                </div>
                                
                                    <hr> 
                                    <button type="submit" class="btn btn-success btn-lg ml-5 mb-3" onclick="updateData(event)">Aplicar cambios</button>

                                      

                                    


                                    
                                    
                                    
                                </div>
                                </form>
                                @endcan

                                


                                </div>
                                <br>
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
        <div class="modal-dialog modal-lg">
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
                                                            <th style="display: none;font-size:20px">ID</th>
                                                            <th style="color:#fff;font-size:17px">Asignar a</th>
                                                            <th style="color:#fff;font-size:17px">E-mail</th>
                                                            <th style="color:#fff;font-size:17px">Telefono</th>
                                                            
                                                                                                                                
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
                                                            <th style="color:#fff;font-size:17px">E-mail</th>
                                                            <th style="color:#fff;font-size:17px">Telefono</th>
                                                            
                                                                                                                                
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($talleres as $taller)
                                                            <tr>
                                                                <td style="display: none;">{{ $taller->id }}</td>
                                                                <td onclick="selectedRow3(),tallerData('{{ $taller->id }}')">{{ $taller->taller }}</td>
                                                                <td onclick="selectedRow3(),tallerData('{{ $taller->id }}')">{{ $taller->telefonos }}</td>
                                                                <td onclick="selectedRow3(),tallerData('{{ $taller->id }}')">{{ $taller->email }}</td>

                                                                
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
    
    $(document).ready(function() {
    $('.productores').DataTable({
        pageLength : 10,
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



function productorData(id){
    



 
    $.ajax({
        type:"get",
        dataType:"json",
        url:"/teacher/productores/"+id,
        success: function(data){
           

            // $('#id_inspector').val(data.id);
            $('#nombre').val(data.nombre);
            $('#correo').val(data.correo);
            
           

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
 var comentariosdelperitovisita = $('#comentariosdelperitovisita').val();
 var comentariosdelperitofinales = $('#comentariosdelperitofinales').val();
 
 
 
 
 
 
  
 
 $.ajaxSetup({
headers:{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
})


 $.ajax({
     type: "PUT",
     
     data: {modalidad:modalidad, motivo:motivo, fechaip:fechaip, patente:patente, siniestro:siniestro, localidad:localidad, direccion:direccion, email:email, estado:estado,
     observaciones:observaciones, nombretaller:nombretaller, telefono:telefonos, localidad:localidad, enviarorden:enviarorden, horario:horario, comentariosparaip:comentariosparaip, link:link,
     nrocorto:nrocorto, cliente:cliente, comentariosdelperitovisita:comentariosdelperitovisita, comentariosdelperitofinales:comentariosdelperitofinales},
     url: "/teacher/update/"+id,
     success: function(response){
      Swal.fire({
          icon: 'success',
          position: 'top-end',
          showConfirmButton: false,
          title: 'Siniestro asignado con éxito',
      })
      
     
        
      
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
 
 $.ajaxSetup({
headers:{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
})


 $.ajax({
     type: "POST",
     
     data: {siniestro:siniestro, emailPas:emailPas, patente:patente, coordinador:coordinador},
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
var email = $('#email').val();
var fechaip = $('#fechaip').val();
var patente = $('#patente').val();
var nrocorto =  $('#nrocorto').val();
var comentariosparaip = $('#comentariosparaip').val();
var telefono = $('#telefono').val();
var localidad = $('#localidad').val();
var direccion =  $('#direccion').val();
var modalidad = $('#modalidad').val();

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
     
     data: {siniestro:siniestro, email:email, fechaip:fechaip, patente:patente, nrocorto:nrocorto, comentariosparaip:comentariosparaip, telefono:telefono, localidad:localidad, direccion:direccion,
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

