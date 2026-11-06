<?php

namespace App\Services;

use App\Models\Claim;
use App\Models\InspectionPayment;
use App\Models\InspectionRate;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResolveInspection
{
    public function handle(Claim $claim, User $user): InspectionPayment
    {
        abort_unless($user->canManageInspectionPayments(), 403);

        return DB::transaction(function () use ($claim, $user): InspectionPayment {
            $claim = Claim::lockForUpdate()->findOrFail($claim->id);
            if ($payment = $claim->inspectionPayment()->first()) {
                return $payment;
            }
            if ($claim->status !== 'IPgestion' || ! $claim->inspector_id) {
                throw ValidationException::withMessages(['claim' => 'Solo se pueden resolver inspecciones en gestión con un perito asignado.']);
            }
            $modality = $claim->inspectionModality();
            $rate = $modality ? InspectionRate::where('company_id', $claim->company_id)->value($modality) : null;
            if ($rate === null) {
                throw ValidationException::withMessages(['claim' => 'Configurá el honorario de esta compañía y una modalidad válida antes de resolver.']);
            }
            $payment = InspectionPayment::create([
                'claim_id' => $claim->id, 'inspector_id' => $claim->inspector_id, 'company_id' => $claim->company_id,
                'modality' => $modality, 'amount' => $rate, 'completed_at' => now(), 'resolved_by' => $user->id,
            ]);
            $claim->forceFill(['status' => 'Resuelto', 'closed_at' => now()->toDateString()])->save();

            return $payment;
        });
    }
}
