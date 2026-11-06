<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveInspectionRateRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InspectionRateController extends Controller
{
    public function index(): View
    {
        $companies = Company::with('inspectionRates')->orderBy('name')->get();
        $fields = ['in_person' => 'Pericia presencial', 'photos' => 'Pericia por fotos', 'video_call' => 'Pericia por videollamada'];
        $currentRates = $companies->mapWithKeys(fn (Company $company): array => [$company->id => $company->inspectionRates?->only(array_keys($fields)) ?? []])->all();

        return view('settings.rates', ['companies' => $companies, 'fields' => $fields, 'currentRates' => $currentRates,
            'relation' => 'inspectionRates', 'title' => 'Honorarios de inspecciones', 'applyRoute' => 'settings.inspection-rates.apply']);
    }

    public function apply(SaveInspectionRateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($data): void {
            Company::when($data['scope'] === 'company', fn ($query) => $query->whereKey($data['company_id']))
                ->orderBy('id')->each(function (Company $company) use ($data): void {
                    $company->inspectionRates()->updateOrCreate(['company_id' => $company->id], Arr::only($data, ['in_person', 'photos', 'video_call']));
                });
        });

        return redirect()->route('settings.inspection-rates')->with('success', 'Valores actualizados.');
    }

    public function update(SaveInspectionRateRequest $request, Company $company): RedirectResponse
    {
        $company->inspectionRates()->updateOrCreate(['company_id' => $company->id], $request->validated());

        return redirect()->route('settings.rates')->with('success', 'Honorarios de '.$company->name.' actualizados.');
    }
}
