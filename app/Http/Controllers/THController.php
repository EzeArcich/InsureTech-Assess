<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TH;
use Carbon\Carbon;

class THController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $talleres = TH::paginate(100);

          //return DB::select('select localidad from siniestros'); //---> Devuelve los datos de la columna estados
    
        
        return view('talleres.index', compact('talleres'));

        $now = Carbon::now();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('talleres.crear');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        TH::create($request->all());
        return redirect()->route('talleres.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(TH $talleres)
    {
        $taller = TH::all();
        return view('talleres.editar', compact('taller', 'talleres'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $taller->update($request->all());
        return redirect()->route('talleres.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $taller->delete();
        return redirect()->route('talleres.index');
    }
}
