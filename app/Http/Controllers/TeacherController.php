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
         $data = User::orderBy('name')->get();
        
         return response()->json($data);
     }

    //-----------storeData------------

    public function storeData(\App\Http\Requests\SaveClaimRequest $request, \App\Services\SaveClaim $saveClaim)
    {
        return response()->json($saveClaim->handle($request->validated()));
    }

     public function editData($id){
         $data = User::findOrFail($id);
         return response()->json($data);
     }

     

   

}
