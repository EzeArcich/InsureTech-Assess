<form action="{{route('coordination.contact.store')}}" method="post" enctype="multipart/form-data">
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
                        <label for="city">Localidad</label>
                            <input type="text" name="city" class="form-control" value="{{ $siniestro->city }}">
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4">
                            <div class="form-group">
                                <label for="inspection_date">Fecha IP</label>
                                <input type="date" name="inspection_date" class="form-control" value="{{ $siniestro->inspection_date }}">
                            </div>
                        </div>
                   
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                   <label for="claim_number">Siniestro</label>
                                   <input type="text" name="claim_number" class="form-control" value="{{ $siniestro->claim_number }}">
                                </div>
                            </div>
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                        <label for="inspector_email">Email del perito</label>
                                        <input type="text" name="inspector_email" class="form-control" value="{{ $siniestro->inspector_email }}">
                                </div>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <div class="form-group">
                                    <label for="claim_url">Link al siniestro actual</label>
                                    <input type="text" name="claim_url" class="form-control"  value="{{ $siniestro->claim_url }}">
                                    </div>
                                </div>
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <div class="form-group">
                                <label for="license_plate">Patente</label>
                                    <input type="text" name="license_plate" class="form-control" value="{{ $siniestro->license_plate }}">
                                </div>
                            </div>
                    
                    <div class="col-xs-4 col-sm-4 col-md-4">
                        <div class="form-group">
                        <label for="inspection_mode">Tipo de inspección</label>
                            <input type="text" name="inspection_mode" class="form-control" value="{{ $siniestro->inspection_mode }}">
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
