<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siniestro;

class DataTableController extends Controller
{
    public function paraderivar()
    {
        $siniestros = Siniestro::select('id', 'siniestro','fechaip', 'modalidad','direccion','localidad', 'inspector')->get();

        return datatables()->of($siniestros)->toJson();
    }
}
