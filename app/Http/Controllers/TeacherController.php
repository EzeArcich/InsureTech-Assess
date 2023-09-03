<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Siniestro;
use App\Models\User;


use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // public function index(){
        
    //     $data = Siniestro::all();
    //     return view('teacher.index', compact('data'));
    // }

     //------------allData-------------
     public function allUser(){
         $data = User::orderBy('fechaip','DESC')->get();
        
         return response()->json($data);
     }

    //-----------storeData------------

    public function storeData(Request $request){
        $request->validate([
            // 'siniestro' => 'required',
            // 'fechaip' => 'required',
            // 'institute' => 'required',
        ]);


        $data = Siniestro::insert([
            'siniestro' => $request->siniestro,
             'link' => $request->link,
             'patente' => $request->patente,
             'cliente' => $request->cliente,
             'fechaip' => $request->fechaip,
             'estado' => $request->estado,
             'modalidad' => $request->modalidad,
             'lugar' => $request->lugar,
             'direccion' => $request->direccion,
             'nombredeltaller' => $request->nombredeltaller,
             'telefono' => $request->telefono,
             'localidad' => $request->localidad,
             'email' => $request->email,
             'motivo' => $request->motivo,
             'enviarorden' => $request->enviarorden,
             'observaciones' => $request->observaciones,
             'comentariosparaip' => $request->comentariosparaip,
            
        ]);

        return response()->json($data);
    }

     public function editData($id){
         $data = User::findOrFail($id);
         return response()->json($data);
     }

     

   

}
