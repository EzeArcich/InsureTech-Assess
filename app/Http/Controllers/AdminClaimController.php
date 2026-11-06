<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AdminClaimController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        return view('siniestros.all', compact('filters'));
    }

    public function data(Request $request): JsonResponse
    {
        $filters = $this->filters($request);
        $request->merge([
            'length' => min(100, max(10, $request->integer('length', 25))),
            'start' => max(0, $request->integer('start')),
        ]);

        $claims = DB::table('claims')
            ->leftJoin('companies', 'claims.company_id', '=', 'companies.id')
            ->leftJoin('clients', 'claims.client_id', '=', 'clients.id')
            ->leftJoin('inspectors', 'claims.inspector_id', '=', 'inspectors.id')
            ->select([
                'claims.id', 'claims.claim_number', 'claims.license_plate', 'claims.status',
                'claims.inspection_date', 'claims.created_at', 'claims.client_id', 'claims.inspector_id',
                'companies.name as company_name', 'clients.name as client_name',
                'inspectors.name as inspector_name',
            ]);

        if (isset($filters['status'])) {
            if ($filters['status'] === '__unset__') {
                $claims->where(fn ($query) => $query->whereNull('claims.status')->orWhere('claims.status', ''));
            } else {
                $claims->where('claims.status', $filters['status']);
            }
        }
        if (isset($filters['company_id'])) {
            $claims->where('claims.company_id', $filters['company_id']);
        }

        return DataTables::of($claims)->escapeColumns('*')->toJson();
    }
    /** @return array{status?: ?string, company_id?: int|string|null} */
    private function filters(Request $request): array
    {
        return $request->validate([
            'status' => ['sometimes', 'nullable', 'string', 'max:191'],
            'company_id' => ['sometimes', 'nullable', 'integer', 'exists:companies,id'],
        ]);
    }
}
