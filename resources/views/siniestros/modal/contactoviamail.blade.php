<form action="{{route('correo')}}" method="post" enctype="multipart/form-data">
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
                                   <label for="siniestro">Siniestro</label>
                                   <input type="text" name="siniestro" id="siniestro" class="form-control" value="{{ $siniestro->siniestro }}">
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                        <label for="emailPas">Email del PAS</label>
                                        <input type="text" name="emailPas" id="emailPas" class="form-control" value="{{ $siniestro->emailPas }}">
                                </div>
                            </div>

                            <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="plantilla">Plantilla</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="plantilla" name="plantilla"" value="{{ $siniestro->plantilla }}">
                                            <option selected value="emails.contactomail">Contacto PAS</option>
                                            <!-- <option value="emails.derivacion">Para Edu</option> -->
                                            
                                        </select>
                                    </div>
                                
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                <label for="patente">Patente</label>
                                    <input type="text" name="patente" id="patente" class="form-control" value="{{ $siniestro->patente }}">
                                </div>
                            </div>

                            <div class="col-xs-3 col-sm-3 col-md-3">
                                <div class="form-group">
                                    <label for="fechaip">Fecha IP</label>
                                    <input type="date" name="fechaip" class="form-control" value="{{ $siniestro->fechaip }}" >
                                </div>
                            </div>

                            <div class="col-xs-4 col-sm-4 col-md-4" hide="true">
                                        <div class="form-group">
                                        <label for="coordinador">Coordinador</label>
                                        <input type="text" name="coordinador" id="coordinador" class="form-control" value="{{\Illuminate\Support\Facades\Auth::user()->name}}">
                                        </div>
                                    </div>
                            <hr>
                            <!-- <div class="col-xs-12 col-sm-12 col-md-12"><h3>Para Edu</h3></div>
                            
                            <hr>

                            
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="fechaip">Fecha IP</label>
                                        <input type="date" name="fechaip" class="form-control"  value="{{ $siniestro->fechaip }}">
                                        </div>
                                    </div>  
                                    
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="nombretaller">Nombre del taller</label>
                                        <input type="text" name="nombretaller"  class="form-control" value="{{ $siniestro->nombretaller }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="direccion">Dirección</label>
                                        <input type="text" name="direccion"  class="form-control" value="{{ $siniestro->direccion }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" name="telefono"  class="form-control" value="{{ $siniestro->telefono }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="localidad">Localidad</label>
                                        <input type="text" name="localidad"  class="form-control" value="{{ $siniestro->localidad }}">
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
                                        <label for="enviarorden">Enviar orden</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="enviarorden" name="enviarorden" value="{{ $siniestro->enviarorden }}">
                                            <option selected value="si">Si</option>
                                            <option value="no">No</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="horario">Rango horario</label>
                                        <input type="text" name="horario"  class="form-control" value="{{ $siniestro->horario }}">
                                        </div>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="lugar">Lugar de inspección</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" for="lugar" name="lugar" value="{{ $siniestro->lugar }}">
                                            <option selected>--seleccionar</option>
                                            <option value="TH">Taller homologado</option>
                                            <option value="Taller del asegurado">Taller del asegurado</option>
                                            <option value="Domicilio particular">Domicilio particular</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                    <label for="modalidad">Tipo de inspeccion</label>
                                    <select class="form-select col-xs-12 col-sm-12 col-md-12" aria-label="Default select example" for="modalidad" name="modalidad" value="{{ $siniestro->modalidad }}">
                                            <option selected >--seleccionar--</option>
                                            <option value="presencial">Presencial</option>
                                            <option value="videollamada">Videollamada</option>
                                            <option value="foto y presupuesto">Por foto y presupuesto</option>
                                            <option value="foto">Por foto</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-3 col-sm-3 col-md-3">
                                        <div class="form-group">
                                        <label for="motivo">Motivo</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="motivo" name="motivo" value="{{ $siniestro->motivo }}">
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
                                    <label for="cliente">Cliente</label>
                                        <select class="form-select col-xs-12 col-sm-12 col-md-12"  aria-label="Default select example" for="cliente" name="cliente" value="{{ $siniestro->cliente }}">
                                            <option selected >--seleccionar--</option>
                                            <option value="Asegurado">Asegurado</option>
                                            <option value="Tercero">Tercero</option>
                                        </select>
                                    </div>

                                    
                                    <div class="col-xs-12 col-sm-12 col-md-12">           
                                        <label for="observaciones">Observaciones</label>    
                                        <div class="form-floating">
                                        <textarea class="form-control" name="observaciones" style="height:100px" ">{{ $siniestro->observaciones }}</textarea>
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
     dataType: "json",
     data: {siniestro:siniestro, emailPas:emailPas, patente:patente, coordinador:coordinador},
     url: "/correo",
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
