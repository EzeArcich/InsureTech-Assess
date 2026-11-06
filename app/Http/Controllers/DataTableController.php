<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siniestro;

class DataTableController extends Controller
{
    public function paraderivar()
    {
        $siniestros = Siniestro::query()->get();

        return datatables()->of($siniestros)->toJson();
    }
}
