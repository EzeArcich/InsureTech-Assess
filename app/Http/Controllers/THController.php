<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TH;
use App\Http\Requests\SaveWorkshopRequest;
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
        $talleres = TH::orderBy('id')->get();

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
    public function store(SaveWorkshopRequest $request)
    {
        TH::create($request->workshopAttributes());
        return redirect()->route('workshops.index');
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
    public function edit(TH $workshop)
    {
        $talleres = $workshop;
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
    public function update(SaveWorkshopRequest $request, int $id)
    {
        TH::findOrFail($id)->update($request->workshopAttributes());
        return redirect()->route('workshops.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        TH::findOrFail($id)->delete();
        return redirect()->route('workshops.index');
    }
}
