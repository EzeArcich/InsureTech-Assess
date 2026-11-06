<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $counts = Claim::visibleTo(auth()->user())->without(['company', 'client', 'inspector'])->selectRaw('status, COUNT(*) as total')->groupBy('status')->get();
        $statusCounts = collect(auth()->user()->isRestrictedInspector() ? ['Peritando', 'IPgestion'] : (auth()->user()->isRoutePlanner() ? ['Coordinado', 'Peritando'] : (auth()->user()->isRestrictedCoordinator() ? ['Pendiente', 'Ausente'] : ['Pendiente', 'Ausente', 'Coordinado', 'Peritando', 'IPgestion'])))
            ->mapWithKeys(fn (string $status): array => [$status => 0]);
        foreach ($counts as $count) {
            $key = $count->status === null || $count->status === '' ? '__unset__' : $count->status;
            $statusCounts[$key] = ($statusCounts[$key] ?? 0) + (int) $count->total;
        }
        $totalClaims = (int) $statusCounts->sum();
        $companies = Company::withCount(['claims' => fn ($query) => $query->visibleTo(auth()->user())])->orderByDesc('claims_count')->orderBy('name')->get();
        $companies = $companies->filter(fn ($company) => ! auth()->user()->isRestrictedInspector() || $company->claims_count > 0)->values();
        $recentClaims = Claim::visibleTo(auth()->user())->latest('id')->limit(5)->get();

        return view('home', compact('statusCounts', 'totalClaims', 'companies', 'recentClaims'));
    }
}
