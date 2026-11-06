<form action="{{route('coordination.emails.store')}}" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade text-left" id="ModalContactoViaMail" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Enviar correo</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>                    
                </div>
                <div class="modal-body">
                    <div class="row col-xs-12 col-sm-12 col-md-12">
                    
                    <div class="col-xs-12 col-sm-12 col-md-12"><h3>Para el PAS</h3></div>
                    <hr>
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                   <label for="claim_number">Siniestro</label>
                                   <input type="text" name="claim_number" id="claim_number" class="form-control" value="{{ $siniestro->claim_number }}">
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                        <label for="emailPas">Email del PAS</label>
                                        <input type="text" name="emailPas" id="emailPas" class="form-control" value="{{ $siniestro->emailPas }}">
                                </div>
                            </div>

                            <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="template">Plantilla</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="template" name="template"" value="{{ $siniestro->template }}">
                                            <option selected value="emails.contactomail">Contacto PAS</option>
                                            <!-- <option value="emails.derivacion">Para Edu</option> -->
                                            
                                        </select>
                                    </div>
                                
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                <label for="license_plate">Patente</label>
                                    <input type="text" name="license_plate" id="license_plate" class="form-control" value="{{ $siniestro->license_plate }}">
                                </div>
                            </div>

                            <div class="col-xs-3 col-sm-3 col-md-3">
                                <div class="form-group">
                                    <label for="inspection_date">Fecha IP</label>
                                    <input type="date" name="inspection_date" class="form-control" value="{{ $siniestro->inspection_date }}" >
                                </div>
                            </div>

                            <div class="col-xs-4 col-sm-4 col-md-4" hide="true">
                                        <div class="form-group">
                                        <label for="coordinator">Coordinador</label>
                                        <input type="text" name="coordinator" id="coordinator" class="form-control" value="{{\Illuminate\Support\Facades\Auth::user()->name}}">
                                        </div>
                                    </div>
                            <hr>
                            <!-- <div class="col-xs-12 col-sm-12 col-md-12"><h3>Para Edu</h3></div>
                            
                            <hr>

                            
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="inspection_date">Fecha IP</label>
                                        <input type="date" name="inspection_date" class="form-control"  value="{{ $siniestro->inspection_date }}">
                                        </div>
                                    </div>  
                                    
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="workshop_name">Nombre del taller</label>
                                        <input type="text" name="workshop_name"  class="form-control" value="{{ $siniestro->workshop_name }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="address">Dirección</label>
                                        <input type="text" name="address"  class="form-control" value="{{ $siniestro->address }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="phone">Teléfono</label>
                                        <input type="text" name="phone"  class="form-control" value="{{ $siniestro->phone }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="city">Localidad</label>
                                        <input type="text" name="city"  class="form-control" value="{{ $siniestro->city }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="email">E-mail</label>
                                        <input type="text" name="email"  class="form-control" value="{{ $siniestro->email }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="send_order">Enviar orden</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="send_order" name="send_order" value="{{ $siniestro->send_order }}">
                                            <option selected value="si">Si</option>
                                            <option value="no">No</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="inspection_time">Rango horario</label>
                                        <input type="text" name="inspection_time"  class="form-control" value="{{ $siniestro->inspection_time }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="location">Lugar de inspección</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" for="location" name="location" value="{{ $siniestro->location }}">
                                            <option selected>--seleccionar</option>
                                            <option value="TH">Taller homologado</option>
                                            <option value="Taller del asegurado">Taller del asegurado</option>
                                            <option value="Domicilio particular">Domicilio particular</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="inspection_mode">Tipo de inspeccion</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" for="inspection_mode" name="inspection_mode" value="{{ $siniestro->inspection_mode }}">
                                            <option selected >--seleccionar--</option>
                                            <option value="presencial">Presencial</option>
                                            <option value="videollamada">Videollamada</option>
                                            <option value="foto y presupuesto">Por foto y presupuesto</option>
                                            <option value="foto">Por foto</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="reason">Motivo</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="reason" name="reason" value="{{ $siniestro->reason }}">
                                            <option selected >--seleccionar--</option>
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
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="client_type">Cliente</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="client_type" name="client_type" value="{{ $siniestro->client_type }}">
                                            <option selected >--seleccionar--</option>
                                            <option value="Asegurado">Asegurado</option>
                                            <option value="Tercero">Tercero</option>
                                        </select>
                                    </div>

                                    
                                    <div class="col-xs-12 col-sm-12 col-md-12">           
                                        <label for="notes">Observaciones</label>
                                        <div class="form-floating">
                                        <textarea class="form-control" name="notes" style="height:100px" ">{{ $siniestro->notes }}</textarea>
                                        </div>
                                    </div> -->

                                    
                            
                    
                    
                    
                    </div>
                   
                    
                    
                    
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        
                        <button type="submit" class="btn btn-primary btn-lg ml-5 mb-3" onclick="Correo(event)">Enviar via Ajax</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function Correo(event){

event.preventDefault();
// var id = $('#id').val();
var siniestro =  $('#claim_number').val();
var emailPas = $('#emailPas').val();
var coordinador = $('#coordinator').val();
var patente = $('#license_plate').val();
 
 $.ajaxSetup({
headers:{
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
})


 $.ajax({
     type: "POST",
     dataType: "json",
     data: {claim_number:siniestro, emailPas:emailPas, license_plate:patente, coordinator:coordinador},
     url: "/coordination/emails",
     success: function(response){
        Swal.fire({
             icon: 'success',
             position: 'top-end',
             showConfirmButton: false,
             title: 'Siniestro asignado con éxito',
         })
         timer: 1500
        
           ;
      
      
     console.log('Mail enviado con éxito');
     }
  
 })


}
</script>

                                        @if (session('info'))
                                            <script>
                                                alert('{{session('info')}}');
                                            </script>

                                        @endif
