<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function show(Client $client): View
    {
        abort_if((auth()->user()->isRestrictedInspector() || auth()->user()->isRoutePlanner()) && ! $client->claim()->visibleTo(auth()->user())->exists(), 404);
        $client->load('company', 'claim');

        return view('clients.show', compact('client'));
    }
}
