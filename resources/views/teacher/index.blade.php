@extends('layouts.app')

@section('content')
    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Derivar IP</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-sm-9">
                    <div class="card">
                        <div class="card-header">                          
                                All teacher                        
                        </div>
                        <div class="card-body">
                            <table class="table table-sm m-1 p-1 table-bordered table-hover table-striped tablita" style="width:100%">
                                <thead>
                                    <tr>
                                         <th scope="col">#</th>
                                        <th scope="col">Siniestro</th>
                                        <th scope="col">Fecha IP</th>
                                        <th scope="col">Modalidad</th>
                                        <th scope="col">Dirección</th>
                                        <th scope="col">Localidad</th>
                                        <th scope="col">Inspector</th>
                                        <th scope="col">Acciones</th>

                                         
                                        
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table> 
                            
                            
                        </div>
                    </div>
                </div>
                <div class="col-sm-3" style="position:fixed; bottom:550px; right:0px">
                    <div class="card">
                    <div class="card-header">                          
                                <span id="addT">Asignar IP</span>
                                <span id="updateT">Asignar IP</span>                         
                        </div>
                        <div class="card-body">
                                    

                                    
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                    <label for="inspector">Inspector</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" id="inspector" for="inspector" name="inspector" >
                                            <option selected></option>
                                            @foreach($users as $user)

                                            <option value="{{ $user->name }}">{{ $user->name }}</option>
                                            
                                            @endforeach
                                        </select>
                                        </div>
                                    </div>
                                    

                                    

                                    <input type="hidden" id="id">
                                    <!-- <button type="button" href="#" data-target="#ModalEnviar" class="btn btn-primary m-2" data-toggle="modal">Enviar IP</button> -->
                                    
                                    <button type="submit" id="updateButton" class="btn btn-primary btn-sm" onclick="updateData(event)">Update</button>
                                                      
                                                        
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
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.11.5/b-2.2.2/b-colvis-2.2.2/b-html5-2.2.2/b-print-2.2.2/r-2.2.9/datatables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<!-- searchPanes   -->
<script src="https://cdn.datatables.net/searchpanes/1.0.1/js/dataTables.searchPanes.min.js"></script>
<!-- select -->
<script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script> 


    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" integrity="sha512-6PM0qYu5KExuNcKt5bURAoT6KCThUmHRewN3zUFNaoI6Di7XJPTMoT6K0nsagZKk2OB4L7E3q1uQKHNHd4stIQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


@section('javas')

<script>
    
    $('.tablita').DataTable({
        "ajax": "{{route('datatable.paraderivar')}}",
        "select": "true",
        "columns":[
            {data: 'id'},
            {data: 'siniestro'},
            {data: 'fechaip'},
            {data: 'modalidad'},
            {data: 'direccion'},
            {data: 'localidad'},
            {data: 'inspector'},
            
            
            
            
        ]
        
    

        
    });

</script>

<script>



    





 $('#addT').hide();
 $('#addButton').hide();
 $('#updateT').show();
 $('#updateButton').show();

$.ajaxSetup({
    headers:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})

//---------------------------------------------- Llamar datos de la BD ---------------------------------------------------------------

function allData(){
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "/teacher/all",
        success: function (response){
            var data = ""
            $.each(response, function(key, value){
                data = data + "<tr>"
                data = data + "<td>"+value.id+"</td>"
                data = data + "<td>"+value.siniestro+"</td>"
                data = data + "<td>"+value.fechaip+"</td>"
                data = data + "<td>"+value.modalidad+"</td>"
                data = data + "<td>"+value.direccion+"</td>"
                data = data + "<td>"+value.localidad+"</td>"
                data = data + "<td>"+value.inspector+"</td>"
                data = data + "<td>"
                data = data + "<button class='btn btn-info btn-sm' onclick='editData("+value.id+")'>Asignar</button>"
                
                data = data + "</td>"
                data = data + "</tr>"
            })
            $('tbody').html(data);
        }
    })
}

// --------------------------------------------- fin de llamar datos de la DB ----------------------------------------------------------

allData();

// --------------------------------------------- Limpiar los campos despues del submit -------------------------------------------------

function clearData(){
 $('#siniestro').val('');
 $('#fechaip').val('');
 $('#inspector').val('');
 $('#nameError').text('');
 $('#titleError').text('');
 $('#instituteError').text('');

}

// --------------------------------------------- fin de limpiar los campos despues del submit -------------------------------------------------

// --------------------------------------------- Agregar registros a la table de BD -------------------------------------------------


function addData(){
    var siniestro = $('#siniestro').val();
    var fechaip = $('#fechaip').val();
    var inspector = $('#inspector').val();

    $.ajax({
        type: "POST",
        dataType: "Json",
        data: {siniestro:siniestro, fechaip:fechaip, inspector:inspector},
        url:"/teacher/store/",
        success: function(data){
            allData();
            clearData();
            console.log('datos agregados con éxito');
        },

        error: function(error){
            $('#nameError').text(error.responseJSON.errors.name);
            $('#titleError').text(error.responseJSON.errors.title);
            $('#instituteError').text(error.responseJSON.errors.institute);
            
        }
    })

}

// --------------------------------------------- fin de agregar registros a la table de BD -------------------------------------------------
// --------------------------------------------- Editar registros a la table de BD ---------------------------------------------------------

function editData(id){
    



 
      $.ajax({
          type:"get",
          dataType:"json",
          url:"/teacher/edit/"+id,
          success: function(data){
             $('#addT').hide();
             $('#addButton').hide();
             $('#updateT').show();
             $('#updateButton').show();

              $('#id').val(data.id);
            //   $('#siniestro').val(data.siniestro);
            //   $('#fechaip').val(data.fechaip);
              $('#inspector').val(data.inspector);
             

              console.log(data);
          }
      })
 }

 // --------------------------------------------- Fin de editar registros a la table de BD -------------------------------------------------
 // --------------------------------------------- Update de registros a la table de BD -----------------------------------------------------



 function updateData(event){

    event.preventDefault();
     var id = $('#id').val();
    //  var siniestro =  $('#siniestro').val();
    //  var fechaip = $('#fechaip').val();
     var inspector = $('#inspector').val();

     $.ajaxSetup({
    headers:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
})
     

     $.ajax({
         type: "PUT",
         dataType: "json",
         data: {inspector:inspector},
         url: "/teacher/update/"+id,
         success: function(response){
            allData();
            clearData();
         console.log('Siniestro asignado con éxito');
         }
         
     })


 }

 


</script>
@endsection

