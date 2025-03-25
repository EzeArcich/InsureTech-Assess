<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siniestro;
use App\Models\File;
use App\Models\TH;
use App\Models\User;
use App\Models\Productores;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

//Carbon
use Illuminate\Support\Carbon;

//mail
use App\Mail\ContactanosMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;


class SiniestroController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:ver-siniestro|crear-siniestro|editar-siniestro|borrar-siniestro|ceo-siniestro')->only('index');
        $this->middleware('permission:crear-siniestro|ceo-siniestro', ['only'=>['create','store']]);
        $this->middleware('permission:editar-siniestro|derivar-siniestro|peritos-siniestro|terceros-siniestro|cotizar-siniestro|ceo-siniestro', ['only'=>['edit','update']]);
        $this->middleware('permission:borrar-siniestro', ['only'=>['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $siniestros = Siniestro::where('estado', 'pendiente')->orWhere('estado', 'Ausente')->paginate(10);          
         return view('siniestros.index', compact('siniestros'));
  
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function pendientes()
    {
        $currentUser = Auth::user();
        $siniestros = Siniestro::where('updated_by', $currentUser->id)->where('estado', 'Pendiente')->paginate(10);  
        
        return view('siniestros.index', compact('siniestros'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function ausentes()
    {
        $currentUser = Auth::user();
        $siniestros = Siniestro::where('updated_by', $currentUser->id)->where('estado', 'Ausente')->paginate(10);       
        
        
        return view('siniestros.index', compact('siniestros'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function peritos()
    {
        $currentUser = Auth::user();
        $siniestros = Siniestro::where('inspector', $currentUser->name)->where('estado', 'Peritando')->orderBy('fechaip')->get();
        return view('siniestros.peritos', compact('siniestros'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function peritosgestion()
    {
        $currentUser = Auth::user();
        $siniestros = Siniestro::where('inspector', $currentUser->name)->where('estado', 'IPgestion')->orderBy('fechaip')->get();
        return view('siniestros.peritos', compact('siniestros'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function terceros()
    {
        $siniestros = Siniestro::where('cliente', 'Tercero')->get();        
         return view('siniestros.terceros', compact('siniestros'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function cotizaciones()
    {
        $siniestros = Siniestro::where('cliente', 'Cotizacion')->get();        
         return view('siniestros.cotizaciones', compact('siniestros'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

   
    public function create(Siniestro $siniestro)
    {
        $users = User::all();
        $talleres = TH::all();
        $siniestros = Siniestro::all();
        $productores = Productores::all();

        return view('siniestros.crear', compact('talleres','users', 'siniestros', 'siniestro','productores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $nrosiniestro= $request->siniestro;
        if($request->hasFile('imagen')){
        $file = $request->file('imagen');
        $fileName= pathinfo($request->file('imagen')->getClientOriginalName(), PATHINFO_FILENAME).'-'.$request->siniestro.'.'.$request->file('imagen')->getClientOriginalExtension();
        $request->imagen = $fileName;
        $file->move(("cover/"),$fileName);

       }
       
       
       $siniestro = Siniestro::create([
        'siniestro' => $request->siniestro,
        'patente' => $request->patente,
        'imagen' => $request->imagen,
        'observaciones' => $request->observaciones,
        'compania' => $request->compania,
        'estado' => $request->estado,
        'nrocorto' => $request->nrocorto,
        'cliente' => $request->cliente

       ]);
        

        

        if($request->hasFile('urls')){
            $files=$request->file('urls');
            foreach ($files as $file) {
                $urlName=time().'_'.$file->getClientOriginalName();
                $request['siniestro_id']=$siniestro->id;
                $request['url']=$urlName;
                $file->move(("urls"),$urlName);
                File::create($request->all());
            }
        } 

       
        return redirect()->route('siniestros.index')
        ->with('success','Siniestro cargado con éxtio');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Siniestro $siniestro)
    {
        
        
        $files = File::where('siniestro_id', $siniestro->id)->get();
        return view('siniestros.show', compact('files','siniestro'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit(Siniestro $siniestro)
    {
        $relaciones = Siniestro::where('patente', $siniestro->patente)->where('estado', 'Pendiente')->get();
        $users = User::all();
        $talleres = TH::all();
        $productores = Productores::all();
        
        
        return view('siniestros.editar', compact('siniestro', 'talleres','users', 'productores', 'relaciones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     

   
    public function derivar(Siniestro $siniestro)
    {
        
         $siniestros = Siniestro::where('estado', 'coordinado')->orderBy('localidad')->get();

         return view('siniestros.derivar', compact('siniestros'));

        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     

    public function update(Request $request, $id) // Siniestro, $siniestro ivan antes de las modificaciones para multiples archivos
    {
       

        $siniestro=Siniestro::findOrFail($id);
        $nrosiniestro= $siniestro->siniestro;
        if($request->hasFile('imagen')){
            if(File::exists("imagen/".$siniestro->imagen)){
                File::delete("imagen/".$siniestro->imagen);
            }
            $file=$request->file("imagen");
            $siniestro->imagen=time()."_".$file->getClientOriginalName();
            $file->move(("imagen/$nrosiniestro"),$siniestro->imagen);
            $request['imagen']=$siniestro->imagen;
        }

       

        if($request->hasFile('urls')){
            $files=$request->file('urls');
            foreach ($files as $file) {
                $urlName=time().'_'.' '.$nrosiniestro.' '.$file->getClientOriginalName();
                $request['siniestro_id']=$id;
                $request['url']=$urlName;
                $file->move(("urls/$nrosiniestro"),$urlName);
                File::create($request->all());
            }
        }

       
        $siniestro->update( $request->all());
        return redirect()->back()
        ->with('success','Siniestro actualizado con éxito');

        
          
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */




    

    public function destroy($id)
    {
        $siniestros=Siniestro::findOrFail($id);

        if($siniestros->exists("imagen/".$siniestros->imagen)){
            $siniestros->delete("imagen/".$siniestros->imagen);
        }

        $files=File::where("siniestro_id",$siniestros->id)->get();
        foreach($files as $file){
            if ($file->exists("urls/".$file->url)) { // Hay que corregir el código para que borre las imagenes de las carpetas
                $file->delete("urls/".$file->url);
            }

        }

        $siniestros->delete();
        return redirect()->route('siniestros.index');


        // $siniestro->delete(); asi estaba funcionando bien, con este
        
    }

    public function tallerData($id){
        $taller = TH::findOrFail($id);
        return response()->json($taller);
    }

    public function editData($id){
        $data = Siniestro::findOrFail($id);
        return response()->json($data);
    }

    public function userData($id){
        $users = User::findOrFail($id);
        return response()->json($users);
    }

    public function productoresData($id){
        $productores = Productores::findOrFail($id);
        return response()->json($productores);
    }

    public function storeData(Request $request){
        $data = Siniestro::insert($request->all());

        return response()->json($data);
    }

    public function updateData(Request $request, $id)
    {
        $request->validate([
            
            
        ]);

        $data=Siniestro::findOrFail($id);
        if($request->hasFile('imagen')){
            if(File::exists("imagen/".$data->imagen)){
                File::delete("imagen/".$data->imagen);
            }
            $file=$request->file("imagen");
            $data->imagen=time()."_".$file->getClientOriginalName();
            $file->move(("imagen"),$data->imagen);
            $request['imagen']=$data->imagen;
        }

            

        if($request->hasFile('urls')){
            $files=$request->file('urls');
            foreach ($files as $file) {
                $urlName=time().'_'.$file->getClientOriginalName();
                $request['siniestro_id']=$id;
                $request['url']=$urlName;
                $file->move(("urls"),$urlName);
                File::create($request->all());
            }
        }

        




        $data = Siniestro::findOrFail($id)->update(( $request->all()));
        return response()->json($data);

        /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    }

    public function asignaciones(){
        
        
        $siniestros = Siniestro::where('estado','coordinado')->get();

        $users = User::all();

        
        
        return view('teacher.index', compact('siniestros', 'users'));
    }

    //------------allData-------------
    public function allData(){
          
        $data = Siniestro::where('estado','coordinado')->latest()->get();
        

           return response()->json($data);
  
    }

    public function derivaciones(Siniestro $siniestro)
    {
        
        

        $date = new Carbon('tomorrow');
        if( $date->dayOfWeek == Carbon::SATURDAY || $date->dayOfWeek == Carbon::SUNDAY || $date->dayOfWeek == Carbon::FRIDAY ){
        $date = $date->next('Monday');
        }

        $siguiente = $date->toDateString();

        $siniestros = Siniestro::where('estado', 'coordinado')->where('fechaip', $siguiente)->get();
        $users = User::all();

        

         return view('siniestros.derivar', compact('siniestros', 'users'));

        
    }

    public function coordinados()
    {
        $siniestros = Siniestro::where('estado', 'coordinado')->get();
        return view('siniestros.index', compact('siniestros'));
    }

    public function check(Request $request)
    {
        if($request->get('patente'))
        {
            $patente = $request->get('patente');
            $data = DB::table("siniestros")
            ->where('patente', $patente)
            ->count();

            if($data > 0)
            {
                echo 'not_unique';
            }
            else
            {
                echo 'unique';
            }
        }
    }

    

    
         

       

        


        
    
}
