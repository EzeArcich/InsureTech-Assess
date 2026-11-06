<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveCompanyRateRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompanyRateController extends Controller
{
    public function index(): View
    {
        $companies = Company::with('rates')->orderBy('name')->get();
        $fields = ['bodywork_day' => 'Chapa / día', 'bodywork_hour' => 'Chapa / hora', 'mechanical_day' => 'Mecánica / día', 'mechanical_hour' => 'Mecánica / hora', 'paint_panel' => 'Pintura / paño'];
        $currentRates = $companies->mapWithKeys(fn (Company $company): array => [$company->id => $company->rates?->only(array_keys($fields)) ?? []])->all();

        return view('settings.rates', ['companies' => $companies, 'fields' => $fields, 'currentRates' => $currentRates,
            'relation' => 'rates', 'title' => 'Chapa, pintura y mecánica', 'applyRoute' => 'settings.repair-rates.apply']);
    }

    public function apply(SaveCompanyRateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($data): void {
            Company::when($data['scope'] === 'company', fn ($query) => $query->whereKey($data['company_id']))
                ->orderBy('id')->each(function (Company $company) use ($data): void {
                    $company->rates()->updateOrCreate(['company_id' => $company->id], Arr::only($data, ['bodywork_day', 'bodywork_hour', 'mechanical_day', 'mechanical_hour', 'paint_panel']));
                });
        });

        return redirect()->route('settings.repair-rates')->with('success', 'Valores actualizados.');
    }

    public function update(SaveCompanyRateRequest $request, Company $company): RedirectResponse
    {
        $company->rates()->updateOrCreate(['company_id' => $company->id], $request->validated());

        return redirect()->route('settings.rates')->with('success', 'Tarifas de '.$company->name.' actualizadas.');
    }
}
