@extends('layouts.app')

@section('content')


    <section class="section">
        <div class="section-header">
            <h3 class="page__heading">Dashboard</h3>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">                          
                            <div class="row">
                                    
                                <div class="col-md-3 col-xl-3">
                                         <div class="card order-card" style="background: linear-gradient(70deg, black, red);">
                                            <div class="card-block p-3">
                                            <h5 class="text-white">Pendientes</h5>                                               
                                                @php
                                                $cant_usuarios = DB::table('siniestros')
                                                    ->whereIn('estado', ['pendiente'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa fa-clock f-left  me-2"></i><span style="font-size:xl">{{$cant_usuarios}}</span></h5>
                                                
                                                <p class="m-b-0 text-right"><a href="/siniestros/pendientes" class="text-white" style="text-decoration:none">Ver más</a></p>
                                                
                                                </div> 
                                             </div> 
                                             </div>
                                             
                                    <div class="col-md-3 col-xl-3">
                                    <div class="card order-card" style="background: linear-gradient(70deg, black, yellow);">
                                            <div class="card-block p-3">
                                            <h5 class="text-white">Coordinados</h5>                                               
                                                @php
                                                $cant_usuarios = DB::table('siniestros')
                                                    ->whereIn('estado', ['coordinado'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa fa-calendar-check f-left  me-2"></i><span>{{$cant_usuarios}}</span></h5>
                                                
                                                <p class="m-b-0 text-right"><a href="/siniestros/derivaciones" class="text-white" style="text-decoration:none">Ver más</a></p>
                                                
                                            </div>                                            
                                        </div>                                    
                                    </div>
                                    

                                    <div class="col-md-3 col-xl-3">
                                        <div class="card order-card" style="background: linear-gradient(70deg, black, green);">
                                            <div class="card-block p-3">
                                            <h5 class="text-white">IP en pericia</h5>                                               
                                                @php
                                                $cant_roles = DB::table('siniestros')
                                                    ->whereIn('estado', ['Peritando'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa fa-user-tie f-left  me-2"></i><span>{{$cant_roles}}</span></h5>
                                                <p class="m-b-0 text-right"><a href="/siniestros" class="text-white" style="text-decoration:none">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>                                                                
                                    
                                    <div class="col-md-3 col-xl-3">
                                        <div class="card order-card " style="background: linear-gradient(70deg, black, #0072BB);">
                                            <div class="card-block p-3">
                                                <h5 class="text-white">Ausentes</h5>                                               
                                                @php
                                                $cant_siniestros = DB::table('siniestros')
                                                    ->whereIn('estado', ['ausente'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa  me-2 fa-calendar-xmark f-left "></i><span>{{$cant_siniestros}}</span></h5>
                                                <p class="m-b-0 text-right"><a href="/siniestros/ausentes" class="text-white" style="text-decoration:none">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>
                            
                            
                                    
                                <div class="col-md-3 col-xl-3">
                                         <div class="card order-card" style="background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,60,9,1) 100%, rgba(0,212,255,1) 100%);">
                                            <div class="card-block p-3">
                                            <h5 class="text-white">IP Peritadas</h5>                                               
                                                @php
                                                $cant_usuarios = DB::table('siniestros')
                                                    ->whereIn('estado', ['peritadas'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa fa-clock f-left  me-2"></i><span style="font-size:xl">{{$cant_usuarios}}</span></h5>
                                                
                                                <p class="m-b-0 text-right"><a href="/siniestros/pendientes" class="text-white" style="text-decoration:none">Ver más</a></p>
                                                
                                                </div> 
                                             </div> 
                                            </div>
                                            

                                    <div class="col-md-3 col-xl-3">
                                        <div class="card order-card" style="background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(20,209,156,1) 100%, rgba(0,212,255,1) 100%);">
                                            <div class="card-block p-3">
                                            <h5 class="text-white">IP Asignadas</h5>                                               
                                                @php
                                                $cant_roles = DB::table('siniestros')
                                                    ->whereIn('estado', ['Asignado'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa fa-user-tie f-left  me-2"></i><span>{{$cant_roles}}</span></h5>
                                                <p class="m-b-0 text-right"><a href="/siniestros" class="text-white" style="text-decoration:none">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>                                                                
                                    
                                    <div class="col-md-3 col-xl-3">
                                        <div class="card order-card " style="background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(83,20,209,1) 100%, rgba(0,212,255,1) 100%);">
                                            <div class="card-block p-3">
                                                <h5 class="text-white">Repuestos</h5>                                               
                                                @php
                                                $cant_siniestros = DB::table('siniestros')
                                                    ->whereIn('estado', ['Reclamo de repuestos'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa  me-2 fa-calendar-xmark f-left "></i><span>{{$cant_siniestros}}</span></h5>
                                                <p class="m-b-0 text-right"><a href="/siniestros/ausentes" class="text-white" style="text-decoration:none">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>
                            
                                <div class="col-md-3 col-xl-3">
                                        <div class="card order-card " style="background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(209,20,154,1) 100%, rgba(0,212,255,1) 100%);">
                                            <div class="card-block p-3">
                                                <h5 class="text-white">Cargar ampliacion</h5>                                               
                                                @php
                                                $cant_siniestros = DB::table('siniestros')
                                                    ->whereIn('estado', ['Cargar ampliacion'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa  me-2 fa-calendar-xmark f-left "></i><span>{{$cant_siniestros}}</span></h5>
                                                <p class="m-b-0 text-right"><a href="/siniestros/ausentes" class="text-white" style="text-decoration:none">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xl-3">
                                        <div class="card order-card " style="background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(20,208,209,1) 100%, rgba(0,212,255,1) 100%);">
                                            <div class="card-block p-3">
                                                <h5 class="text-white">Actualizacion de valores</h5>                                               
                                                @php
                                                $cant_siniestros = DB::table('siniestros')
                                                    ->whereIn('estado', ['Actualizacion de valores'])
                                                    ->count();                                               
                                                @endphp
                                                <h5 class="text-right text-white" ><i class="fa  me-2 fa-calendar-xmark f-left "></i><span>{{$cant_siniestros}}</span></h5>
                                                <p class="m-b-0 text-right"><a href="/siniestros/ausentes" class="text-white" style="text-decoration:none">Ver más</a></p>
                                            </div>
                                        </div>
                                    </div>
                            </div>                               
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

