<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function __invoke(Request $request, User $user): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);
        abort_if($request->user()->is($user), 422, 'Ya estás conectado con este usuario.');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        Auth::guard('web')->login($user, false);
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Ingresaste como '.$user->name.'.');
    }
}
