<form action="{{route('contactanos.store')}}" method="post" enctype="multipart/form-data">
    {{ csrf_field() }}
    <div class="modal fade text-left" id="ModalEnviar" tabindex="-1" role="dialog" aria-hidden="true">
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
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="form-group">
                        <label for="localidad">Localidad</label>
                            <input type="text" name="localidad" class="form-control" value="{{ $siniestro->localidad }}">
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                            <div class="form-group">
                                <label for="fechaip">Fecha IP</label>
                                <input type="date" name="fechaip" class="form-control" value="{{ $siniestro->fechaip }}">
                            </div>
                        </div>
                   
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                   <label for="siniestro">Siniestro</label>
                                   <input type="text" name="siniestro" class="form-control" value="{{ $siniestro->siniestro }}">
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                        <label for="emailperito">Email del perito</label>
                                        <input type="text" name="emailperito" class="form-control" value="{{ $siniestro->emailperito }}">
                                </div>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <div class="form-group">
                                    <label for="url_siniestro">Link al siniestro actual</label>
                                    <input type="text" name="url_siniestro" class="form-control"  value="{{ $siniestro->url_siniestro }}">
                                    </div>
                                </div>
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                <label for="patente">Patente</label>
                                    <input type="text" name="patente" class="form-control" value="{{ $siniestro->patente }}">
                                </div>
                            </div>
                    
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="form-group">
                        <label for="modalidad">Tipo de inspección</label>
                            <input type="text" name="modalidad" class="form-control" value="{{ $siniestro->modalidad }}">
                        </div>
                    </div>
                    
                    </div>
                   
                    
                    
                    
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

                                        @if (session('info'))
                                            <script>
                                                alert('{{session('info')}}');
                                            </script>

                                        @endif
