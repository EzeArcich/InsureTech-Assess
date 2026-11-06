<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignInspectionRequest;
use App\Http\Requests\SaveInspectionLocationRequest;
use App\Models\Claim;
use App\Models\Inspector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RouteSheetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canPlanInspectionRoutes() || $user->isRestrictedInspector(), 403);
        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'inspector_id' => ['nullable', 'integer', 'exists:inspectors,id'],
        ]);
        $date = $filters['date'] ?? now()->toDateString();
        $inspectors = $user->isRestrictedInspector()
            ? Inspector::where('user_id', $user->id)->get()
            : Inspector::orderBy('name')->get();
        $inspectorId = $user->isRestrictedInspector() ? $inspectors->first()?->id : ($filters['inspector_id'] ?? null);
        $assignments = Claim::visibleTo($user)->with('workshop')->whereIn('status', ['Peritando', 'IPgestion'])
            ->whereNotNull('inspector_id')->whereDate('inspection_date', $date)
            ->when($inspectorId, fn ($query) => $query->where('inspector_id', $inspectorId))
            ->orderBy('inspection_time')->orderBy('id')->get();
        $points = $assignments->filter(fn (Claim $claim): bool => $claim->inspectionModality() === 'in_person'
            && $claim->inspection_latitude !== null && $claim->inspection_longitude !== null)
            ->map(fn (Claim $claim): array => [
                'id' => $claim->id, 'number' => $claim->claim_number, 'inspector' => $claim->inspector?->name,
                'address' => $claim->inspection_address, 'time' => $claim->inspection_time,
                'lat' => (float) $claim->inspection_latitude, 'lng' => (float) $claim->inspection_longitude,
                'url' => route('claims.show', $claim->id),
            ])->values();
        $candidates = $user->canPlanInspectionRoutes()
            ? Claim::visibleTo($user)->with('workshop')->where('status', 'Coordinado')->orderBy('inspection_date')->orderBy('id')->get()
            : collect();

        return view('route-sheets.index', compact('date', 'inspectorId', 'inspectors', 'assignments', 'points', 'candidates'));
    }

    public function assign(AssignInspectionRequest $request, Claim $claim): RedirectResponse
    {
        DB::transaction(function () use ($request, $claim): void {
            $claim = Claim::visibleTo($request->user())->lockForUpdate()->findOrFail($claim->id);
            abort_unless(in_array($claim->status, ['Coordinado', 'Peritando'], true), 422, 'Esta inspección ya no se puede asignar.');
            $claim->fill([...$request->validated(), 'status' => 'Peritando'])->save();
        });

        return redirect()->route('route-sheets.index', ['date' => $request->validated('inspection_date'), 'inspector_id' => $request->validated('inspector_id')])
            ->with('success', 'Inspección asignada.');
    }

    public function location(SaveInspectionLocationRequest $request, Claim $claim): RedirectResponse
    {
        DB::transaction(function () use ($request, $claim): void {
            $claim = Claim::visibleTo($request->user())->lockForUpdate()->findOrFail($claim->id);
            if ($claim->inspectionModality() !== 'in_person') {
                throw ValidationException::withMessages(['inspection_address' => 'Solo las inspecciones presenciales llevan una ubicación en el mapa.']);
            }
            $claim->forceFill($request->validated())->save();
        });

        return back()->with('success', 'Ubicación de la inspección guardada.');
    }
}
