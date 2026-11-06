<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveClaimRequest;
use App\Models\Client;
use App\Models\Company;
use App\Models\File;
use App\Models\Inspector;
use App\Models\Productores;
use App\Models\Siniestro;
use App\Models\TH;
use App\Services\SaveClaim;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class SiniestroController extends Controller
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next): Response {
            if ($request->user()->isRestrictedInspector() || $request->user()->isRoutePlanner() || $request->user()->canCoordinateInspections()) {
                return $next($request);
            }
            abort_unless($request->user()->canAny(['ver-siniestro', 'crear-siniestro', 'editar-siniestro', 'borrar-siniestro', 'ceo-siniestro']), 403);

            return $next($request);
        })->only('index');
        $this->middleware('permission:crear-siniestro|ceo-siniestro', ['only'=>['create','store']]);
        $this->middleware(function (Request $request, Closure $next): Response {
            abort_unless($request->user()->isRestrictedInspector() || $request->user()->canCoordinateInspections() || $request->user()->canAny([
                'editar-siniestro', 'derivar-siniestro', 'peritos-siniestro', 'terceros-siniestro', 'cotizar-siniestro', 'ceo-siniestro',
            ]), 403);

            return $next($request);
        })->only(['edit', 'update']);
        $this->middleware('permission:borrar-siniestro', ['only'=>['destroy']]);
    }

    public function index(): View
    {
        if (Auth::user()->isRoutePlanner()) {
            $status = request('status', 'Peritando');
            abort_unless(in_array($status, ['Peritando', 'Coordinado'], true), 403);
            if ($status === 'Coordinado') {
                return app(RouteSheetController::class)->index(request());
            }
            request()->merge(['status' => 'Peritando']);
        }
        $filters = request()->validate([
            'status' => ['sometimes', 'nullable', 'string', 'max:191'],
            'company_id' => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
            'date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
        ]);
        $query = Siniestro::visibleTo(Auth::user());
        if (Auth::user()->isRestrictedInspector() && ! empty($filters['status'])) {
            abort_unless(in_array($filters['status'], ['Peritando', 'IPgestion'], true), 403);
        }
        if (! empty($filters['status'])) {
            if ($filters['status'] === '__unset__') {
                $query->where(fn ($query) => $query->whereNull('status')->orWhere('status', ''));
            } else {
                $query->where('status', $filters['status']);
            }
        } elseif (empty($filters['company_id']) && ! Auth::user()->isRestrictedInspector()) {
            $query->whereIn('status', ['Pendiente', 'Ausente']);
        }
        if (! empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }
        if (! empty($filters['date'])) {
            $query->whereDate('inspection_date', $filters['date']);
        }
        $siniestros = $query->orderBy('inspection_date')->orderBy('id')->get();
        return view('siniestros.index', compact('siniestros'));
    }

    public function pendientes(): View
    {
        $currentUser = Auth::user();
        $siniestros = Siniestro::visibleTo(Auth::user())->where('updated_by', $currentUser->id)->where('status', 'Pendiente')->get();

        return view('siniestros.index', compact('siniestros'));
    }

    public function ausentes(): View
    {
        $currentUser = Auth::user();
        $siniestros = Siniestro::visibleTo(Auth::user())->where('updated_by', $currentUser->id)->where('status', 'Ausente')->get();

        return view('siniestros.index', compact('siniestros'));
    }

    public function peritos(): View
    {
        request()->merge(['status' => 'Peritando']);

        return $this->index();
    }

    public function peritosgestion(): View
    {
        request()->merge(['status' => 'IPgestion']);

        return $this->index();
    }

    public function terceros(): View
    {
        $siniestros = Siniestro::visibleTo(Auth::user())->where('client_type', 'Tercero')->get();
        return view('siniestros.terceros', compact('siniestros'));
    }

    public function cotizaciones(): View
    {
        $siniestros = Siniestro::visibleTo(Auth::user())->where('client_type', 'Cotizacion')->get();
        return view('siniestros.cotizaciones', compact('siniestros'));
    }

    public function create(Siniestro $claim): View
    {
        $siniestro = $claim;
        $users = Inspector::orderBy('name')->get();
        $talleres = TH::all();
        $siniestros = Siniestro::all();
        $productores = Productores::all();
        $companies = Company::orderBy('name')->get();

        return view('siniestros.crear', compact('talleres', 'users', 'siniestros', 'siniestro', 'productores', 'companies'));
    }

    public function store(SaveClaimRequest $request, SaveClaim $saveClaim): RedirectResponse
    {
        $claim = $saveClaim->handle($request->validated());
        $this->storeUploads($request, $claim);

        return redirect()->route('claims.index')->with('success', 'Siniestro cargado con éxito');
    }

    public function show(Siniestro $claim): View
    {
        $siniestro = Siniestro::visibleTo(Auth::user())->findOrFail($claim->id);
        $files = File::where('claim_id', $siniestro->id)->get();
        return view('siniestros.show', compact('files', 'siniestro'));
    }

    public function edit(Siniestro $claim): View
    {
        $siniestro = Siniestro::visibleTo(Auth::user())->findOrFail($claim->id);
        if (Auth::user()->isRestrictedInspector()) {
            $siniestro->load('company.rates', 'parts', 'workshop');
            return view('siniestros.inspection', ['siniestro' => $siniestro, 'selectedWorkshop' => \App\Models\Workshop::find(old('workshop_id', $siniestro->workshop_id))]);
        }
        $relaciones = Siniestro::visibleTo(Auth::user())->where('license_plate', $siniestro->license_plate)->where('status', 'Pendiente')->get();
        $users = Inspector::orderBy('name')->get();
        $talleres = TH::all();
        $productores = Productores::all();
        $companies = Company::orderBy('name')->get();

        return view('siniestros.editar', compact('siniestro', 'talleres','users', 'productores', 'relaciones', 'companies'));
    }

    public function inspection(Siniestro $claim): View
    {
        $siniestro = Siniestro::visibleTo(Auth::user())->findOrFail($claim->id);
        abort_unless(Auth::user()->isRestrictedInspector() || Auth::user()->canAny(['editar-siniestro', 'ceo-siniestro', 'peritos-siniestro']), 403);
        $siniestro->load('company.rates', 'parts', 'workshop');

        return view('siniestros.inspection', ['siniestro' => $siniestro, 'selectedWorkshop' => \App\Models\Workshop::find(old('workshop_id', $siniestro->workshop_id))]);
    }

    public function derivar(Siniestro $claim): View
    {
        $siniestro = $claim;
        $siniestros = Siniestro::visibleTo(Auth::user())->where('status', 'Coordinado')->orderBy(Client::select('city')->whereColumn('clients.id', 'claims.client_id'))->get();

         $users = Inspector::orderBy('name')->get();

        return view('siniestros.derivar', compact('siniestros', 'users'));
    }

    public function update(SaveClaimRequest $request, int $id, SaveClaim $saveClaim): RedirectResponse
    {
        $claim = $saveClaim->handle($request->validated(), Siniestro::visibleTo(Auth::user())->findOrFail($id));
        $this->storeUploads($request, $claim);

        return redirect()->back()->with('success', 'Siniestro actualizado con éxito');
    }

    public function destroy(int $id): RedirectResponse
    {
        $claim = Siniestro::visibleTo(Auth::user())->findOrFail($id);
        DB::transaction(function () use ($claim): void {
            $claim = Siniestro::lockForUpdate()->findOrFail($claim->id);
            if ($claim->inspectionPayment()->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages(['claim' => 'No se puede eliminar un siniestro con honorarios registrados.']);
            }
            $client = $claim->client;
            $claim->delete();
            $client?->delete();
        });

        return redirect()->route('claims.index');
    }

    public function tallerData(int $id): JsonResponse
    {
        $taller = TH::findOrFail($id);
        return response()->json($taller);
    }

    public function editData(int $id): JsonResponse
    {
        $data = Siniestro::visibleTo(Auth::user())->findOrFail($id);
        return response()->json($data);
    }

    public function allUserData(): JsonResponse
    {
        return response()->json(Inspector::orderBy('name')->get());
    }

    public function userData(int $id): JsonResponse
    {
        $inspector = Inspector::findOrFail($id);
        abort_if(Auth::user()->isRestrictedInspector() && $inspector->user_id !== Auth::id(), 404);

        return response()->json($inspector);
    }

    public function productoresData(int $id): JsonResponse
    {
        $productores = Productores::findOrFail($id);
        return response()->json($productores);
    }

    public function storeData(SaveClaimRequest $request, SaveClaim $saveClaim): JsonResponse
    {
        return response()->json($saveClaim->handle($request->validated()));
    }

    public function updateData(SaveClaimRequest $request, int $id, SaveClaim $saveClaim): JsonResponse
    {
        $claim = $saveClaim->handle($request->validated(), Siniestro::visibleTo(Auth::user())->findOrFail($id));
        $this->storeUploads($request, $claim);

        return response()->json($claim->fresh());
    }

    public function asignaciones(): View
    {
        $siniestros = Siniestro::visibleTo(Auth::user())->where('status','Coordinado')->get();

        $users = Inspector::orderBy('name')->get();

        return view('teacher.index', compact('siniestros', 'users'));
    }

    public function allData(): JsonResponse
    {
        $data = Siniestro::visibleTo(Auth::user())->where('status','Coordinado')->latest()->get();

        return response()->json($data);
    }

    public function derivaciones(Siniestro $claim): View
    {
        $siniestro = $claim;
        $date = new Carbon('tomorrow');
        if( $date->dayOfWeek == Carbon::SATURDAY || $date->dayOfWeek == Carbon::SUNDAY || $date->dayOfWeek == Carbon::FRIDAY ){
        $date = $date->next('Monday');
        }

        $siguiente = $date->toDateString();

        $siniestros = Siniestro::visibleTo(Auth::user())->where('status', 'Coordinado')->where('inspection_date', $siguiente)->get();
        $users = Inspector::orderBy('name')->get();

        return view('siniestros.derivar', compact('siniestros', 'users'));
    }

    public function coordinados(): View
    {
        if (Auth::user()->isRoutePlanner()) {
            return app(RouteSheetController::class)->index(request());
        }
        $siniestros = Siniestro::visibleTo(Auth::user())->where('status', 'Coordinado')->get();
        return view('siniestros.index', compact('siniestros'));
    }

    public function check(Request $request): string
    {
        return Siniestro::visibleTo(Auth::user())->where('license_plate', $request->input('license_plate'))->exists() ? 'not_unique' : 'unique';
    }

    private function storeUploads(SaveClaimRequest $request, \App\Models\Claim $claim): void
    {
        if ($request->hasFile('image')) {
            $claim->image = $request->file('image')->store('claims/'.$claim->id.'/covers', 'public');
            $claim->save();
        }
        foreach ($request->file('urls', []) as $file) {
            $claim->files()->create(['url' => $file->store('claims/'.$claim->id.'/attachments', 'public')]);
        }
    }
}
