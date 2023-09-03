@extends('layouts.app')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css">

@section('content')


 
<style type="text/css">
    
    /* img:hover{
        filter: brightness(50%);
    } */
    #fotos {
        -webkit-border-radius: 15px 15px 15px 15px;
        padding: 10px;
        
        transition: .5s ease-in-out;
    }
</style>



    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Adjuntos</h3>
        </div>
        <div class="section-body">
            <div class="row">
                
                    
                        

                           @foreach($files as $file)


                                <div class="card border-primary col-xs-4 col-sm-4 col-md-4" style="max-width: 18rem;">
                                
                                    <div class="card-body text-primary">
                                        <a href="../urls/{{$siniestro->siniestro}}/{{ ($file->url) }}" class="fancybox" data-fancybox="gallery1">
                                            <img src="../urls/{{$siniestro->siniestro}}/{{ ($file->url) }}" width="250px" height="300px" id="fotos" alt="../urls/{{$siniestro->siniestro}}/{{ ($file->url) }} - Vista preliminar no disponible - Click para descargar archivo" style="padding:2px;">
                                            
                                        </a>
                                    </div>
                                    <div class="card-footer">
                                        
                                    </div>
                                </div>

                            @endforeach
                            
                        
                    
                
            </div>
        </div>
    </section>
   

    
@endsection
@section('javas')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

@endsection


