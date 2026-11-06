<?php

namespace App\Http\Controllers;

use App\Models\Inspector;
use Illuminate\View\View;

class InspectorController extends Controller
{
    public function show(Inspector $inspector): View
    {
        abort_if(auth()->user()->isRestrictedInspector() && $inspector->user_id !== auth()->id(), 404);
        $claims = $inspector->claims()->visibleTo(auth()->user())->latest('id')->get();

        return view('inspectors.show', compact('inspector', 'claims'));
    }
}
