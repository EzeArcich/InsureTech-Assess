<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Company;
use App\Models\InspectionPayment;
use App\Models\Inspector;
use App\Services\ResolveInspection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InspectionPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->canManageInspectionPayments() || $user->isRestrictedInspector(), 403);
        $filters = $request->validate([
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'inspector_id' => ['nullable', 'integer', 'exists:inspectors,id'],
            'payment_status' => ['nullable', 'in:pending,paid'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
        ]);
        $query = InspectionPayment::visibleTo($user)
            ->when($filters['company_id'] ?? null, fn (Builder $query, $id) => $query->where('company_id', $id))
            ->when($user->canManageInspectionPayments() && ! empty($filters['inspector_id']), fn (Builder $query) => $query->where('inspector_id', $filters['inspector_id']))
            ->when(($filters['payment_status'] ?? null) === 'pending', fn (Builder $query) => $query->whereNull('paid_at'))
            ->when(($filters['payment_status'] ?? null) === 'paid', fn (Builder $query) => $query->whereNotNull('paid_at'))
            ->when($filters['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('completed_at', '>=', $date))
            ->when($filters['to'] ?? null, fn (Builder $query, $date) => $query->whereDate('completed_at', '<=', $date));
        $totals = (clone $query)->selectRaw('COUNT(*) as total, COALESCE(SUM(amount), 0) as amount, COALESCE(SUM(CASE WHEN paid_at IS NULL THEN amount ELSE 0 END), 0) as pending_amount, COALESCE(SUM(CASE WHEN paid_at IS NULL THEN 1 ELSE 0 END), 0) as pending_count')->first();
        $byCompany = (clone $query)->selectRaw('company_id, COUNT(*) as total, SUM(amount) as amount')->groupBy('company_id')->with('company')->orderByDesc('amount')->get();
        $byInspector = (clone $query)->selectRaw('inspector_id, COUNT(*) as total, SUM(amount) as amount')->groupBy('inspector_id')->with('inspector')->orderByDesc('amount')->get();
        $payments = (clone $query)->with(['claim', 'company', 'inspector'])->orderByDesc('completed_at')->paginate(25)->withQueryString();
        $companies = Company::orderBy('name')->get();
        $inspectors = $user->canManageInspectionPayments() ? Inspector::orderBy('name')->get() : collect();
        $readyClaims = $user->canManageInspectionPayments()
            ? Claim::where('status', 'IPgestion')->whereDoesntHave('inspectionPayment')
                ->when($filters['company_id'] ?? null, fn (Builder $query, $id) => $query->where('company_id', $id))
                ->when($filters['inspector_id'] ?? null, fn (Builder $query, $id) => $query->where('inspector_id', $id))
                ->orderBy('id')->paginate(15, ['*'], 'ready_page')->withQueryString()
            : null;

        return view('inspection-payments.index', compact('totals', 'byCompany', 'byInspector', 'payments', 'companies', 'inspectors', 'readyClaims'));
    }

    public function show(Request $request, InspectionPayment $payment): View
    {
        abort_unless($request->user()->canManageInspectionPayments() || $request->user()->isRestrictedInspector(), 403);
        $payment = InspectionPayment::visibleTo($request->user())->with(['claim', 'company', 'inspector'])->findOrFail($payment->id);

        return view('inspection-payments.show', compact('payment'));
    }

    public function resolve(Request $request, Claim $claim, ResolveInspection $resolver): RedirectResponse
    {
        $resolver->handle($claim, $request->user());

        return back()->with('success', 'Siniestro resuelto. Honorario registrado como pendiente de pago.');
    }

    public function pay(Request $request, InspectionPayment $payment): RedirectResponse
    {
        abort_unless($request->user()->canManageInspectionPayments(), 403);
        DB::transaction(function () use ($request, $payment): void {
            $payment = InspectionPayment::lockForUpdate()->findOrFail($payment->id);
            if ($payment->paid_at === null) {
                $payment->update(['paid_at' => now(), 'paid_by' => $request->user()->id]);
            }
        });

        return back()->with('success', 'Pago registrado.');
    }
}
