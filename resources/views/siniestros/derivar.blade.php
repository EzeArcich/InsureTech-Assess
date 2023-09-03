@extends('layouts.app')

@section('content')

@php
$cant_th = $siniestros
    ->whereIn('modalidad', ['videollamada'])
    ->count();                                               
@endphp
@php
$cant_foto = $siniestros
    ->whereIn('modalidad', ['foto'])
    ->count();                                               
@endphp
@php
$cant_caba = $siniestros
    ->whereIn('modalidad', ['foto y presupuesto'])
    ->count();                                               
@endphp
@php
$cant_total = $siniestros
    ->count();                                               
@endphp


<style>
    .selected{
        background-color: #bdffff; font-weight: bold; color: black;
    }
</style>

    <section class="section">
        <div class="section-header">
            <h3 class="page__heading position-relative">Inspecciones a derivar<span class="badge badge-info position-absolute top-0 start-100 translate-middle rounded-pill">{{$cant_total}}</span></h3>
                
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-sm-8">
                    <div class="card">
                    <div class="card-header" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                    <h3 style="color:white">Inspecciones para asignar</h3>
                    <div class="float-right">
                    <button class="btn btn-info m-2 float-right position-relative">
                            TH <span class="badge badge-light position-absolute top-0 start-100 translate-middle rounded-pill">{{$cant_th}}</span>
                    </button>
                    <button class="btn btn-info m-2 float-right position-relative">
                            Foto <span class="badge badge-light position-absolute top-0 start-100 translate-middle rounded-pill">{{$cant_foto}}</span>
                    </button>
                    <button class="btn btn-info m-2 float-right position-relative">
                            Foto/Pres. <span class="badge badge-light position-absolute top-0 start-100 translate-middle rounded-pill">{{$cant_caba}}</span>
                    </button>
                    </div>
                    
                    </div>
                
                        
                        
                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            
                         <table class="table  mt-2 siniestros" id="siniestros">
                             <thead style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">                                     
                                  <th style="display: none;">ID</th>
                                   <th style="color:#fff;">Siniestro</th>
                                  <th style="color:#fff;">Fecha IP</th>
                                  <th style="color:#fff;">Modalidad</th>
                                  <th style="color:#fff;">Patente</th>               
                                 <th style="color:#fff;">Domicilio</th>
                                  <th style="color:#fff;">Localidad</th>
                                  <th style="color:#fff;">Inspector</th>
                                                    
                                                                                                                        
                             </thead>
                             <tbody>
                                 @foreach ($siniestros as $siniestro)
                                 <tr>
                                       <td style="display: none;">{{ $siniestro->id }}</td>
                                      <td onclick="selectedRow(),editData('{{ $siniestro->id }}')">{{ $siniestro->siniestro }}</td>
                                      <td onclick="selectedRow(),editData('{{ $siniestro->id }}')">{{ $siniestro->fechaip }}</td>
                                      <td onclick="selectedRow(),editData('{{ $siniestro->id }}')">{{ $siniestro->modalidad }}</td>
                                      <td onclick="selectedRow(),editData('{{ $siniestro->id }}')">{{ $siniestro->patente }}</td>
                                      <td onclick="selectedRow(),editData('{{ $siniestro->id }}')">{{ $siniestro->direccion }}</td>
                                      <td onclick="selectedRow(),editData('{{ $siniestro->id }}')">{{ $siniestro->localidad }}</td>
                                     <td onclick="selectedRow(),editData('{{ $siniestro->id }}')">{{ $siniestro->inspector }}</td>



                                                        
                                  </tr>
                                  @endforeach
                              </tbody>
                         </table>
                                            
                                    
                         </div>          
                    </div>
                                    
                </div>

                
                <div class="col-sm-4" style=" bottom:0px; right:-25px">
                    <div class="card">
                    <div class="card-header" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                    
                    <h3 style="color:white">Datos de IP</h3>
                    <hr>
                    </div>
                    
                    
                        <hr>
                        <div class="row">
                                    <div class="col-xs-5 col-sm-5 col-md-5">
                                        <div class="form-group">
                                        <label class="ml-2" for="siniestro">Siniestro</label>
                                        <input type="text" name="siniestro" id="siniestro" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-5 col-sm-5 col-md-5">
                                        <div class="form-group">
                                        <label  for="fechaip">Fecha de inspección</label>
                                        <input type="date" name="fechaip" id="fechaip" class="form-control">
                                        </div>
                                    </div>
                                   
                                    <div class="row">
                                    <div class="col-xs-5 col-sm-5 col-md-5 ml-3">
                                        <div class="form-group">
                                        <label class="ml-1" for="direccion">Dirección</label>
                                        <input type="text" name="direccion" id="direccion" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-xs-5 col-sm-5 col-md-5" >
                                        <div class="form-group">
                                        <label for="localidad">Localidad</label>
                                        <input type="text" name="localidad" id="localidad" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-lg-6 form-group ml-4">
                                    <label for="name" class="ml-2">Inspector</label>    
                                        <div class="input-group ml-1">	
                                    <input type="text" class="form-control name" id="inspector" name="name">
                                        <span class="input-group-btn">
                                        <button class="btn btn-info pull-right" data-toggle="modal" href="#" data-target="#modal_clientes" id="btnBuscarCliente"><span class="fa fa-search-plus"  aria-hidden="true"></span></button>
                                    </span>	
                                    </div>	
                                    </div>
                                    <div class="col-xs-5 col-sm-5 col-md-5" >
                                        <div class="form-group ml-2">
                                        <label class="ml-2" for="email">Mail inspector</label>
                                        <input type="text" name="email" id="email" class="form-control">
                                        </div>
                                    </div>
                                    </div>
                                    <div class="col-xs-5 col-sm-5 col-md-5" >
                                        <div class="form-group ml-2">
                                        <label class="ml-2" for="patente">Patente</label>
                                        <input type="text" name="patente" id="patente" class="form-control">
                                        </div>
                                    </div>

                                    <div class="col-xs-5 col-sm-5 col-md-5" hidden="true" >
                                        <div class="form-group ml-2">
                                        <label class="ml-2" for="estado">Estado</label>
                                        <input type="text" name="estado" id="estado" class="form-control" value="Peritando">
                                        </div>
                                    </div>

                                    </div>
                                   
                                
                                    
                                    </div>
                                    <input id="id" hidden="true">
                                <div>
                                <button type="submit" class="btn btn-primary btn-lg ml-5 mb-3" onclick="updateData(event)">Aplicar cambios</button>


                                </div>
                               
                                
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
	<div class="modal fade" id="modal_clientes" tabindex="-1" role="dialog" >
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
                    <table  class="table table-bordered inspectores" id="inspectores" cellspacing="0" width="100%">
                    <thead style="background-color:hsl(213, 99%, 49%)">                                     
                             <th style="display: none;">ID</th>
                             <th style="color:#fff;">Inspector</th>
                            <th style="color:#fff;">E-mail</th>
                                                                                                                       
                         </thead>
                         <tbody>
                             @foreach ($users as $user)
                            <tr>
                                <td style="display: none;">{{ $user->id }}</td>
                                 <td onclick="selectedRow(),userData('{{ $user->id }}')">{{ $user->name }}</td>
                                <td onclick="selectedRow(),userData('{{ $user->id }}')">{{ $user->email }}</td>
                                                     
                            </tr>
                            @endforeach
                        </tbody>
                    </table>   
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
    $('.siniestros').DataTable({
        pageLength : 10,
        pagingType: "simple",
        lengthChange: false,
        responsive: true,
        processing: true,
        
        "rowCallback": function( row, data, index ) {
          var allData = this.api().column(4).data().toArray();
          if (allData.indexOf(data[4]) != allData.lastIndexOf(data[4])) {
            $(row).css('background-color', '#E8DE3E');
          }
        }
  
        
    
        
    });
    })
    

