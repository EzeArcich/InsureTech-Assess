@extends('layouts.app')

@section('content')
    
<section class="section">
        <div class="section-header">
            <h3 class="page__heading">Editar datos del taller</h3>
        </div>
        <nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/home#">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="https://siniestrosdag.com/siniestros">Siniestros</a></li>
    <li class="breadcrumb-item active" aria-current="page">Editar TH</li> 
  </ol>
</nav>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                    <div class="card-header" style="background:linear-gradient(132deg, rgba(2,0,36,1) 0%, rgba(9,51,121,1) 0%, rgba(0,176,255,1) 100%);">
                                <h4 style="color:white">Datos del taller </h4>
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

                            <form action="{{ route('talleres.update',$talleres->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="taller">Nombre</label>
                                        <input type="text" name="taller" class="form-control" id="taller" >
                                        </div>
                                    </div>   
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="direccion">Direccion</label>
                                        <input type="text" name="direccion" class="form-control" id="direccion">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="barrio">Barrio</label>
                                        <input type="text" name="barrio" class="form-control" id="barrio">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="localidad">Localidad</label>
                                        <input type="text" name="localidad" class="form-control" id="localidad" >
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="vacaiones">Vacaciones</label>
                                        <input type="text" name="vacaiones" class="form-control" id="vacaiones" >
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="descfranq">Descuento de franquicia</label>
                                        <input type="text" name="descfranq" class="form-control" id="descfranq" >
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                        <label for="telefonos">Teléfonos</label>
                                        <input type="text" name="telefonos" class="form-control" id="telefonos" >
                                        </div>
                                    </div>   
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="email">E-mail</label>
                                        <input type="text" name="email" class="form-control" id="email">
                                        </div>
                                    </div>
                                    
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="zona">Zona</label>
                                        <input type="text" name="zona" class="form-control" id="zona">
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="grua">Grúa</label>
                                        <input type="text" name="grua" class="form-control" id="grua" >
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="traslado">Traslado</label>
                                        <input type="text" name="traslado" class="form-control" id="traslado" >
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="granizo">Granizo</label>
                                        <input type="text" name="granizo" class="form-control" id="granizo" >
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="ponerep">Pone repuestos</label>
                                        <input type="text" name="ponerep" class="form-control" id="ponerep" >
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="razon">Razón</label>
                                        <input type="text" name="razon" class="form-control" id="razon" >
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <div class="form-group">
                                        <label for="cuit">CUIT</label>
                                        <input type="text" name="cuit" class="form-control" id="cuit" >
                                        </div>
                                    </div>
                                    
                                      
                                    

                                    

                                    
                                    
                                    

                                   
                                </div>
                                </div>
                                </div>
                                </div>
                                
                                                    <hr> 
                                    
                                    <button type="submit" class="btn btn-primary btn-lg">Confirmar Cambios</button>

                                    
                                    
                                    </div>
                                
                                </div>
                            </form>
                        </div>   
                    </div>    
                </div>
            </div>
        </div>
    </section>
    

    
@endsection