</script>   

<script>
    
    $(document).ready(function() {
    $('.inspectores').DataTable({
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
                    table = document.getElementById("siniestros");
            
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
            $('#email').val(data.email);
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
            $('#inspector').val(data.name);
            $('#email').val(data.email);
            
           

            console.log(data);
        }
    })
}


 // --------------------------------------------- Update de registros a la table de BD -----------------------------------------------------




    function updateData(event){

   event.preventDefault();
    var id = $('#id').val();
    var siniestro =  $('#siniestro').val();
    var fechaip = $('#fechaip').val();
    var inspector = $('#inspector').val();
    var localidad = $('#localidad').val();
    var estado = $('#estado').val();
    var direccion = $('#direccion').val();
    var email = $('#email').val(); 
    var patente = $('#patente').val();
    $.ajaxSetup({
   headers:{
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   }
   })
 

    $.ajax({
        type: "PUT",
        dataType: "json",
        data: {inspector:inspector, fechaip:fechaip, estado:estado, patente:patente, siniestro:siniestro, localidad:localidad, direccion:direccion, email:email},
        url: "/teacher/update/"+id,
        success: function(response){
         setTimeout(reladData,1200);
         Swal.fire({
             icon: 'success',
             position: 'top-end',
             showConfirmButton: false,
             title: 'Siniestro asignado con éxito',
         })
         timer: 1500
        
           clearData();
         
        console.log('Siniestro asignado con éxito');
        }
     
    })


   }
// ---------------------------------------------------------------------------------------------------------------------------

function Correo(event){

event.preventDefault();
 
 $.ajaxSetup({
headers:{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
})


 $.ajax({
     type: "POST",
     dataType: "json",
     data: {},
     url: "/correo",
     success: function(response){
      
      
     console.log('Siniestro asignado con éxito');
     }
  
 })


}

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
var lugar = $('#lugar').val();
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
    modalidad:modalidad, lugar:lugar, nombretaller:nombretaller, motivo:motivo, horario:horario, enviarorden:enviarorden, cliente:cliente},
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

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

